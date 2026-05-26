# PRD — ANGONUEVE (Site Institucional + CRM)

## 1. Visão Geral

**Nome do Projecto:** ANGONUEVE - Hospedagem de Sites, Domínios e Serviços Web  
**Propósito:** Plataforma digital para divulgação, venda e gestão de serviços web: hospedagem, domínios, email corporativo e criação de sites  
**Público-alvo:** Empresas, residências e pequenos negócios em Angola  
**Língua:** Português (PT)

---

## 2. Objectivos de Negócio

- Apresentar a marca ANGONUEVE como referência em soluções web e hospedagem
- Gerar leads e contactos comerciais através do site
- Exibir catálogo de serviços web completo e detalhado
- Gerir encomendas, mensagens e visitantes via painel administrativo
- Oferecer área do cliente para acompanhamento de serviços de hospedagem e domínios
- Transmitir profissionalismo e confiança

---

## 3. Serviços Oferecidos

| Serviço | Descrição |
|---------|-----------|
| **Hospedagem de Sites** | Planos de hosting partilhado, VPS e dedicado com SSD NVMe |
| **Registo de Domínios** | Domínios .com, .ao, .co.ao, .net, .org |
| **Email Corporativo** | Email profissional com o seu domínio, webmail, sincronização móvel |
| **Criação de Sites Profissionais** | Desenvolvimento de sites personalizados e responsivos |


---

## 4. Funcionalidades do Site

### 4.1 Frontend
- Página inicial com carrossel profissional de 3 slides
- Catálogo de serviços web com descrições detalhadas e preços
- Páginas institucionais (Sobre, Contacto, Privacidade, Termos)
- Formulário de contacto funcional com API PHP
- Página de orçamento online para serviços digitais
- Design responsivo (mobile, tablet, desktop)
- Animações suaves (scroll reveal, fade-in, glassmorphism)
- Navegação fluída e intuitiva
- Spinner de carregamento ao navegar entre páginas

### 4.2 Backend (Admin CRM/ERP)
- Painel administrativo com dashboard de estatísticas
- Gestão de mensagens recebidas (ler, responder, arquivar)
- Gestão de encomendas de serviços web (confirmar, progresso, concluir)
- Rastreamento de visitantes (páginas, dispositivos)
- Configurações do site (email, telefone, WhatsApp)
- Sistema de autenticação com roles (admin, manager, employee)
- Gestão de clientes (aprovação, perfis, reset de password)
- Gestão de colaboradores (RH, permissões, contratos)
- Processamento de salários (contracheques em PDF)
- Gestão de contratos com clientes
- Facturação e recibos (PDF)
- Gestão de pagamentos e receitas
- Relatórios financeiros (receitas, PDF export)
- Chat de suporte ao cliente (admin)
- Log de actividades do sistema
- Carrinho abandonado
- Instalação inicial via setup.php

### 4.3 Área do Cliente
- Login e registo de conta
- Recuperação e redefinição de password
- Dashboard pessoal com resumo de serviços (hospedagem, domínios, email)
- Acompanhamento de encomendas e estado
- Histórico de mensagens enviadas
- Chat de suporte em tempo real
- Facturas e recibos online
- Pagamento de serviços online
- Perfil e alteração de dados pessoais
- Contacto rápido (telefone, email, WhatsApp)

---

## 5. Stack Tecnológica

| Componente | Tecnologia |
|------------|------------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend** | PHP 8 com PDO |
| **Base de Dados** | MySQL/MariaDB |
| **Ícones** | Font Awesome 6 (CDN) |
| **Fontes** | Google Fonts (Inter, Poppins) |
| **Email** | PHPMailer |
| **Servidor** | Apache (XAMPP) |

---

## 6. Estrutura do Projecto

```
ANGONUEVE/
├── index.html                # Página principal com carrossel
├── about.html                # Sobre nós
├── services.html             # Serviços detalhados
├── contact.html              # Contacto
├── servico.html              # Página de serviço (HTML)
├── servico.php               # Página de serviço (PHP)
├── servicos.php              # Listagem de serviços web
├── orcamento.php             # Pedido de orçamento
├── privacidade.php           # Política de privacidade
├── termos.php                # Termos de serviço
├── prd.md                    # Documento de requisitos
├── specs.md                  # Especificações técnicas
├── .htaccess                 # Configuração Apache
│
├── assets/                   # Recursos estáticos
├── css/
│   └── style.css             # Estilos globais
├── js/
│   └── script.js             # Funcionalidades JS
├── images/
│   ├── logo.png              # Logótipo
│   ├── Hero.png              # Imagem do carrossel
│   ├── blog-img-03.jpg       # Imagens diversas
│   ├── img-4.jpg
│   └── related-pro-04.jpg
│
├── includes/
│   ├── config.php            # Configuração BD
│   ├── db.php                # Classe Database (PDO)
│   ├── auth.php              # Autenticação e sessão
│   ├── functions.php         # Funções auxiliares
│   └── spinner.php           # Spinner de carregamento
│
├── api/
│   ├── contact.php           # API formulário de contacto
│   ├── order.php             # API encomendas
│   ├── track.php             # API tracking visitas
│   ├── chat.php              # API chat suporte
│   ├── chat-support.php      # API chat administrativo
│   ├── notifications.php     # API notificações
│   └── validate-nif.php      # Validação NIF
│
├── database/
│   ├── schema.sql            # Schema base
│   ├── schema-update.sql     # Migração 1
│   └── schema-update2.sql    # Migração 2
│
├── admin/
│   ├── index.php             # Login admin
│   ├── dashboard.php         # Dashboard
│   ├── sidebar.php           # Sidebar partilhada
│   ├── logout.php            # Logout
│   ├── messages.php          # Gestão mensagens
│   ├── orders.php            # Gestão encomendas
│   ├── visitors.php          # Relatório visitantes
│   ├── settings.php          # Configurações
│   ├── clients.php           # Gestão clientes
│   ├── employees.php         # Gestão colaboradores
│   ├── employee-dashboard.php# Dashboard colaborador
│   ├── contracts.php         # Contratos
│   ├── invoices.php          # Facturas
│   ├── invoice-pdf.php       # PDF factura
│   ├── payments.php          # Pagamentos
│   ├── revenue.php           # Relatório receitas
│   ├── revenue-pdf.php       # PDF receitas
│   ├── payslips.php          # Contracheques
│   ├── payslip-pdf.php       # PDF contracheque
│   ├── activity-log.php      # Log actividades
│   ├── chat-conversations.php# Conversas chat
│   ├── support-chat.php      # Chat suporte admin
│   ├── abandoned.php         # Carrinhos abandonados
│   ├── setup.php             # Instalação inicial
│   ├── css/
│   │   └── admin.css         # Estilos admin
│   └── js/
│       └── admin.js          # Funcionalidades admin
│
├── client/
│   ├── login.php             # Login cliente
│   ├── register.php          # Registo cliente
│   ├── logout.php            # Logout
│   ├── forgot-password.php   # Recuperar password
│   ├── reset-password.php    # Redefinir password
│   ├── change-password.php   # Alterar password
│   ├── dashboard.php         # Dashboard cliente
│   ├── profile.php           # Perfil cliente
│   ├── orders.php            # Encomendas
│   ├── services.php          # Serviços
│   ├── service-view.php      # Detalhe serviço
│   ├── invoices.php          # Facturas
│   ├── invoice-view.php      # Detalhe factura
│   ├── pay.php               # Pagamento online
│   └── chat.php              # Chat suporte
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

## 7. Critérios de Sucesso

- Site responsivo e funcional em todos os dispositivos
- Admin CRM/ERP operacional com gestão de leads, encomendas de serviços web, RH e finanças
- Área do cliente funcional com chat, facturas e pagamentos para hospedagem e domínios
- Tempo de carregamento < 3 segundos
- Navegação intuitiva com taxa de rejeição < 50%
- Geração de documentos PDF (facturas, contracheques, relatórios)
- Envio de email transaccional via PHPMailer
