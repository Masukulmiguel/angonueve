<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('revenue');

$user = currentUser();

$startDateInput = sanitize($_GET['start_date'] ?? '');
$endDateInput = sanitize($_GET['end_date'] ?? '');

if (!$startDateInput) {
    $startDate = date('Y-01-01');
    $startDateInput = date('Y-m');
} else {
    $startDate = $startDateInput . '-01';
}

if (!$endDateInput) {
    $endDate = date('Y-m-t');
    $endDateInput = date('Y-m');
} else {
    $endDate = $endDateInput . '-01';
    $endDate = date('Y-m-t', strtotime($endDate));
}

$stats = getRevenueStats($startDate, $endDate);

$export = $_GET['export'] ?? '';

if ($export === 'csv') {
    logActivity($user['id'], 'export_csv', 'Exportou CSV de receitas (' . $startDate . ' a ' . $endDate . ')');
    $rows = [];
    foreach ($stats['monthly'] as $m) {
        $rows[] = [
            $m['month'],
            number_format($m['total'], 0, ',', ' '),
            $m['count']
        ];
    }
    $headers = ['Mês', 'Total (Kz)', 'Facturas Pagas'];
    exportCSV($headers, $rows, 'receitas-' . $startDateInput . '.csv');
}

if ($export === 'monthly') {
    $headers = ['Mês', 'Total (Kz)', 'Facturas Pagas'];
    $rows = [];
    foreach ($stats['monthly'] as $m) {
        $rows[] = [$m['month'], number_format($m['total'], 0, ',', ' '), $m['count']];
    }
    exportCSV($headers, $rows, 'receitas-mensais.csv');
}

if ($export === 'services') {
    $headers = ['Serviço', 'Total (Kz)', 'Vendas'];
    $rows = [];
    foreach ($stats['by_service'] as $s) {
        $rows[] = [$s['service'], number_format($s['total'], 0, ',', ' '), $s['count']];
    }
    exportCSV($headers, $rows, 'receitas-servicos.csv');
}

$monthDiff = ((int)substr($endDate, 0, 4) - (int)substr($startDate, 0, 4)) * 12
    + (int)substr($endDate, 5, 2) - (int)substr($startDate, 5, 2) + 1;

$extraStats = db()->fetchOne(
    "SELECT COUNT(DISTINCT i.client_id) as unique_clients, MAX(i.total) as max_invoice
     FROM invoices i WHERE i.status = 'paid' AND i.paid_at >= ? AND i.paid_at <= ?",
    [$startDate, $endDate . ' 23:59:59']
);

$avgMonthly = $monthDiff > 0 ? round($stats['total_revenue'] / $monthDiff, 2) : 0;
$ticketMedio = $stats['total_paid_invoices'] > 0
    ? round($stats['total_revenue'] / $stats['total_paid_invoices'], 2)
    : 0;

$yearlyData = db()->fetchAll(
    "SELECT YEAR(paid_at) as year, COALESCE(SUM(total), 0) as total, COUNT(*) as count
     FROM invoices WHERE status = 'paid'
     GROUP BY YEAR(paid_at) ORDER BY year ASC"
);

$hasMultipleYears = count($yearlyData) > 1;

$topClientsFiltered = db()->fetchAll(
    "SELECT i.client_id, i.client_name, COALESCE(SUM(i.total), 0) as total,
            COUNT(*) as count, MAX(i.paid_at) as last_purchase
     FROM invoices i WHERE i.status = 'paid' AND i.paid_at >= ? AND i.paid_at <= ?
     GROUP BY i.client_id, i.client_name ORDER BY total DESC LIMIT 10",
    [$startDate, $endDate . ' 23:59:59']
);

$monthlyFiltered = db()->fetchAll(
    "SELECT DATE_FORMAT(i.paid_at, '%Y-%m') as month, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
     FROM invoices i WHERE i.status = 'paid' AND i.paid_at >= ? AND i.paid_at <= ?
     GROUP BY DATE_FORMAT(i.paid_at, '%Y-%m') ORDER BY month ASC",
    [$startDate, $endDate . ' 23:59:59']
);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faturamento - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        .revenue-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .revenue-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 24px; text-align: center; }
        .revenue-card .value { font-size: 1.5rem; font-weight: 800; margin-top: 8px; }
        .revenue-card .label { font-size: 0.75rem; color: var(--text-muted); }
        .revenue-card .icon { font-size: 1.2rem; margin-bottom: 4px; }
        .filter-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-bar input[type="month"] { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.85rem; outline: none; }
        .filter-bar input[type="month"]:focus { border-color: var(--primary); }
        .filter-bar label { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
        .bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 180px; padding: 16px 0; }
        .bar-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .bar { width: 100%; border-radius: 6px 6px 0 0; min-height: 4px; transition: all 0.3s; position: relative; }
        .bar:hover { opacity: 0.8; }
        .bar-value { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; }
        .bar-label { font-size: 0.7rem; color: var(--text-muted); text-align: center; }
        .ranking-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid var(--border); }
        .ranking-item:last-child { border-bottom: none; }
        .ranking-pos { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: rgba(255,255,255,0.05); color: var(--text-muted); flex-shrink: 0; }
        .ranking-pos.top1 { background: rgba(255,171,0,0.2); color: var(--warning); }
        .ranking-pos.top2 { background: rgba(192,192,192,0.15); color: #c0c0c0; }
        .ranking-pos.top3 { background: rgba(205,127,50,0.15); color: #cd7f32; }
        .chart-container { position: relative; height: 250px; padding: 12px 16px; }
        .export-bar { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px; }
        .top-client-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .top-client-table th { text-align: left; padding: 10px 16px; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
        .top-client-table td { padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.03); }
        .top-client-table tbody tr:hover { background: rgba(255,255,255,0.02); }
        @media (max-width: 768px) { .revenue-summary { grid-template-columns: repeat(2, 1fr); } .bar-chart { height: 120px; } .chart-container { height: 200px; } }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-chart-line"></i> <span>Faturamento & Receitas</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <div class="filter-bar">
                <form method="GET" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                    <div>
                        <div class="filter-label">De</div>
                        <input type="month" name="start_date" value="<?= $startDateInput ?>">
                    </div>
                    <div>
                        <div class="filter-label">Até</div>
                        <input type="month" name="end_date" value="<?= $endDateInput ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Aplicar</button>
                    <a href="revenue.php" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
                </form>
                <div class="export-bar">
                    <a href="?export=csv&start_date=<?= $startDateInput ?>&end_date=<?= $endDateInput ?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> Exportar CSV</a>
                    <a href="revenue-pdf.php?start_date=<?= $startDateInput ?>&end_date=<?= $endDateInput ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
                </div>
            </div>

            <div class="revenue-summary">
                <div class="revenue-card" style="border-color:rgba(0,212,255,0.3);">
                    <div class="icon" style="color:var(--primary);"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="value" style="color:var(--primary);">Kz <?= number_format($stats['total_revenue'], 0, ',', ' ') ?></div>
                    <div class="label">Receita Total (período)</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(0,230,118,0.3);">
                    <div class="icon" style="color:var(--success);"><i class="fas fa-check-circle"></i></div>
                    <div class="value" style="color:var(--success);"><?= $stats['total_paid_invoices'] ?></div>
                    <div class="label">Facturas Pagas</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(255,171,0,0.3);">
                    <div class="icon" style="color:var(--warning);"><i class="fas fa-clock"></i></div>
                    <div class="value" style="color:var(--warning);">Kz <?= number_format($stats['pending_total'], 0, ',', ' ') ?></div>
                    <div class="label">A Receber (Pendente)</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(179,136,255,0.3);">
                    <div class="icon" style="color:var(--purple);"><i class="fas fa-percentage"></i></div>
                    <div class="value" style="color:var(--purple);">
                        <?php
                            $total = $stats['total_revenue'] + $stats['pending_total'];
                            $pct = $total > 0 ? round(($stats['total_revenue'] / $total) * 100) : 0;
                            echo $pct . '%';
                        ?>
                    </div>
                    <div class="label">Taxa de Recebimento</div>
                </div>
            </div>

            <div class="revenue-summary">
                <div class="revenue-card" style="border-color:rgba(0,212,255,0.2);">
                    <div class="icon" style="color:var(--info);"><i class="fas fa-calendar-alt"></i></div>
                    <div class="value" style="color:var(--info);">Kz <?= number_format($avgMonthly, 0, ',', ' ') ?></div>
                    <div class="label">Média Mensal</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(0,230,118,0.2);">
                    <div class="icon" style="color:var(--success);"><i class="fas fa-users"></i></div>
                    <div class="value" style="color:var(--success);"><?= $extraStats['unique_clients'] ?></div>
                    <div class="label">Total de Clientes Facturados</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(255,171,0,0.2);">
                    <div class="icon" style="color:var(--warning);"><i class="fas fa-crown"></i></div>
                    <div class="value" style="color:var(--warning);">Kz <?= number_format($extraStats['max_invoice'] ?? 0, 0, ',', ' ') ?></div>
                    <div class="label">Maior Factura</div>
                </div>
                <div class="revenue-card" style="border-color:rgba(179,136,255,0.2);">
                    <div class="icon" style="color:var(--purple);"><i class="fas fa-receipt"></i></div>
                    <div class="value" style="color:var(--purple);">Kz <?= number_format($ticketMedio, 0, ',', ' ') ?></div>
                    <div class="label">Ticket Médio</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-bar"></i> Receitas Mensais</h3>
                        <a href="?export=monthly&start_date=<?= $startDateInput ?>&end_date=<?= $endDateInput ?>" class="btn btn-sm btn-primary"><i class="fas fa-download"></i></a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($monthlyFiltered)): ?>
                            <p class="empty-state">Nenhuma receita no período</p>
                        <?php else:
                            $maxVal = max(array_column($monthlyFiltered, 'total')) ?: 1;
                        ?>
                            <div class="bar-chart" style="padding:16px 20px;">
                                <?php foreach ($monthlyFiltered as $m): ?>
                                    <?php $pct = ($m['total'] / $maxVal) * 100; ?>
                                    <div class="bar-item">
                                        <div class="bar-value">Kz <?= number_format($m['total'] / 1000, 0) ?>k</div>
                                        <div class="bar" style="height:<?= max($pct, 4) ?>%;background:linear-gradient(to top,var(--primary),rgba(0,212,255,0.6));" title="<?= $m['month'] ?>: Kz <?= number_format($m['total'], 0, ',', ' ') ?> (<?= $m['count'] ?> facturas)"></div>
                                        <div class="bar-label"><?= date('M', strtotime($m['month'] . '-01')) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-pie"></i> Receitas por Serviço</h3>
                        <a href="?export=services&start_date=<?= $startDateInput ?>&end_date=<?= $endDateInput ?>" class="btn btn-sm btn-primary"><i class="fas fa-download"></i></a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($stats['by_service'])): ?>
                            <p class="empty-state">Nenhuma receita no período</p>
                        <?php else: ?>
                            <div class="chart-container">
                                <canvas id="serviceChart"></canvas>
                            </div>
                            <?php $maxSrv = max(array_column($stats['by_service'], 'total')) ?: 1; ?>
                            <?php foreach (array_slice($stats['by_service'], 0, 5) as $s): ?>
                                <?php $pct = ($s['total'] / $maxSrv) * 100; ?>
                                <div class="list-item">
                                    <div class="list-info" style="flex:1;">
                                        <strong><?= sanitize($s['service'] ?: 'Outro') ?></strong>
                                        <div style="height:4px;background:rgba(255,255,255,0.05);border-radius:2px;margin-top:4px;max-width:200px;">
                                            <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,var(--success),rgba(0,230,118,0.5));border-radius:2px;"></div>
                                        </div>
                                    </div>
                                    <div class="list-meta">
                                        <strong style="color:var(--success);">Kz <?= number_format($s['total'], 0, ',', ' ') ?></strong>
                                        <small><?= $s['count'] ?> vendas</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-trophy"></i> Top Clientes</h3>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <?php if (empty($topClientsFiltered)): ?>
                            <p class="empty-state">Nenhum cliente com pagamentos</p>
                        <?php else: ?>
                            <table class="top-client-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Fact.</th>
                                        <th>Última Compra</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topClientsFiltered as $i => $c): ?>
                                        <tr>
                                            <td>
                                                <div class="ranking-pos <?= $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) ?>" style="width:24px;height:24px;font-size:0.65rem;"><?= $i + 1 ?></div>
                                            </td>
                                            <td><strong style="font-size:0.85rem;"><?= sanitize($c['client_name']) ?></strong></td>
                                            <td><strong style="color:var(--success);">Kz <?= number_format($c['total'], 0, ',', ' ') ?></strong></td>
                                            <td style="color:var(--text-muted);"><?= $c['count'] ?></td>
                                            <td style="color:var(--text-muted);font-size:0.8rem;"><?= $c['last_purchase'] ? date('d/m/Y', strtotime($c['last_purchase'])) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-credit-card"></i> Receitas por Método</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($stats['by_method'])): ?>
                            <p class="empty-state">Nenhum pagamento registado</p>
                        <?php else:
                            $totalMethod = array_sum(array_column($stats['by_method'], 'total'));
                        ?>
                            <?php foreach ($stats['by_method'] as $m): ?>
                                <?php $pct = $totalMethod > 0 ? round(($m['total'] / $totalMethod) * 100) : 0; ?>
                                <div class="list-item">
                                    <div class="list-info">
                                        <strong><i class="fas fa-<?= $m['method'] === 'express' ? 'bolt' : ($m['method'] === 'iban' ? 'university' : 'qrcode') ?>"></i> <?= strtoupper($m['method']) ?></strong>
                                    </div>
                                    <div class="list-meta">
                                        <strong>Kz <?= number_format($m['total'], 0, ',', ' ') ?></strong>
                                        <small><?= $pct ?>% • <?= $m['count'] ?> pagamentos</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-invoice"></i> Últimas Facturas Pagas</h3>
                        <a href="invoices.php?status=paid" class="btn-sm">Ver todas</a>
                    </div>
                    <div class="card-body">
                        <?php
                        $recentPaid = db()->fetchAll(
                            "SELECT * FROM invoices WHERE status = 'paid' ORDER BY paid_at DESC LIMIT 5"
                        );
                        ?>
                        <?php if (empty($recentPaid)): ?>
                            <p class="empty-state">Nenhuma factura paga</p>
                        <?php else: ?>
                            <?php foreach ($recentPaid as $inv): ?>
                                <div class="list-item">
                                    <div class="list-info">
                                        <strong><?= sanitize($inv['invoice_no']) ?></strong>
                                        <p><?= sanitize($inv['client_name']) ?></p>
                                    </div>
                                    <div class="list-meta">
                                        <strong style="color:var(--success);">Kz <?= number_format($inv['total'], 0, ',', ' ') ?></strong>
                                        <small><?= $inv['paid_at'] ? timeAgo($inv['paid_at']) : '' ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($hasMultipleYears): ?>
            <div class="dashboard-card" style="margin-top:20px;">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Comparativo Anual</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:300px;">
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script>
<?php if (!empty($stats['by_service'])): ?>
const serviceCtx = document.getElementById('serviceChart').getContext('2d');
new Chart(serviceCtx, {
    type: 'doughnut',
    data: {
        labels: [<?php foreach ($stats['by_service'] as $s): ?><?= json_encode(sanitize($s['service'] ?: 'Outro')) ?>,<?php endforeach; ?>],
        datasets: [{
            data: [<?php foreach ($stats['by_service'] as $s): ?><?= $s['total'] ?>,<?php endforeach; ?>],
            backgroundColor: ['#00d4ff','#00e676','#ffab00','#ff5252','#b388ff','#ff6d00','#40c4ff','#78909c','#26c6da','#66bb6a'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#e0e6ed', font: { size: 11 }, padding: 12 } }
        }
    }
});
<?php endif; ?>

<?php if ($hasMultipleYears): ?>
const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
new Chart(yearlyCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($yearlyData as $y): ?><?= json_encode((string)$y['year']) ?>,<?php endforeach; ?>],
        datasets: [{
            label: 'Receita (Kz)',
            data: [<?php foreach ($yearlyData as $y): ?><?= $y['total'] ?>,<?php endforeach; ?>],
            backgroundColor: 'rgba(0,212,255,0.7)',
            borderColor: '#00d4ff',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#8899aa', callback: function(v) { return 'Kz ' + (v / 1000).toFixed(0) + 'k'; } },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#8899aa' },
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>
