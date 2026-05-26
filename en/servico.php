<?php
require_once __DIR__ . '/../includes/auth.php';

$slug = sanitize($_GET['slug'] ?? '');
$service = db()->fetchOne("SELECT * FROM services_db WHERE slug = ? AND is_active = 1", [$slug]);

if (!$service) {
    $pageTitle = 'Service Not Found - ANGONUEVE';
    $pageDescription = 'The service you are looking for was not found.';
} else {
    $features = json_decode($service['features'], true) ?? [];
    $pageTitle = htmlspecialchars($service['name']) . ' - ANGONUEVE';
    $pageDescription = htmlspecialchars($service['short_description']);
}

$allServices = db()->fetchAll("SELECT * FROM services_db WHERE is_active = 1 AND slug != ? ORDER BY sort_order ASC LIMIT 4", [$slug]);
?>
<!DOCTYPE html>
<html lang="en">
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <main id="main-content">
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.html" class="nav-logo">
                <img src="../images/logo.png" alt="ANGONUEVE">
                <span>ANGONUEVE</span>
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="../servico.php" class="lang-switch" title="Versão Portuguesa">PT</a></li>
                <li><a href="index.html">Home</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="criar-site.php">Create Site</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="../client/login.php" class="btn btn-secondary"><i class="fas fa-user-circle"></i> Client Area</a></li>
                <li><a href="../orcamento.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Quote</a></li>
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
                <h1>Service <span>Not Found</span></h1>
                <p>The service you are looking for is not available.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($service): ?>
        <section class="section">
            <div class="container">
                <div class="about-content">
                    <div class="about-text fade-in">
                        <h2>About the Service</h2>
                        <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                        <?php if (!empty($features)): ?>
                            <ul class="service-features" style="margin-top: 24px;">
                                <?php foreach ($features as $f): ?>
                                    <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div style="margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap;">
                            <a href="../orcamento.php?service=<?= urlencode($service['slug']) ?>" class="btn btn-accent">
                                <i class="fas fa-paper-plane"></i> Request Quote
                            </a>
                            <a href="services.html" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> View All Services
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
                        <span class="section-tag">Plans &amp; Pricing</span>
                        <h2 class="section-title">Choose the ideal plan for <span>you</span></h2>
                        <p class="section-subtitle">Prices in Kz (kwanzas). Plans for all needs</p>
                    </div>
                    <div class="pricing-grid" style="grid-template-columns: repeat(<?= ($hasMonthly ? 1 : 0) + ($hasYearly ? 1 : 0) + ($hasOnetime ? 1 : 0) ?>, 1fr); max-width: 900px; margin: 0 auto;">
                        <?php if ($hasMonthly): ?>
                            <div class="pricing-card glass fade-in">
                                <div class="pricing-name">Monthly</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['monthly_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">per month</span>
                                </div>
                                <a href="../orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=monthly" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Request Quote</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($hasYearly): ?>
                            <div class="pricing-card glass fade-in<?= $hasMonthly && !$hasOnetime ? ' featured' : '' ?>">
                                <div class="pricing-name">Yearly</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['yearly_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">per year</span>
                                    <?php if ($hasMonthly): ?>
                                        <?php $monthlyVal = (float)$service['monthly_price']; $yearlyVal = (float)$service['yearly_price']; ?>
                                        <?php if ($monthlyVal > 0 && $yearlyVal < $monthlyVal * 12): ?>
                                            <div class="pricing-yearly">Save <?= number_format($monthlyVal * 12 - $yearlyVal, 0, ',', ' ') ?> Kz/year</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <a href="../orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=yearly" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Request Quote</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($hasOnetime): ?>
                            <div class="pricing-card glass fade-in">
                                <div class="pricing-name">One-time</div>
                                <div class="pricing-price">
                                    <div class="pricing-monthly">Kz <?= number_format((float)$service['onetime_price'], 0, ',', ' ') ?></div>
                                    <span class="pricing-period">one-time payment</span>
                                </div>
                                <a href="../orcamento.php?service=<?= urlencode($service['slug']) ?>&plan=onetime" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Request Quote</a>
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
                        <span class="section-tag">Other Services</span>
                        <h2 class="section-title">See Also</h2>
                    </div>
                    <div class="services-grid">
                        <?php foreach ($allServices as $s): ?>
                            <div class="service-card glass fade-in">
                                <div class="service-icon"><i class="fas <?= htmlspecialchars($s['icon']) ?>"></i></div>
                                <h3><?= htmlspecialchars($s['name']) ?></h3>
                                <p><?= htmlspecialchars($s['short_description']) ?></p>
                                <a href="servico.php?slug=<?= urlencode($s['slug']) ?>" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
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
                    <h2 style="margin-bottom: 16px;">Service Not Found</h2>
                    <p style="color: var(--text-muted); margin-bottom: 32px;">The service you are looking for is not available or has been removed.</p>
                    <a href="services.html" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Services</a>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-wrapper">
                <div class="newsletter-text">
                    <h3>Stay up to date</h3>
                    <p>Get exclusive offers and tips for your online business.</p>
                </div>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="Your best email" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Subscribe
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
                    <p>Professional web hosting, domains and corporate email solutions.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="index.html">Home</a>
                    <a href="services.html">Services</a>
                    <a href="criar-site.php">Create Site</a>
                    <a href="about.html">About Us</a>
                    <a href="contact.html">Contact</a>
                    <a href="../privacidade.php">Privacy Policy</a>
                    <a href="../termos.php">Terms of Service</a>
                </div>
                <div class="footer-services">
                    <h4>Services</h4>
                    <?php
                    $footerServices = db()->fetchAll("SELECT slug, name FROM services_db WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");
                    foreach ($footerServices as $fs):
                    ?>
                        <a href="servico.php?slug=<?= urlencode($fs['slug']) ?>"><?= htmlspecialchars($fs['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="footer-contact">
                    <h4>Contact</h4>
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
                <p>&copy; 2026 ANGONUEVE. All rights reserved.</p>
                <p>
                    <a href="../privacidade.php">Privacy Policy</a>
                    <span style="margin:0 12px;color:var(--text-muted);">|</span>
                    <a href="../termos.php">Terms of Service</a>
                    <span style="margin:0 12px;color:var(--text-muted);">|</span>
                    Made with <i class="fas fa-heart" style="color: var(--accent);"></i> in Angola
                </p>
            </div>
        </div>
    </footer>

    
    <button class="scroll-top" id="scrollTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
<script src="../js/script.js"></script>
    <div id="pageLoader" class="page-loader">
        <div class="loader-spinner">
            <i class="fas fa-circle-notch fa-spin"></i>
        </div>
    </div>
</main>
</body>
</html>
