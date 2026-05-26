# Segurança

## Medidas Implementadas

### Base de Dados
- Todas as queries usam **PDO prepared statements** (excepto uma, já corrigida)
- Credenciais isoladas no `config.php` (fora do repositório)
- Charset `utf8mb4` para prevenir XSS via encoding

### Autenticação
- **Password hashing** com `password_hash(PASSWORD_DEFAULT)` (bcrypt/argon2)
- **Rate limiting**: 5 tentativas por IP a cada 15 minutos
- **Timeout de sessão**: 1 hora de inactividade
- **Regeneração de sessão** no login (`session_regenerate_id(true)`)
- **CSRF tokens** em todos os formulários (`random_bytes(32)`, `hash_equals()`)
- **Cookie de sessão**: HttpOnly, SameSite=Lax

### Headers HTTP (`.htaccess`)
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

### Protecção de Ficheiros
- `.htaccess` bloqueia acesso directo a `includes/`, `database/`
- `.sql`, `.md`, `.log` bloqueados
- Directory listing desligado globalmente
- `uploads/` bloqueado contra execução de PHP
- Uploads com nomes aleatórios (`bin2hex(random_bytes(8))`)

### Validação de Input
- `sanitize()` — `htmlspecialchars(strip_tags())` para output
- NIF validado com regex
- Email validado com `filter_var()`
- Telefone validado com regex

## Recomendações para Produção

1. **HTTPS obrigatório** — configura SSL no Apache
2. **Alterar `SITE_URL`** para o domínio real
3. **Remover `display_errors`** em produção (`config.php`)
4. **Backups automáticos** da base de dados
5. **Monitorizar logs** do Apache e PHP
6. **Manter PHP e MariaDB actualizados**
7. **Usar `.env`** em vez de `config.php` para credenciais

## Histórico de Vulnerabilidades Corrigidas

| Data | Vulnerabilidade | Ficheiro |
|---|---|---|
| 26/05/2026 | SQL Injection em `getRevenueStats()` | `includes/functions.php:236` |
| 26/05/2026 | Open Redirect | `client/login.php:7` |
| 26/05/2026 | DB connection details expostos | `includes/db.php:17` |
| 26/05/2026 | Upload sem protecção | `uploads/` |
| 26/05/2026 | Cookie de sessão sem HttpOnly/SameSite | `includes/auth.php:5` |
| 26/05/2026 | undefined method `lastInsertId()` (crash) | 6 ficheiros |
| 26/05/2026 | Duplicação `WHERE` em queries | `admin/orders.php`, `admin/chat-conversations.php` |
