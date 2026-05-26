<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);

$slug = sanitize($_GET['id'] ?? '');

$services = [
    'hospedagem' => [
        'title' => 'Hospedagem de Sites',
        'subtitle' => 'Planos robustos e escaláveis para o seu site ou aplicação',
        'icon' => 'fa-globe',
        'description' => '<p>A <strong>ANGONUEVE</strong> oferece planos de hospedagem de sites de alta performance para todos os tipos de projectos. Desde pequenos sites institucionais a grandes portais e aplicações web, temos a solução ideal para si.</p><p>Nossa infraestrutura conta com servidores de última geração, armazenamento SSD NVMe e largura de banda dedicada para garantir que o seu site esteja sempre online e rápido.</p><p>Todos os planos incluem painel de controlo intuitivo, instalador automático de CMS (WordPress, Joomla, etc.), certificado SSL grátis e suporte técnico 24 horas por dia, 7 dias por semana.</p>',
        'features' => ['Hospedagem partilhada, VPS e dedicada', 'SSD NVMe de alta velocidade', 'SSL Grátis incluído', 'Suporte 24/7', 'Backup diário automático', 'Painel de controlo cPanel/WHM', 'Instalador automático de CMS', 'Largura de banda ilimitada'],
        'pricing' => [
            ['name' => 'Básico', 'monthly' => 5000, 'period' => '/mês', 'storage' => '10 GB SSD', 'sites' => '1 Site', 'emails' => '1 Email', 'featured' => false, 'badge' => ''],
            ['name' => 'Standard', 'monthly' => 10000, 'period' => '/mês', 'storage' => '20 GB SSD', 'sites' => '3 Sites', 'emails' => '3 Emails', 'featured' => true, 'badge' => 'Mais Popular'],
            ['name' => 'Profissional', 'monthly' => 15000, 'period' => '/mês', 'storage' => '50 GB SSD', 'sites' => '5 Sites', 'emails' => '5 Emails', 'featured' => false, 'badge' => ''],
            ['name' => 'Empresarial', 'monthly' => 25000, 'period' => '/mês', 'storage' => '100 GB SSD', 'sites' => 'Ilimitados', 'emails' => 'Ilimitados', 'featured' => false, 'badge' => '']
        ]
    ],
    'dominios' => [
        'title' => 'Registo de Domínios',
        'subtitle' => 'O domínio perfeito para o seu projecto',
        'icon' => 'fa-tag',
        'description' => '<p>Registe o nome perfeito para o seu negócio ou projecto com a <strong>ANGONUEVE</strong>. Trabalhamos com as principais extensões de domínio aos melhores preços do mercado.</p><p>Oferecemos gestão completa de DNS, proteção de privacidade WHOIS, redirecionamento de domínios e muito mais. Todo o processo é simples e rápido.</p><p>Além do registo, também oferecemos transferência de domínios, renovação automática e suporte dedicado para qualquer questão relacionada ao seu domínio.</p>',
        'features' => ['Domínios .com, .ao, .co.ao', 'Preços competitivos', 'Gestão intuitiva de DNS', 'Proteção WHOIS', 'Transferência gratuita', 'Renovação automática', 'Redirecionamento de domínios', 'Suporte especializado'],
        'pricing' => [
            ['name' => '.com', 'monthly' => 5000, 'period' => '/ano', 'featured' => false, 'badge' => ''],
            ['name' => '.co.ao', 'monthly' => 7000, 'period' => '/ano', 'featured' => true, 'badge' => 'Nacional'],
            ['name' => '.ao', 'monthly' => 10000, 'period' => '/ano', 'featured' => false, 'badge' => ''],
            ['name' => '.net', 'monthly' => 5500, 'period' => '/ano', 'featured' => false, 'badge' => ''],
            ['name' => '.org', 'monthly' => 5500, 'period' => '/ano', 'featured' => false, 'badge' => '']
        ]
    ],
    'email' => [
        'title' => 'Email Corporativo',
        'subtitle' => 'Email profissional com o seu domínio',
        'icon' => 'fa-envelope',
        'description' => '<p>A <strong>ANGONUEVE</strong> oferece soluções de email corporativo profissional com o seu próprio domínio. Transmita credibilidade e profissionalismo em cada comunicação com emails personalizados como vendas@seudominio.com ou geral@seudominio.com.</p><p>Todos os planos incluem webmail intuitivo, sincronização móvel via IMAP, calendário integrado e proteção anti-spam e antivírus avançada.</p><p>Garanta comunicações seguras e profissionais para a sua equipa com suporte 24 horas por dia, 7 dias por semana.</p>',
        'features' => ['Contas de email ilimitadas', 'Webmail intuitivo', 'Sincronização móvel (IMAP)', 'Calendário integrado', 'Anti-spam e antivírus', 'Assinatura profissional', 'Redirecionamento de email', 'Suporte 24/7'],
        'pricing' => [
            ['name' => 'Básico', 'monthly' => 5000, 'period' => '/mês', 'featured' => false, 'badge' => ''],
            ['name' => 'Standard', 'monthly' => 10000, 'period' => '/mês', 'featured' => true, 'badge' => 'Popular'],
            ['name' => 'Profissional', 'monthly' => 20000, 'period' => '/mês', 'featured' => false, 'badge' => ''],
            ['name' => 'Empresarial', 'monthly' => 40000, 'period' => '/mês', 'featured' => false, 'badge' => '']
        ]
    ],
    'criacao-sites' => [
        'title' => 'Criação de Sites Profissionais',
        'subtitle' => 'Sites modernos e responsivos para o seu negócio',
        'icon' => 'fa-code',
        'description' => '<p>A <strong>ANGONUEVE</strong> cria sites profissionais modernos e responsivos, feitos à medida do seu negócio. Desde landing pages a sites institucionais completos, entregamos soluções que destacam a sua marca na internet.</p><p>Cada site é desenvolvido com design personalizado, optimizado para motores de busca (SEO), totalmente responsivo para dispositivos móveis e integrado com redes sociais.</p><p>Incluímos formulário de contacto, galeria de imagens, Google Analytics e suporte contínuo para garantir que o seu site está sempre actualizado e funcional.</p>',
        'features' => ['Design personalizado', 'Responsivo mobile', 'Optimizado SEO', 'Integração redes sociais', 'Formulário de contacto', 'Galeria de imagens', 'Google Analytics', 'Suporte e manutenção'],
        'pricing' => [
            ['name' => 'Básico', 'monthly' => 50000, 'period' => '/projeto', 'featured' => false, 'badge' => ''],
            ['name' => 'Standard', 'monthly' => 100000, 'period' => '/projeto', 'featured' => true, 'badge' => 'Recomendado'],
            ['name' => 'Profissional', 'monthly' => 200000, 'period' => '/projeto', 'featured' => false, 'badge' => ''],
            ['name' => 'Empresarial', 'monthly' => 400000, 'period' => '/projeto', 'featured' => false, 'badge' => '']
        ]
    ]
];

if (!$slug || !isset($services[$slug])) {
    header('Location: dashboard.php');
    exit;
}

$svc = $services[$slug];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'order') {
    $planName = sanitize($_POST['plan_name'] ?? '');
    $planPrice = floatval($_POST['plan_price'] ?? 0);
    $planPeriod = sanitize($_POST['plan_period'] ?? 'monthly');

    if ($planName && $planPrice > 0) {
        $paymentType = match ($planPeriod) { '/ano' => 'yearly', '/mês' => 'monthly', default => 'onetime' };
        db()->insert('orders', [
            'customer_name' => $user['name'],
            'customer_email' => $user['email'],
            'customer_phone' => $user['phone'] ?? '',
            'service_id' => $slug,
            'service_name' => $svc['title'],
            'plan_name' => $planName,
            'price_monthly' => $planPrice,
            'payment_type' => $paymentType,
            'status' => 'pending'
        ]);
        $msg = 'Encomenda realizada com sucesso!';
        $msgType = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $svc['title'] ?> - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }

        .svc-header { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 24px; }
        .svc-icon { width: 56px; height: 56px; border-radius: 12px; background: rgba(0,230,118,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--success); flex-shrink: 0; }
        .svc-header h1 { font-size: 1.4rem; font-weight: 700; margin: 0 0 4px; }
        .svc-header p { color: var(--text-muted); font-size: 0.88rem; margin: 0; }

        .svc-desc { line-height: 1.7; font-size: 0.92rem; color: var(--text); margin-bottom: 24px; }
        .svc-desc p { margin: 0 0 12px; }

        .features-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; margin-bottom: 32px; }
        .feature-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; }
        .feature-item i { color: var(--success); font-size: 0.8rem; }

        .pricing-section { margin-top: 8px; }
        .pricing-section h2 { font-size: 1.2rem; font-weight: 700; margin: 0 0 4px; }
        .pricing-section > p { font-size: 0.85rem; color: var(--text-muted); margin: 0 0 20px; }

        .plan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .plan-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 10px; padding: 20px; position: relative; transition: all 0.3s; }
        .plan-card:hover { border-color: var(--success); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .plan-card.featured { border-color: var(--success); box-shadow: 0 0 0 1px var(--success); }
        .plan-badge { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: var(--success); color: #000; font-size: 0.65rem; font-weight: 700; padding: 3px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .plan-name { font-size: 0.95rem; font-weight: 600; margin-bottom: 12px; }
        .plan-price { margin-bottom: 16px; }
        .plan-price .amount { font-size: 1.4rem; font-weight: 800; }
        .plan-price .currency { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); }
        .plan-price .period { font-size: 0.75rem; color: var(--text-muted); }
        .plan-details { list-style: none; padding: 0; margin: 0 0 16px; font-size: 0.8rem; }
        .plan-details li { padding: 4px 0; display: flex; align-items: center; gap: 8px; }
        .plan-details li i { color: var(--success); font-size: 0.7rem; width: 14px; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 16px; }
        .alert-success { background: rgba(0,230,118,0.1); color: var(--success); border: 1px solid rgba(0,230,118,0.2); }

        @media (max-width: 768px) { .svc-header { flex-direction: column; } }
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
            <div class="header-search"><i class="fas <?= $svc['icon'] ?>"></i> <span><?= $svc['title'] ?></span></div>
            <div class="header-user">
                <span class="avatar"><i class="fas fa-user"></i></span>
                <span><?= sanitize($user['name']) ?></span>
                <a href="orders.php" class="btn-sm"><i class="fas fa-shopping-cart"></i></a>
                <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <div class="admin-content" style="padding:16px 24px;">
            <?php if (isset($msg)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?> <a href="orders.php" style="color:var(--success);text-decoration:underline;margin-left:8px;">Ver encomendas</a></div>
            <?php endif; ?>

            <div class="svc-header">
                <div class="svc-icon"><i class="fas <?= $svc['icon'] ?>"></i></div>
                <div>
                    <h1><?= $svc['title'] ?></h1>
                    <p><?= $svc['subtitle'] ?></p>
                </div>
            </div>

            <div class="svc-desc"><?= $svc['description'] ?></div>

            <div class="features-grid">
                <?php foreach ($svc['features'] as $f): ?>
                    <div class="feature-item"><i class="fas fa-check"></i> <?= $f ?></div>
                <?php endforeach; ?>
            </div>

            <div class="pricing-section">
                <h2>Planos e Preços</h2>
                <p>Preços em Kz. Escolha o plano ideal para si.</p>
                <div class="plan-grid">
                    <?php foreach ($svc['pricing'] as $plan): ?>
                        <div class="plan-card<?= $plan['featured'] ? ' featured' : '' ?>">
                            <?php if ($plan['badge']): ?>
                                <div class="plan-badge"><?= $plan['badge'] ?></div>
                            <?php endif; ?>
                            <div class="plan-name"><?= $plan['name'] ?></div>
                            <div class="plan-price">
                                <span class="currency">Kz</span>
                                <span class="amount"><?= number_format($plan['monthly'], 0, ',', ' ') ?></span>
                                <span class="period"><?= $plan['period'] ?></span>
                            </div>
                            <?php if (isset($plan['storage'])): ?>
                                <ul class="plan-details">
                                    <li><i class="fas fa-hdd"></i> <?= $plan['storage'] ?></li>
                                    <li><i class="fas fa-globe"></i> <?= $plan['sites'] ?></li>
                                    <li><i class="fas fa-envelope"></i> <?= $plan['emails'] ?></li>
                                </ul>
                            <?php else: ?>
                                <ul class="plan-details">
                                    <li><i class="fas fa-check"></i> Plano <?= $plan['name'] ?></li>
                                    <li><i class="fas fa-check"></i> Preço: Kz <?= number_format($plan['monthly'], 0, ',', ' ') . $plan['period'] ?></li>
                                </ul>
                            <?php endif; ?>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="action" value="order">
                                <input type="hidden" name="plan_name" value="<?= $plan['name'] ?>">
                                <input type="hidden" name="plan_price" value="<?= $plan['monthly'] ?>">
                                <input type="hidden" name="plan_period" value="<?= $plan['period'] ?>">
                                <button type="submit" class="btn <?= $plan['featured'] ? 'btn-success' : 'btn-primary' ?>" style="width:100%;"><i class="fas fa-paper-plane"></i> Contratar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
</body>
</html>