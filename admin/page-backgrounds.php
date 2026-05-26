<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('settings');

$user = currentUser();
$uploadDir = __DIR__ . '/../uploads/page-bg/';
$uploadUrl = SITE_URL . '/uploads/page-bg';

$pages = [
    'home'     => 'Início (index.html)',
    'about'    => 'Sobre (about.html)',
    'contact'  => 'Contacto (contact.html)',
    'services' => 'Serviços (servicos.php)',
    'models'   => 'Modelos (modelos.php)',
    'login'    => 'Login Cliente (client/login.php)',
    'register' => 'Registo Cliente (client/register.php)',
];

$error = '';
$success = '';

// Delete
if (isset($_GET['delete'])) {
    $page = sanitize($_GET['delete']);
    if (array_key_exists($page, $pages)) {
        $key = 'page_bg_' . $page;
        $row = db()->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        if ($row && $row['setting_value']) {
            $path = $uploadDir . $row['setting_value'];
            if (file_exists($path)) unlink($path);
        }
        updateSetting($key, '');
        logActivity($user['id'], 'delete_page_bg', "Fundo da página {$page} removido");
        $success = 'Fundo removido com sucesso!';
    }
}

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page'])) {
    $page = sanitize($_POST['page']);
    if (!array_key_exists($page, $pages)) {
        $error = 'Página inválida.';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Seleccione uma imagem válida.';
    } else {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) {
            $error = 'Formato não permitido. Use JPG, PNG, WebP ou GIF.';
        } else {
            $filename = $page . '_bg_' . time() . '.' . $ext;
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Remove old file
            $key = 'page_bg_' . $page;
            $old = db()->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
            if ($old && $old['setting_value']) {
                $oldPath = $uploadDir . $old['setting_value'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                updateSetting($key, $filename);
                logActivity($user['id'], 'upload_page_bg', "Fundo da página {$page} alterado");
                $success = 'Fundo actualizado com sucesso!';
            } else {
                $error = 'Erro ao fazer upload.';
            }
        }
    }
}

$backgrounds = [];
foreach ($pages as $key => $label) {
    $val = getSetting('page_bg_' . $key, '');
    $backgrounds[$key] = [
        'label' => $label,
        'filename' => $val,
        'url' => $val ? ($uploadUrl . '/' . $val) : ''
    ];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fundos de Página - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .bg-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;margin-top:8px}
        .bg-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;overflow:hidden;transition:border-color 0.3s}
        .bg-card:hover{border-color:rgba(0,212,255,0.2)}
        .bg-card-header{padding:16px 20px;border-bottom:1px solid var(--card-border)}
        .bg-card-header h3{font-size:1rem}
        .bg-card-header p{font-size:0.78rem;color:var(--text-muted);margin-top:2px}
        .bg-preview{height:160px;background:var(--dark);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
        .bg-preview img{width:100%;height:100%;object-fit:cover}
        .bg-preview .no-bg{color:var(--text-muted);font-size:0.85rem;display:flex;flex-direction:column;align-items:center;gap:8px}
        .bg-preview .no-bg i{font-size:2rem;opacity:0.3}
        .bg-card-body{padding:16px 20px}
        .bg-form{display:flex;gap:10px;flex-wrap:wrap}
        .bg-form input[type=file]{flex:1;min-width:140px;font-size:0.82rem;padding:8px;border-radius:6px;border:1px solid var(--card-border);background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif}
        .bg-form input[type=file]::file-selector-button{padding:6px 12px;border-radius:4px;border:none;background:var(--primary);color:white;font-weight:500;cursor:pointer;font-size:0.78rem;margin-right:8px}
        .bg-actions{display:flex;gap:8px;margin-top:10px}
        .bg-actions .btn{font-size:0.82rem;padding:8px 16px}
        .bg-actions .btn-danger{background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.2);color:#ff4444}
        .bg-actions .btn-danger:hover{background:rgba(255,68,68,0.2)}
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-image"></i> <span>Fundos de Página</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <p style="margin-bottom:20px;color:var(--text-muted);font-size:0.9rem;">Adicione imagens de fundo para cada página do site. Tamanho recomendado: <strong>1920x1080px</strong> ou superior. Formatos: JPG, PNG, WebP, GIF.</p>

                <div class="bg-grid">
                    <?php foreach ($backgrounds as $key => $bg): ?>
                    <div class="bg-card">
                        <div class="bg-card-header">
                            <h3><?= $bg['label'] ?></h3>
                            <p>page_bg_<?= $key ?></p>
                        </div>
                        <div class="bg-preview">
                            <?php if ($bg['url']): ?>
                                <img src="<?= $bg['url'] ?>" alt="Fundo <?= $key ?>">
                            <?php else: ?>
                                <div class="no-bg"><i class="fas fa-image"></i> Nenhuma imagem</div>
                            <?php endif; ?>
                        </div>
                        <div class="bg-card-body">
                            <form method="POST" enctype="multipart/form-data" class="bg-form">
                                <input type="hidden" name="page" value="<?= $key ?>">
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload"></i> Upload</button>
                            </form>
                            <?php if ($bg['url']): ?>
                            <div class="bg-actions">
                                <a href="?delete=<?= $key ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remover fundo desta página?')"><i class="fas fa-trash"></i> Remover</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
