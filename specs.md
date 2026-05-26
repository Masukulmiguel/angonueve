# SPECS — ANGONUEVE Hospedagem de Sites e Serviços Web

## 1. Stack Tecnológica

| Componente | Tecnologia |
|------------|------------|
| **HTML** | HTML5 semântico |
| **CSS** | CSS3 puro com custom properties |
| **JavaScript** | Vanilla JS (ES6+) |
| **Backend** | PHP 8 com PDO |
| **Email** | PHPMailer 6 |
| **Base de Dados** | MySQL/MariaDB |
| **Ícones** | Font Awesome 6 (CDN) |
| **Fontes** | Google Fonts: Inter (textos), Poppins (títulos) |
| **Servidor** | Apache (XAMPP) |
| **Compatibilidade** | Chrome, Firefox, Edge, Safari, Opera |

---

## 2. Especificações de Design

### 2.1 Paleta de Cores
```css
--primary:       #0a1628      /* Azul escuro profundo */
--primary-light: #1a2d4a      /* Azul escuro médio */
--secondary:     #00d4ff      /* Ciano/azul neon (detalhes) */
--secondary-alt: #0099cc      /* Ciano escuro */
--accent:        #ff6b35      /* Laranja (CTAs) */
--accent-hover:  #e55a2b      /* Laranja hover */
--dark:          #050d1a      /* Fundo escuro */
--text:          #e0e6ed      /* Texto claro */
--text-muted:    #8899aa      /* Texto secundário */
--success:       #00e676      /* Verde (confirmações) */
```

### 2.2 Tipografia
- **Títulos:** Poppins, 600-800 weight
- **Corpo:** Inter, 400-500 weight
- **Tamanhos:** h1 (3.8rem carrossel), h2 (2.5rem), h3 (1.3rem), body (1rem)

### 2.3 Glassmorphism
```css
background: rgba(255, 255, 255, 0.05);
backdrop-filter: blur(12px);
border: 1px solid rgba(255, 255, 255, 0.1);
border-radius: 16px;
```

---

## 3. Estrutura de Componentes

### 3.1 Header/Nav
- Logótipo à esquerda com gradiente
- Menu de navegação central
- Botão CTA "Orçamento"
- Menu hamburger em mobile com overlay
- Efeito blur ao scrollar (classe `.scrolled`)

### 3.2 Hero Carrossel (3 Slides)
- Slide 1: Imagem Hero.png com overlay gradiente (hospedagem de sites)
- Slide 2: Gradiente (domínios e email corporativo)
- Slide 3: Gradiente verde (criação de sites)
- Auto-play 6s com barra de progresso
- Navegação: dots, setas laterais, teclado (← →)
- Animações em cascata: badge → título → texto → botões
- Stats glassmorphism na parte inferior
- Pausa ao hover

### 3.3 Serviços (Cards)
- Grid 4 serviços: Hospedagem de Sites, Registo de Domínios, Email Corporativo, Criação de Sites
- Layout: Grid adaptativo (desktop: múltiplas colunas → tablet: 2 colunas → mobile: 1 coluna)
- Ícone + título + descrição + link
- Hover com glow e elevação
- Glassmorphism
- Cada card representa um serviço web específico

### 3.4 Porquê Nós
- Grid 3 colunas
- Ícone + título + descrição
- Contadores animados com Intersection Observer

### 3.5 Contacto
- Formulário com validação JS e API PHP
- Campos: nome, email, telefone, assunto, mensagem
- Integração WhatsApp no submit
- Informações: telefone, email, morada, horário

### 3.6 Footer
- Grid 4 colunas: marca, links, serviços web, contacto
- Redes sociais com hover gradiente

---

## 4. Backend (PHP/MySQL)

### 4.1 Base de Dados (`angonueve_db`)
| Tabela | Finalidade |
|--------|-----------|
| `users` | Utilizadores (admin, manager, employee, client) |
| `visitors` | Tracking de visitas |
| `messages` | Mensagens do formulário de contacto |
| `orders` | Encomendas de serviços web |
| `services` | Catálogo de serviços web (dinâmico) |
| `settings` | Configurações do site |
| `activity_log` | Log de actividades |
| `employees` | Colaboradores (RH) |
| `employee_permissions` | Permissões por colaborador |
| `contracts` | Contratos com clientes |
| `invoices` | Facturas emitidas |
| `payments` | Pagamentos recebidos |
| `payslips` | Contracheques processados |
| `chat_messages` | Mensagens do chat suporte |
| `chat_conversations` | Conversas do chat suporte |
| `notifications` | Notificações do sistema |
| `password_resets` | Tokens de recuperação de password |

### 4.2 Autenticação
- Sessão PHP com `session_regenerate_id()`
- Roles: admin (acesso total), manager (gestão), employee (colaborador), client (cliente)
- `requireAdmin()` para páginas administrativas
- `requireClient()` para área do cliente
- `requireEmployee()` para dashboard colaborador
- Timeout de sessão configurável (3600s)
- Protecção CSRF com tokens de sessão

### 4.3 API Endpoints
| Endpoint | Método | Descrição |
|----------|--------|-----------|
| `api/contact.php` | POST | Enviar mensagem |
| `api/order.php` | POST | Registar encomenda de serviço web |
| `api/order.php` | GET | Listar encomendas (auth) |
| `api/track.php` | POST | Registar visita |
| `api/chat.php` | POST/GET | Mensagens chat suporte |
| `api/chat-support.php` | POST/GET | Chat administração |
| `api/notifications.php` | GET | Notificações do sistema |
| `api/validate-nif.php` | GET | Validação de NIF angolano |

---

## 5. Área do Cliente

### 5.1 Funcionalidades
- Login com email + password
- Registo de nova conta
- Recuperação e redefinição de password
- Dashboard com estatísticas pessoais
- Listagem de encomendas com filtros por status
- Visualização de serviços web contratados (hospedagem, domínios, email, sites)
- Facturas e recibos online (download PDF)
- Pagamento de serviços
- Chat de suporte em tempo real
- Perfil e alteração de dados
- Contacto rápido (telefone, email, WhatsApp)

### 5.2 Segurança
- Password hashed com `password_hash()` (bcrypt)
- Separação de sessões admin/cliente/employee por role
- Sanitização de inputs com `htmlspecialchars()`
- Protecção CSRF com tokens
- Validação de tokens de reset password

---

## 6. Responsividade

| Breakpoint | Largura | Comportamento |
|------------|---------|---------------|
| **Desktop** | > 1024px | Layout completo, múltiplas colunas de serviços |
| **Tablet** | 768px - 1024px | 2 colunas serviços, sidebar reduzida |
| **Mobile** | < 768px | 1 coluna, menu hamburger, stats empilhados |

---

## 7. Performance

- CSS/JS em ficheiros externos com cache do browser
- Lazy loading de conteúdo via Intersection Observer
- Animações CSS otimizadas (GPU accelerated)
- Consultas PDO preparadas (proteção SQL injection)
- Geração de PDF server-side com biblioteca nativa PHP
- Envio de email via PHPMailer com SMTP

---

## 8. Estrutura de Ficheiros

```
ANGONUEVE/
├── index.html
├── about.html
├── services.html
├── contact.html
├── servico.html
├── servico.php
├── servicos.php
├── orcamento.php
├── privacidade.php
├── termos.php
├── prd.md
├── specs.md
├── .htaccess
├── .gitignore
│
├── assets/
├── css/
│   └── style.css
├── js/
│   └── script.js
├── images/
│   ├── logo.png
│   ├── Hero.png
│   ├── blog-img-03.jpg
│   ├── img-4.jpg
│   └── related-pro-04.jpg
│
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── auth.php
│   ├── functions.php
│   └── spinner.php
│
├── api/
│   ├── contact.php
│   ├── order.php
│   ├── track.php
│   ├── chat.php
│   ├── chat-support.php
│   ├── notifications.php
│   └── validate-nif.php
│
├── database/
│   ├── schema.sql
│   ├── schema-update.sql
│   └── schema-update2.sql
│
├── admin/
│   ├── index.php
│   ├── dashboard.php
│   ├── sidebar.php
│   ├── logout.php
│   ├── messages.php
│   ├── orders.php
│   ├── visitors.php
│   ├── settings.php
│   ├── clients.php
│   ├── employees.php
│   ├── employee-dashboard.php
│   ├── contracts.php
│   ├── invoices.php
│   ├── invoice-pdf.php
│   ├── payments.php
│   ├── revenue.php
│   ├── revenue-pdf.php
│   ├── payslips.php
│   ├── payslip-pdf.php
│   ├── activity-log.php
│   ├── chat-conversations.php
│   ├── support-chat.php
│   ├── abandoned.php
│   ├── setup.php
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
│
├── client/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   ├── change-password.php
│   ├── dashboard.php
│   ├── profile.php
│   ├── orders.php
│   ├── services.php
│   ├── service-view.php
│   ├── invoices.php
│   ├── invoice-view.php
│   ├── pay.php
│   └── chat.php
│
├── uploads/
│   ├── chat/                 # Anexos chat
│   ├── contracts/            # Documentos contratos
│   ├── employees/            # Fotos colaboradores
│   └── proofs/               # Comprovativos
│
├── vendor/
│   └── phpmailer/            # PHPMailer (dependência)
│
└── .github/
    └── workflows/
        └── generator-generic-ossf-slsa3-publish.yml
```

---

## 9. Próximos Passos (Roadmap)

| Fase | Tarefa |
|------|--------|
| **V1** | Site estático com serviços web ✅ |
| **V2** | Backend PHP com formulário e BD ✅ |
| **V3** | Admin CRM (gestão de serviços web) ✅ |
| **V4** | Área do cliente e sistema de suporte ✅ |
| **V5** | Módulo RH (colaboradores, permissões, contratos) ✅ |
| **V6** | Módulo Financeiro (facturas, pagamentos, contracheques, relatórios) ✅ |
| **V7** | Chat suporte em tempo real e notificações ✅ |
| **V8** | Carrinho de compras para hospedagem, domínios e email |
| **V9** | Blog com dicas de web hosting e gestão de sites |
| **V10** | API pública para integração de registo de domínios e hospedagem |
