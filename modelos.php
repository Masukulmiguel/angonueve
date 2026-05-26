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

        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;animation:fadeIn 0.2s}
        .modal-overlay.active{display:flex}
        .modal-box{background:var(--primary);border:1px solid var(--card-border);border-radius:var(--radius);padding:40px;max-width:460px;width:100%;position:relative;animation:slideUp 0.3s}
        .modal-close{position:absolute;top:16px;right:16px;width:36px;height:36px;border-radius:50%;border:1px solid var(--card-border);background:transparent;color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.3s;font-size:1.1rem}
        .modal-close:hover{background:var(--card-bg);border-color:var(--secondary);color:var(--secondary)}
        .modal-icon{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,rgba(0,212,255,0.1),rgba(0,153,204,0.05));border:1px solid rgba(0,212,255,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.4rem;color:var(--secondary)}
        .modal-box h2{text-align:center;font-size:1.3rem;margin-bottom:4px}
        .modal-box .modal-sub{text-align:center;font-size:0.88rem;color:var(--text-muted);margin-bottom:24px}
        .modal-box .form-group{margin-bottom:14px}
        .modal-box .form-group label{display:block;font-size:0.82rem;font-weight:500;color:var(--text-muted);margin-bottom:6px}
        .modal-box .form-group input{width:100%;padding:12px 14px;border-radius:var(--radius-sm);border:1px solid var(--card-border);background:var(--dark);color:var(--text);font-size:0.9rem;font-family:'Inter',sans-serif;transition:border-color 0.3s;outline:none}
        .modal-box .form-group input:focus{border-color:var(--secondary)}
        .modal-box .btn-submit{width:100%;padding:14px;border-radius:var(--radius-sm);border:none;background:var(--gradient-2);color:var(--primary);font-weight:600;font-size:0.95rem;cursor:pointer;transition:opacity 0.3s;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px}
        .modal-box .btn-submit:hover{opacity:0.9}
        .modal-box .btn-submit:disabled{opacity:0.5;cursor:not-allowed}
        .modal-success{text-align:center;display:none}
        .modal-success.active{display:block}
        .modal-success-icon{width:64px;height:64px;border-radius:50%;background:rgba(0,212,255,0.1);border:1px solid rgba(0,212,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.6rem;color:#00e676}
        .modal-success h2{font-size:1.3rem;margin-bottom:8px}
        .modal-success p{font-size:0.9rem;color:var(--text-muted);margin-bottom:20px;line-height:1.6}
        .modal-success .btn-whatsapp{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:var(--radius-sm);background:#25d366;color:white;font-weight:600;font-size:0.9rem;text-decoration:none;transition:opacity 0.3s;font-family:'Inter',sans-serif}
        .modal-success .btn-whatsapp:hover{opacity:0.9}
        .modal-error{text-align:center;color:#ff4444;font-size:0.85rem;margin-top:10px;display:none}

        @media(max-width:768px){
            .page-header{padding:100px 0 40px}
            .page-header h1{font-size:1.8rem}
            .template-grid{grid-template-columns:1fr;padding:40px 0}
            .template-card .preview{height:200px}
            .modal-box{padding:28px 20px}
        }
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
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
                <li><a href="criar-site.php">Criar Site</a></li>
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
                    <button class="btn btn-order" data-template="Business Pro" data-category="Empresarial" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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
                    <button class="btn btn-order" data-template="Creative Portfolio" data-category="Portfolio" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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
                    <button class="btn btn-order" data-template="SmartLead" data-category="Landing Page" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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
                    <button class="btn btn-order" data-template="Sabores &amp; Arte" data-category="Restaurante" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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
                    <button class="btn btn-order" data-template="Personal Blog" data-category="Blog" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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
                    <button class="btn btn-order" data-template="TechStore" data-category="E-commerce" data-service="criacao-sites" onclick="openOrderModal(this)"><i class="fas fa-shopping-cart"></i> Solicitar</button>
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

    <div class="modal-overlay" id="orderModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeOrderModal()"><i class="fas fa-times"></i></button>
            <div id="modalForm">
                <div class="modal-icon"><i class="fas fa-shopping-cart"></i></div>
                <h2>Solicitar Template</h2>
                <p class="modal-sub" id="modalTemplateInfo">Business Pro</p>
                <input type="hidden" id="modalTemplateName">
                <input type="hidden" id="modalServiceId">
                <div class="form-group">
                    <label for="modalName">Nome completo</label>
                    <input type="text" id="modalName" placeholder="O seu nome" required>
                </div>
                <div class="form-group">
                    <label for="modalEmail">Email</label>
                    <input type="email" id="modalEmail" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label for="modalPhone">Telefone</label>
                    <input type="tel" id="modalPhone" placeholder="+244 900 000 000" required>
                </div>
                <button class="btn-submit" id="modalSubmitBtn" onclick="submitOrder()"><i class="fas fa-paper-plane"></i> Solicitar</button>
                <div class="modal-error" id="modalError"></div>
            </div>
            <div class="modal-success" id="modalSuccess">
                <div class="modal-success-icon"><i class="fas fa-check-circle"></i></div>
                <h2>Pedido Recebido!</h2>
                <p>Recebemos o seu pedido do template. Entraremos em contacto consigo em breve pelo WhatsApp para confirmar os detalhes.</p>
                <a href="#" id="modalWhatsAppLink" target="_blank" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> Falar pelo WhatsApp</a>
                <button class="btn-submit" style="margin-top:12px;background:var(--card-bg);color:var(--text);border:1px solid var(--card-border)" onclick="closeOrderModal()">Fechar</button>
            </div>
        </div>
    </div>

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
                    <a href="criar-site.php">Criar Site</a>
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
    <script>
    function openOrderModal(btn) {
        const name = btn.getAttribute('data-template');
        const service = btn.getAttribute('data-service');
        document.getElementById('modalTemplateName').value = name;
        document.getElementById('modalServiceId').value = service;
        document.getElementById('modalTemplateInfo').textContent = name;
        document.getElementById('modalForm').style.display = '';
        document.getElementById('modalSuccess').classList.remove('active');
        document.getElementById('modalError').style.display = 'none';
        document.getElementById('modalSubmitBtn').disabled = false;
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Solicitar';
        document.getElementById('modalName').value = '';
        document.getElementById('modalEmail').value = '';
        document.getElementById('modalPhone').value = '';
        document.getElementById('orderModal').classList.add('active');
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.remove('active');
    }

    document.getElementById('orderModal').addEventListener('click', function(e) {
        if (e.target === this) closeOrderModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeOrderModal();
    });

    function submitOrder() {
        const name = document.getElementById('modalName').value.trim();
        const email = document.getElementById('modalEmail').value.trim();
        const phone = document.getElementById('modalPhone').value.trim();
        const template = document.getElementById('modalTemplateName').value;
        const service = document.getElementById('modalServiceId').value;
        const errorEl = document.getElementById('modalError');

        if (!name || !email || !phone) {
            errorEl.textContent = 'Preencha todos os campos.';
            errorEl.style.display = 'block';
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errorEl.textContent = 'Insira um email válido.';
            errorEl.style.display = 'block';
            return;
        }

        errorEl.style.display = 'none';
        const btn = document.getElementById('modalSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A enviar...';

        fetch('api/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_name: name,
                customer_email: email,
                customer_phone: phone,
                service_id: service,
                plan_name: 'Template: ' + template,
                payment_type: 'onetime',
                price_monthly: 0
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalForm').style.display = 'none';
                document.getElementById('modalSuccess').classList.add('active');
                if (data.whatsapp_url) {
                    document.getElementById('modalWhatsAppLink').href = data.whatsapp_url;
                }
            } else {
                errorEl.textContent = data.error || 'Erro ao enviar. Tente novamente.';
                errorEl.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Solicitar';
            }
        })
        .catch(() => {
            errorEl.textContent = 'Erro de conexão. Tente novamente.';
            errorEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Solicitar';
        });
    }
    </script>
    <div id="pageLoader" class="page-loader"><div class="loader-spinner"><i class="fas fa-circle-notch fa-spin"></i></div></div>
    </main>
</body>
</html>
