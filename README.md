# ANGONUEVE — Hospedagem de Sites, Domínios e Serviços Web

Plataforma digital para divulgação, venda e gestão de serviços web: hospedagem, domínios, email corporativo e criação de sites.

## Stack Tecnológica

| Componente | Tecnologia |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Backend | PHP 8 com PDO |
| Base de Dados | MySQL/MariaDB |
| Email | PHPMailer 6 |
| Ícones | Font Awesome 6 (CDN) |
| Fontes | Google Fonts (Inter, Poppins) |
| Servidor | Apache (mod_rewrite activo) |

## Instalação Local (XAMPP)

1. Coloca a pasta na `htdocs` do XAMPP.

2. Importa a base de dados:
   ```bash
   mysql -u root < database/schema-full.sql
   mysql -u root < database/seed.sql
   ```

3. Acede a `http://localhost/ANGONUEVE/admin/setup.php` para criar o admin.

4. **Admin padrão:** `admin@angonueve.co` / `admin123`

## Deploy para Produção (Hospedagem Real)

### 1. Base de Dados

1. Acede ao **phpMyAdmin** ou MySQL da tua hospedagem.
2. Cria uma base de dados (ex: `angonueve_db`).
3. Importa o ficheiro `database/schema-full.sql`.
4. Importa o ficheiro `database/seed.sql`.

### 2. Configuração

1. Copia `.env.example` para `.env` no servidor:
   ```bash
   cp .env.example .env
   ```
2. Edita `.env` com os dados da tua hospedagem:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` — dados da base de dados
   - `SITE_URL` — URL do teu site (ex: `https://teudominio.com`)
   - `APP_ENV=production`

### 3. Upload dos ficheiros

Faz upload de **todos os ficheiros** (excepto `database/`) para a pasta `public_html` ou `www` da tua hospedagem via FTP.

### 4. Setup Inicial

Acede a `https://teudominio.com/admin/setup.php` para criar o administrador.

### 5. Email (SMTP)

No painel **Admin > Configurações > Email**, preenche os dados SMTP da tua hospedagem.

## Estrutura do Projecto

```
ANGONUEVE/
├── index.html, about.html, contact.html, services.html
├── servico.php, servicos.php, orcamento.php
├── privacidade.php, termos.php
├── en/                    ─ Versão inglesa
├── css/                   ─ Folhas de estilo
├── js/                    ─ JavaScript
├── includes/              ─ Config, DB, funções
├── api/                   ─ Endpoints REST
├── database/              ─ Schemas SQL
│   ├── schema-full.sql    ─ Schema completo (produção)
│   ├── seed.sql           ─ Dados iniciais
│   └── schema.sql         ─ Schema base (desenvolvimento)
├── admin/                 ─ Painel administrativo
├── client/                ─ Área do cliente
├── uploads/               ─ Uploads
├── vendor/                ─ Dependências (PHPMailer)
├── .env.example           ─ Template de configuração
└── .htaccess              ─ Configuração Apache
```

## Funcionalidades

### Frontend
- Página inicial com carrossel
- Catálogo de serviços com preços
- Formulário de contacto + API
- Pedido de orçamento online
- Design responsivo com glassmorphism
- Animações suaves (scroll reveal)
- Chat inteligente (Gemini AI)

### Admin CRM/ERP
- Dashboard com estatísticas
- Gestão de mensagens e encomendas
- Gestão de clientes e colaboradores
- Facturação e recibos (PDF)
- Gestão de pagamentos
- Contracheques (PDF)
- Chat de suporte ao cliente
- WhatsApp Cloud API
- Log de actividades

### Área do Cliente
- Login/Registo
- Dashboard pessoal
- Acompanhamento de encomendas
- Facturas e pagamentos
- Chat de suporte
- Perfil

## Licença

© 2026 ANGONUEVE. Todos os direitos reservados.
