<?php
require_once __DIR__ . '/includes/auth.php';

$slug = sanitize($_GET['slug'] ?? '');
$service = db()->fetchOne("SELECT * FROM services_db WHERE slug = ? AND is_active = 1", [$slug]);

if (!$service) {
    $pageTitle = 'Serviço Não Encontrado - ANGONUEVE';
    $pageDescription = 'O serviço que procura não foi encontrado.';
} else {
    $features = json_decode($service['features'], true) ?? [];
    $pageTitle = htmlspecialchars($service['name']) . ' - ANGONUEVE';
    $pageDescription = htmlspecialchars($service['short_description']);
}

$allServices = db()->fetchAll("SELECT * FROM services_db WHERE is_active = 1 AND slug != ? ORDER BY sort_order ASC LIMIT 4", [$slug]);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDescription ?>">
    <meta property="og:type" content="website">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <a href="#main-content" class="skip-link">Ir para o conteúdo principal</a>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <main id="main-content">
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.html" class="nav-logo">
                <img src="images/logo.png" alt="ANGONUEVE">
                <span>ANGONUEVE</span>
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="en/servico.html" class="lang-switch" title="English version">EN</a></li>
                <li><a href="index.html">Início</a></li>
                <li><a href="servicos.php">Serviços</a></li>
                <li><a href="modelos.php">Modelos</a></li>
                <li><a href="criar-site.php">Criar Site</a></li>
                <li><a href="about.html">Sobre</a></li>
                <li><a href="contact.html">Contacto</a></li>
                <li><a href="client/login.php" class="btn btn-secondary"><i class="fas fa-user-circle"></i> Área Cliente</a></li>
                <li><a href="orcamento.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Orçamento</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <div class="nav-overlay" id="navOverlay"></div>

    <section class="breadcrumb">
        <div class="container">
            <?php if ($service): ?>
                <div style="font-size: 3rem; margin-bottom: 16px; color: var(--secondary);">
                    <i class="fas <?= htmlspecialchars($service['icon']) ?>"></i>
                </div>
                <h1><?= htmlspecialchars($service['name']) ?></h1>
                <p><?= htmlspecialchars($service['short_description']) ?></p>
            <?php else: ?>
                <h1>Serviço <span>Não Encontrado</span></h1>
                <p>O serviço que procura não está disponível.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($service): ?>
        <section class="section">
            <div class="container">
                <div class="about-content">
                    <div class="about-text fade-in">
                        <h2>Sobre o Serviço</h2>
                        <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                        <?php if (!empty($features)): ?>
                            <ul class="service-features" style="margin-top: 24px;">
                                <?php foreach ($features as $f): ?>
                                    <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div style="margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap;">
                            <a href="orcamento.php?service=<?= urlencode($service['slug']) ?>" class="btn btn-accent">
                                <i class="fas fa-paper-plane"></i> Solicitar Orçamento
                            </a>
                            <a href="servicos.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Ver Todos os Serviços
                            </a>
                        </div>
                    </div>
                    <div class="about-image fade-in">
                        <?php if ($service['image']): ?>
                            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>" style="width: 100%; border-radius: var(--radius); border: 1px solid var(--card-border);">
                        <?php else: ?>
                            <div class="about-image-placeholder">
                                <i class="fas <?= htmlspecialchars($service['icon']) ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php
        $hasMonthly = !is_null($service['monthly_price']);
        $hasYearly = !is_null($service['yearly_price']);
        $hasOnetime = !is_null($service['onetime_price']);
        $hasAnyPrice = $hasMonthly || $hasYearly || $hasOnetime;
        ?>

        <?php if ($hasAnyPrice): ?>
            <section class="pricing-section" style="background: var(--primary);">
                <div class="container">
                    <div class="section-header fade-in">
                        <span class="section-tag">Planos e Preços</span>
                        <h2 class="section-title">Escolha o plano ideal para <span>si</span></h2>
                        <p class="section-subtitle">Preços em Kz (kwanzas). Planos para todas as necessidades</p>
                    </div>
                    <div class="pricing-grid" style="grid-template-columns: repeat(<?= ($hasMonthly ? 1 : 0) + ($hasYearly ? 1 : 0) + ($hasOnetime ? 1 : 0) ?>, 1fr); max-width: 900px; margin: 0 auto;">
                        <?php if ($hasMonthly): ?>
                            <div class="pricing-card glass fade-in">
                                <div class="pricing-name">Mensal</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['monthly_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">por mês</span>
                                </div>
                                <a href="orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=mensal" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Solicitar Orçamento</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($hasYearly): ?>
                            <div class="pricing-card glass fade-in<?= $hasMonthly && !$hasOnetime ? ' featured' : '' ?>">
                                <div class="pricing-name">Anual</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['yearly_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">por ano</span>
                                    <?php if ($hasMonthly): ?>
                                        <?php $monthlyVal = (float)$service['monthly_price']; $yearlyVal = (float)$service['yearly_price']; ?>
                                        <?php if ($monthlyVal > 0 && $yearlyVal < $monthlyVal * 12): ?>
                                            <div class="pricing-yearly">Economize <?= number_format($monthlyVal * 12 - $yearlyVal, 0, ',', ' ') ?> Kz/ano</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <a href="orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=anual" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Solicitar Orçamento</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($hasOnetime): ?>
                            <div class="pricing-card glass fade-in">
                                <div class="pricing-name">Único</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['onetime_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">pagamento único</span>
                                </div>
                                <a href="orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=unico" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Solicitar Orçamento</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($allServices)): ?>
            <section class="section">
                <div class="container">
                    <div class="section-header fade-in">
                        <span class="section-tag">Outros Serviços</span>
                        <h2 class="section-title">Veja também</h2>
                    </div>
                    <div class="services-grid">
                        <?php foreach ($allServices as $s): ?>
                            <div class="service-card glass fade-in">
                                <div class="service-icon"><i class="fas <?= htmlspecialchars($s['icon']) ?>"></i></div>
                                <h3><?= htmlspecialchars($s['name']) ?></h3>
                                <p><?= htmlspecialchars($s['short_description']) ?></p>
                                <a href="servico.php?slug=<?= urlencode($s['slug']) ?>" class="service-link">Saiba mais <i class="fas fa-arrow-right"></i></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <section class="section">
            <div class="container">
                <div style="text-align: center; padding: 60px 0;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--accent); margin-bottom: 24px;"></i>
                    <h2 style="margin-bottom: 16px;">Serviço Não Encontrado</h2>
                    <p style="color: var(--text-muted); margin-bottom: 32px;">O serviço que procura não está disponível ou foi removido.</p>
                    <a href="servicos.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar aos Serviços</a>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-wrapper">
                <div class="newsletter-text">
                    <h3>Fique por dentro das novidades</h3>
                    <p>Receba ofertas exclusivas e dicas para o seu negócio online.</p>
                </div>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="O seu melhor email" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Subscrever
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.html" class="nav-logo" style="font-size: 1.4rem;">
                        <span>ANGONUEVE</span>
                    </a>
                    <p>Soluções profissionais de web hosting, domínios e email corporativo.</p>
                </div>
                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <a href="index.html">Início</a>
                    <a href="servicos.php">Serviços</a>
                    <a href="modelos.php">Modelos</a>
                    <a href="criar-site.php">Criar Site</a>
                    <a href="about.html">Sobre Nós</a>
                    <a href="contact.html">Contacto</a>
                    <a href="privacidade.php">Política de Privacidade</a>
                    <a href="termos.php">Termos de Serviço</a>
                </div>
                <div class="footer-services">
                    <h4>Serviços</h4>
                    <?php
                    $footerServices = db()->fetchAll("SELECT slug, name FROM services_db WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");
                    foreach ($footerServices as $fs):
                    ?>
                        <a href="servico.php?slug=<?= urlencode($fs['slug']) ?>"><?= htmlspecialchars($fs['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="footer-contact">
                    <h4>Contacto</h4>
                    <p><i class="fas fa-phone-alt"></i> 935 603 163</p>
                    <p><i class="fas fa-envelope"></i> geral@angonueve.co</p>
                    <p><i class="fas fa-map-marker-alt"></i> Luanda, Angola</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 ANGONUEVE. Todos os direitos reservados.</p>
                <p>
                    <a href="privacidade.php">Política de Privacidade</a>
                    <span style="margin:0 12px;color:var(--text-muted);">|</span>
                    <a href="termos.php">Termos de Serviço</a>
                    <span style="margin:0 12px;color:var(--text-muted);">|</span>
                    Desenvolvido por SeeFast
                </p>
            </div>
        </div>
    </footer>

    
    <button class="scroll-top" id="scrollTop" aria-label="Voltar ao topo">
        <i class="fas fa-arrow-up"></i>
    </button>
<script src="js/script.js?v=2"></script>
    <div id="pageLoader" class="page-loader">
        <div class="loader-spinner">
            <i class="fas fa-circle-notch fa-spin"></i>
        </div>
    </div>
</main>
</body>
</html>
