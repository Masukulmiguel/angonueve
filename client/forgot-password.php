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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !validateEmail($email)) {
            $error = 'Introduza um email válido.';
        } else {
            $user = db()->fetchOne("SELECT id, name FROM users WHERE email = ? AND role = 'client'", [$email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                try {
                    db()->insert('password_resets', [
                        'email' => $email,
                        'token' => $token,
                        'expires_at' => $expiresAt
                    ]);

                    $resetLink = SITE_URL . '/client/reset-password.php?token=' . urlencode($token);
                    $siteName = getSetting('site_name', 'ANGONUEVE');
                    $subject = 'Recuperação de Password - ' . $siteName;
                    $body = '
                    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
                        <div style="background: #0a1628; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="color: #00e676; margin: 0; font-size: 22px;">' . $siteName . '</h1>
                        </div>
                        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <p style="color: #333; font-size: 16px;">Olá <strong>' . sanitize($user['name']) . '</strong>,</p>
                            <p style="color: #555; font-size: 15px;">Recebemos um pedido de recuperação de password para a sua conta.</p>
                            <p style="color: #555; font-size: 15px;">Clique no botão abaixo para definir uma nova password:</p>
                            <div style="text-align: center; margin: 25px 0;">
                                <a href="' . $resetLink . '" style="background: #00e676; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">Recuperar Password</a>
                            </div>
                            <p style="color: #999; font-size: 13px;">Este link expira em 1 hora.</p>
                            <p style="color: #999; font-size: 13px;">Se não solicitou esta recuperação, ignore este email.</p>
                        </div>
                    </div>';

                    sendEmail($email, $subject, $body);
                } catch (Exception $e) {
                    error_log("Password reset error: " . $e->getMessage());
                }
            }

            $success = 'Se o email existir, receberá um link de recuperação.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Password - ANGONUEVE</title>
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
                <p>Recuperar Password</p>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>
                <button type="submit" class="btn-login" style="background:linear-gradient(135deg,#00e676,#00c853);">
                    <i class="fas fa-paper-plane"></i> Enviar Link de Recuperação
                </button>
            </form>
            <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:0.85rem;">
                <a href="login.php" style="color:var(--success);"><i class="fas fa-arrow-left"></i> Voltar ao Login</a>
            </p>
        </div>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
