<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('orders');

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$user = currentUser();

if ($action === 'view' && $id) {
    $order = db()->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
    if (!$order) { header('Location: orders.php'); exit; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
        $newStatus = sanitize($_POST['status']);
        $notes = sanitize($_POST['notes'] ?? '');
        db()->update('orders', ['status' => $newStatus, 'notes' => $notes], 'id = :id', ['id' => $id]);
        logActivity($user['id'], 'update_status', "{$user['name']} alterou estado da encomenda #{$id} para {$newStatus}");
        echo '<meta http-equiv="refresh" content="0">';
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Encomenda #<?= $id ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body>
        <div class="admin-layout">
            <?php include __DIR__ . '/sidebar.php'; ?>
            <main class="admin-main">
                <div class="admin-header">
                    <div class="header-search"><i class="fas fa-shopping-cart"></i> <span>Encomenda #<?= $id ?></span></div>
                    <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
                </div>
                <div class="admin-content">
                    <div class="detail-card">
                        <div class="detail-header">
                            <h2><?= sanitize($order['customer_name']) ?></h2>
                            <?= statusBadge($order['status']) ?>
                        </div>
                        <div class="detail-meta">
                            <p><i class="fas fa-envelope"></i> <?= sanitize($order['customer_email']) ?></p>
                            <p><i class="fas fa-phone"></i> <?= sanitize($order['customer_phone'] ?: 'Não informado') ?></p>
                            <p><i class="fas fa-cogs"></i> <?= sanitize($order['service_name']) ?></p>
                            <p><i class="fas fa-box"></i> Plano: <?= sanitize($order['plan_name']) ?></p>
                            <p><i class="fas fa-tag"></i> Kz <?= number_format($order['price_monthly'], 0, ',', ' ') ?>/<?= $order['payment_type'] === 'onetime' ? 'serviço' : 'mês' ?></p>
                            <?php if ($order['payment_type'] === 'monthly'): ?>
                                <p><i class="fas fa-calendar"></i> Kz <?= number_format($order['price_yearly'], 0, ',', ' ') ?>/ano</p>
                            <?php endif; ?>
                            <p><i class="fas fa-clock"></i> <?= formatDate($order['created_at']) ?></p>
                        </div>
                        <form method="POST" class="detail-actions">
                            <input type="hidden" name="action" value="update_status">
                            <div class="form-group" style="display:inline-block">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmada</option>
                                    <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>Em Progresso</option>
                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Concluída</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                            </div>
                            <div class="form-group" style="display:inline-block; width:300px;">
                                <input type="text" name="notes" placeholder="Notas sobre a encomenda" value="<?= sanitize($order['notes'] ?? '') ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Guardar</button>
                            <a href="https://wa.me/<?= getSetting('whatsapp_number', '244935603163') ?>?text=Olá <?= urlencode($order['customer_name']) ?>, recebemos a sua encomenda de <?= urlencode($order['service_name']) ?>" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i> Contactar</a>
                            <a href="orders.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </form>
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
        db()->delete('orders', 'id = ?', [$id]);
        logActivity($user['id'], 'delete', "{$user['name']} eliminou encomenda #{$id}");
        header('Location: orders.php');
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
if ($status) { $where = 'status = ?'; $params[] = $status; }
$total = db()->count('orders', $where, $params);
$orders = db()->fetchAll("SELECT * FROM orders {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomendas - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-shopping-cart"></i> <span>Encomendas</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="table-controls">
                    <div class="filter-tabs">
                        <a href="?status=" class="btn-sm <?= !$status ? 'active' : '' ?>">Todas</a>
                        <a href="?status=pending" class="btn-sm <?= $status === 'pending' ? 'active' : '' ?>">Pendentes</a>
                        <a href="?status=confirmed" class="btn-sm <?= $status === 'confirmed' ? 'active' : '' ?>">Confirmadas</a>
                        <a href="?status=in_progress" class="btn-sm <?= $status === 'in_progress' ? 'active' : '' ?>">Em Progresso</a>
                        <a href="?status=completed" class="btn-sm <?= $status === 'completed' ? 'active' : '' ?>">Concluídas</a>
                    </div>
                </div>
                <div class="table-card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Plano</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="8" class="empty-state">Nenhuma encomenda encontrada</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><strong><?= sanitize($order['customer_name']) ?></strong></td>
                                        <td><?= sanitize($order['service_name']) ?></td>
                                        <td><?= sanitize($order['plan_name']) ?></td>
                                        <td>Kz <?= number_format($order['price_monthly'], 0, ',', ' ')?></td>
                                        <td><?= statusBadge($order['status']) ?></td>
                                        <td><?= formatDate($order['created_at'], 'd/m/Y') ?></td>
                                        <td class="actions">
                                            <a href="?action=view&id=<?= $order['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                            <a href="?action=delete&id=<?= $order['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar encomenda?')"><i class="fas fa-trash"></i></a>
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
