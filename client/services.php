<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);

$services = [
    ['slug' => 'hospedagem', 'title' => 'Hospedagem de Sites', 'subtitle' => 'Planos robustos e escaláveis', 'icon' => 'fa-globe', 'color' => 'var(--success)'],
    ['slug' => 'dominios', 'title' => 'Registo de Domínios', 'subtitle' => 'O domínio perfeito para si', 'icon' => 'fa-tag', 'color' => '#0ea5e9'],
    ['slug' => 'email', 'title' => 'Email Corporativo', 'subtitle' => 'Email profissional com o seu domínio', 'icon' => 'fa-envelope', 'color' => '#f59e0b'],
    ['slug' => 'criacao-sites', 'title' => 'Criação de Sites Profissionais', 'subtitle' => 'Sites modernos e responsivos', 'icon' => 'fa-code', 'color' => '#8b5cf6']
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }

        .svc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .svc-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; display: block; transition: all 0.3s; position: relative; overflow: hidden; }
        .svc-card:hover { border-color: var(--success); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .svc-card .svc-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 14px; }
        .svc-card h3 { font-size: 1rem; font-weight: 600; margin: 0 0 4px; }
        .svc-card p { font-size: 0.82rem; color: var(--text-muted); margin: 0 0 12px; line-height: 1.5; }
        .svc-card .svc-link { font-size: 0.82rem; font-weight: 600; color: var(--success); }
        .svc-card .svc-link i { font-size: 0.7rem; margin-left: 4px; transition: transform 0.2s; }
        .svc-card:hover .svc-link i { transform: translateX(4px); }
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
            <a href="services.php" class="active"><i class="fas fa-concierge-bell"></i> Serviços</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Encomendas</a>
            <a href="invoices.php"><i class="fas fa-file-invoice"></i> Facturas</a>
            <a href="chat.php"><i class="fas fa-comments"></i> Chat Suporte</a>
            <hr>
            <a href="../index.html" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
            <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-concierge-bell"></i> <span>Serviços</span></div>
            <div class="header-user">
                <span class="avatar"><i class="fas fa-user"></i></span>
                <span><?= sanitize($user['name']) ?></span>
                <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <div class="admin-content" style="padding:16px 24px;">
            <div style="margin-bottom:20px;">
                <h1 style="font-size:1.3rem;font-weight:700;margin:0 0 4px;">Nossos Serviços</h1>
                <p style="color:var(--text-muted);font-size:0.88rem;margin:0;">Escolha um serviço para ver detalhes e planos</p>
            </div>

            <div class="svc-grid">
                <?php foreach ($services as $s): ?>
                    <?php $bg = $s['color'] . '15'; ?>
                    <a href="service-view.php?id=<?= $s['slug'] ?>" class="svc-card">
                        <div class="svc-icon" style="background:<?= $bg ?>;color:<?= $s['color'] ?>;">
                            <i class="fas <?= str_replace('fab ', '', $s['icon']) ?>"></i>
                        </div>
                        <h3><?= $s['title'] ?></h3>
                        <p><?= $s['subtitle'] ?></p>
                        <span class="svc-link">Ver detalhes <i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>