<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $nif = sanitize($_POST['nif'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Preencha todos os campos obrigatórios';
    } elseif (!validateEmail($email)) {
        $error = 'Email inválido';
    } elseif (strlen($password) < 6) {
        $error = 'A password deve ter pelo menos 6 caracteres';
    } elseif ($password !== $confirm) {
        $error = 'As passwords não coincidem';
    } elseif ($nif && !validateNIF($nif)) {
        $error = 'NIF inválido. O NIF angolano tem 10 dígitos (ex: 1234567-890).';
    } else {
        $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'Este email já está registado';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            db()->insert('users', [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'nif' => $nif,
                'address' => $address,
                'role' => 'client',
                'status' => 'pending'
            ]);
            logActivity(0, 'register', "Novo cliente registado: {$name} ({$email})");
            $success = 'Conta criada com sucesso! Aguarde a aprovação do administrador.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - ANGONUEVE</title>
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
                    <i class="fas fa-user-plus" style="color:var(--success);"></i>
                    <span>Criar Conta</span>
                </div>
                <p>Área do Cliente ANGONUEVE</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <p style="text-align:center;margin-top:16px;"><a href="login.php" class="btn btn-primary">Ir para o Login</a></p>
            <?php else: ?>
            <form method="POST" class="login-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nome completo *</label>
                    <input type="text" name="name" placeholder="O seu nome" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Telefone</label>
                    <input type="text" name="phone" placeholder="+244 900 000 000">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> NIF (opcional)</label>
                    <input type="text" name="nif" placeholder="1234567-890" maxlength="11">
                    <small style="color:var(--text-muted);font-size:0.7rem;">NIF angolano com 10 dígitos</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Endereço (opcional)</label>
                    <input type="text" name="address" placeholder="Luanda, Angola">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password *</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirmar Password *</label>
                    <input type="password" name="confirm_password" placeholder="Repita a password" required minlength="6">
                </div>
                <button type="submit" class="btn-login" style="background:linear-gradient(135deg,#00e676,#00c853);">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </button>
            </form>
            <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:0.85rem;">
                Já tem conta? <a href="login.php" style="color:var(--success);">Fazer login</a>
            </p>
            <?php endif; ?>
        </div>
    </div>
<script src="../js/script.js?v=2"></script>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>
