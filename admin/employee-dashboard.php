<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$user = currentUser();
if ($user['role'] !== 'employee') {
    header('Location: dashboard.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

$modules = [
    'dashboard' => ['icon' => 'fa-th-large', 'label' => 'Dashboard', 'color' => '#00d4ff'],
    'messages' => ['icon' => 'fa-envelope', 'label' => 'Mensagens', 'color' => '#8b5cf6'],
    'orders' => ['icon' => 'fa-shopping-cart', 'label' => 'Encomendas', 'color' => '#ff9800'],
    'invoices' => ['icon' => 'fa-file-invoice', 'label' => 'Facturas', 'color' => '#00e676'],
    'payments' => ['icon' => 'fa-credit-card', 'label' => 'Pagamentos', 'color' => '#ff4081'],
    'revenue' => ['icon' => 'fa-chart-line', 'label' => 'Faturamento', 'color' => '#00e6c8'],
    'support_chat' => ['icon' => 'fa-comments', 'label' => 'Chat Suporte', 'color' => '#0ea5e9'],
    'abandoned' => ['icon' => 'fa-cart-arrow-down', 'label' => 'Compras Abandonadas', 'color' => '#ef4444'],
    'visitors' => ['icon' => 'fa-eye', 'label' => 'Visitantes', 'color' => '#10b981'],
    'clients' => ['icon' => 'fa-users', 'label' => 'Clientes', 'color' => '#f59e0b'],
    'chatbot' => ['icon' => 'fa-robot', 'label' => 'Chatbot', 'color' => '#ec4899'],
    'payslips' => ['icon' => 'fa-file-invoice-dollar', 'label' => 'Recibos de Vencimento', 'color' => '#00d4ff'],
    'settings' => ['icon' => 'fa-cog', 'label' => 'Configurações', 'color' => '#64748b'],
];

$error = sanitize($_GET['error'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Funcionário - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .emp-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px; }
        .emp-header h1 { font-size:1.3rem;margin:0; }
        .emp-header p { color:var(--text-muted);font-size:0.85rem;margin:4px 0 0; }
        .emp-badge { background:rgba(0,212,255,0.1);color:var(--primary);padding:3px 12px;border-radius:20px;font-size:0.72rem;font-weight:600; }
        .module-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px; }
        .module-card { display:flex;flex-direction:column;align-items:center;text-align:center;padding:24px 16px;border-radius:12px;border:1px solid var(--border);background:var(--card-bg);text-decoration:none;color:var(--text);transition:all 0.3s; }
        .module-card:hover { transform:translateY(-3px);border-color:var(--primary);box-shadow:0 8px 24px rgba(0,0,0,0.12); }
        .module-card .mod-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:10px; }
        .module-card .mod-label { font-size:0.85rem;font-weight:600; }
        .alert-permission { padding:14px 20px;border-radius:10px;background:rgba(255,82,82,0.08);border:1px solid rgba(255,82,82,0.15);color:var(--danger);margin-bottom:20px;font-size:0.88rem; }
        .alert-permission i { margin-right:8px; }
        .emp-info { display:flex;align-items:center;gap:14px;background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:24px; }
        .emp-info .avatar { width:56px;height:56px;border-radius:50%;background:rgba(0,212,255,0.1);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--primary);flex-shrink:0; }
        .emp-info h2 { font-size:1.1rem;margin:0 0 2px; }
        .emp-info p { color:var(--text-muted);font-size:0.82rem;margin:0; }
        .perm-list { display:flex;flex-wrap:wrap;gap:4px;margin-top:6px; }
        .perm-list span { font-size:0.68rem;padding:2px 10px;border-radius:20px;background:rgba(0,212,255,0.06);border:1px solid var(--border);color:var(--text-muted); }
        .header-user .avatar { width:32px;height:32px;border-radius:50%;background:rgba(0,212,255,0.15);display:inline-flex;align-items:center;justify-content:center;font-size:0.85rem; }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-user-tie"></i> <span>Painel do Funcionário</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content" style="padding:16px 24px;">
            <?php if ($error === 'permission'): ?>
                <div class="alert-permission"><i class="fas fa-exclamation-triangle"></i> Não tem permissão para aceder a essa página.</div>
            <?php endif; ?>

            <div class="emp-info">
                <div class="avatar"><i class="fas fa-user"></i></div>
                <div>
                    <h2>Bem-vindo, <?= sanitize($user['name']) ?>!</h2>
                    <p>Funcionário — <?= sanitize(db()->fetchOne("SELECT position FROM users WHERE id = ?", [$user['id']])['position'] ?? 'Sem cargo') ?></p>
                    <div class="perm-list">
                        <?php
                        $perms = getEmployeePermissions($user['id']);
                        $allPerms = getAllPermissions();
                        foreach ($perms as $p):
                        ?>
                            <span><?= $allPerms[$p] ?? $p ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="emp-header">
                <div>
                    <h1>Módulos Disponíveis</h1>
                    <p>Os módulos a que tem acesso estão listados abaixo.</p>
                </div>
                <span class="emp-badge"><i class="fas fa-key"></i> <?= count($perms) ?> permissões</span>
            </div>

            <div class="module-grid">
                <?php foreach ($modules as $key => $mod): ?>
                    <?php if (hasPermission($key)): ?>
                        <a href="<?= $key === 'support_chat' ? 'support-chat.php' : $key . '.php' ?>" class="module-card">
                            <div class="mod-icon" style="background:<?= $mod['color'] ?>12;color:<?= $mod['color'] ?>;">
                                <i class="fas <?= $mod['icon'] ?>"></i>
                            </div>
                            <span class="mod-label"><?= $mod['label'] ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if (empty(array_filter($modules, fn($k) => hasPermission($k), ARRAY_FILTER_USE_KEY))): ?>
                <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
                    <i class="fas fa-lock" style="font-size:3rem;display:block;margin-bottom:16px;opacity:0.3;"></i>
                    <p>Nenhum módulo disponível. Contacte o administrador para atribuir permissões.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>