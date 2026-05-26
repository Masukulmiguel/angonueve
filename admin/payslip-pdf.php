<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$id = intval($_GET['id'] ?? 0);
$ps = db()->fetchOne("SELECT * FROM payslips WHERE id = ?", [$id]);
if (!$ps) { die('Recibo de vencimento não encontrado'); }

$siteName = getSetting('site_name', 'ANGONUEVE');
$siteEmail = getSetting('site_email', 'geral@angonueve.co');
$sitePhone = getSetting('site_phone', '935 603 163');
$siteAddress = getSetting('site_address', 'Luanda, Angola');
$companyNif = getSetting('company_nif', getSetting('bank_nif', '5000000000'));

$statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago'];
$statusLabel = $statusLabels[$ps['status']] ?? $ps['status'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Vencimento — <?= sanitize($ps['employee_name']) ?> — <?= sanitize($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #1a1a2e; background: #fff; padding: 40px; font-size: 13px; line-height: 1.6; }
        .payslip-box { max-width: 700px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; padding: 40px; }
        .header { text-align: center; margin-bottom: 32px; padding-bottom: 20px; border-bottom: 2px solid #00d4ff; }
        .header h1 { font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #00d4ff, #0099cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
        .header p { color: #666; font-size: 0.85rem; }
        .header .nif { color: #999; font-size: 0.8rem; }
        .title-section { text-align: center; margin-bottom: 32px; }
        .title-section h2 { font-size: 1.4rem; color: #1a1a2e; font-weight: 700; }
        .title-section .period { color: #00d4ff; font-size: 1rem; font-weight: 600; margin-top: 4px; }
        .title-section .status { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-top: 8px; background: <?= $ps['status'] === 'paid' ? '#e8f5e9' : '#fff3e0' ?>; color: <?= $ps['status'] === 'paid' ? '#2e7d32' : '#e65100' ?>; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .info-block h3 { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 6px; }
        .info-block p { font-size: 0.9rem; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        th { background: #1a1a2e; color: #fff; padding: 12px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .grand-total td { font-size: 1.1rem; font-weight: 800; color: #00d4ff; border-top: 2px solid #00d4ff; border-bottom: none; }
        .footer { margin-top: 32px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #999; font-size: 0.8rem; }
        .footer p { margin-bottom: 4px; }
        .signature { margin-top: 32px; padding-top: 20px; text-align: center; }
        .signature .line { width: 250px; border-top: 1px solid #333; margin: 0 auto 6px; }
        .signature p { font-size: 0.85rem; color: #666; }
        .notes { background: #fff8e1; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 0.85rem; color: #666; }
        @media print { body { padding: 0; } .payslip-box { border: none; box-shadow: none; } .no-print { display: none; } }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button { padding: 10px 24px; background: #00d4ff; color: #000; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>
        <button onclick="window.close()" style="margin-left:8px;background:#eee;color:#333;">Fechar</button>
    </div>
    <div class="payslip-box">
        <div class="header">
            <h1><?= sanitize($siteName) ?></h1>
            <p><?= nl2br(sanitize($siteAddress)) ?></p>
            <p><?= sanitize($sitePhone) ?> | <?= sanitize($siteEmail) ?></p>
            <p class="nif">NIF: <?= sanitize($companyNif) ?></p>
        </div>

        <div class="title-section">
            <h2>RECIBO DE VENCIMENTO</h2>
            <div class="period">Período: <?= sanitize($ps['month_year']) ?></div>
            <div class="status"><?= $statusLabel ?></div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h3>Funcionário</h3>
                <p><strong><?= sanitize($ps['employee_name']) ?></strong></p>
                <p><?= sanitize($ps['position'] ?: '—') ?></p>
            </div>
            <div class="info-block">
                <h3>Detalhes</h3>
                <p><strong>Data de Emissão:</strong> <?= formatDate($ps['generated_at'] ?: $ps['created_at'], 'd/m/Y') ?></p>
                <?php if ($ps['paid_at']): ?>
                    <p><strong>Pago em:</strong> <?= formatDate($ps['paid_at'], 'd/m/Y') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="text-align:right;">Valor (Kz)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Salário Base</strong></td>
                    <td style="text-align:right;"><?= number_format($ps['salary'], 0, ',', ' ') ?></td>
                </tr>
                <?php if ($ps['bonus'] > 0): ?>
                    <tr>
                        <td><strong>Bónus</strong></td>
                        <td style="text-align:right;">+ <?= number_format($ps['bonus'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($ps['deductions'] > 0): ?>
                    <tr>
                        <td><strong>Deduções</strong></td>
                        <td style="text-align:right;">— <?= number_format($ps['deductions'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="grand-total">
                    <td><strong>Valor Líquido</strong></td>
                    <td style="text-align:right;">Kz <?= number_format($ps['net_salary'], 0, ',', ' ') ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if ($ps['notes']): ?>
            <div class="notes">
                <strong>Notas:</strong><br><?= nl2br(sanitize($ps['notes'])) ?>
            </div>
        <?php endif; ?>

        <div class="signature">
            <div class="line"></div>
            <p>Assinatura / Carimbo</p>
        </div>

        <div class="footer">
            <p><strong><?= sanitize($siteName) ?></strong> — Soluções Tecnológicas Completas</p>
            <p><?= sanitize($siteAddress) ?> | Tel: <?= sanitize($sitePhone) ?> | Email: <?= sanitize($siteEmail) ?></p>
            <p>NIF: <?= sanitize($companyNif) ?> | © <?= date('Y') ?> <?= sanitize($siteName) ?>. Todos os direitos reservados.</p>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
