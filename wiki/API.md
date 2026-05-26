# Documentação da API

Todas as respostas são JSON. Os endpoints públicos não requerem autenticação salvo indicação contrária.

## `POST /api/generate-site.php`

Gera um site completo usando IA Gemini.

**Request:**
```json
{
  "prompt": "Quero um site para um restaurante angolano",
  "history": []
}
```

**Response (sucesso):**
```json
{
  "success": true,
  "html": "<!DOCTYPE html>...",
  "site_id": 1,
  "session_id": "abc123",
  "tokens_used": 450,
  "remaining": 2,
  "limit": 3
}
```

**Response (limite):**
```json
{
  "error": "Limite de geração atingido",
  "remaining": 0,
  "limit": 3
}
```

**Limites:** Máximo 3 sites por sessão/utilizador.

---

## `POST /api/create-site-invoice.php`

Requer login. Cria uma factura para descarregar o site gerado.

**Request:**
```json
{
  "site_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "invoice_id": 42,
  "invoice_no": "FT-2026-0042",
  "price": 15000
}
```

---

## `POST /api/chat.php`

Chatbot com IA. Mantém histórico por sessão.

**Request:**
```json
{
  "message": "Quanto custa um site?",
  "session_id": "abc123"
}
```

**Response:**
```json
{
  "success": true,
  "reply": "Os preços variam conforme o tipo de site...",
  "session_id": "abc123"
}
```

---

## `POST /api/newsletter.php`

Subscreve à newsletter.

**Request:**
```json
{
  "email": "cliente@email.com"
}
```

**Response:**
```json
{
  "success": true
}
```

---

## `POST /api/order.php`

Submete um pedido de orçamento.

**Request:**
```json
{
  "name": "João Silva",
  "email": "joao@email.com",
  "phone": "+244 900 000 000",
  "service": "criacao-sites",
  "description": "Preciso de um site institucional"
}
```

**Response:**
```json
{
  "success": true
}
```

---

## `GET /api/check-auth.php`

Verifica se o admin está logado (usado pela secção de equipa).

**Response:**
```json
{
  "isAdmin": true,
  "isLoggedIn": true,
  "userName": "Admin",
  "userRole": "admin"
}
```
