<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();
checkSessionTimeout();

$user = currentUser();
$userId = $user['id'];

$userData = db()->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
if (!$userData) {
    header('Location: logout.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $nif = trim($_POST['nif'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($name)) {
            $error = 'O nome é obrigatório.';
        } else {
            db()->update('users', [
                'name' => $name,
                'phone' => $phone,
                'nif' => $nif,
                'address' => $address
            ], 'id = :id', ['id' => $userId]);

            $_SESSION['user_name'] = $name;

            logActivity($userId, 'profile_update', "{$name} atualizou o perfil");

            $success = 'Perfil actualizado com sucesso.';
            $userData = db()->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
        }
    }
}

$totalOrders = db()->count('orders', 'customer_email = ?', [$userData['email']]);
$totalInvoices = db()->count('invoices', 'client_id = ?', [$userId]);
$totalPayments = db()->count('payments', 'client_id = ? AND status = ?', [$userId, 'confirmed']);
$activeServices = db()->count('client_services', 'client_id = ? AND status = ?', [$userId, 'active']);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - <?= sanitize($userData['name']) ?> | ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } }
        .profile-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 28px; }
        .profile-card h2 { font-size: 1.1rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .profile-card h2 i { color: var(--success); }
        .profile-card .form-group { margin-bottom: 18px; }
        .profile-card label { display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; }
        .profile-card label i { margin-right: 6px; color: var(--success); }
        .profile-card input, .profile-card textarea { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--body-bg); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.9rem; transition: border-color 0.2s; }
        .profile-card input:focus, .profile-card textarea:focus { outline: none; border-color: var(--success); }
        .profile-card textarea { min-height: 80px; resize: vertical; }
        .profile-card input:disabled { opacity: 0.6; cursor: not-allowed; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
        .info-value { font-weight: 600; font-size: 0.95rem; text-align: right; }
        .info-value .badge { font-size: 0.75rem; }
        .empty-state i { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.3; display: block; }
        .btn-save { background: var(--success); color: #000; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 8px; }
        .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
        .email-display { padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--body-bg); color: var(--text-muted); font-size: 0.9rem; opacity: 0.7; }
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
                <a href="chat.php" class="<?= $currentPage === 'chat.php' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Chat Suporte
                </a>
                <hr>
                <a href="profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i> Meu Perfil
                </a>
                <a href="change-password.php" class="<?= $currentPage === 'change-password.php' ? 'active' : '' ?>">
                    <i class="fas fa-key"></i> Alterar Password
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
                    <i class="fas fa-user-cog"></i>
                    <span>Meu Perfil</span>
                </div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($userData['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-content">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="profile-grid">
                    <div class="profile-card">
                        <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
                        <form method="POST">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Nome</label>
                                <input type="text" name="name" value="<?= sanitize($userData['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <div class="email-display"><?= sanitize($userData['email'] ?? '') ?></div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Telefone</label>
                                <input type="text" name="phone" value="<?= sanitize($userData['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> NIF</label>
                                <input type="text" name="nif" value="<?= sanitize($userData['nif'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt"></i> Morada</label>
                                <textarea name="address"><?= sanitize($userData['address'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Alterações</button>
                        </form>
                    </div>

                    <div class="profile-card">
                        <h2><i class="fas fa-info-circle"></i> Informações da Conta</h2>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Membro desde</span>
                            <span class="info-value"><?= formatDate($userData['created_at'], 'd/m/Y') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-flag"></i> Estado</span>
                            <span class="info-value"><?= statusBadge($userData['status'] ?? 'active') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-shopping-cart"></i> Total Encomendas</span>
                            <span class="info-value"><?= $totalOrders ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-file-invoice"></i> Total Facturas</span>
                            <span class="info-value"><?= $totalInvoices ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-credit-card"></i> Pagamentos Efectuados</span>
                            <span class="info-value"><?= $totalPayments ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-cogs"></i> Serviços Activos</span>
                            <span class="info-value"><?= $activeServices ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
