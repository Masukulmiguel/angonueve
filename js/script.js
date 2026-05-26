const servicesData = {
    hospedagem: {
        title: 'Hospedagem de Sites',
        subtitle: 'Planos robustos e escaláveis para o seu site ou aplicação',
        icon: 'fa-globe',
        description: `
            <p>A <strong>ANGONUEVE</strong> oferece planos de hospedagem de sites de alta performance para todos os tipos de projectos. Desde pequenos sites institucionais a grandes portais e aplicações web, temos a solução ideal para si.</p>
            <p>Nossa infraestrutura conta com servidores de última geração, armazenamento SSD NVMe e largura de banda dedicada para garantir que o seu site esteja sempre online e rápido.</p>
            <p>Todos os planos incluem painel de controlo intuitivo, instalador automático de CMS (WordPress, Joomla, etc.), certificado SSL grátis e suporte técnico 24 horas por dia, 7 dias por semana.</p>
        `,
        features: [
            'Hospedagem partilhada, VPS e dedicada',
            'SSD NVMe de alta velocidade',
            'SSL Grátis incluído',
            'Suporte 24/7',
            'Backup diário automático',
            'Painel de controlo cPanel/WHM',
            'Instalador automático de CMS',
            'Largura de banda ilimitada'
        ],
        pricing: [
            {
                name: 'Básico',
                monthly: 5000,
                storage: '10 GB SSD',
                sites: '1 Site',
                emails: '1 Email',
                databases: '1 BD',
                dominio: '1 Domínio',
                bandwidth: '50 GB',
                featured: false,
                badge: ''
            },
            {
                name: 'Standard',
                monthly: 10000,
                storage: '20 GB SSD',
                sites: '3 Sites',
                emails: '3 Emails',
                databases: '3 BD',
                dominio: '1 Domínio',
                bandwidth: '100 GB',
                featured: true,
                badge: 'Mais Popular'
            },
            {
                name: 'Profissional',
                monthly: 15000,
                storage: '50 GB SSD',
                sites: '5 Sites',
                emails: '5 Emails',
                databases: '5 BD',
                dominio: '1 Domínio',
                bandwidth: '200 GB',
                featured: false,
                badge: ''
            },
            {
                name: 'Empresarial',
                monthly: 25000,
                storage: '100 GB SSD',
                sites: 'Ilimitados',
                emails: 'Ilimitados',
                databases: 'Ilimitadas',
                dominio: '1 Domínio',
                bandwidth: '500 GB',
                featured: false,
                badge: ''
            }
        ]
    },
    dominios: {
        title: 'Registo de Domínios',
        subtitle: 'O domínio perfeito para o seu projecto',
        icon: 'fa-tag',
        description: `
            <p>Registe o nome perfeito para o seu negócio ou projecto com a <strong>ANGONUEVE</strong>. Trabalhamos com as principais extensões de domínio aos melhores preços do mercado.</p>
            <p>Oferecemos gestão completa de DNS, proteção de privacidade WHOIS, redirecionamento de domínios e muito mais. Todo o processo é simples e rápido.</p>
            <p>Além do registo, também oferecemos transferência de domínios, renovação automática e suporte dedicado para qualquer questão relacionada ao seu domínio.</p>
        `,
        features: [
            'Domínios .com, .ao, .co.ao',
            'Preços competitivos',
            'Gestão intuitiva de DNS',
            'Proteção WHOIS',
            'Transferência gratuita',
            'Renovação automática',
            'Redirecionamento de domínios',
            'Suporte especializado'
        ],
        pricing: [
            { name: '.com', monthly: 5000, period: '/ano', features_list: ['Registo 1 ano', 'DNS incluído', 'Proteção WHOIS'], featured: false },
            { name: '.co.ao', monthly: 7000, period: '/ano', features_list: ['Registo 1 ano', 'DNS incluído', 'Proteção WHOIS'], featured: true, badge: 'Nacional' },
            { name: '.ao', monthly: 10000, period: '/ano', features_list: ['Registo 1 ano', 'DNS incluído', 'Proteção WHOIS'], featured: false },
            { name: '.net', monthly: 5500, period: '/ano', features_list: ['Registo 1 ano', 'DNS incluído', 'Proteção WHOIS'], featured: false },
            { name: '.org', monthly: 5500, period: '/ano', features_list: ['Registo 1 ano', 'DNS incluído', 'Proteção WHOIS'], featured: false }
        ]
    },
    'email-corporativo': {
        title: 'Email Corporativo',
        subtitle: 'Email profissional com o seu próprio domínio',
        icon: 'fa-envelope',
        description: `
            <p>Comunique-se com profissionalismo utilizando o <strong>ANGONUEVE</strong> Email Corporativo. Tenha contas de email com o seu próprio domínio (seu@empresa.com) e transmita credibilidade aos seus clientes e parceiros.</p>
            <p>Oferecemos webmail de fácil acesso, configuração em qualquer cliente de email (Outlook, Thunderbird, mobile), calendário partilhado e contactos sincronizados.</p>
            <p>Todos os planos incluem anti-spam, antivírus, suporte técnico dedicado e garantia de disponibilidade para que nunca perca uma mensagem importante.</p>
        `,
        features: [
            'Email com o seu domínio',
            'Webmail intuitivo',
            'Configuração IMAP/POP3',
            'Anti-spam e antivírus',
            'Calendário e contactos',
            'Acesso móvel',
            'Suporte técnico',
            'Alta disponibilidade'
        ],
        pricing: [
            { name: 'Básico', monthly: 2000, period: '/mês', features_list: ['1 Conta de email', '1 GB armazenamento', 'Webmail', 'Anti-spam'], featured: false },
            { name: 'Standard', monthly: 5000, period: '/mês', features_list: ['3 Contas de email', '5 GB armazenamento', 'Webmail + Calendário', 'Suporte prioritário'], featured: true, badge: 'Popular' },
            { name: 'Profissional', monthly: 10000, period: '/mês', features_list: ['10 Contas de email', '20 GB armazenamento', 'Calendário partilhado', 'Suporte 24/7'], featured: false },
            { name: 'Empresarial', monthly: 20000, period: '/mês', features_list: ['Contas ilimitadas', 'Armazenamento ilimitado', 'Recursos avançados', 'Suporte dedicado'], featured: false }
        ]
    },
    'criacao-sites': {
        title: 'Criação de Sites Profissionais',
        subtitle: 'Sites personalizados para o seu negócio',
        icon: 'fa-code',
        description: `
            <p>A <strong>ANGONUEVE</strong> cria sites profissionais sob medida para o seu negócio. Desde landing pages a portais completos, a nossa equipa de desenvolvimento transforma a sua visão em realidade digital.</p>
            <p>Todos os sites são responsivos (adaptam-se a qualquer dispositivo), optimizados para SEO (Google), com desempenho rápido e design moderno que reflecte a identidade da sua marca.</p>
            <p>Incluímos integração com redes sociais, formulários de contacto, mapa interactivo, galeria de imagens e tudo o que precisa para ter uma presença online de sucesso.</p>
        `,
        features: [
            'Design responsivo',
            'Otimizado para SEO',
            'Alta performance',
            'Painel de gestão',
            'Integração redes sociais',
            'Formulários interactivos',
            'Hospedagem incluída (1 ano)',
            'Suporte e manutenção'
        ],
        pricing: [
            { name: 'Landing Page', monthly: 75000, period: '/projeto', features_list: ['1 Página', 'Design responsivo', 'Formulário contacto', 'SEO básico'], featured: false },
            { name: 'Institucional', monthly: 150000, period: '/projeto', features_list: ['Até 5 páginas', 'Design responsivo', 'Galeria de imagens', 'SEO completo'], featured: true, badge: 'Recomendado' },
            { name: 'Profissional', monthly: 300000, period: '/projeto', features_list: ['Até 10 páginas', 'Blog integrado', 'Painel administrativo', 'E-commerce básico'], featured: false },
            { name: 'Portal', monthly: 600000, period: '/projeto', features_list: ['Páginas ilimitadas', 'Sistema completo', 'Área de membros', 'Suporte prioritário'], featured: false }
        ]
    },
};

const servicesDataEn = {
    hospedagem: {
        title: 'Web Hosting',
        subtitle: 'Robust and scalable plans for your website or application',
        icon: 'fa-globe',
        description: `
            <p><strong>ANGONUEVE</strong> offers high-performance web hosting plans for all types of projects. From small business websites to large portals and web applications, we have the ideal solution for you.</p>
            <p>Our infrastructure features state-of-the-art servers, SSD NVMe storage and dedicated bandwidth to ensure your website is always online and fast.</p>
            <p>All plans include an intuitive control panel, automatic CMS installer (WordPress, Joomla, etc.), free SSL certificate, and 24/7 technical support.</p>
        `,
        features: [
            'Shared, VPS and Dedicated hosting',
            'High-speed SSD NVMe',
            'Free SSL included',
            '24/7 Support',
            'Daily automatic backup',
            'cPanel/WHM control panel',
            'Automatic CMS installer',
            'Unlimited bandwidth'
        ],
        pricing: [
            {
                name: 'Basic',
                monthly: 5000,
                storage: '10 GB SSD',
                sites: '1 Site',
                emails: '1 Email',
                databases: '1 DB',
                dominio: '1 Domain',
                bandwidth: '50 GB',
                featured: false,
                badge: ''
            },
            {
                name: 'Standard',
                monthly: 10000,
                storage: '20 GB SSD',
                sites: '3 Sites',
                emails: '3 Emails',
                databases: '3 DB',
                dominio: '1 Domain',
                bandwidth: '100 GB',
                featured: true,
                badge: 'Most Popular'
            },
            {
                name: 'Professional',
                monthly: 15000,
                storage: '50 GB SSD',
                sites: '5 Sites',
                emails: '5 Emails',
                databases: '5 DB',
                dominio: '1 Domain',
                bandwidth: '200 GB',
                featured: false,
                badge: ''
            },
            {
                name: 'Enterprise',
                monthly: 25000,
                storage: '100 GB SSD',
                sites: 'Unlimited',
                emails: 'Unlimited',
                databases: 'Unlimited',
                dominio: '1 Domain',
                bandwidth: '500 GB',
                featured: false,
                badge: ''
            }
        ]
    },
    dominios: {
        title: 'Domain Registration',
        subtitle: 'The perfect domain for your project',
        icon: 'fa-tag',
        description: `
            <p>Register the perfect name for your business or project with <strong>ANGONUEVE</strong>. We work with the main domain extensions at the best market prices.</p>
            <p>We offer complete DNS management, WHOIS privacy protection, domain redirection and much more. The entire process is simple and fast.</p>
            <p>In addition to registration, we also offer domain transfer, automatic renewal and dedicated support for any domain-related questions.</p>
        `,
        features: [
            'Domains .com, .ao, .co.ao',
            'Competitive prices',
            'Intuitive DNS management',
            'WHOIS protection',
            'Free transfer',
            'Automatic renewal',
            'Domain redirection',
            'Specialized support'
        ],
        pricing: [
            { name: '.com', monthly: 5000, period: '/year', features_list: ['1 year registration', 'DNS included', 'WHOIS protection'], featured: false },
            { name: '.co.ao', monthly: 7000, period: '/year', features_list: ['1 year registration', 'DNS included', 'WHOIS protection'], featured: true, badge: 'National' },
            { name: '.ao', monthly: 10000, period: '/year', features_list: ['1 year registration', 'DNS included', 'WHOIS protection'], featured: false },
            { name: '.net', monthly: 5500, period: '/year', features_list: ['1 year registration', 'DNS included', 'WHOIS protection'], featured: false },
            { name: '.org', monthly: 5500, period: '/year', features_list: ['1 year registration', 'DNS included', 'WHOIS protection'], featured: false }
        ]
    },
    'email-corporativo': {
        title: 'Business Email',
        subtitle: 'Professional email with your own domain',
        icon: 'fa-envelope',
        description: `
            <p>Communicate professionally with <strong>ANGONUEVE</strong> Business Email. Have email accounts with your own domain (you@yourcompany.com) and convey credibility to your clients and partners.</p>
            <p>We offer easy-to-access webmail, configuration on any email client (Outlook, Thunderbird, mobile), shared calendar and synchronized contacts.</p>
            <p>All plans include anti-spam, antivirus, dedicated technical support and availability guarantee so you never miss an important message.</p>
        `,
        features: [
            'Email with your domain',
            'Intuitive webmail',
            'IMAP/POP3 setup',
            'Anti-spam and antivirus',
            'Calendar and contacts',
            'Mobile access',
            'Technical support',
            'High availability'
        ],
        pricing: [
            { name: 'Basic', monthly: 2000, period: '/month', features_list: ['1 Email account', '1 GB storage', 'Webmail', 'Anti-spam'], featured: false },
            { name: 'Standard', monthly: 5000, period: '/month', features_list: ['3 Email accounts', '5 GB storage', 'Webmail + Calendar', 'Priority support'], featured: true, badge: 'Popular' },
            { name: 'Professional', monthly: 10000, period: '/month', features_list: ['10 Email accounts', '20 GB storage', 'Shared calendar', '24/7 Support'], featured: false },
            { name: 'Enterprise', monthly: 20000, period: '/month', features_list: ['Unlimited accounts', 'Unlimited storage', 'Advanced features', 'Dedicated support'], featured: false }
        ]
    },
    'criacao-sites': {
        title: 'Professional Website Development',
        subtitle: 'Custom websites for your business',
        icon: 'fa-code',
        description: `
            <p><strong>ANGONUEVE</strong> creates custom professional websites for your business. From landing pages to complete portals, our development team turns your vision into digital reality.</p>
            <p>All websites are responsive (adapt to any device), optimized for SEO (Google), with fast performance and modern design that reflects your brand identity.</p>
            <p>We include social media integration, contact forms, interactive maps, image galleries and everything you need for a successful online presence.</p>
        `,
        features: [
            'Responsive design',
            'SEO optimized',
            'High performance',
            'Management panel',
            'Social media integration',
            'Interactive forms',
            'Hosting included (1 year)',
            'Support and maintenance'
        ],
        pricing: [
            { name: 'Landing Page', monthly: 75000, period: '/project', features_list: ['1 Page', 'Responsive design', 'Contact form', 'Basic SEO'], featured: false },
            { name: 'Business', monthly: 150000, period: '/project', features_list: ['Up to 5 pages', 'Responsive design', 'Image gallery', 'Full SEO'], featured: true, badge: 'Recommended' },
            { name: 'Professional', monthly: 300000, period: '/project', features_list: ['Up to 10 pages', 'Integrated blog', 'Admin panel', 'Basic e-commerce'], featured: false },
            { name: 'Portal', monthly: 600000, period: '/project', features_list: ['Unlimited pages', 'Complete system', 'Members area', 'Priority support'], featured: false }
        ]
    },
};

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initHeroSlider();
    initScrollAnimations();
    initCounters();
    initContactForm();
    initServicePage();
    initPageLoader();
    initChatbot();
    initScrollToTop();
    initNewsletter();
    initHomeData();
    initPageBackground();
    trackVisit();
});

function initPageLoader() {
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.hasAttribute('download') && link.hostname === window.location.hostname && link.target !== '_blank') {
            document.getElementById('pageLoader').classList.add('active');
        }
    });
    window.addEventListener('pageshow', function(e) {
        document.getElementById('pageLoader').classList.remove('active');
    });
}

function trackVisit() {
    try {
        fetch('api/track.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ page: window.location.pathname + window.location.search })
        });
    } catch (e) {}
}

function initNavbar() {
    const navbar = document.getElementById('navbar');
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    const overlay = document.getElementById('navOverlay');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    toggle.addEventListener('click', () => {
        toggle.classList.toggle('active');
        links.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = links.classList.contains('active') ? 'hidden' : '';
    });

    overlay.addEventListener('click', () => {
        toggle.classList.remove('active');
        links.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            toggle.classList.remove('active');
            links.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
}

function initHeroSlider() {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.slider-dot');
    const prevBtn = slider.querySelector('.slider-prev');
    const nextBtn = slider.querySelector('.slider-next');
    const currentEl = slider.querySelector('.slider-current');
    const progressBar = slider.querySelector('.slider-progress-bar');

    let current = 0;
    let interval = null;
    const AUTOPLAY_DELAY = 6000;
    const PROGRESS_STEP = 100 / (AUTOPLAY_DELAY / 100);

    function goTo(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        current = index;
        if (currentEl) {
            currentEl.textContent = String(current + 1).padStart(2, '0');
        }
        resetProgress();
    }

    function next() {
        goTo((current + 1) % slides.length);
    }

    function prev() {
        goTo((current - 1 + slides.length) % slides.length);
    }

    function startAutoplay() {
        stopAutoplay();
        interval = setInterval(next, AUTOPLAY_DELAY);
    }

    function stopAutoplay() {
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
    }

    function resetProgress() {
        if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.style.transition = 'none';
            requestAnimationFrame(() => {
                progressBar.style.transition = `width ${AUTOPLAY_DELAY}ms linear`;
                progressBar.style.width = '100%';
            });
        }
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.dataset.slide));
            startAutoplay();
        });
    });

    if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAutoplay(); });

    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { prev(); startAutoplay(); }
        if (e.key === 'ArrowRight') { next(); startAutoplay(); }
    });

    startAutoplay();
}

function initParticles() {
    const container = document.getElementById('particles');
    if (!container) return;

    for (let i = 0; i < 30; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.width = (Math.random() * 4 + 2) + 'px';
        particle.style.height = particle.style.width;
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (Math.random() * 20 + 15) + 's';
        container.appendChild(particle);
    }
}

function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right').forEach(el => {
        observer.observe(el);
    });
}

function initCounters() {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.target);
                const duration = 2000;
                const step = Math.max(1, Math.floor(target / 60));
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target + '+';
                        return;
                    }
                    counter.textContent = current;
                    requestAnimationFrame(updateCounter);
                };

                updateCounter();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const plano = params.get('plano');
    if (plano && document.getElementById('subject')) {
        document.getElementById('subject').value = 'hospedagem';
        const msgEl = document.getElementById('message');
        if (msgEl) {
            msgEl.value = `Olá, estou interessado no plano ${decodeURIComponent(plano)} de hospedagem. Por favor, enviem-me mais informações.`;
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const subject = document.getElementById('subject').value || 'Sem assunto';
        const message = document.getElementById('message').value.trim();
        const success = document.getElementById('formSuccess');

        if (!name || !email || !phone || !message) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return;
        }

        if (!isValidEmail(email)) {
            alert('Por favor, insira um email válido.');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        const params = new URLSearchParams(window.location.search);
        const plan = params.get('plano') || '';

        fetch('api/contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, phone, subject, message, plan })
        })
        .then(res => res.json())
        .then(data => {
            success.classList.add('show');
            form.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Mensagem';

            if (data.whatsapp_url) {
                setTimeout(() => {
                    window.open(data.whatsapp_url, '_blank');
                }, 1500);
            }
        })
        .catch(err => {
            alert('Erro ao enviar mensagem. Tente novamente.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Mensagem';
        });
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function getServicesData() {
    return window.location.pathname.startsWith('/en/') || window.location.pathname.startsWith('/ANGONUEVE/en/') ? servicesDataEn : servicesData;
}

function initServicePage() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const data = getServicesData();
    const service = data[id];
    if (!service) return;

    document.title = `${service.title} | ANGONUEVE`;

    const titleEl = document.getElementById('serviceTitle');
    const subtitleEl = document.getElementById('serviceSubtitle');
    const descEl = document.getElementById('serviceDescription');
    const featuresEl = document.getElementById('serviceFeatures');

    if (titleEl) titleEl.innerHTML = `${service.title}`;
    if (subtitleEl) subtitleEl.textContent = service.subtitle;

    const iconSpan = document.createElement('span');
    iconSpan.style.background = 'var(--gradient-2)';
    iconSpan.style.webkitBackgroundClip = 'text';
    iconSpan.style.webkitTextFillColor = 'transparent';
    iconSpan.innerHTML = `<i class="fas ${service.icon.replace('fab ', '')}" style="-webkit-text-fill-color: transparent;"></i>`;

    if (descEl) descEl.innerHTML = service.description;
    if (featuresEl) {
        featuresEl.innerHTML = '';
        service.features.forEach(f => {
            const li = document.createElement('li');
            li.innerHTML = `<i class="fas fa-check"></i> ${f}`;
            featuresEl.appendChild(li);
        });
    }

    const pricingSection = document.getElementById('pricingSection');
    const pricingGrid = document.getElementById('pricingGrid');
    if (service.pricing && pricingSection && pricingGrid) {
        pricingSection.style.display = '';
        pricingGrid.innerHTML = '';
        service.pricing.forEach(plan => {
            const yearly = plan.monthly * 12;
            const isMonthly = plan.period === '/mês';
            const card = document.createElement('div');
            card.className = `pricing-card glass fade-in${plan.featured ? ' featured' : ''}`;
            const featuresHtml = plan.features_list
                ? plan.features_list.map(f => `<li><i class="fas fa-check"></i> ${f}</li>`).join('')
                : '';
            card.innerHTML = `
                ${plan.badge ? `<div class="pricing-badge">${plan.badge}</div>` : ''}
                <div class="pricing-name">${plan.name}</div>
                <div class="pricing-price">
                    <div class="pricing-monthly">
                        <span class="pricing-currency">Kz</span>
                        ${plan.monthly.toLocaleString()}
                    </div>
                    <span class="pricing-period">${plan.period}</span>
                    ${isMonthly ? `<div class="pricing-yearly">Kz ${yearly.toLocaleString()} /ano</div>` : ''}
                </div>
                <ul class="pricing-features">
                    ${featuresHtml}
                </ul>
                <a href="../orcamento.php?service=${encodeURIComponent(id)}&plan=${encodeURIComponent(plan.name)}" class="btn ${plan.featured ? 'btn-accent' : 'btn-primary'}">
                    <i class="fas fa-paper-plane"></i> Contratar
                </a>
            `;
            pricingGrid.appendChild(card);
        });
        setTimeout(initScrollAnimations, 100);
    }

    const otherGrid = document.getElementById('otherServices');
    if (otherGrid) {
        otherGrid.innerHTML = '';
        Object.keys(getServicesData()).forEach(key => {
            if (key === id) return;
            const s = getServicesData()[key];
            const card = document.createElement('a');
            const isEn = window.location.pathname.startsWith('/en/') || window.location.pathname.startsWith('/ANGONUEVE/en/');
            card.href = `servico.html?id=${key}`;
            card.className = 'service-card glass fade-in';
            card.style.display = 'block';
            card.style.textDecoration = 'none';
            card.style.color = 'inherit';
            card.innerHTML = `
                <div class="service-icon"><i class="fas ${s.icon.replace('fab ', '')}"></i></div>
                <h3>${s.title}</h3>
                <p>${s.subtitle}</p>
                <span class="service-link">${isEn ? 'Learn more' : 'Saiba mais'} <i class="fas fa-arrow-right"></i></span>
            `;
            otherGrid.appendChild(card);
        });
        setTimeout(initScrollAnimations, 100);
    }
}

function setActiveNav() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage.startsWith('servico') && href === 'services.html')) {
            link.classList.add('active');
        }
    });
}

function initChatbot() {
    const STORAGE_KEY = 'angonueve_chat_history';

    const btn = document.createElement('button');
    btn.className = 'chatbot-btn';
    btn.id = 'chatbotBtn';
    btn.setAttribute('aria-label', 'Abrir chat');
    btn.innerHTML = '<span class="btn-open-icon"><i class="fas fa-comment"></i></span><span class="btn-close-icon"><i class="fas fa-times"></i></span>';
    document.body.appendChild(btn);

    const panel = document.createElement('div');
    panel.className = 'chatbot-panel';
    panel.id = 'chatbotPanel';
    panel.innerHTML = `
        <div class="chatbot-header">
            <div class="chatbot-header-avatar"><i class="fas fa-robot"></i></div>
            <div class="chatbot-header-info">
                <h4>ANGONUEVE Bot</h4>
                <p><span class="chatbot-status"><span class="chatbot-status-dot"></span> Online</span> • Resposta automática</p>
            </div>
            <button class="chatbot-header-close" id="chatbotClose" aria-label="Fechar chat"><i class="fas fa-chevron-down"></i></button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-welcome">
                <div class="chatbot-welcome-icon"><i class="fas fa-robot"></i></div>
                <h4>Olá! 👋</h4>
                <p>Bem-vindo à ANGONUEVE! Sou o assistente virtual. Podes perguntar-me sobre os nossos serviços, preços, ou qualquer outra dúvida. Estou aqui para ajudar!</p>
            </div>
        </div>
        <div class="chatbot-input">
            <textarea id="chatbotInput" placeholder="Escreve a tua mensagem..." rows="1"></textarea>
            <button class="chat-send" id="chatbotSend" aria-label="Enviar"><i class="fas fa-paper-plane"></i></button>
        </div>
    `;
    document.body.appendChild(panel);

    const messagesEl = document.getElementById('chatbotMessages');
    const inputEl = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('chatbotSend');
    const closeBtn = document.getElementById('chatbotClose');
    let isOpen = false;
    let isLoading = false;

    function loadHistory() {
        try {
            const data = localStorage.getItem(STORAGE_KEY);
            return data ? JSON.parse(data) : [];
        } catch { return []; }
    }

    function saveHistory(history) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
        } catch {}
    }

    function restoreMessages() {
        const history = loadHistory();
        const welcome = messagesEl.querySelector('.chatbot-welcome');
        if (welcome) welcome.remove();
        history.forEach(msg => {
            appendMessage(msg.role, msg.content, false);
        });
    }

    function appendMessage(role, text, save = true) {
        const welcome = messagesEl.querySelector('.chatbot-welcome');
        if (welcome) welcome.remove();

        const div = document.createElement('div');
        div.className = `chat-msg ${role}`;
        const time = new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
        div.innerHTML = `${text}<div class="chat-msg-time">${time}</div>`;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        if (save) {
            const history = loadHistory();
            history.push({ role, content: text });
            if (history.length > 100) history.splice(0, history.length - 100);
            saveHistory(history);
        }
    }

    function showTyping() {
        const div = document.createElement('div');
        div.className = 'chat-typing';
        div.id = 'chatTyping';
        div.innerHTML = '<span></span><span></span><span></span>';
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const el = document.getElementById('chatTyping');
        if (el) el.remove();
    }

    function showError(msg) {
        const div = document.createElement('div');
        div.className = 'chatbot-error';
        div.textContent = msg;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        setTimeout(() => div.remove(), 5000);
    }

    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text || isLoading) return;

        inputEl.value = '';
        inputEl.style.height = 'auto';
        appendMessage('user', text, false);
        isLoading = true;
        sendBtn.disabled = true;
        showTyping();

        const history = loadHistory();

        try {
            const res = await fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text, history })
            });

            hideTyping();
            const data = await res.json();

            const history2 = loadHistory();
            history2.push({ role: 'user', content: text });
            if (data.success && data.reply) {
                const formatted = data.reply.replace(/\n/g, '<br>');
                appendMessage('assistant', formatted, false);
                history2.push({ role: 'assistant', content: data.reply });
            } else {
                showError(data.error || 'Desculpa, ocorreu um erro. Tenta novamente.');
            }
            if (history2.length > 100) history2.splice(0, history2.length - 100);
            saveHistory(history2);
        } catch (err) {
            hideTyping();
            showError('Erro de conexão. Verifica a tua internet e tenta novamente.');
        }

        isLoading = false;
        sendBtn.disabled = false;
        inputEl.focus();
    }

    function togglePanel() {
        isOpen = !isOpen;
        panel.classList.toggle('active', isOpen);
        btn.classList.toggle('active', isOpen);
        if (isOpen) {
            const history = loadHistory();
            if (history.length > 0) restoreMessages();
            inputEl.focus();
        }
    }

    btn.addEventListener('click', togglePanel);
    closeBtn.addEventListener('click', togglePanel);

    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    inputEl.addEventListener('input', () => {
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 120) + 'px';
    });

    sendBtn.addEventListener('click', sendMessage);
}

function initScrollToTop() {
    const btn = document.getElementById('scrollTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function initNewsletter() {
    const form = document.getElementById('newsletterForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = form.querySelector('input[type="email"]');
        const email = input.value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert('Por favor, insira um email válido.');
            return;
        }
        const btn = form.querySelector('.btn');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A subscrever...';
        fetch('api/newsletter.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                btn.innerHTML = '<i class="fas fa-check"></i> Subscrito!';
                setTimeout(() => { btn.innerHTML = original; btn.disabled = false; }, 3000);
            } else {
                btn.innerHTML = original;
                btn.disabled = false;
                alert(data.error || 'Erro ao subscrever. Tente novamente.');
            }
        })
        .catch(() => {
            btn.innerHTML = original;
            btn.disabled = false;
            alert('Erro ao subscrever. Tente novamente.');
        });
    });
}

function createCarousel(sectionId, trackId, dotsId, prevClass, nextClass) {
    const section = document.getElementById(sectionId);
    if (!section) return null;

    const track = document.getElementById(trackId);
    const dotsContainer = document.getElementById(dotsId);
    const prevBtn = section.querySelector('.' + prevClass);
    const nextBtn = section.querySelector('.' + nextClass);
    if (!track) return null;

    let currentIndex = 0;
    let cardWidth = 0;
    let gap = 20;
    let visibleCards = 1;

    function calcDimensions() {
        const cards = track.children;
        if (cards.length === 0) return;
        const wrapper = track.parentElement;
        const wrapperWidth = wrapper.clientWidth;
        cardWidth = cards[0].offsetWidth;
        gap = 20;
        const totalCardSpace = cardWidth + gap;
        visibleCards = Math.max(1, Math.floor((wrapperWidth + gap) / totalCardSpace));
        const maxIndex = Math.max(0, cards.length - visibleCards);
        if (currentIndex > maxIndex) currentIndex = maxIndex;
    }

    function update() {
        const cards = track.children;
        if (cards.length === 0) return;
        const maxIndex = Math.max(0, cards.length - visibleCards);
        if (currentIndex > maxIndex) currentIndex = maxIndex;
        const offset = -(currentIndex * (cardWidth + gap));
        track.style.transform = 'translateX(' + offset + 'px)';
        updateDots();
        updateArrows();
    }

    function updateDots() {
        if (!dotsContainer) return;
        const cards = track.children;
        if (cards.length === 0) return;
        const totalPages = Math.max(1, cards.length - visibleCards + 1);
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('span');
            dot.className = i === currentIndex ? 'active' : '';
            dot.addEventListener('click', () => { currentIndex = i; update(); });
            dotsContainer.appendChild(dot);
        }
    }

    function updateArrows() {
        if (!prevBtn || !nextBtn) return;
        const cards = track.children;
        if (cards.length === 0) return;
        const maxIndex = Math.max(0, cards.length - visibleCards);
        prevBtn.style.opacity = currentIndex <= 0 ? '0.3' : '1';
        nextBtn.style.opacity = currentIndex >= maxIndex ? '0.3' : '1';
    }

    function nextSlide() {
        const cards = track.children;
        if (cards.length === 0) return;
        const maxIndex = Math.max(0, cards.length - visibleCards);
        if (currentIndex < maxIndex) { currentIndex++; update(); }
    }

    function prevSlide() {
        if (currentIndex > 0) { currentIndex--; update(); }
    }

    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    calcDimensions();
    update();
    window.addEventListener('resize', () => { calcDimensions(); update(); });

    return { next: nextSlide, prev: prevSlide, update, refresh: () => { calcDimensions(); update(); } };
}

function initHomeData() {
    const teamSection = document.getElementById('teamSection');
    const testimonialsSection = document.getElementById('testimonialsSection');
    const clientsSection = document.getElementById('clientsSection');
    const cpanelSection = document.getElementById('cpanelFeatures');

    fetch('api/get-home-data.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            if (data.employees && data.employees.length > 0) {
                populateTeam(data.employees);
                if (teamSection) teamSection.style.display = '';
            }

            if (data.testimonials && data.testimonials.length > 0) {
                populateTestimonials(data.testimonials);
                if (testimonialsSection) testimonialsSection.style.display = '';
            }

            if (data.hasClients && data.clients && data.clients.length > 0) {
                populateClientLogos(data.clients);
                if (clientsSection) clientsSection.style.display = '';
            }

            if (data.cpanelFeatures && data.cpanelFeatures.length > 0) {
                populateCpanelFeatures(data.cpanelFeatures);
                if (cpanelSection) cpanelSection.style.display = '';
            }

            setTimeout(initScrollAnimations, 100);
        })
        .catch(() => {});
}

function populateTeam(employees) {
    const track = document.getElementById('teamTrack');
    if (!track) return;

    track.innerHTML = '';
    employees.forEach(emp => {
        const card = document.createElement('div');
        card.className = 'team-card';
        const photoHtml = emp.photo
            ? `<img src="${emp.photo}" alt="${emp.name}" class="team-avatar-img" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--card-border);margin:0 auto 16px;display:block;">`
            : `<div class="team-avatar"><i class="fas fa-user-tie"></i></div>`;
        card.innerHTML = `
            ${photoHtml}
            <h3>${escHtml(emp.name)}</h3>
            <span class="team-role">${escHtml(emp.position)}</span>
            ${emp.bio ? `<p class="team-bio">${escHtml(emp.bio)}</p>` : ''}
            <div class="team-social">
                ${emp.email ? `<a href="mailto:${escHtml(emp.email)}" aria-label="Email"><i class="fas fa-envelope"></i></a>` : ''}
            </div>
        `;
        track.appendChild(card);
    });

    const carousel = createCarousel('teamSection', 'teamTrack', 'teamDots', 'team-prev', 'team-next');
}

function populateTestimonials(testimonials) {
    const track = document.getElementById('testimonialsTrack');
    if (!track) return;

    track.innerHTML = '';
    testimonials.forEach(t => {
        const card = document.createElement('div');
        card.className = 'testimonial-card';
        const stars = '<i class="fas fa-star"></i>'.repeat(t.rating || 5);
        const photoHtml = t.photo
            ? `<img src="${t.photo}" alt="${t.name}" class="testimonial-avatar-img" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">`
            : `<div class="testimonial-avatar"><i class="fas fa-user"></i></div>`;
        card.innerHTML = `
            <div class="testimonial-stars">${stars}</div>
            <p class="testimonial-text">"${escHtml(t.text)}"</p>
            <div class="testimonial-author">
                ${photoHtml}
                <div>
                    <h4>${escHtml(t.name)}</h4>
                    <span>Cliente</span>
                </div>
            </div>
        `;
        track.appendChild(card);
    });

    const carousel = createCarousel('testimonialsSection', 'testimonialsTrack', 'testimonialsDots', 'testimonial-prev', 'testimonial-next');
}

function populateClientLogos(clients) {
    const track = document.getElementById('clientsTrack');
    if (!track) return;

    track.innerHTML = '';
    clients.forEach(c => {
        const card = document.createElement('div');
        card.className = 'client-card';
        const initials = c.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        card.innerHTML = `
            <div class="client-logo-placeholder">
                <span class="client-initials">${escHtml(initials)}</span>
            </div>
            <h4>${escHtml(c.name)}</h4>
        `;
        track.appendChild(card);
    });

    const carousel = createCarousel('clientsSection', 'clientsTrack', 'clientsDots', 'clients-prev', 'clients-next');
}

function populateCpanelFeatures(features) {
    const grid = document.getElementById('cpanelGrid');
    if (!grid) return;

    grid.innerHTML = '';
    features.forEach(f => {
        const card = document.createElement('div');
        card.className = 'cpanel-card glass fade-in';
        card.innerHTML = `
            <div class="cpanel-icon"><i class="fas ${escHtml(f.icon)}"></i></div>
            <h3 class="cpanel-title">${escHtml(f.title)}</h3>
            <p class="cpanel-desc">${escHtml(f.description)}</p>
        `;
        grid.appendChild(card);
    });

    // stagger animation
    grid.querySelectorAll('.cpanel-card').forEach((card, i) => {
        card.style.transitionDelay = (i * 0.05) + 's';
    });
}

function getPageKey() {
    const path = window.location.pathname;
    if (path.endsWith('index.html') || path.endsWith('/') || path === '' || path.endsWith('/ANGONUEVE') || path.endsWith('/ANGONUEVE/')) return 'home';
    if (path.includes('about')) return 'about';
    if (path.includes('contact')) return 'contact';
    if (path.includes('servicos')) return 'services';
    if (path.includes('modelos')) return 'models';
    return null;
}

function initPageBackground() {
    const page = getPageKey();
    if (!page) return;
    fetch('api/get-page-bg.php?page=' + page)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.url) {
                const overlay = document.createElement('div');
                overlay.className = 'page-bg-overlay';
                overlay.style.cssText = 'position:fixed;inset:0;z-index:-2;background-image:url(' + JSON.stringify(data.url) + ');background-size:cover;background-position:center;background-attachment:fixed;';
                document.body.prepend(overlay);
                const darken = document.createElement('div');
                darken.className = 'page-bg-darken';
                darken.style.cssText = 'position:fixed;inset:0;z-index:-1;background:var(--dark);opacity:0.7;';
                document.body.prepend(darken);
            }
        })
        .catch(() => {});
}

function escHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

window.addEventListener('DOMContentLoaded', setActiveNav);
