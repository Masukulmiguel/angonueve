<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('abandoned');

$user = currentUser();
$days = intval($_GET['days'] ?? 2);
$data = getAbandonedOrders($days);

if (isset($_GET['recover']) && $days) {
    $oid = intval($_GET['recover']);
    db()->update('orders', ['status' => 'confirmed'], 'id = :id', ['id' => $oid]);
    logActivity($user['id'], 'recover', "Encomenda #{$oid} recuperada");
    header('Location: abandoned.php?days=' . $days . '&msg=recovered');
    exit;
}

if (isset($_GET['cancel']) && $days) {
    $oid = intval($_GET['cancel']);
    db()->update('orders', ['status' => 'cancelled', 'abandoned' => 1], 'id = :id', ['id' => $oid]);
    logActivity($user['id'], 'cancel', "Encomenda #{$oid} cancelada");
    header('Location: abandoned.php?days=' . $days . '&msg=cancelled');
    exit;
}

if (isset($_GET['export'])) {
    $headers = ['#', 'Cliente', 'Email', 'Telefone', 'Serviço', 'Plano', 'Valor (Kz)', 'Data', 'Dias'];
    $rows = [];
    foreach ($data['abandoned'] as $o) {
        $rows[] = [$o['id'], $o['customer_name'], $o['customer_email'], $o['customer_phone'], $o['service_name'], $o['plan_name'], number_format($o['price_monthly'], 0, ',', ' '), $o['created_at'], $o['days_old']];
    }
    exportCSV($headers, $rows, 'compras-abandonadas.csv');
}

$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras Abandonadas - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .abandoned-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .summary-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; text-align: center; }
        .summary-card .num { font-size: 1.6rem; font-weight: 800; margin-top: 4px; }
        .summary-card .lbl { font-size: 0.8rem; color: var(--text-muted); }
        .filter-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .filter-bar select { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.85rem; outline: none; }
        .filter-bar select:focus { border-color: var(--primary); }
        .filter-bar select option { background: #0d1f3c; }
        @media (max-width: 768px) { .abandoned-summary { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-cart-arrow-down"></i> <span>Compras Abandonadas</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'recovered'): ?><div class="alert alert-success">Encomenda recuperada com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'cancelled'): ?><div class="alert alert-success">Encomenda cancelada e marcada como abandonada!</div><?php endif; ?>

            <div class="abandoned-summary">
                <div class="summary-card" style="border-color:rgba(255,82,82,0.3);">
                    <div style="font-size:1.5rem;color:var(--danger);"><i class="fas fa-times-circle"></i></div>
                    <div class="num" style="color:var(--danger);"><?= $data['total_abandoned'] ?></div>
                    <div class="lbl">Abandonadas</div>
                </div>
                <div class="summary-card" style="border-color:rgba(255,171,0,0.3);">
                    <div style="font-size:1.5rem;color:var(--warning);"><i class="fas fa-ban"></i></div>
                    <div class="num" style="color:var(--warning);"><?= $data['total_cancelled'] ?></div>
                    <div class="lbl">Canceladas</div>
                </div>
                <div class="summary-card" style="border-color:rgba(255,82,82,0.3);">
                    <div style="font-size:1.5rem;color:var(--danger);"><i class="fas fa-coins"></i></div>
                    <div class="num" style="color:var(--danger);">Kz <?= number_format($data['abandoned_value'], 0, ',', ' ') ?></div>
                    <div class="lbl">Valor Abandonado</div>
                </div>
                <div class="summary-card" style="border-color:rgba(255,171,0,0.3);">
                    <div style="font-size:1.5rem;color:var(--warning);"><i class="fas fa-wallet"></i></div>
                    <div class="num" style="color:var(--warning);">Kz <?= number_format($data['cancelled_value'], 0, ',', ' ') ?></div>
                    <div class="lbl">Valor Cancelado</div>
                </div>
            </div>

            <div class="filter-bar">
                <form method="GET" style="display:flex;gap:8px;align-items:center;">
                    <label style="font-size:0.85rem;color:var(--text-muted);">Abandonadas há mais de</label>
                    <select name="days" onchange="this.form.submit()">
                        <?php foreach ([1, 2, 3, 5, 7, 14, 30] as $d): ?>
                            <option value="<?= $d ?>" <?= $d === $days ? 'selected' : '' ?>><?= $d ?> dia(s)</option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?export=1&days=<?= $days ?>" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Exportar CSV</a>
                </form>
            </div>

            <div class="table-card">
                <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i>
                    <strong>Encomendas Abandonadas (<?= $data['total_abandoned'] ?>)</strong>
                    <span style="color:var(--text-muted);font-size:0.8rem;">— Pendentes há +<?= $days ?> dias sem factura</span>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Plano</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Dias</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['abandoned'])): ?>
                            <tr><td colspan="8" class="empty-state">Nenhuma encomenda abandonada encontrada</td></tr>
                        <?php else: ?>
                            <?php foreach ($data['abandoned'] as $o): ?>
                                <tr>
                                    <td>#<?= $o['id'] ?></td>
                                    <td><strong><?= sanitize($o['customer_name']) ?></strong><br><small style="color:var(--text-muted);"><?= sanitize($o['customer_email']) ?></small></td>
                                    <td><?= sanitize($o['service_name']) ?></td>
                                    <td><?= sanitize($o['plan_name']) ?></td>
                                    <td>Kz <?= number_format($o['price_monthly'], 0, ',', ' ')?></td>
                                    <td><?= formatDate($o['created_at'], 'd/m/Y') ?></td>
                                    <td><span class="badge badge-danger"><?= $o['days_old'] ?> dias</span></td>
                                    <td class="actions">
                                        <a href="?recover=<?= $o['id'] ?>&days=<?= $days ?>" class="btn-icon" style="color:var(--success);" title="Recuperar" onclick="return confirm('Confirmar como recuperada?')"><i class="fas fa-check"></i></a>
                                        <a href="?cancel=<?= $o['id'] ?>&days=<?= $days ?>" class="btn-icon danger" title="Cancelar" onclick="return confirm('Cancelar encomenda?')"><i class="fas fa-ban"></i></a>
                                        <a href="orders.php?action=view&id=<?= $o['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-card" style="margin-top:20px;">
                <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-ban" style="color:var(--warning);"></i>
                    <strong>Encomendas Canceladas (<?= $data['total_cancelled'] ?>)</strong>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['cancelled'])): ?>
                            <tr><td colspan="6" class="empty-state">Nenhuma encomenda cancelada</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($data['cancelled'], 0, 20) as $o): ?>
                                <tr>
                                    <td>#<?= $o['id'] ?></td>
                                    <td><strong><?= sanitize($o['customer_name']) ?></strong></td>
                                    <td><?= sanitize($o['service_name']) ?></td>
                                    <td>Kz <?= number_format($o['price_monthly'], 0, ',', ' ')?></td>
                                    <td><?= formatDate($o['created_at'], 'd/m/Y') ?></td>
                                    <td><span style="color:var(--text-muted);font-size:0.85rem;"><?= sanitize($o['notes'] ?: '—') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
