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

$monthlyData = db()->fetchAll(
    "SELECT DATE_FORMAT(i.paid_at, '%Y-%m') as month, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
     FROM invoices i WHERE i.status = 'paid' AND i.paid_at >= ? AND i.paid_at <= ?
     GROUP BY DATE_FORMAT(i.paid_at, '%Y-%m') ORDER BY month ASC",
    [$startDate, $endDate . ' 23:59:59']
);

$topClients = db()->fetchAll(
    "SELECT i.client_id, i.client_name, COALESCE(SUM(i.total), 0) as total,
            COUNT(*) as count, MAX(i.paid_at) as last_purchase
     FROM invoices i WHERE i.status = 'paid' AND i.paid_at >= ? AND i.paid_at <= ?
     GROUP BY i.client_id, i.client_name ORDER BY total DESC LIMIT 10",
    [$startDate, $endDate . ' 23:59:59']
);

$serviceData = db()->fetchAll(
    "SELECT COALESCE(i.service_name, 'Outro') as service, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
     FROM invoices i WHERE i.status = 'paid'
     GROUP BY i.service_name ORDER BY total DESC"
);

logActivity($user['id'], 'export_pdf', 'Exportou PDF de receitas (' . $startDate . ' a ' . $endDate . ')');

$siteName = getSetting('site_name', 'ANGONUEVE');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Receitas - <?= sanitize($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #1a1a2e; background: #fff; padding: 40px; font-size: 12px; line-height: 1.5; }
        .report-box { max-width: 900px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; padding: 36px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding-bottom: 16px; border-bottom: 2px solid #00d4ff; }
        .header-left h1 { font-size: 1.6rem; font-weight: 800; background: linear-gradient(135deg, #00d4ff, #0099cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .header-left p { color: #666; font-size: 0.8rem; margin-top: 4px; }
        .header-right { text-align: right; }
        .header-right h2 { font-size: 1rem; color: #333; font-weight: 600; }
        .header-right .period { color: #666; font-size: 0.8rem; margin-top: 4px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 28px; }
        .summary-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center; }
        .summary-card .value { font-size: 1.2rem; font-weight: 800; margin-top: 4px; }
        .summary-card .label { font-size: 0.65rem; color: #999; text-transform: uppercase; letter-spacing: 0.5px; }
        h3.section-title { font-size: 0.9rem; font-weight: 700; color: #333; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #f5f5f5; padding: 10px 12px; text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 0.8rem; }
        tbody tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .text-muted { color: #999; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e0e0e0; text-align: center; color: #999; font-size: 0.75rem; }
        .footer p { margin-bottom: 2px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; }
        .badge-primary { background: #e3f2fd; color: #1565c0; }
        @media print { body { padding: 0; } .report-box { border: none; box-shadow: none; } .no-print { display: none; } }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button { padding: 10px 24px; background: #00d4ff; color: #000; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; cursor: pointer; margin: 0 4px; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>
        <button onclick="window.close()" style="background:#eee;color:#333;">Fechar</button>
    </div>
    <div class="report-box">
        <div class="header">
            <div class="header-left">
                <h1><?= sanitize($siteName) ?></h1>
                <p>Relatório de Faturamento & Receitas</p>
            </div>
            <div class="header-right">
                <h2>Relatório de Receitas</h2>
                <div class="period"><?= date('d/m/Y', strtotime($startDate)) ?> — <?= date('d/m/Y', strtotime($endDate)) ?></div>
                <div class="period" style="margin-top:4px;">Gerado em <?= date('d/m/Y H:i') ?></div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card" style="border-color:#00d4ff;">
                <div class="label">Receita Total</div>
                <div class="value" style="color:#00d4ff;">Kz <?= number_format($stats['total_revenue'], 0, ',', ' ') ?></div>
            </div>
            <div class="summary-card" style="border-color:#00e676;">
                <div class="label">Facturas Pagas</div>
                <div class="value" style="color:#00e676;"><?= $stats['total_paid_invoices'] ?></div>
            </div>
            <div class="summary-card" style="border-color:#ffab00;">
                <div class="label">Média Mensal</div>
                <div class="value" style="color:#ffab00;">Kz <?= number_format($avgMonthly, 0, ',', ' ') ?></div>
            </div>
            <div class="summary-card" style="border-color:#b388ff;">
                <div class="label">Ticket Médio</div>
                <div class="value" style="color:#b388ff;">Kz <?= number_format($ticketMedio, 0, ',', ' ') ?></div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card" style="border-color:#40c4ff;">
                <div class="label">Clientes Facturados</div>
                <div class="value" style="color:#40c4ff;"><?= $extraStats['unique_clients'] ?></div>
            </div>
            <div class="summary-card" style="border-color:#ff6d00;">
                <div class="label">Maior Factura</div>
                <div class="value" style="color:#ff6d00;">Kz <?= number_format($extraStats['max_invoice'] ?? 0, 0, ',', ' ') ?></div>
            </div>
            <div class="summary-card" style="border-color:#ff5252;">
                <div class="label">A Receber</div>
                <div class="value" style="color:#ff5252;">Kz <?= number_format($stats['pending_total'], 0, ',', ' ') ?></div>
            </div>
            <div class="summary-card" style="border-color:#78909c;">
                <div class="label">Taxa Recebimento</div>
                <div class="value" style="color:#78909c;">
                    <?php
                        $total = $stats['total_revenue'] + $stats['pending_total'];
                        echo $total > 0 ? round(($stats['total_revenue'] / $total) * 100) . '%' : '0%';
                    ?>
                </div>
            </div>
        </div>

        <h3 class="section-title">Receitas Mensais</h3>
        <table>
            <thead>
                <tr>
                    <th>Mês</th>
                    <th class="text-right">Total (Kz)</th>
                    <th class="text-right">Facturas</th>
                    <th class="text-right">Média por Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($monthlyData)): ?>
                    <tr><td colspan="4" style="text-align:center;color:#999;padding:20px;">Nenhuma receita no período</td></tr>
                <?php else: ?>
                    <?php foreach ($monthlyData as $m): ?>
                        <tr>
                            <td><?= date('F Y', strtotime($m['month'] . '-01')) ?></td>
                            <td class="text-right">Kz <?= number_format($m['total'], 0, ',', ' ') ?></td>
                            <td class="text-right"><?= $m['count'] ?></td>
                            <td class="text-right">Kz <?= $m['count'] > 0 ? number_format($m['total'] / $m['count'], 0, ',', ' ') : '0' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h3 class="section-title">Top Clientes</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Cliente</th>
                    <th class="text-right">Total (Kz)</th>
                    <th class="text-right">Facturas</th>
                    <th class="text-right">Última Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($topClients)): ?>
                    <tr><td colspan="5" style="text-align:center;color:#999;padding:20px;">Nenhum cliente com pagamentos</td></tr>
                <?php else: ?>
                    <?php foreach ($topClients as $i => $c): ?>
                        <tr>
                            <td style="font-weight:700;color:#999;"><?= $i + 1 ?></td>
                            <td><strong><?= sanitize($c['client_name']) ?></strong></td>
                            <td class="text-right"><strong>Kz <?= number_format($c['total'], 0, ',', ' ') ?></strong></td>
                            <td class="text-right"><?= $c['count'] ?></td>
                            <td class="text-right"><?= $c['last_purchase'] ? date('d/m/Y', strtotime($c['last_purchase'])) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h3 class="section-title">Receitas por Serviço</h3>
        <table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th class="text-right">Total (Kz)</th>
                    <th class="text-right">Vendas</th>
                    <th class="text-right">%</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($serviceData)): ?>
                    <tr><td colspan="4" style="text-align:center;color:#999;padding:20px;">Nenhuma receita registada</td></tr>
                <?php else:
                    $totalService = array_sum(array_column($serviceData, 'total'));
                ?>
                    <?php foreach ($serviceData as $s): ?>
                        <tr>
                            <td><?= sanitize($s['service'] ?: 'Outro') ?></td>
                            <td class="text-right">Kz <?= number_format($s['total'], 0, ',', ' ') ?></td>
                            <td class="text-right"><?= $s['count'] ?></td>
                            <td class="text-right"><?= $totalService > 0 ? round(($s['total'] / $totalService) * 100, 1) : 0 ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <p><strong><?= sanitize($siteName) ?></strong> — Soluções Tecnológicas Completas</p>
            <p>Relatório gerado em <?= date('d/m/Y \à\s H:i') ?> | Período: <?= date('d/m/Y', strtotime($startDate)) ?> — <?= date('d/m/Y', strtotime($endDate)) ?></p>
            <p>© <?= date('Y') ?> <?= sanitize($siteName) ?>. Todos os direitos reservados.</p>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
