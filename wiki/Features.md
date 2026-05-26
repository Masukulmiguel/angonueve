# Funcionalidades

## Site Principal

- Design moderno escuro com gradientes
- Hero com slideshow
- Serviços com ícones e descrições
- Modelos de site (6 templates pré-construídos)
- Equipa (visível apenas para admin)
- Testemunhos
- FAQ interativo
- Formulário de orçamento
- Chatbot com IA
- Newsletter
- Modo escuro consistente

## Gerador de Sites com IA

- Interface com painel lateral (prompt) + pré-visualização
- Chat histórico com as interações
- Sugestões de prompt pré-definidas
- Pré-visualização responsiva (desktop, tablet, mobile)
- Botão refresh e download
- Sistema de 3 tentativas por sessão/utilizador
- Geração paga via factura (Kz 15.000 configurável)
- Suporte PT e EN

## Área do Cliente

- Registo com aprovação manual
- Login com rate limiting (5 tentativas / 15min)
- Dashboard com resumo
- Perfil editável
- Facturas com estado de pagamento
- Suporte via chat
- Download de sites gerados (após pagamento)

## API Pública

| Endpoint | Método | Descrição |
|---|---|---|
| `/api/generate-site.php` | POST | Gera site com IA |
| `/api/create-site-invoice.php` | POST | Cria factura para download |
| `/api/chat.php` | POST | Chatbot IA |
| `/api/newsletter.php` | POST | Subscrição newsletter |
| `/api/order.php` | POST | Submeter orçamento |
| `/api/check-auth.php` | GET | Verifica sessão admin |
| `/api/track.php` | POST | Tracking de visitas |

## Segurança

- Prepared statements (PDO)
- CSRF tokens em todos os formulários
- Password hashing com bcrypt/argon2
- Rate limiting no login
- Sessão com timeout (1h)
- Headers de segurança (X-Frame-Options, X-Content-Type-Options, etc.)
- Bloqueio de diretorias sensíveis via .htaccess
- Uploads protegidos contra execução de scripts
