<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'send') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Método não permitido'], 405);
    }

    $user = currentUser();
    if (!$user) jsonResponse(['error' => 'Não autenticado'], 401);

    $ticketId = sanitize($_POST['ticket_id'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $subject = sanitize($_POST['subject'] ?? 'Suporte');
    $senderType = $user['role'] === 'admin' ? 'admin' : 'client';

    if (!$ticketId) {
        $ticketId = 'TKT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    $attachment = null;
    if (!empty($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = uploadChatFile($_FILES['file']);
        if (isset($upload['error'])) {
            jsonResponse(['error' => $upload['error']], 400);
        }
        $attachment = $upload['filename'];
    }

    if (empty($message) && !$attachment) {
        jsonResponse(['error' => 'Mensagem ou ficheiro necessário'], 400);
    }

    $clientId = $user['role'] === 'admin' ? intval($_POST['client_id'] ?? 0) : $user['id'];
    $clientName = $user['role'] === 'admin' ? ($_POST['client_name'] ?? '') : $user['name'];
    $clientEmail = $user['role'] === 'admin' ? ($_POST['client_email'] ?? '') : $user['email'];
    $adminId = $user['role'] === 'admin' ? $user['id'] : null;

    if ($user['role'] !== 'admin') {
        $existing = db()->fetchOne("SELECT client_id, client_name, client_email FROM support_chat WHERE ticket_id = ? LIMIT 1", [$ticketId]);
        if (!$existing) {
            $clientId = $user['id'];
            $clientName = $user['name'];
            $clientEmail = $user['email'];
        } else {
            $clientId = $existing['client_id'];
            $clientName = $existing['client_name'];
            $clientEmail = $existing['client_email'];
        }
    }

    db()->insert('support_chat', [
        'ticket_id' => $ticketId,
        'client_id' => $clientId,
        'client_name' => $clientName,
        'client_email' => $clientEmail,
        'subject' => $subject,
        'sender_type' => $senderType,
        'admin_id' => $adminId,
        'message' => $message,
        'attachment' => $attachment,
        'is_read' => 0
    ]);

    logActivity($user['id'], 'chat_send', "Mensagem enviada no ticket {$ticketId}");

    if ($user['role'] === 'admin') {
        db()->update('support_chat', ['is_read' => 0], 'ticket_id = :tid AND sender_type = :st', ['tid' => $ticketId, 'st' => 'admin']);
    } else {
        db()->query("UPDATE support_chat SET is_read = 0 WHERE ticket_id = ? AND sender_type = 'admin'", [$ticketId]);
    }

    jsonResponse([
        'success' => true,
        'ticket_id' => $ticketId,
        'message' => 'Mensagem enviada'
    ]);
}

if ($action === 'poll') {
    $user = currentUser();
    if (!$user) jsonResponse(['error' => 'Não autenticado'], 401);

    $ticketId = sanitize($_GET['ticket_id'] ?? '');
    $lastId = intval($_GET['last_id'] ?? 0);

    if ($user['role'] === 'admin') {
        $where = $ticketId ? "WHERE ticket_id = ?" : "WHERE 1=1";
        $params = $ticketId ? [$ticketId] : [];
        $newMessages = db()->fetchAll(
            "SELECT sc.*, u.name as user_name FROM support_chat sc LEFT JOIN users u ON sc.admin_id = u.id {$where} AND sc.id > ? ORDER BY sc.created_at DESC LIMIT 50",
            array_merge($params, [$lastId])
        );
        if ($ticketId) {
            db()->query("UPDATE support_chat SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'client' AND is_read = 0", [$ticketId]);
        }
        $unread = db()->count('support_chat', "is_read = 0 AND sender_type = 'client'");
    } else {
        $newMessages = db()->fetchAll(
            "SELECT * FROM support_chat WHERE ticket_id = ? AND id > ? ORDER BY created_at ASC",
            [$ticketId, $lastId]
        );

        db()->query("UPDATE support_chat SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'admin' AND is_read = 0", [$ticketId]);
    }

    $tickets = [];
    if ($user['role'] === 'admin') {
        $tickets = db()->fetchAll(
            "SELECT ticket_id, client_id, client_name, client_email, subject,
                    MAX(created_at) as last_msg,
                    (SELECT message FROM support_chat st WHERE st.ticket_id = sc.ticket_id ORDER BY id DESC LIMIT 1) as last_message,
                    (SELECT COUNT(*) FROM support_chat st WHERE st.ticket_id = sc.ticket_id AND st.is_read = 0 AND st.sender_type = 'client') as unread_count
             FROM support_chat sc
             GROUP BY ticket_id, client_id, client_name, client_email, subject
             ORDER BY unread_count DESC, last_msg DESC
             LIMIT 50"
        );
    }

    jsonResponse([
        'messages' => $newMessages,
        'tickets' => $tickets,
        'unread' => $unread ?? 0,
        'server_time' => date('Y-m-d H:i:s')
    ]);
}

if ($action === 'tickets') {
    $user = currentUser();
    if (!$user) jsonResponse(['error' => 'Não autenticado'], 401);
    if ($user['role'] === 'admin') {
        $tickets = db()->fetchAll(
            "SELECT ticket_id, client_id, client_name, client_email, subject,
                    MAX(created_at) as last_msg,
                    (SELECT message FROM support_chat st WHERE st.ticket_id = sc.ticket_id ORDER BY id DESC LIMIT 1) as last_message,
                    (SELECT COUNT(*) FROM support_chat st WHERE st.ticket_id = sc.ticket_id AND st.is_read = 0 AND st.sender_type = 'client') as unread_count
             FROM support_chat sc
             GROUP BY ticket_id, client_id, client_name, client_email, subject
             ORDER BY unread_count DESC, last_msg DESC
             LIMIT 50"
        );
    } else {
        $tickets = db()->fetchAll(
            "SELECT ticket_id, subject, MAX(created_at) as last_msg,
                    (SELECT message FROM support_chat st WHERE st.ticket_id = sc.ticket_id ORDER BY id DESC LIMIT 1) as last_message,
                    (SELECT COUNT(*) FROM support_chat st WHERE st.ticket_id = sc.ticket_id AND st.is_read = 0 AND st.sender_type = 'admin') as unread_count
             FROM support_chat sc WHERE client_id = ?
             GROUP BY ticket_id, subject
             ORDER BY last_msg DESC",
            [$user['id']]
        );
    }

    jsonResponse(['tickets' => $tickets]);
}

if ($action === 'history') {
    $user = currentUser();
    if (!$user) jsonResponse(['error' => 'Não autenticado'], 401);

    $ticketId = sanitize($_GET['ticket_id'] ?? '');
    if (!$ticketId) jsonResponse(['error' => 'Ticket ID necessário'], 400);

    if ($user['role'] === 'admin') {
        $messages = db()->fetchAll(
            "SELECT sc.*, u.name as admin_name FROM support_chat sc LEFT JOIN users u ON sc.admin_id = u.id WHERE sc.ticket_id = ? ORDER BY sc.created_at ASC",
            [$ticketId]
        );
        db()->query("UPDATE support_chat SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'client' AND is_read = 0", [$ticketId]);
    } else {
        $messages = db()->fetchAll(
            "SELECT * FROM support_chat WHERE ticket_id = ? AND client_id = ? ORDER BY created_at ASC",
            [$ticketId, $user['id']]
        );
        db()->query("UPDATE support_chat SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'admin' AND is_read = 0", [$ticketId]);
    }

    jsonResponse(['messages' => $messages]);
}

jsonResponse(['error' => 'Ação inválida'], 400);
