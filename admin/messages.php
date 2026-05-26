<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('messages');

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$user = currentUser();

if ($action === 'view' && $id) {
    $msg = db()->fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$msg) { header('Location: messages.php'); exit; }
    if ($msg['status'] === 'unread') {
        db()->update('messages', ['status' => 'read'], 'id = :id', ['id' => $id]);
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mensagem #<?= $id ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body>
        <div class="admin-layout">
            <?php include __DIR__ . '/sidebar.php'; ?>
            <main class="admin-main">
                <div class="admin-header">
                    <div class="header-search"><i class="fas fa-envelope"></i> <span>Mensagem #<?= $id ?></span></div>
                    <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
                </div>
                <div class="admin-content">
                    <div class="detail-card">
                        <div class="detail-header">
                            <h2><?= sanitize($msg['name']) ?></h2>
                            <?= statusBadge($msg['status']) ?>
                        </div>
                        <div class="detail-meta">
                            <p><i class="fas fa-envelope"></i> <?= sanitize($msg['email']) ?></p>
                            <p><i class="fas fa-phone"></i> <?= sanitize($msg['phone'] ?: 'Não informado') ?></p>
                            <p><i class="fas fa-tag"></i> <?= sanitize($msg['subject']) ?></p>
                            <?php if ($msg['plan_name']): ?>
                                <p><i class="fas fa-box"></i> Plano: <?= sanitize($msg['plan_name']) ?></p>
                            <?php endif; ?>
                            <p><i class="fas fa-clock"></i> <?= formatDate($msg['created_at']) ?></p>
                        </div>
                        <div class="detail-body">
                            <h4>Mensagem:</h4>
                            <p><?= nl2br(sanitize($msg['message'])) ?></p>
                        </div>
                        <div class="detail-actions">
                            <a href="https://wa.me/<?= getSetting('whatsapp_number', '244935603163') ?>?text=Olá <?= urlencode($msg['name']) ?>, recebemos a sua mensagem sobre <?= urlencode($msg['subject']) ?>" target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> Responder via WhatsApp
                            </a>
                            <a href="mailto:<?= $msg['email'] ?>" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Responder por Email
                            </a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="read" <?= $msg['status'] === 'read' ? 'selected' : '' ?>>Lida</option>
                                    <option value="replied" <?= $msg['status'] === 'replied' ? 'selected' : '' ?>>Respondida</option>
                                    <option value="archived" <?= $msg['status'] === 'archived' ? 'selected' : '' ?>>Arquivada</option>
                                </select>
                            </form>
                            <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
                                    $newStatus = sanitize($_POST['status']);
                                    db()->update('messages', ['status' => $newStatus], 'id = :id', ['id' => $id]);
                                    $actionKey = $newStatus === 'replied' ? 'reply' : ($newStatus === 'archived' ? 'archive' : 'update_message');
                                    logActivity($user['id'], $actionKey, "Mensagem #{$id} status atualizado para {$newStatus}");
                                    echo '<meta http-equiv="refresh" content="0">';
                                }
                            ?>
                            <a href="messages.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    
</body>
    </html>
    <?php
    exit;
}

if ($action === 'delete' && $id) {
    if (isAdmin()) {
        db()->delete('messages', 'id = ?', [$id]);
        logActivity($user['id'], 'delete', "Mensagem #{$id} eliminada");
        header('Location: messages.php');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;
$where = '';
$params = [];
if ($status) { $where = 'WHERE status = ?'; $params[] = $status; }
$total = db()->count('messages', $where, $params);
$messages = db()->fetchAll("SELECT * FROM messages {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-envelope"></i> <span>Mensagens</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="table-controls">
                    <div class="filter-tabs">
                        <a href="?status=" class="btn-sm <?= !$status ? 'active' : '' ?>">Todas</a>
                        <a href="?status=unread" class="btn-sm <?= $status === 'unread' ? 'active' : '' ?>">Não lidas</a>
                        <a href="?status=read" class="btn-sm <?= $status === 'read' ? 'active' : '' ?>">Lidas</a>
                        <a href="?status=replied" class="btn-sm <?= $status === 'replied' ? 'active' : '' ?>">Respondidas</a>
                    </div>
                </div>
                <div class="table-card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Assunto</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr><td colspan="6" class="empty-state">Nenhuma mensagem encontrada</td></tr>
                            <?php else: ?>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="<?= $msg['status'] === 'unread' ? 'row-unread' : '' ?>">
                                        <td><strong><?= sanitize($msg['name']) ?></strong></td>
                                        <td><?= sanitize($msg['email']) ?></td>
                                        <td><?= sanitize($msg['subject']) ?></td>
                                        <td><?= statusBadge($msg['status']) ?></td>
                                        <td><?= formatDate($msg['created_at'], 'd/m/Y') ?></td>
                                        <td class="actions">
                                            <a href="?action=view&id=<?= $msg['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                            <a href="?action=delete&id=<?= $msg['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar mensagem?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($total > $perPage): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                                <a href="?page=<?= $i ?>&status=<?= $status ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
