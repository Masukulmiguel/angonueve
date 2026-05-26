<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();
checkSessionTimeout();

$user = currentUser();
$userId = $user['id'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Preencha todos os campos.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'A nova password deve ter pelo menos 8 caracteres.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'As novas passwords não coincidem.';
        } else {
            $userData = db()->fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);
            if (!$userData || !password_verify($currentPassword, $userData['password'])) {
                $error = 'A password actual está incorrecta.';
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                db()->update('users', ['password' => $hash], 'id = :id', ['id' => $userId]);

                logActivity($userId, 'password_change', "{$user['name']} alterou a password");

                $success = 'Password alterada com sucesso.';
            }
        }
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Password - <?= sanitize($user['name']) ?> | ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .password-card { max-width: 520px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 28px; }
        .password-card h2 { font-size: 1.1rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .password-card h2 i { color: var(--success); }
        .password-card .form-group { margin-bottom: 18px; }
        .password-card label { display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; }
        .password-card label i { margin-right: 6px; color: var(--success); }
        .password-card input { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--body-bg); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.9rem; transition: border-color 0.2s; }
        .password-card input:focus { outline: none; border-color: var(--success); }
        .btn-save { background: var(--success); color: #000; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 8px; }
        .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
        .password-hint { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; }
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
                    <i class="fas fa-key"></i>
                    <span>Alterar Password</span>
                </div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
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

                <div class="password-card">
                    <h2><i class="fas fa-lock"></i> Alterar Password</h2>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label><i class="fas fa-key"></i> Password Actual</label>
                            <input type="password" name="current_password" placeholder="••••••••" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Nova Password</label>
                            <input type="password" name="new_password" placeholder="••••••••" required minlength="8">
                            <div class="password-hint">Mínimo de 8 caracteres</div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Confirmar Nova Password</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" required minlength="8">
                        </div>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Alterar Password</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
