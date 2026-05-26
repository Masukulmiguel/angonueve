<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$id = intval($_GET['id'] ?? 0);
$inv = db()->fetchOne("SELECT * FROM invoices WHERE id = ? AND client_id = ?", [$id, $user['id']]);
if (!$inv) { header('Location: invoices.php'); exit; }

$payments = db()->fetchAll("SELECT * FROM payments WHERE invoice_id = ? ORDER BY created_at DESC", [$id]);
$activeServices = db()->fetchAll("SELECT * FROM client_services WHERE client_id = ? AND status = 'active'", [$user['id']]);

$showPayForm = isset($_GET['pay']) && $inv['status'] === 'pending';

$siteName = getSetting('site_name', 'ANGONUEVE');
$siteEmail = getSetting('site_email', 'geral@angonueve.co');
$sitePhone = getSetting('site_phone', '935 603 163');
$siteAddress = getSetting('site_address', 'Luanda, Angola');
$bankName = getSetting('bank_name', 'Banco Angolano de Investimentos');
$bankHolder = getSetting('bank_holder', 'ANGONUEVE Lda');
$bankNif = getSetting('bank_nif', '5000000000');
$iban = getSetting('payment_iban', 'AO06004400000000000012345');
$expressName = getSetting('payment_express_name', 'Express');
$expressNumber = getSetting('payment_express_number', '');
$entity = getSetting('payment_referencia_entity', '99999');

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?= sanitize($inv['invoice_no']) ?> - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .pay-methods { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .pay-method { background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s; }
        .pay-method:hover { border-color: var(--success); background: rgba(0,230,118,0.05); }
        .pay-method.selected { border-color: var(--success); background: rgba(0,230,118,0.1); }
        .pay-method h4 { font-size: 0.95rem; margin-bottom: 8px; }
        .pay-method p { font-size: 0.8rem; color: var(--text-muted); }
        .pay-method .icon { font-size: 1.5rem; margin-bottom: 8px; display: block; }
        .proof-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 40px; text-align: center; transition: all 0.3s; cursor: pointer; }
        .proof-drop:hover { border-color: var(--success); background: rgba(0,230,118,0.03); }
        .proof-drop.has-file { border-color: var(--success); background: rgba(0,230,118,0.05); }
        .proof-drop i { font-size: 2rem; color: var(--text-muted); margin-bottom: 12px; display: block; }
        .invoice-detail { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 32px; margin-bottom: 24px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--primary); }
        .invoice-header h2 { font-size: 1.6rem; }
        .invoice-header .status-big { font-size: 0.85rem; }
        .invoice-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .invoice-table th { text-align: left; padding: 12px 16px; background: rgba(255,255,255,0.03); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
        .invoice-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .invoice-total { text-align: right; padding: 16px; font-size: 1.3rem; font-weight: 800; color: var(--primary); }
        .payment-history { margin-top: 24px; }
        .payment-history .payment-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border); }
        .payment-history .payment-item:last-child { border-bottom: none; }
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
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Encomendas</a>
                <a href="invoices.php" class="active"><i class="fas fa-file-invoice"></i> Facturas</a>
                <a href="chat.php"><i class="fas fa-comments"></i> Chat Suporte</a>
                <hr>
                <a href="../index.html" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-file-invoice"></i> <span>Factura <?= sanitize($inv['invoice_no']) ?></span></div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-content">
                <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="invoices.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
                    <?php if ($inv['status'] === 'pending'): ?>
                        <a href="?id=<?= $id ?>&pay=1" class="btn btn-success btn-sm"><i class="fas fa-credit-card"></i> Pagar Agora</a>
                    <?php endif; ?>
                </div>

                <?php if ($showPayForm): ?>
                    <div class="invoice-detail" style="border-color:var(--success);">
                        <h3 style="margin-bottom:16px;color:var(--success);"><i class="fas fa-credit-card"></i> Pagar Factura <?= sanitize($inv['invoice_no']) ?></h3>
                        <p style="color:var(--text-muted);margin-bottom:24px;">Valor a pagar: <strong style="color:var(--text);font-size:1.2rem;">Kz <?= number_format($inv['total'], 0, ',', ' ') ?></strong></p>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success">Pagamento registado com sucesso! Aguarde a confirmação do administrador.</div>
                        <?php endif; ?>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">Erro: <?= sanitize($_GET['error']) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="pay.php" enctype="multipart/form-data" id="paymentForm">
                            <input type="hidden" name="invoice_id" value="<?= $inv['id'] ?>">
                            <?= csrf_field() ?>

                            <h4 style="margin-bottom:12px;color:var(--text-muted);">1. Escolha o método de pagamento</h4>
                            <div class="pay-methods">
                                <label class="pay-method" onclick="selectMethod(this, 'express')">
                                    <input type="radio" name="method" value="express" style="display:none;">
                                    <span class="icon"><i class="fas fa-bolt"></i></span>
                                    <h4>Express</h4>
                                    <p><?= sanitize($expressName) ?><br><?= sanitize($expressNumber) ?></p>
                                </label>
                                <label class="pay-method" onclick="selectMethod(this, 'iban')">
                                    <input type="radio" name="method" value="iban" style="display:none;">
                                    <span class="icon"><i class="fas fa-university"></i></span>
                                    <h4>Transferência IBAN</h4>
                                    <p><?= sanitize($iban) ?><br><?= sanitize($bankHolder) ?></p>
                                </label>
                                <label class="pay-method" onclick="selectMethod(this, 'referencia')">
                                    <input type="radio" name="method" value="referencia" style="display:none;">
                                    <span class="icon"><i class="fas fa-qrcode"></i></span>
                                    <h4>Referência</h4>
                                    <p>Entidade <?= sanitize($entity) ?><br>Ref: <?= str_replace('{ID}', $inv['id'], getSetting('payment_referencia_ref', 'ANGONUEVE-{ID}')) ?></p>
                                </label>
                            </div>

                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="display:block;font-size:0.85rem;color:var(--text-muted);margin-bottom:6px;">Referência do pagamento (opcional)</label>
                                <input type="text" name="reference" placeholder="Nº de referência ou descrição" style="width:100%;max-width:400px;padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.9rem;outline:none;">
                            </div>

                            <h4 style="margin-bottom:12px;color:var(--text-muted);">2. Faça upload do comprovativo de pagamento</h4>
                            <div class="proof-drop" id="proofDrop" onclick="document.getElementById('proofFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Clique para selecionar o comprovativo</strong></p>
                                <p style="font-size:0.8rem;color:var(--text-muted);">JPG, PNG, GIF, WebP ou PDF (máx. 10MB)</p>
                                <p id="fileName" style="margin-top:8px;color:var(--success);font-weight:500;"></p>
                            </div>
                            <input type="file" name="proof_file" id="proofFile" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" style="display:none;" onchange="handleFile(this)">

                            <button type="submit" class="btn btn-success" style="margin-top:20px;width:100%;justify-content:center;padding:14px;font-size:1rem;">
                                <i class="fas fa-check-circle"></i> Confirmar Pagamento
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="invoice-detail">
                    <div class="invoice-header">
                        <div>
                            <h2><?= sanitize($inv['invoice_no']) ?></h2>
                            <p style="color:var(--text-muted);font-size:0.85rem;"><?= sanitize($siteName) ?></p>
                        </div>
                        <div style="text-align:right;">
                            <?= statusBadge($inv['status']) ?>
                            <p style="color:var(--text-muted);font-size:0.8rem;margin-top:4px;">Emissão: <?= formatDate($inv['created_at'], 'd/m/Y') ?></p>
                            <p style="color:var(--text-muted);font-size:0.8rem;">Vencimento: <?= $inv['due_date'] ? formatDate($inv['due_date'], 'd/m/Y') : '-' ?></p>
                        </div>
                    </div>

                    <div class="invoice-meta-grid">
                        <div>
                            <h4 style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:8px;">Faturado a</h4>
                            <p style="font-size:0.9rem;"><strong><?= sanitize($user['name']) ?></strong></p>
                            <p style="font-size:0.85rem;color:var(--text-muted);"><?= sanitize($user['email']) ?></p>
                        </div>
                        <div>
                            <h4 style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:8px;">Serviço</h4>
                            <p style="font-size:0.9rem;"><strong><?= sanitize($inv['service_name'] ?: '—') ?></strong></p>
                            <?php if ($inv['plan_name']): ?>
                                <p style="font-size:0.85rem;color:var(--text-muted);">Plano: <?= sanitize($inv['plan_name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($inv['description']): ?>
                        <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:16px;"><?= nl2br(sanitize($inv['description'])) ?></p>
                    <?php endif; ?>

                    <table class="invoice-table">
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
                                <td><?= sanitize($inv['service_name'] ?: 'Serviço') ?></td>
                                <td style="text-align:center;"><?= $inv['quantity'] ?></td>
                                <td style="text-align:right;">Kz <?= number_format($inv['unit_price'], 0, ',', ' ') ?></td>
                                <td style="text-align:right;">Kz <?= number_format($inv['unit_price'] * $inv['quantity'], 0, ',', ' ') ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="text-align:right;padding:0 16px;">
                        <?php if ($inv['discount'] > 0): ?>
                            <p style="font-size:0.85rem;color:var(--text-muted);">Desconto: -Kz <?= number_format($inv['discount'], 0, ',', ' ') ?></p>
                        <?php endif; ?>
                        <?php if ($inv['tax'] > 0): ?>
                            <p style="font-size:0.85rem;color:var(--text-muted);">Taxa/IVA: Kz <?= number_format($inv['tax'], 0, ',', ' ') ?></p>
                        <?php endif; ?>
                        <p class="invoice-total">Total: Kz <?= number_format($inv['total'], 0, ',', ' ') ?></p>
                    </div>
                </div>

                <?php if (!empty($payments)): ?>
                    <div class="invoice-detail">
                        <h3 style="margin-bottom:16px;"><i class="fas fa-history"></i> Histórico de Pagamentos</h3>
                        <div class="payment-history">
                            <?php foreach ($payments as $p): ?>
                                <div class="payment-item">
                                    <div>
                                        <strong style="font-size:0.9rem;">Kz <?= number_format($p['amount'], 0, ',', ' ') ?></strong>
                                        <span class="badge badge-info"><?= strtoupper($p['method']) ?></span>
                                        <?= statusBadge($p['status']) ?>
                                    </div>
                                    <div style="text-align:right;">
                                        <small style="color:var(--text-muted);display:block;"><?= formatDate($p['created_at']) ?></small>
                                        <?php if ($p['status'] === 'confirmed' && $p['confirmed_at']): ?>
                                            <small style="color:var(--success);">Confirmado em <?= formatDate($p['confirmed_at'], 'd/m/Y H:i') ?></small>
                                        <?php endif; ?>
                                        <?php if ($p['proof_file']): ?>
                                            <a href="../uploads/proofs/<?= urlencode($p['proof_file']) ?>" target="_blank" class="btn-icon" style="display:inline-flex;margin-top:4px;" title="Comprovativo"><i class="fas fa-paperclip"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
<script>
function selectMethod(el, value) {
    document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input[type="radio"]').checked = true;
}
function handleFile(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('fileName').textContent = '✓ ' + file.name;
        document.getElementById('proofDrop').classList.add('has-file');
    }
}
</script>
</body>
</html>
