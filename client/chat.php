<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);

$ticketId = sanitize($_GET['ticket'] ?? '');
if ($ticketId) {
    $exists = db()->fetchOne("SELECT id FROM support_chat WHERE ticket_id = ? AND client_id = ?", [$ticketId, $user['id']]);
    if (!$exists) $ticketId = '';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Suporte - ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }

        .chat-layout { display: grid; grid-template-columns: 300px 1fr; gap: 0; min-height: calc(100vh - 140px); background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        .chat-sidebar { border-right: 1px solid var(--border); display: flex; flex-direction: column; }
        .chat-sidebar-header { padding: 16px; border-bottom: 1px solid var(--border); }
        .chat-sidebar-header h3 { font-size: 0.95rem; }
        .chat-sidebar-header .btn-new { width: 100%; margin-top: 8px; }
        .chat-ticket-list { flex: 1; overflow-y: auto; max-height: 500px; }
        .chat-ticket { padding: 12px 16px; cursor: pointer; transition: all 0.3s; border-bottom: 1px solid var(--border); }
        .chat-ticket:hover { background: rgba(255,255,255,0.03); }
        .chat-ticket.active { background: rgba(0,230,118,0.08); border-left: 3px solid var(--success); }
        .chat-ticket .ticket-subject { font-weight: 600; font-size: 0.85rem; }
        .chat-ticket .ticket-preview { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-ticket .ticket-time { font-size: 0.65rem; color: var(--text-muted); }
        .chat-ticket .unread-badge { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success); margin-left: 6px; }

        .chat-main { display: flex; flex-direction: column; }
        .chat-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .chat-header h3 { font-size: 0.95rem; }
        .chat-header .ticket-id { font-size: 0.75rem; color: var(--text-muted); }
        .chat-messages { flex: 1; padding: 16px 20px; overflow-y: auto; max-height: 400px; display: flex; flex-direction: column; gap: 12px; }
        .chat-msg { max-width: 80%; padding: 10px 16px; border-radius: 12px; font-size: 0.88rem; line-height: 1.5; }
        .chat-msg.client { align-self: flex-end; background: rgba(0,230,118,0.15); border-bottom-right-radius: 4px; }
        .chat-msg.admin { align-self: flex-start; background: rgba(0,212,255,0.12); border-bottom-left-radius: 4px; }
        .chat-msg .msg-sender { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .chat-msg.client .msg-sender { color: var(--success); text-align: right; }
        .chat-msg.admin .msg-sender { color: var(--primary); }
        .chat-msg .msg-time { font-size: 0.6rem; color: var(--text-muted); margin-top: 4px; }
        .chat-msg.client .msg-time { text-align: right; }
        .chat-input-area { padding: 12px 20px; border-top: 1px solid var(--border); display: flex; gap: 8px; align-items: flex-end; }
        .chat-input-area textarea { flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.02); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.88rem; outline: none; resize: none; min-height: 44px; max-height: 120px; }
        .chat-input-area textarea:focus { border-color: var(--success); }
        .chat-input-area .btn-send { width: 44px; height: 44px; border-radius: 8px; background: var(--success); color: #000; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.3s; }
        .chat-input-area .btn-send:hover { transform: scale(1.05); }
        .chat-input-area .btn-attach { width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: all 0.2s; flex-shrink:0; }
        .chat-input-area .btn-attach:hover { background: rgba(255,255,255,0.1); color: var(--text); }
        .chat-msg .msg-attachment { margin-top: 8px; display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: rgba(255,255,255,0.04); border-radius: 6px; font-size:0.82rem; }
        .chat-msg .msg-attachment a { color: var(--primary); text-decoration:none; }
        .chat-msg .msg-attachment a:hover { text-decoration:underline; }
        .chat-msg.client .msg-attachment a { color: var(--success); }
        .chat-msg.client .msg-attachment img, .chat-msg.admin .msg-attachment img { max-width:200px; border-radius:6px; cursor:pointer; }
        .chat-msg .msg-attachment i { font-size:1.1rem; }
        .chat-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); padding: 40px; text-align: center; }
        .chat-empty i { font-size: 3rem; margin-bottom: 16px; opacity: 0.3; }
        .typing-indicator { align-self: flex-start; padding: 8px 16px; background: rgba(0,212,255,0.08); border-radius: 12px; font-size: 0.8rem; color: var(--text-muted); display: none; }
        .typing-indicator.show { display: block; }
        @media (max-width: 768px) { .chat-layout { grid-template-columns: 1fr; } .chat-sidebar { display: none; } .chat-sidebar.show-mobile { display: flex; } }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar client-sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-user-circle"></i>
                <span>Cliente</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="services.php"><i class="fas fa-concierge-bell"></i> Serviços</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Encomendas</a>
                <a href="invoices.php"><i class="fas fa-file-invoice"></i> Facturas</a>
                <a href="chat.php" class="active"><i class="fas fa-comments"></i> Chat Suporte</a>
                <hr>
                <a href="../index.html" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-comments"></i> <span>Chat de Suporte</span></div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
            <div class="admin-content" style="padding:16px 24px;">
                <div class="chat-layout">
                    <div class="chat-sidebar">
                        <div class="chat-sidebar-header">
                            <h3><i class="fas fa-ticket-alt"></i> Meus Tickets</h3>
                            <button class="btn btn-success btn-sm btn-new" onclick="newTicket()"><i class="fas fa-plus"></i> Novo Ticket</button>
                        </div>
                        <div class="chat-ticket-list" id="ticketList"></div>
                    </div>

                    <div class="chat-main">
                        <div class="chat-header">
                            <div>
                                <h3 id="chatTitle">Selecione um ticket</h3>
                                <span class="ticket-id" id="ticketIdDisplay"></span>
                            </div>
                            <button class="btn btn-sm btn-secondary" onclick="refreshTickets()" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
                        </div>

                        <div class="chat-messages" id="chatMessages">
                            <div class="chat-empty" id="chatEmpty">
                                <i class="fas fa-comment-dots"></i>
                                <h4>Suporte ANGONUEVE</h4>
                                <p>Selecione um ticket ou crie um novo para iniciar uma conversa.</p>
                                <button class="btn btn-success" onclick="newTicket()" style="margin-top:16px;"><i class="fas fa-plus"></i> Novo Ticket</button>
                            </div>
                        </div>

                        <div class="chat-input-area" id="chatInputArea" style="display:none;">
                            <textarea id="msgInput" placeholder="Escreva a sua mensagem..." rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg()}"></textarea>
                            <label class="btn-attach" title="Anexar ficheiro (JPG, PNG, GIF, PDF)">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="fileInput" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" style="display:none;" onchange="updateFileName()">
                            </label>
                            <button class="btn-send" onclick="sendMsg()"><i class="fas fa-paper-plane"></i></button>
                        </div>
                        <div id="filePreview" style="display:none;padding:4px 20px 0;font-size:0.78rem;color:var(--text-muted);"><i class="fas fa-paperclip"></i> <span id="fileName"></span> <a href="#" onclick="removeFile();return false" style="color:var(--danger);margin-left:6px;"><i class="fas fa-times"></i></a></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
const API = '../api/chat-support.php';
let activeTicket = '<?= $ticketId ?>';
let lastMsgId = 0;
let pollTimer = null;
let currentSubject = '';

function newTicket() {
    const subj = prompt('Assunto do ticket:', 'Preciso de ajuda');
    if (!subj || !subj.trim()) return;
    currentSubject = subj.trim();
    activeTicket = '';
    document.getElementById('chatTitle').textContent = 'Novo Ticket: ' + currentSubject;
    document.getElementById('ticketIdDisplay').textContent = 'A enviar primeira mensagem...';
    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatInputArea').style.display = 'flex';
    document.getElementById('chatMessages').innerHTML = '';
    document.getElementById('msgInput').focus();
    lastMsgId = 0;
}

function updateFileName() {
    const input = document.getElementById('fileInput');
    const preview = document.getElementById('filePreview');
    const label = document.getElementById('fileName');
    if (input.files.length > 0) {
        label.textContent = input.files[0].name;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
}

async function sendMsg() {
    const input = document.getElementById('msgInput');
    const msg = input.value.trim();
    const fileInput = document.getElementById('fileInput');
    if (!msg && !fileInput.files.length) return;

    const formData = new FormData();
    formData.append('message', msg);
    formData.append('subject', currentSubject);

    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    if (activeTicket) {
        formData.append('ticket_id', activeTicket);
    }

    input.value = '';
    input.style.height = 'auto';
    fileInput.value = '';
    document.getElementById('filePreview').style.display = 'none';

    try {
        const res = await fetch(API + '?action=send', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success && data.ticket_id) {
            if (!activeTicket) {
                activeTicket = data.ticket_id;
                document.getElementById('ticketIdDisplay').textContent = data.ticket_id;
                loadTickets();
            }
            await loadMessages();
        }
    } catch(e) {
        console.error('Erro ao enviar:', e);
    }
}

async function loadMessages() {
    if (!activeTicket) return;
    try {
        const res = await fetch(API + '?action=history&ticket_id=' + encodeURIComponent(activeTicket));
        const data = await res.json();
        renderMessages(data.messages);
        if (data.messages.length > 0) {
            lastMsgId = Math.max(...data.messages.map(m => m.id));
        }
    } catch(e) {}
}

async function pollMessages() {
    if (!activeTicket) return;
    try {
        const res = await fetch(API + '?action=poll&ticket_id=' + encodeURIComponent(activeTicket) + '&last_id=' + lastMsgId);
        const data = await res.json();
        if (data.messages && data.messages.length > 0) {
            appendMessages(data.messages);
            lastMsgId = Math.max(...data.messages.map(m => m.id));
        }
    } catch(e) {}
}

async function loadTickets() {
    try {
        const res = await fetch(API + '?action=tickets');
        const data = await res.json();
        renderTickets(data.tickets || []);
    } catch(e) {}
}

function renderTickets(tickets) {
    const el = document.getElementById('ticketList');
    if (tickets.length === 0) {
        el.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:0.85rem;">Nenhum ticket ainda</div>';
        return;
    }
    el.innerHTML = tickets.map(t => `
        <div class="chat-ticket ${t.ticket_id === activeTicket ? 'active' : ''}" onclick="openTicket('${t.ticket_id}','${escHtml(t.subject)}')">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div class="ticket-subject">${escHtml(t.subject)} ${parseInt(t.unread_count||0) > 0 ? '<span class="unread-badge"></span>' : ''}</div>
                <div class="ticket-time">${timeAgo(t.last_msg)}</div>
            </div>
            <div class="ticket-preview">${escHtml((t.last_message||'').substring(0, 60))}${(t.last_message||'').length > 60 ? '...' : ''}</div>
        </div>
    `).join('');
}

function attachmentHtml(m) {
    if (!m.attachment) return '';
    const ext = m.attachment.split('.').pop().toLowerCase();
    if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
        return `<div class="msg-attachment"><img src="../uploads/chat/${m.attachment}" alt="Anexo" onclick="window.open(this.src,'_blank')"></div>`;
    }
    return `<div class="msg-attachment"><i class="fas fa-paperclip"></i> <a href="../uploads/chat/${m.attachment}" target="_blank">${escHtml(m.attachment)}</a></div>`;
}

function renderMessages(messages) {
    const el = document.getElementById('chatMessages');
    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatInputArea').style.display = 'flex';

    el.innerHTML = messages.map(m => `
        <div class="chat-msg ${m.sender_type}">
            <div class="msg-sender">${m.sender_type === 'client' ? 'Você' : 'Suporte'}</div>
            ${m.message ? escHtml(m.message) : ''}
            ${attachmentHtml(m)}
            <div class="msg-time">${formatTime(m.created_at)}</div>
        </div>
    `).join('');
    el.scrollTop = el.scrollHeight;
}

function appendMessages(messages) {
    const el = document.getElementById('chatMessages');
    messages.forEach(m => {
        const div = document.createElement('div');
        div.className = 'chat-msg ' + m.sender_type;
        div.innerHTML = `
            <div class="msg-sender">${m.sender_type === 'client' ? 'Você' : 'Suporte'}</div>
            ${m.message ? escHtml(m.message) : ''}
            ${attachmentHtml(m)}
            <div class="msg-time">${formatTime(m.created_at)}</div>
        `;
        el.appendChild(div);
    });
    el.scrollTop = el.scrollHeight;
}

function openTicket(ticketId, subject) {
    activeTicket = ticketId;
    currentSubject = subject;
    document.getElementById('chatTitle').textContent = subject;
    document.getElementById('ticketIdDisplay').textContent = ticketId;
    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatInputArea').style.display = 'flex';
    lastMsgId = 0;
    loadMessages();
    loadTickets();
    window.history.replaceState(null, '', '?ticket=' + ticketId);
}

function refreshTickets() {
    loadTickets();
    if (activeTicket) loadMessages();
}

function escHtml(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function formatTime(dt) {
    if (!dt) return '';
    const d = new Date(dt.replace(' ', 'T') + 'Z');
    return d.toLocaleString('pt-PT', {day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit'});
}

function timeAgo(dt) {
    if (!dt) return '';
    const now = new Date();
    const d = new Date(dt.replace(' ', 'T') + 'Z');
    const sec = Math.floor((now - d) / 1000);
    if (sec < 60) return 'agora';
    if (sec < 3600) return Math.floor(sec/60) + 'min';
    if (sec < 86400) return Math.floor(sec/3600) + 'h';
    return Math.floor(sec/86400) + 'd';
}

// Init
loadTickets();
if (activeTicket) {
    loadMessages();
    pollTimer = setInterval(pollMessages, 3000);
}
</script>
</body>
</html>
