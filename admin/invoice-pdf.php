<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$id = intval($_GET['id'] ?? 0);
$inv = db()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$id]);
if (!$inv) { die('Factura não encontrada'); }

$siteName = getSetting('site_name', 'ANGONUEVE');
$siteEmail = getSetting('site_email', 'geral@angonueve.co');
$sitePhone = getSetting('site_phone', '935 603 163');
$siteAddress = getSetting('site_address', 'Luanda, Angola');
$companyNif = getSetting('company_nif', getSetting('bank_nif', '5000000000'));
$bankName = getSetting('bank_name', 'Banco Angolano de Investimentos');
$bankHolder = getSetting('bank_holder', 'ANGONUEVE Lda');
$bankNif = getSetting('bank_nif', '5000000000');
$iban = getSetting('payment_iban', 'AO06004400000000000012345');

$statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
$statusLabel = $statusLabels[$inv['status']] ?? $inv['status'];

$subtotal = $inv['unit_price'] * $inv['quantity'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Factura <?= sanitize($inv['invoice_no']) ?> - <?= sanitize($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #1a1a2e; background: #fff; padding: 40px; font-size: 13px; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #00d4ff; }
        .header-left h1 { font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #00d4ff, #0099cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header-left p { color: #666; font-size: 0.85rem; }
        .header-right { text-align: right; }
        .header-right h2 { font-size: 1.6rem; color: #00d4ff; }
        .header-right .status { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-top: 8px; background: <?= $inv['status'] === 'paid' ? '#e8f5e9' : ($inv['status'] === 'cancelled' ? '#ffebee' : '#fff3e0') ?>; color: <?= $inv['status'] === 'paid' ? '#2e7d32' : ($inv['status'] === 'cancelled' ? '#c62828' : '#e65100') ?>; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-block h3 { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 8px; }
        .info-block p { font-size: 0.9rem; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f5f5f5; padding: 12px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }
        td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        tfoot td { border-bottom: none; font-weight: 600; }
        .total-row td { padding: 8px 16px; }
        .grand-total td { font-size: 1.1rem; font-weight: 800; color: #00d4ff; border-top: 2px solid #00d4ff; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #999; font-size: 0.8rem; }
        .footer p { margin-bottom: 4px; }
        .payment-info { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .payment-info h3 { font-size: 0.85rem; color: #333; margin-bottom: 12px; }
        .payment-info p { font-size: 0.85rem; color: #555; margin-bottom: 4px; }
        .notes { background: #fff8e1; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 0.85rem; color: #666; }
        @media print { body { padding: 0; } .invoice-box { border: none; box-shadow: none; } .no-print { display: none; } }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button { padding: 10px 24px; background: #00d4ff; color: #000; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Imprimir / Guardar PDF</button>
        <button onclick="window.close()" style="margin-left:8px;background:#eee;color:#333;">Fechar</button>
    </div>
    <div class="invoice-box">
        <div class="header">
            <div class="header-left">
                <h1><?= sanitize($siteName) ?></h1>
                <p><?= nl2br(sanitize($siteAddress)) ?></p>
                <p><?= sanitize($sitePhone) ?> | <?= sanitize($siteEmail) ?></p>
                <p>NIF: <?= sanitize($companyNif) ?></p>
            </div>
            <div class="header-right">
                <h2><?= sanitize($inv['invoice_no']) ?></h2>
                <div class="status"><?= $statusLabel ?></div>
                <p style="margin-top:8px;color:#666;font-size:0.85rem;">Emissão: <?= formatDate($inv['created_at'], 'd/m/Y') ?></p>
                <p style="color:#666;font-size:0.85rem;">Vencimento: <?= $inv['due_date'] ? formatDate($inv['due_date'], 'd/m/Y') : '-' ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h3>Faturado a</h3>
                <p><strong><?= sanitize($inv['client_name']) ?></strong></p>
                <p><?= sanitize($inv['client_email']) ?></p>
                <?php if ($inv['client_phone']): ?><p><?= sanitize($inv['client_phone']) ?></p><?php endif; ?>
                <?php if ($inv['client_address']): ?><p><?= nl2br(sanitize($inv['client_address'])) ?></p><?php endif; ?>
            </div>
            <div class="info-block">
                <h3>Dados Bancários</h3>
                <p><strong>Banco:</strong> <?= sanitize($bankName) ?></p>
                <p><strong>Titular:</strong> <?= sanitize($bankHolder) ?></p>
                <p><strong>IBAN:</strong> <?= sanitize($iban) ?></p>
                <p><strong>NIF:</strong> <?= sanitize($bankNif) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="text-align:center;">Qtd</th>
                    <th style="text-align:right;">Preço Unit.</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= sanitize($inv['service_name'] ?: 'Serviço') ?></strong>
                        <?php if ($inv['plan_name']): ?><br><span style="color:#999;font-size:0.8rem;">Plano: <?= sanitize($inv['plan_name']) ?></span><?php endif; ?>
                        <?php if ($inv['description']): ?><br><span style="color:#999;font-size:0.8rem;"><?= sanitize($inv['description']) ?></span><?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?= $inv['quantity'] ?></td>
                    <td style="text-align:right;">Kz <?= number_format($inv['unit_price'], 0, ',', ' ') ?></td>
                    <td style="text-align:right;">Kz <?= number_format($subtotal, 0, ',', ' ') ?></td>
                </tr>
            </tbody>
            <tfoot>
                <?php if ($inv['discount'] > 0): ?>
                    <tr class="total-row"><td colspan="3" style="text-align:right;">Desconto:</td><td style="text-align:right;">- Kz <?= number_format($inv['discount'], 0, ',', ' ') ?></td></tr>
                <?php endif; ?>
                <?php if ($inv['tax'] > 0): ?>
                    <tr class="total-row"><td colspan="3" style="text-align:right;">Taxa/IVA:</td><td style="text-align:right;">Kz <?= number_format($inv['tax'], 0, ',', ' ') ?></td></tr>
                <?php endif; ?>
                <tr class="grand-total"><td colspan="3" style="text-align:right;">Total:</td><td style="text-align:right;">Kz <?= number_format($inv['total'], 0, ',', ' ') ?></td></tr>
            </tfoot>
        </table>

        <?php if ($inv['notes']): ?>
            <div class="notes">
                <strong>Notas:</strong><br><?= nl2br(sanitize($inv['notes'])) ?>
            </div>
        <?php endif; ?>

        <div class="payment-info">
            <h3><i class="fas fa-credit-card"></i> Informação de Pagamento</h3>
            <p><strong>Transferência Bancária (IBAN):</strong> <?= sanitize($iban) ?></p>
            <p><strong>Express:</strong> <?= sanitize(getSetting('payment_express_name', 'Express')) ?> - <?= sanitize(getSetting('payment_express_number', '')) ?></p>
            <p><strong>Referência:</strong> Entidade <?= sanitize(getSetting('payment_referencia_entity', '99999')) ?></p>
            <p style="margin-top:8px;font-size:0.8rem;color:#999;">Após o pagamento, envie o comprovativo para <?= sanitize($siteEmail) ?> ou faça upload na sua área de cliente.</p>
        </div>

        <div class="footer">
            <p><strong><?= sanitize($siteName) ?></strong> — Soluções Tecnológicas Completas</p>
            <p><?= sanitize($siteAddress) ?> | Tel: <?= sanitize($sitePhone) ?> | Email: <?= sanitize($siteEmail) ?></p>
            <p>NIF: <?= sanitize($bankNif) ?> | © <?= date('Y') ?> <?= sanitize($siteName) ?>. Todos os direitos reservados.</p>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
