<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('settings');

$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_email' => sanitize($_POST['site_email'] ?? ''),
        'site_phone' => sanitize($_POST['site_phone'] ?? ''),
        'site_address' => sanitize($_POST['site_address'] ?? ''),
        'company_nif' => sanitize($_POST['company_nif'] ?? ''),
        'whatsapp_number' => sanitize($_POST['whatsapp_number'] ?? ''),
        'gemini_api_key' => $_POST['gemini_api_key'] ?? '',
        'payment_express_name' => sanitize($_POST['payment_express_name'] ?? ''),
        'payment_express_number' => sanitize($_POST['payment_express_number'] ?? ''),
        'payment_iban' => sanitize($_POST['payment_iban'] ?? ''),
        'payment_iban_holder' => sanitize($_POST['payment_iban_holder'] ?? ''),
        'payment_iban_bank' => sanitize($_POST['payment_iban_bank'] ?? ''),
        'payment_referencia_entity' => sanitize($_POST['payment_referencia_entity'] ?? ''),
        'payment_referencia_ref' => sanitize($_POST['payment_referencia_ref'] ?? ''),
        'bank_name' => sanitize($_POST['bank_name'] ?? ''),
        'bank_holder' => sanitize($_POST['bank_holder'] ?? ''),
        'bank_nif' => sanitize($_POST['bank_nif'] ?? ''),
        'invoice_prefix' => sanitize($_POST['invoice_prefix'] ?? ''),
        'mail_driver' => sanitize($_POST['mail_driver'] ?? 'smtp'),
        'smtp_host' => sanitize($_POST['smtp_host'] ?? ''),
        'smtp_port' => sanitize($_POST['smtp_port'] ?? '587'),
        'smtp_user' => sanitize($_POST['smtp_user'] ?? ''),
        'smtp_pass' => $_POST['smtp_pass'] ?? '',
        'smtp_encryption' => sanitize($_POST['smtp_encryption'] ?? 'tls'),
        'mail_from' => sanitize($_POST['mail_from'] ?? ''),
        'mail_from_name' => sanitize($_POST['mail_from_name'] ?? ''),
        'whatsapp_api_token' => $_POST['whatsapp_api_token'] ?? '',
        'whatsapp_phone_number_id' => sanitize($_POST['whatsapp_phone_number_id'] ?? ''),
        'whatsapp_business_account_id' => sanitize($_POST['whatsapp_business_account_id'] ?? ''),
        'whatsapp_webhook_verify_token' => sanitize($_POST['whatsapp_webhook_verify_token'] ?? ''),
        'whatsapp_api_version' => sanitize($_POST['whatsapp_api_version'] ?? 'v22.0'),
        'map_latitude' => sanitize($_POST['map_latitude'] ?? ''),
        'map_longitude' => sanitize($_POST['map_longitude'] ?? ''),
        'map_address' => sanitize($_POST['map_address'] ?? ''),
        'map_zoom' => sanitize($_POST['map_zoom'] ?? '13'),
    ];
    foreach ($updates as $key => $value) {
        updateSetting($key, $value);
    }
    logActivity($user['id'], 'update_settings', 'Configurações atualizadas');
    $success = 'Configurações atualizadas com sucesso!';
}

$settings = [];
$rows = db()->fetchAll("SELECT * FROM settings");
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .settings-section { margin-bottom: 32px; }
        .settings-section h3 { font-size: 1rem; color: var(--primary); margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
        .settings-section h3 i { margin-right: 8px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        select { width: 100%; max-width: 500px; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.95rem; outline: none; }
        select:focus { border-color: var(--primary); }
        select option { background: #0d1f3c; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-cog"></i> <span>Configurações</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <div class="detail-card">
                    <form method="POST" class="settings-form">
                        <div class="settings-section">
                            <h3><i class="fas fa-globe"></i> Geral</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nome do Site</label>
                                    <input type="text" name="site_name" value="<?= $settings['site_name'] ?? 'ANGONUEVE' ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email Geral</label>
                                    <input type="email" name="site_email" value="<?= $settings['site_email'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="text" name="site_phone" value="<?= $settings['site_phone'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label>Endereço</label>
                                    <input type="text" name="site_address" value="<?= $settings['site_address'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Número WhatsApp (com código, ex: 244935603163)</label>
                                <input type="text" name="whatsapp_number" value="<?= $settings['whatsapp_number'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Gemini API Key (gratuita — para o chatbot)</label>
                                <input type="text" name="gemini_api_key" value="<?= $settings['gemini_api_key'] ?? '' ?>" placeholder="AIza...">
                                <small style="color: var(--text-muted); font-size: 0.75rem;">Obtenha a chave grátis em <a href="https://aistudio.google.com/app/apikey" target="_blank" style="color: var(--secondary);">aistudio.google.com</a> — sem cartão de crédito</small>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3><i class="fas fa-file-invoice"></i> Facturas</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Prefixo da Factura</label>
                                    <input type="text" name="invoice_prefix" value="<?= $settings['invoice_prefix'] ?? 'INV-' ?>">
                                </div>
                                <div class="form-group">
                                    <label>NIF da Empresa</label>
                                    <input type="text" name="company_nif" value="<?= $settings['company_nif'] ?? '' ?>" placeholder="5000000000">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Dados Bancários — Nome do Banco</label>
                                    <input type="text" name="bank_name" value="<?= $settings['bank_name'] ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label>Titular da Conta</label>
                                    <input type="text" name="bank_holder" value="<?= $settings['bank_holder'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>NIF Bancário (se diferente do NIF da empresa)</label>
                                    <input type="text" name="bank_nif" value="<?= $settings['bank_nif'] ?? '' ?>" placeholder="Deixe vazio se igual ao NIF da empresa">
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3><i class="fas fa-credit-card"></i> Métodos de Pagamento</h3>
                            <h4 style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;">1. Express</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nome do Express</label>
                                    <input type="text" name="payment_express_name" value="<?= $settings['payment_express_name'] ?? 'Express' ?>" placeholder="Ex: Express">
                                </div>
                                <div class="form-group">
                                    <label>Número Express</label>
                                    <input type="text" name="payment_express_number" value="<?= $settings['payment_express_number'] ?? '' ?>" placeholder="+244 900 000 000">
                                </div>
                            </div>
                            <h4 style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px; margin-top:16px;">2. Transferência Bancária (IBAN)</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>IBAN</label>
                                    <input type="text" name="payment_iban" value="<?= $settings['payment_iban'] ?? '' ?>" placeholder="AO06004400000000000012345">
                                </div>
                                <div class="form-group">
                                    <label>Titular da Conta (IBAN)</label>
                                    <input type="text" name="payment_iban_holder" value="<?= $settings['payment_iban_holder'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Banco (IBAN)</label>
                                <input type="text" name="payment_iban_bank" value="<?= $settings['payment_iban_bank'] ?? '' ?>">
                            </div>
                            <h4 style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px; margin-top:16px;">3. Referência Multicaixa</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Entidade</label>
                                    <input type="text" name="payment_referencia_entity" value="<?= $settings['payment_referencia_entity'] ?? '99999' ?>" placeholder="99999">
                                </div>
                                <div class="form-group">
                                    <label>Formato Referência</label>
                                    <input type="text" name="payment_referencia_ref" value="<?= $settings['payment_referencia_ref'] ?? 'ANGONUEVE-{ID}' ?>" placeholder="ANGONUEVE-{ID}">
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3><i class="fas fa-envelope"></i> Email (SMTP)</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Servidor SMTP</label>
                                    <input type="text" name="smtp_host" value="<?= $settings['smtp_host'] ?? '' ?>" placeholder="smtp.gmail.com">
                                </div>
                                <div class="form-group">
                                    <label>Porta SMTP</label>
                                    <input type="text" name="smtp_port" value="<?= $settings['smtp_port'] ?? '587' ?>" placeholder="587">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Utilizador SMTP</label>
                                    <input type="text" name="smtp_user" value="<?= $settings['smtp_user'] ?? '' ?>" placeholder="user@angonueve.co">
                                </div>
                                <div class="form-group">
                                    <label>Password SMTP</label>
                                    <input type="password" name="smtp_pass" value="<?= $settings['smtp_pass'] ?? '' ?>" placeholder="********">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Encriptação</label>
                                    <select name="smtp_encryption">
                                        <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Nenhuma</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Driver</label>
                                    <select name="mail_driver">
                                        <option value="smtp" <?= ($settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                        <option value="mail" <?= ($settings['mail_driver'] ?? '') === 'mail' ? 'selected' : '' ?>>PHP mail()</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email do Remetente</label>
                                    <input type="email" name="mail_from" value="<?= $settings['mail_from'] ?? '' ?>" placeholder="noreply@angonueve.co">
                                </div>
                                <div class="form-group">
                                    <label>Nome do Remetente</label>
                                    <input type="text" name="mail_from_name" value="<?= $settings['mail_from_name'] ?? '' ?>" placeholder="ANGONUEVE">
                                </div>
                            </div>
                        </div>

                        <div class="settings-section" id="mapa">
                            <h3><i class="fas fa-map-marked-alt" style="color:#00d4ff;"></i> Mapa & Localização</h3>
                            <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:16px;">Configura as coordenadas do mapa interactivo na página de contacto.</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="text" name="map_latitude" value="<?= $settings['map_latitude'] ?? '-8.838333' ?>" placeholder="-8.838333">
                                </div>
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" name="map_longitude" value="<?= $settings['map_longitude'] ?? '13.234444' ?>" placeholder="13.234444">
                                </div>
                            </div>
                            <div class="form-group" style="max-width:500px;">
                                <label>Morada Completa (exibida no mapa)</label>
                                <input type="text" name="map_address" value="<?= $settings['map_address'] ?? 'Luanda, Angola' ?>" placeholder="Luanda, Angola">
                            </div>
                            <div class="form-group" style="max-width:500px;">
                                <label>Zoom do Mapa (1-18)</label>
                                <input type="number" name="map_zoom" value="<?= $settings['map_zoom'] ?? '13' ?>" min="1" max="18" style="max-width:100px;">
                            </div>
                        </div>

                        <div class="settings-section" id="whatsapp">
                            <h3><i class="fab fa-whatsapp" style="color:#25d366;"></i> WhatsApp Cloud API</h3>
                            <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:16px;">Configura a API oficial do WhatsApp Business para enviar notificações e receber mensagens no painel.</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Token de Acesso Permanente</label>
                                    <input type="password" name="whatsapp_api_token" value="<?= $settings['whatsapp_api_token'] ?? '' ?>" placeholder="EAAT...">
                                </div>
                                <div class="form-group">
                                    <label>ID do Número de Telefone</label>
                                    <input type="text" name="whatsapp_phone_number_id" value="<?= $settings['whatsapp_phone_number_id'] ?? '' ?>" placeholder="123456789012345">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>ID da Conta Business (WABA)</label>
                                    <input type="text" name="whatsapp_business_account_id" value="<?= $settings['whatsapp_business_account_id'] ?? '' ?>" placeholder="123456789012345">
                                </div>
                                <div class="form-group">
                                    <label>Versão da API</label>
                                    <input type="text" name="whatsapp_api_version" value="<?= $settings['whatsapp_api_version'] ?? 'v22.0' ?>" placeholder="v22.0">
                                </div>
                            </div>
                            <div class="form-group" style="max-width:500px;">
                                <label>Token de Verificação do Webhook</label>
                                <input type="text" name="whatsapp_webhook_verify_token" value="<?= $settings['whatsapp_webhook_verify_token'] ?? '' ?>" placeholder="Cria um token secreto (ex: angonueve_wa_2026)">
                                <small style="color:var(--text-muted);font-size:0.72rem;">Usado para verificar o webhook no Meta Developer. URL do webhook: <code style="background:rgba(255,255,255,0.05);padding:2px 6px;border-radius:4px;"><?= SITE_URL ?>/api/whatsapp-webhook.php</code></small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Configurações</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
