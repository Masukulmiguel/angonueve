<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore os modelos de sites profissionais da ANGONUEVE. Templates modernos e responsivos para o seu negócio em Angola.">
    <title>Modelos de Sites - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        .page-header{padding:120px 0 60px;text-align:center;background:radial-gradient(ellipse at top,rgba(0,212,255,0.06),transparent 70%)}
        .page-header h1{font-size:2.5rem;margin-bottom:12px}
        .page-header p{color:var(--text-muted);max-width:600px;margin:0 auto;font-size:1.05rem}
        .template-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:28px;padding:60px 0}
        .template-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:var(--radius);overflow:hidden;transition:all 0.3s;display:flex;flex-direction:column}
        .template-card:hover{transform:translateY(-6px);border-color:var(--secondary);box-shadow:0 12px 40px rgba(0,0,0,0.3)}
        .template-card .preview{height:240px;display:flex;align-items:center;justify-content:center;font-size:3.5rem;background:linear-gradient(135deg,#0d1f3c,#162d50);position:relative;overflow:hidden}
        .template-card .preview .overlay{position:absolute;inset:0;background:rgba(10,22,40,0.7);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.3s}
        .template-card:hover .preview .overlay{opacity:1}
        .template-card .preview .overlay a{padding:12px 24px;border-radius:50px;background:var(--secondary);color:var(--primary);font-weight:600;text-decoration:none;font-size:0.9rem;transition:transform 0.3s}
        .template-card .preview .overlay a:hover{transform:scale(1.05)}
        .template-card .body{padding:24px;flex:1;display:flex;flex-direction:column}
        .template-card .body .cat{display:inline-block;padding:4px 12px;border-radius:50px;background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.15);color:var(--secondary);font-size:0.72rem;font-weight:600;margin-bottom:10px;width:fit-content;text-transform:uppercase;letter-spacing:0.5px}
        .template-card .body h3{font-size:1.15rem;margin-bottom:8px}
        .template-card .body p{font-size:0.88rem;color:var(--text-muted);margin-bottom:16px;flex:1;line-height:1.6}
        .template-card .body .features{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px}
        .template-card .body .features span{font-size:0.75rem;padding:4px 10px;border-radius:50px;background:rgba(255,255,255,0.03);border:1px solid var(--card-border);color:var(--text-muted)}
        .template-card .body .actions{display:flex;gap:10px}
        .template-card .body .actions .btn{flex:1;text-align:center;padding:10px;border-radius:8px;font-weight:600;font-size:0.85rem;cursor:pointer;border:none;text-decoration:none;transition:all 0.3s}
        .template-card .body .actions .btn-preview{background:rgba(255,255,255,0.05);border:1px solid var(--card-border);color:var(--text)}
        .template-card .body .actions .btn-preview:hover{background:rgba(255,255,255,0.1)}
        .template-card .body .actions .btn-order{background:var(--gradient-2);color:var(--primary)}
        .template-card .body .actions .btn-order:hover{opacity:0.9}
        .cta-section{text-align:center;padding:80px 0;background:radial-gradient(ellipse at center,rgba(0,212,255,0.04),transparent 60%)}
        .cta-section h2{font-size:2rem;margin-bottom:12px}
        .cta-section p{color:var(--text-muted);margin-bottom:28px;max-width:500px;margin-left:auto;margin-right:auto}
        @media(max-width:768px){
            .page-header{padding:100px 0 40px}
            .page-header h1{font-size:1.8rem}
            .template-grid{grid-template-columns:1fr;padding:40px 0}
            .template-card .preview{height:200px}
        }
    </style>
</head>
<body>
    <a href="#main" class="skip-link">Ir para o conteúdo principal</a>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.html" class="nav-logo">
                <img src="images/logo.png" alt="ANGONUEVE">
                <span>ANGONUEVE</span>
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="en/index.html" class="lang-switch" title="English version">EN</a></li>
                <li><a href="index.html">Início</a></li>
                <li><a href="servicos.php">Serviços</a></li>
                <li><a href="modelos.php" class="active">Modelos</a></li>
                <li><a href="about.html">Sobre</a></li>
                <li><a href="contact.html">Contacto</a></li>
                <li><a href="client/login.php" class="btn btn-secondary"><i class="fas fa-user-circle"></i> Área Cliente</a></li>
                <li><a href="orcamento.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Orçamento</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
    <div class="nav-overlay" id="navOverlay"></div>

    <main id="main">
    <section class="page-header">
        <div class="container">
            <h1>Modelos de Sites</h1>
            <p>Escolha o template ideal para o seu negócio e peça o seu site profissional à ANGONUEVE.</p>
        </div>
    </section>

    <section class="container template-grid">
        <div class="template-card">
            <div class="preview"><i class="fas fa-building" style="color:var(--secondary)"></i><div class="overlay"><a href="templates/business-pro.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">Empresarial</span>
                <h3>Business Pro</h3>
                <p>Template profissional para empresas e negócios. Inclui secções de serviços, equipa, depoimentos e contacto.</p>
                <div class="features"><span>Responsivo</span><span>SEO</span><span>5 Secções</span><span>Contacto</span></div>
                <div class="actions">
                    <a href="templates/business-pro.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=business-pro" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>

        <div class="template-card">
            <div class="preview"><i class="fas fa-paint-brush" style="color:var(--accent)"></i><div class="overlay"><a href="templates/creative-portfolio.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">Portfólio</span>
                <h3>Creative Portfolio</h3>
                <p>Template ideal para designers, fotógrafos e freelancers. Mostre o seu trabalho com estilo.</p>
                <div class="features"><span>Galeria</span><span>Filtros</span><span>Depoimentos</span><span>Skills</span></div>
                <div class="actions">
                    <a href="templates/creative-portfolio.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=creative-portfolio" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>

        <div class="template-card">
            <div class="preview"><i class="fas fa-rocket" style="color:#00e676"></i><div class="overlay"><a href="templates/landing-page.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">Landing Page</span>
                <h3>SmartLead</h3>
                <p>Página de conversão focada em vendas e geração de leads. Perfeita para campanhas de marketing.</p>
                <div class="features"><span>Conversão</span><span>Planos</span><span>CTA</span><span>Formulário</span></div>
                <div class="actions">
                    <a href="templates/landing-page.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=landing-page" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>

        <div class="template-card">
            <div class="preview"><i class="fas fa-utensils" style="color:#d4a574"></i><div class="overlay"><a href="templates/restaurante.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">Restaurante</span>
                <h3>Sabores & Arte</h3>
                <p>Template elegante para restaurantes, cafés e bares. Inclui menu digital, galeria e reservas.</p>
                <div class="features"><span>Menu Digital</span><span>Galeria</span><span>Reservas</span><span>Localização</span></div>
                <div class="actions">
                    <a href="templates/restaurante.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=restaurante" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>

        <div class="template-card">
            <div class="preview"><i class="fas fa-pen-fancy" style="color:#818cf8"></i><div class="overlay"><a href="templates/personal-blog.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">Blog</span>
                <h3>Personal Blog</h3>
                <p>Template moderno para bloggers e criadores de conteúdo. Design limpo e foco na leitura.</p>
                <div class="features"><span>Sidebar</span><span>Tags</span><span>Social</span><span>Paginação</span></div>
                <div class="actions">
                    <a href="templates/personal-blog.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=personal-blog" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>

        <div class="template-card">
            <div class="preview"><i class="fas fa-shopping-cart" style="color:var(--secondary)"></i><div class="overlay"><a href="templates/ecommerce.html" target="_blank"><i class="fas fa-eye"></i> Pré-visualizar</a></div></div>
            <div class="body">
                <span class="cat">E-commerce</span>
                <h3>TechStore</h3>
                <p>Template de loja virtual com grid de produtos, carrinho e categorias. Pronto para vender online.</p>
                <div class="features"><span>Carrinho</span><span>Produtos</span><span>Categorias</span><span>Busca</span></div>
                <div class="actions">
                    <a href="templates/ecommerce.html" target="_blank" class="btn btn-preview"><i class="fas fa-eye"></i> Ver</a>
                    <a href="orcamento.php?service=criacao-sites&template=ecommerce" class="btn btn-order"><i class="fas fa-shopping-cart"></i> Solicitar</a>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Não encontrou o que procura?</h2>
            <p>Podemos criar um site personalizado do zero, adaptado 100% às necessidades do seu negócio.</p>
            <a href="orcamento.php" class="btn btn-primary" style="padding:16px 40px;font-size:1rem;"><i class="fas fa-paper-plane"></i> Pedir Orçamento Personalizado</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.html" class="nav-logo" style="font-size: 1.4rem;"><span>ANGONUEVE</span></a>
                    <p>Soluções profissionais de hospedagem de sites, registo de domínios, email corporativo e criação de sites em Angola.</p>
                </div>
                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <a href="index.html">Início</a>
                    <a href="servicos.php">Serviços</a>
                    <a href="modelos.php">Modelos</a>
                    <a href="about.html">Sobre Nós</a>
                    <a href="contact.html">Contacto</a>
                    <a href="privacidade.php">Política de Privacidade</a>
                    <a href="termos.php">Termos de Serviço</a>
                    <a href="client/login.php"><i class="fas fa-user-circle"></i> Área Cliente</a>
                </div>
                <div class="footer-services">
                    <h4>Serviços</h4>
                    <a href="servico.php?slug=hospedagem">Hospedagem de Sites</a>
                    <a href="servico.php?slug=dominios">Registo de Domínios</a>
                    <a href="servico.php?slug=email-corporativo">Email Corporativo</a>
                    <a href="servico.php?slug=criacao-sites">Criação de Sites</a>
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
                <p><a href="privacidade.php">Política de Privacidade</a><span style="margin:0 12px;color:var(--text-muted);">|</span><a href="termos.php">Termos de Serviço</a><span style="margin:0 12px;color:var(--text-muted);">|</span> Feito com <i class="fas fa-heart" style="color: var(--accent);"></i> em Angola</p>
            </div>
        </div>
    </footer>

    <button class="scroll-top" id="scrollTop" aria-label="Voltar ao topo"><i class="fas fa-arrow-up"></i></button>
    <script src="js/script.js"></script>
    <div id="pageLoader" class="page-loader"><div class="loader-spinner"><i class="fas fa-circle-notch fa-spin"></i></div></div>
    </main>
</body>
</html>
