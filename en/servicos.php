<?php
require_once __DIR__ . '/../includes/auth.php';

$services = db()->fetchAll("SELECT * FROM services_db WHERE is_active = 1 ORDER BY sort_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore ANGONUEVE services: SSD NVMe web hosting, .com and .co.ao domain registration, professional corporate email, and custom website creation in Angola.">
    <meta property="og:title" content="Services - ANGONUEVE">
    <meta property="og:description" content="Professional hosting, domains, corporate email and website creation solutions in Angola.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://angonueve.com/en/servicos.php">
    <title>Services - ANGONUEVE</title>
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
                <li><a href="../servicos.php" class="lang-switch" title="Versão Portuguesa">PT</a></li>
                <li><a href="index.html">Home</a></li>
                <li><a href="services.html" class="active">Services</a></li>
                <li><a href="criar-site.php">Create Site</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="../client/login.php" class="btn btn-secondary"><i class="fas fa-user-circle"></i> Client Area</a></li>
                <li><a href="../orcamento.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Quote</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <div class="nav-overlay" id="navOverlay"></div>

    <section class="breadcrumb">
        <div class="container">
            <h1>Our <span>Services</span></h1>
            <p>Professional web, domains, and corporate email solutions</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">What we do</span>
                <h2 class="section-title">All <span>Services</span></h2>
                <p class="section-subtitle">From hosting to corporate email, we have the ideal solution for you</p>
            </div>

            <?php if (empty($services)): ?>
                <div style="text-align: center; padding: 60px 0;">
                    <i class="fas fa-info-circle" style="font-size: 3rem; color: var(--secondary); margin-bottom: 16px;"></i>
                    <p style="font-size: 1.1rem; color: var(--text-muted);">No services available at the moment.</p>
                </div>
            <?php else: ?>
                <div class="services-page-grid">
                    <?php foreach ($services as $s): ?>
                        <div class="service-detail-card glass fade-in">
                            <div class="service-detail-icon"><i class="fas <?= htmlspecialchars($s['icon']) ?>"></i></div>
                            <h3><?= htmlspecialchars($s['name']) ?></h3>
                            <p><?= htmlspecialchars($s['short_description']) ?></p>
                            <div style="margin: 16px 0; padding: 12px; background: rgba(0,212,255,0.05); border-radius: var(--radius); border: 1px solid rgba(0,212,255,0.1);">
                                <?php if (!is_null($s['monthly_price'])): ?>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--secondary);">From Kz <?= number_format((float)$s['monthly_price'], 0, ',', ' ') ?>/month</div>
                                <?php endif; ?>
                                <?php if (!is_null($s['yearly_price'])): ?>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">Kz <?= number_format((float)$s['yearly_price'], 0, ',', ' ') ?>/year</div>
                                <?php endif; ?>
                                <?php if (!is_null($s['onetime_price'])): ?>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--accent);">Kz <?= number_format((float)$s['onetime_price'], 0, ',', ' ') ?> <span style="font-size: 0.75rem; font-weight: 400;">(one-time payment)</span></div>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: auto;">
                                <a href="../servico.php?slug=<?= urlencode($s['slug']) ?>" class="service-link">Learn more <i class="fas fa-arrow-right"></i></a>
                                <a href="../orcamento.php?service=<?= urlencode($s['slug']) ?>" class="btn btn-primary" style="font-size: 0.85rem; padding: 10px 20px; justify-content: center;"><i class="fas fa-paper-plane"></i> Request Quote</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section" style="background: var(--primary);">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Quote</span>
                <h2 class="section-title">Request Your <span>Quote</span></h2>
                <p class="section-subtitle">Request a personalized, no-obligation quote</p>
            </div>
            <div style="text-align: center;">
                <a href="../orcamento.php" class="btn btn-accent" style="font-size: 1.1rem; padding: 18px 48px;">
                    <i class="fas fa-paper-plane"></i> Request Quote
                </a>
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-wrapper">
                <div class="newsletter-text">
                    <h3>Stay up to date with the latest news</h3>
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
                    <p>Professional web hosting, domains, and corporate email solutions.</p>
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
                    <?php foreach ($services as $s): ?>
                        <a href="../servico.php?slug=<?= urlencode($s['slug']) ?>"><?= htmlspecialchars($s['name']) ?></a>
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
