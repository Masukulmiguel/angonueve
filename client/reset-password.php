<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'client') {
        header('Location: dashboard.php');
        exit;
    }
    header('Location: ../admin/dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$validToken = false;
$email = '';

if (empty($token)) {
    $error = 'Token de recuperação inválido.';
} else {
    $reset = db()->fetchOne(
        "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()",
        [$token]
    );

    if (!$reset) {
        $error = 'Token de recuperação inválido ou expirado.';
    } else {
        $validToken = true;
        $email = $reset['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirm)) {
            $error = 'Preencha todos os campos.';
        } elseif (strlen($password) < 8) {
            $error = 'A password deve ter pelo menos 8 caracteres.';
        } elseif ($password !== $confirm) {
            $error = 'As passwords não coincidem.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            db()->update('users', ['password' => $hash], 'email = :email AND role = :role', ['email' => $email, 'role' => 'client']);
            db()->update('password_resets', ['used' => 1], 'token = :token', ['token' => $token]);

            logActivity(0, 'password_reset', "Password redefinida para {$email}");

            $success = 'Password redefinida com sucesso.';
            $validToken = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Password - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .login-logo span { background: linear-gradient(135deg, #00e676, #00c853); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .password-hint { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-user-circle" style="color:var(--success);"></i>
                    <span>ANGONUEVE</span>
                </div>
                <p>Redefinir Password</p>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <p style="text-align:center;margin-top:16px;">
                    <a href="login.php" class="btn-login" style="background:linear-gradient(135deg,#00e676,#00c853);text-decoration:none;display:inline-block;padding:12px 30px;border-radius:8px;color:#000;font-weight:600;">
                        <i class="fas fa-arrow-right"></i> Ir para o Login
                    </a>
                </p>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <p style="text-align:center;margin-top:16px;">
                    <a href="forgot-password.php" style="color:var(--success);"><i class="fas fa-arrow-left"></i> Solicitar novo link</a>
                </p>
            <?php elseif ($validToken): ?>
                <form method="POST" class="login-form">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Nova Password</label>
                        <input type="password" name="password" placeholder="••••••••" required minlength="8">
                        <div class="password-hint">Mínimo de 8 caracteres</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Confirmar Password</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required minlength="8">
                    </div>
                    <button type="submit" class="btn-login" style="background:linear-gradient(135deg,#00e676,#00c853);">
                        <i class="fas fa-save"></i> Redefinir Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
