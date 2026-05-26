# Guia do Administrador

## Acesso

Acede a `http://localhost/ANGONUEVE/admin/` e faz login com a conta criada no `setup.php`.

## Painéis

### Dashboard
Visão geral com métricas: receitas, encomendas recentes, clientes novos, tickets de suporte.

### Clientes
- Lista, pesquisa, edição e bloqueio de clientes
- Aprovação de registos pendentes
- Visualização de histórico de encomendas e facturas

### Funcionários
- CRUD completo com nome, email, telefone, cargo, salário, foto
- Permissões granulares por funcionário (receitas, encomendas, suporte, etc.)
- Dashboard individual limitado ao papel atribuído

### Encomendas
- Lista com filtro por estado (pendente, processando, concluído, cancelado)
- Gestão de estados e notas internas

### Facturas
- Criação manual de facturas
- Visualização, PDF, registo de pagamentos
- Envio de email de notificação

### Contratos
- Gestão de contratos com datas de início/fim
- Upload de ficheiros
- Notificação automática 30 dias antes de expirar

### Receitas
- Gráficos mensais, por serviço, por método de pagamento
- Filtro por data
- Exportação PDF

### Suporte / Chat
- Visualização de todas as conversas de suporte
- Resposta como admin
- Pesquisa por sessão

### Definições
- **Gerais**: Nome da empresa, email, telefone, morada, redes sociais, API Gemini
- **Preços**: Preço do site IA (`ai_site_price`)
- **Aparência**: Logos, cores, about text, missão, visão
- **Slides**: Gestão de slides do hero

### Equipa
- Secção de equipa visível apenas quando admin está logado
- Carrossel com fotos, nomes e cargos

## Permissões de Funcionários

| Permissão | Descrição |
|---|---|
| `revenue` | Aceder a receitas |
| `orders` | Gerir encomendas |
| `invoices` | Gerir facturas |
| `contracts` | Gerir contratos |
| `clients` | Gerir clientes |
| `support` | Aceder ao suporte |
| `chat` | Ver conversas do chat |
| `settings` | Alterar definições |
| `employees` | Gerir funcionários |
| `newsletter` | Gerir newsletter |
| `messages` | Ver mensagens |
