<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('chatbot');

$user = currentUser();

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    if (isAdmin()) {
        db()->delete('chat_conversations', 'id = ?', [$id]);
        logActivity($user['id'], 'delete', "Conversa #{$id} eliminada");
        header('Location: chat-conversations.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

if ($action === 'delete_session' && $id) {
    if (isAdmin()) {
        $session = db()->fetchOne("SELECT session_id FROM chat_conversations WHERE id = ?", [$id]);
        if ($session) {
            db()->delete('chat_conversations', 'session_id = ?', [$session['session_id']]);
            logActivity($user['id'], 'delete', "Sessão {$session['session_id']} eliminada");
        }
        header('Location: chat-conversations.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$sessionFilter = sanitize($_GET['session'] ?? '');
$where = '';
$params = [];
if ($sessionFilter) {
    $where = 'session_id = ?';
    $params[] = $sessionFilter;
}

$total = db()->count('chat_conversations', $where, $params);
$whereClause = $where ? "WHERE {$where}" : '';
$conversations = db()->fetchAll(
    "SELECT * FROM chat_conversations {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
);

$sessions = db()->fetchAll(
    "SELECT session_id, COUNT(*) as msg_count, MIN(created_at) as first_msg, MAX(created_at) as last_msg 
     FROM chat_conversations GROUP BY session_id ORDER BY last_msg DESC LIMIT 50"
);

$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversas Chatbot - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .chat-msg-box { background: rgba(255,255,255,0.03); border-radius: 8px; padding: 16px; margin: 8px 0; border-left: 3px solid var(--primary); }
        .chat-msg-box.user-msg { border-left-color: var(--success); }
        .chat-msg-box.bot-msg { border-left-color: var(--primary); }
        .chat-msg-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .chat-msg-label.user-msg { color: var(--success); }
        .chat-msg-label.bot-msg { color: var(--primary); }
        .chat-msg-text { font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; }
        .session-list { max-height: 400px; overflow-y: auto; }
        .session-item { padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center; border: 1px solid transparent; margin-bottom: 4px; }
        .session-item:hover { background: rgba(255,255,255,0.05); border-color: var(--border); }
        .session-item.active { background: rgba(0,212,255,0.1); border-color: var(--primary); }
        .session-item small { color: var(--text-muted); font-size: 0.75rem; }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-robot"></i> <span>Conversas do Chatbot</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'deleted'): ?>
                <div class="alert alert-success">Conversa(s) eliminada(s) com sucesso!</div>
            <?php endif; ?>

            <?php if ($action === 'view' && $id): ?>
                <?php
                $chat = db()->fetchOne("SELECT * FROM chat_conversations WHERE id = ?", [$id]);
                if (!$chat) { echo '<div class="alert alert-danger">Conversa não encontrada</div>'; exit; }
                $sessionChats = db()->fetchAll("SELECT * FROM chat_conversations WHERE session_id = ? ORDER BY created_at ASC", [$chat['session_id']]);
                ?>
                <div style="margin-bottom:16px;">
                    <a href="chat-conversations.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
                    <a href="?action=delete_session&id=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar toda a sessão?')"><i class="fas fa-trash"></i> Eliminar Sessão</a>
                </div>
                <div class="detail-card">
                    <div class="detail-header">
                        <h2>Sessão: <?= sanitize($chat['session_id']) ?></h2>
                        <small style="color:var(--text-muted);"><?= count($sessionChats) ?> mensagens</small>
                    </div>
                    <div class="detail-meta" style="grid-template-columns:repeat(3,1fr);">
                        <p><i class="fas fa-globe"></i> IP: <?= sanitize($chat['ip_address'] ?: 'N/A') ?></p>
                        <p><i class="fas fa-microchip"></i> Tokens: <?= $chat['tokens_used'] ?></p>
                        <p><i class="fas fa-clock"></i> <?= formatDate($chat['created_at']) ?></p>
                    </div>
                    <div style="margin-top:20px;">
                        <?php foreach ($sessionChats as $c): ?>
                            <div class="chat-msg-box user-msg">
                                <div class="chat-msg-label user-msg"><i class="fas fa-user"></i> Cliente</div>
                                <div class="chat-msg-text"><?= sanitize($c['user_message']) ?></div>
                                <small style="color:var(--text-muted);font-size:0.7rem;"><?= formatDate($c['created_at']) ?></small>
                            </div>
                            <div class="chat-msg-box bot-msg">
                                <div class="chat-msg-label bot-msg"><i class="fas fa-robot"></i> ANGONUEVE Bot</div>
                                <div class="chat-msg-text"><?= sanitize($c['bot_reply']) ?></div>
                                <small style="color:var(--text-muted);font-size:0.7rem;"><?= formatDate($c['created_at']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="display:grid;grid-template-columns:350px 1fr;gap:20px;">
                    <div class="detail-card">
                        <h3 style="margin-bottom:16px;font-size:1rem;"><i class="fas fa-comments"></i> Sessões Recentes</h3>
                        <div class="session-list">
                            <?php if (empty($sessions)): ?>
                                <p class="empty-state" style="padding:20px;">Nenhuma conversa encontrada</p>
                            <?php else: ?>
                                <?php foreach ($sessions as $s): ?>
                                    <div class="session-item <?= $sessionFilter === $s['session_id'] ? 'active' : '' ?>" onclick="window.location='?session=<?= urlencode($s['session_id']) ?>'">
                                        <div>
                                            <strong style="font-size:0.85rem;"><?= substr(sanitize($s['session_id']), 0, 16) ?>...</strong>
                                            <small style="display:block;"><?= $s['msg_count'] ?> msgs • <?= timeAgo($s['last_msg']) ?></small>
                                        </div>
                                        <small><?= formatDate($s['first_msg'], 'd/m') ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-card">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mensagem</th>
                                    <th>Tokens</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($conversations)): ?>
                                    <tr><td colspan="5" class="empty-state">Nenhuma conversa encontrada</td></tr>
                                <?php else: ?>
                                    <?php foreach ($conversations as $c): ?>
                                        <tr>
                                            <td>#<?= $c['id'] ?></td>
                                            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                <strong>Q:</strong> <?= sanitize(substr($c['user_message'], 0, 80)) ?>...
                                            </td>
                                            <td><?= $c['tokens_used'] ?></td>
                                            <td><?= formatDate($c['created_at'], 'd/m/Y H:i') ?></td>
                                            <td class="actions">
                                                <a href="?action=view&id=<?= $c['id'] ?>" class="btn-icon" title="Ver conversa"><i class="fas fa-eye"></i></a>
                                                <a href="?action=delete&id=<?= $c['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar esta mensagem?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if ($total > $perPage): ?>
                            <div class="pagination">
                                <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                                    <a href="?page=<?= $i ?>&session=<?= urlencode($sessionFilter) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
