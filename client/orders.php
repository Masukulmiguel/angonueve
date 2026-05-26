<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$email = $user['email'];

$status = $_GET['status'] ?? '';
$where = 'customer_email = ?';
$params = [$email];

if ($status && in_array($status, ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])) {
    $where .= ' AND status = ?';
    $params[] = $status;
}

$orders = db()->fetchAll(
    "SELECT * FROM orders WHERE {$where} ORDER BY created_at DESC",
    $params
);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Encomendas - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .order-status { display: inline-flex; align-items: center; gap: 6px; }
        .order-status i { font-size: 0.7rem; }
        .empty-state i { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.3; display: block; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar client-sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-user-circle"></i>
                <span>Cliente</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="services.php"><i class="fas fa-concierge-bell"></i> Serviços</a>
                <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Encomendas</a>
                <a href="chat.php"><i class="fas fa-comments"></i> Chat Suporte</a>
                <hr>
                <a href="../index.html" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Minhas Encomendas</span>
                </div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-content">
                <div class="table-controls">
                    <div class="filter-tabs">
                        <a href="orders.php" class="btn btn-sm <?= !$status ? 'btn-primary' : 'btn-secondary' ?>">Todas</a>
                        <a href="orders.php?status=pending" class="btn btn-sm <?= $status === 'pending' ? 'btn-primary' : 'btn-secondary' ?>">Pendentes</a>
                        <a href="orders.php?status=in_progress" class="btn btn-sm <?= $status === 'in_progress' ? 'btn-primary' : 'btn-secondary' ?>">Em Progresso</a>
                        <a href="orders.php?status=completed" class="btn btn-sm <?= $status === 'completed' ? 'btn-primary' : 'btn-secondary' ?>">Concluídas</a>
                    </div>
                </div>

                <div class="table-card">
                    <?php if (empty($orders)): ?>
                        <p class="empty-state">
                            <i class="fas fa-box-open"></i>
                            Nenhuma encomenda encontrada
                        </p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Plano</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td><strong><?= sanitize($o['service_name']) ?></strong></td>
                                        <td><?= sanitize($o['plan_name']) ?></td>
                                        <td>Kz <?= number_format($o['price_monthly'], 0, ',', ' ') ?></td>
                                        <td><?= formatDate($o['created_at'], 'd/m/Y') ?></td>
                                        <td><?= statusBadge($o['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
