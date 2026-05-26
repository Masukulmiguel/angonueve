<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'client') {
        $allowed = ['dashboard.php', 'profile.php', 'invoices.php', 'support.php', 'orcamento.php'];
        $redirect = in_array($_GET['redirect'] ?? '', $allowed) ? $_GET['redirect'] : 'dashboard.php';
        header('Location: ' . $redirect);
        exit;
    }
    header('Location: ../admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Preencha todos os campos';
    } else {
        $loginResult = login($email, $password);
        if ($loginResult === true) {
            $redirect = sanitize($_GET['redirect'] ?? 'dashboard.php');
            header('Location: ' . $redirect);
            exit;
        } elseif ($loginResult === 'rate_limited') {
            $error = 'Muitas tentativas. Aguarde 15 minutos.';
        } elseif ($loginResult === 'pending') {
            $error = 'A sua conta está pendente de aprovação. Aguarde contacto.';
        } elseif ($loginResult === 'blocked') {
            $error = 'A sua conta foi bloqueada. Contacte o suporte.';
        } else {
            $error = 'Email ou password incorretos';
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Cliente - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .login-logo span { background: linear-gradient(135deg, #00e676, #00c853); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
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
                <p>Área do Cliente</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login" style="background:linear-gradient(135deg,#00e676,#00c853);">
                    <i class="fas fa-arrow-right"></i> Entrar
                </button>
            </form>
            <p style="text-align:center;margin-top:12px;color:var(--text-muted);font-size:0.85rem;">
                <a href="forgot-password.php" style="color:var(--text-muted);">Esqueceu-se da password?</a>
            </p>
            <p style="text-align:center;margin-top:12px;color:var(--text-muted);font-size:0.85rem;">
                Ainda não tem conta? <a href="register.php" style="color:var(--success);">Criar conta</a>
            </p>
        </div>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
