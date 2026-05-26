# ANGONUEVE — Hospedagem de Sites, Domínios e Serviços Web

Plataforma digital para divulgação, venda e gestão de serviços web: hospedagem, domínios, email corporativo e criação de sites.

## Funcionalidades

### Frontend
- Página inicial com carrossel profissional (3 slides)
- Catálogo de serviços web com descrições e preços
- Páginas institucionais (Sobre, Contacto, Privacidade, Termos)
- Formulário de contacto com API PHP
- Pedido de orçamento online para serviços digitais
- Design responsivo (mobile, tablet, desktop)
- Animações suaves (scroll reveal, glassmorphism)

### Backend — Admin CRM/ERP
- Dashboard com estatísticas
- Gestão de mensagens, encomendas e visitantes
- Gestão de clientes (aprovação, perfis, reset de password)
- Gestão de colaboradores (RH, permissões, contratos)
- Processamento de salários (contracheques PDF)
- Facturação e recibos (PDF)
- Gestão de pagamentos e receitas
- Relatórios financeiros com exportação PDF
- Chat de suporte ao cliente
- Log de actividades
- Configurações dinâmicas do site
- Sistema de autenticação com roles (admin, manager, employee)

### Área do Cliente
- Login e registo de conta
- Recuperação e redefinição de password
- Dashboard pessoal com resumo de serviços
- Acompanhamento de encomendas
- Chat de suporte em tempo real
- Facturas e recibos online
- Pagamento de serviços
- Perfil e alteração de dados

## Stack Tecnológica

| Componente | Tecnologia |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Backend | PHP 8 com PDO |
| Base de Dados | MySQL/MariaDB |
| Email | PHPMailer 6 |
| Ícones | Font Awesome 6 (CDN) |
| Fontes | Google Fonts (Inter, Poppins) |
| Servidor | Apache (XAMPP) |

## Requisitos

- PHP 8.0+
- MySQL/MariaDB
- Apache (mod_rewrite activo)
- XAMPP (recomendado)

## Instalação

1. Clonar o repositório:
   ```bash
   git clone https://github.com/Masukulmiguel/angonueve.git
   ```

2. Colocar na pasta `htdocs` do XAMPP (ou document root do Apache).

3. Importar o schema da base de dados:
   - Executar `database/schema.sql` no MySQL/MariaDB
   - Executar as migrações `database/schema-update.sql` e `database/schema-update2.sql`

4. Configurar a conexão à BD em `includes/config.php`.

5. Aceder a `http://localhost/ANGONUEVE/admin/setup.php` para configuração inicial.

6. Login admin padrão: `admin@angonueve.co` / `admin123`

## Estrutura do Projecto

```
ANGONUEVE/
├── index.html, about.html, contact.html, services.html
├── servico.php, servicos.php, orcamento.php
├── privacidade.php, termos.php
├── css/ ─ style.css
├── js/ ─ script.js
├── includes/ ─ config.php, db.php, auth.php, functions.php, spinner.php
├── api/ ─ contact.php, order.php, track.php, chat.php, notifications.php, validate-nif.php
├── database/ ─ schema.sql, schema-update.sql, schema-update2.sql
├── admin/ ─ dashboard, messages, orders, clients, employees, invoices, payments, revenue, payslips, contracts, chat, settings, setup
├── client/ ─ login, register, dashboard, profile, orders, services, invoices, pay, chat, password management
├── uploads/ ─ chat/, contracts/, employees/, proofs/
├── vendor/ ─ phpmailer/
├── prd.md ─ Documento de Requisitos
├── specs.md ─ Especificações Técnicas
└── .github/workflows/
```

## Documentação

- [`prd.md`](prd.md) — Documento de Requisitos do Produto
- [`specs.md`](specs.md) — Especificações Técnicas

## Roadmap

| Fase | Estado |
|---|---|
| V1 — Site estático (serviços web) | ✅ Concluído |
| V2 — Backend PHP + BD | ✅ Concluído |
| V3 — Admin CRM (gestão de serviços web) | ✅ Concluído |
| V4 — Área do Cliente | ✅ Concluído |
| V5 — Módulo RH | ✅ Concluído |
| V6 — Módulo Financeiro | ✅ Concluído |
| V7 — Chat + Notificações | ✅ Concluído |
| V8 — Carrinho + Pagamentos (hospedagem, domínios) | ⏳ Pendente |
| V9 — Blog (dicas de web hosting) | 📋 Planeado |
| V10 — API Pública (integração registo domínios) | 📋 Planeado |

## Licença

© 2026 ANGONUEVE. Todos os direitos reservados.
