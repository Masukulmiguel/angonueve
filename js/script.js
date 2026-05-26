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
    trackVisit();
});

function initPageLoader() {
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.hasAttribute('download') && link.hostname === window.location.hostname && link.target !== '_blank') {
            document.getElementById('pageLoader').classList.add('active');
        }
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

function initServicePage() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const service = servicesData[id];
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
        Object.keys(servicesData).forEach(key => {
            if (key === id) return;
            const s = servicesData[key];
            const card = document.createElement('a');
            card.href = `servico.html?id=${key}`;
            card.className = 'service-card glass fade-in';
            card.style.display = 'block';
            card.style.textDecoration = 'none';
            card.style.color = 'inherit';
            card.innerHTML = `
                <div class="service-icon"><i class="fas ${s.icon.replace('fab ', '')}"></i></div>
                <h3>${s.title}</h3>
                <p>${s.subtitle}</p>
                <span class="service-link">Saiba mais <i class="fas fa-arrow-right"></i></span>
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

window.addEventListener('DOMContentLoaded', setActiveNav);
