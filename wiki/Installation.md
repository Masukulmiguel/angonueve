# Instalação

## Requisitos

- XAMPP (Apache + PHP 8.x + MariaDB)
- Git
- Conta Google Cloud (para API Gemini)

## Passos

### 1. Clonar o repositório

```bash
git clone https://github.com/Masukulmiguel/angonueve.git
cd angonueve
```

### 2. Configurar base de dados

1. Abre phpMyAdmin (`http://localhost/phpmyadmin`)
2. Cria a base de dados `angonueve_db` com charset `utf8mb4`
3. Importa `database/schema.sql`
4. (Opcional) Aplica updates em `database/schema-update*.sql` por ordem

### 3. Configurar variáveis

Copia `includes/config.example.php` para `includes/config.php` e ajusta:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'angonueve_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // tua password MySQL
define('DB_CHARSET', 'utf8mb4');
define('SITE_URL', 'http://localhost/ANGONUEVE');
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT', 3600);  // 1 hora
define('ITEMS_PER_PAGE', 20);
```

### 4. Primeiro acesso

Acede a `http://localhost/ANGONUEVE/setup.php` para criar a conta de administrador inicial.

### 5. Configurar API Gemini

1. Obtém uma API key em [Google AI Studio](https://aistudio.google.com/)
2. No painel admin, vai a **Definições → Gerais** e insere a chave em `gemini_api_key`
3. Define o preço do site IA em `ai_site_price` (Kz)

### 6. Verificar

- `http://localhost/ANGONUEVE/` — site principal
- `http://localhost/ANGONUEVE/admin/` — painel admin
- `http://localhost/ANGONUEVE/client/` — área de cliente
- `http://localhost/ANGONUEVE/criar-site.php` — gerador de sites com IA

## Estrutura de Diretorias

```
ANGONUEVE/
├── admin/          # Painel de administração
├── api/            # Endpoints REST
├── client/         # Área do cliente
├── css/            # Folhas de estilo
├── database/       # Schemas SQL
├── en/             # Versão inglesa
├── images/         # Imagens e media
├── includes/       # Core (auth, db, functions, config)
├── js/             # JavaScript
├── templates/      # Modelos HTML
├── uploads/        # Uploads (proofs, chat, employees, contracts)
└── wiki/           # Documentação
```
