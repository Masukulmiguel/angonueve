<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$invoices = getClientInvoices($user['id']);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Facturas - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .empty-state i { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.3; display: block; }
        .btn-pay { background: var(--success); color: #000; }
        .btn-pay:hover { box-shadow: 0 4px 20px rgba(0,230,118,0.3); }
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
                <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="services.php" class="<?= $currentPage === 'services.php' ? 'active' : '' ?>">
                    <i class="fas fa-concierge-bell"></i> Serviços
                </a>
                <a href="orders.php" class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Encomendas
                </a>
                <a href="invoices.php" class="<?= $currentPage === 'invoices.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i> Facturas
                </a>
                <a href="chat.php">
                    <i class="fas fa-comments"></i> Chat Suporte
                </a>
                <hr>
                <a href="../index.html" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Site
                </a>
                <a href="logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search">
                    <i class="fas fa-file-invoice"></i>
                    <span>Minhas Facturas</span>
                </div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-content">
                <div class="table-card">
                    <?php if (empty($invoices)): ?>
                        <p class="empty-state">
                            <i class="fas fa-file-invoice"></i>
                            Nenhuma factura encontrada
                        </p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td><strong><?= sanitize($inv['invoice_no']) ?></strong></td>
                                        <td><?= sanitize($inv['service_name'] ?: 'Serviço') ?></td>
                                        <td>Kz <?= number_format($inv['total'], 0, ',', ' ') ?></td>
                                        <td><?= $inv['due_date'] ? formatDate($inv['due_date'], 'd/m/Y') : '-' ?></td>
                                        <td><?= statusBadge($inv['status']) ?></td>
                                        <td><?= formatDate($inv['created_at'], 'd/m/Y') ?></td>
                                        <td class="actions">
                                            <a href="invoice-view.php?id=<?= $inv['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                            <a href="invoice-view.php?id=<?= $inv['id'] ?>&pay=1" class="btn-icon" title="Pagar" style="color:var(--success);"><i class="fas fa-credit-card"></i></a>
                                        </td>
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
