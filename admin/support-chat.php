<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('support_chat');

$user = currentUser();
$ticketId = sanitize($_GET['ticket'] ?? '');
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Suporte - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .chat-layout { display: grid; grid-template-columns: 320px 1fr; gap: 0; min-height: calc(100vh - 140px); background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        .chat-sidebar { border-right: 1px solid var(--border); display: flex; flex-direction: column; }
        .chat-sidebar-header { padding: 16px; border-bottom: 1px solid var(--border); }
        .chat-sidebar-header h3 { font-size: 0.95rem; }
        .chat-ticket-list { flex: 1; overflow-y: auto; max-height: 520px; }
        .chat-ticket { padding: 12px 16px; cursor: pointer; transition: all 0.3s; border-bottom: 1px solid var(--border); }
        .chat-ticket:hover { background: rgba(255,255,255,0.03); }
        .chat-ticket.active { background: rgba(0,212,255,0.08); border-left: 3px solid var(--primary); }
        .chat-ticket .ticket-client { font-weight: 600; font-size: 0.85rem; }
        .chat-ticket .ticket-subject { font-size: 0.75rem; color: var(--text-muted); }
        .chat-ticket .ticket-preview { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-ticket .unread-count { background: var(--danger); color: #fff; border-radius: 10px; padding: 1px 7px; font-size: 0.65rem; font-weight: 600; margin-left: 6px; }
        .chat-main { display: flex; flex-direction: column; }
        .chat-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .chat-header h3 { font-size: 0.95rem; }
        .chat-header .client-info { font-size: 0.8rem; color: var(--text-muted); }
        .chat-messages { flex: 1; padding: 16px 20px; overflow-y: auto; max-height: 400px; display: flex; flex-direction: column; gap: 12px; }
        .chat-msg { max-width: 80%; padding: 10px 16px; border-radius: 12px; font-size: 0.88rem; line-height: 1.5; }
        .chat-msg.client { align-self: flex-start; background: rgba(0,230,118,0.1); border-bottom-left-radius: 4px; }
        .chat-msg.admin { align-self: flex-end; background: rgba(0,212,255,0.12); border-bottom-right-radius: 4px; }
        .chat-msg .msg-sender { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .chat-msg.client .msg-sender { color: var(--success); }
        .chat-msg.admin .msg-sender { color: var(--primary); text-align: right; }
        .chat-msg .msg-time { font-size: 0.6rem; color: var(--text-muted); margin-top: 4px; }
        .chat-input-area { padding: 12px 20px; border-top: 1px solid var(--border); display: flex; gap: 8px; align-items: flex-end; }
        .chat-input-area textarea { flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.02); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.88rem; outline: none; resize: none; min-height: 44px; max-height: 120px; }
        .chat-input-area textarea:focus { border-color: var(--primary); }
        .chat-input-area .btn-send { width: 44px; height: 44px; border-radius: 8px; background: var(--primary); color: #000; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.3s; }
        .chat-input-area .btn-send:hover { transform: scale(1.05); }
        .chat-input-area .btn-attach { width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: all 0.2s; flex-shrink:0; }
        .chat-input-area .btn-attach:hover { background: rgba(255,255,255,0.1); color: var(--text); }
        .chat-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); padding: 40px; text-align: center; }
        .chat-empty i { font-size: 3rem; margin-bottom: 16px; opacity: 0.3; }
        .chat-msg .msg-attachment { margin-top: 8px; display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: rgba(255,255,255,0.04); border-radius: 6px; font-size:0.82rem; }
        .chat-msg .msg-attachment a { color: var(--primary); text-decoration:none; }
        .chat-msg .msg-attachment a:hover { text-decoration:underline; }
        .chat-msg.client .msg-attachment a { color: var(--success); }
        .chat-msg.client .msg-attachment img, .chat-msg.admin .msg-attachment img { max-width:200px; border-radius:6px; cursor:pointer; }
        .chat-msg .msg-attachment i { font-size:1.1rem; }
        @media (max-width: 768px) { .chat-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-comments"></i> <span>Chat de Suporte</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content" style="padding:16px 24px;">
            <div class="chat-layout">
                <div class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <h3><i class="fas fa-ticket-alt"></i> Tickets <span id="unreadTotal" style="font-size:0.8rem;color:var(--text-muted);"></span></h3>
                        <small style="color:var(--text-muted);font-size:0.75rem;">Clique num ticket para responder</small>
                    </div>
                    <div class="chat-ticket-list" id="ticketList"></div>
                </div>
                <div class="chat-main">
                    <div class="chat-header">
                        <div>
                            <h3 id="chatTitle">Selecione um ticket</h3>
                            <span class="client-info" id="clientInfo"></span>
                        </div>
                        <button class="btn btn-sm btn-secondary" onclick="refreshTickets()" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-empty" id="chatEmpty">
                            <i class="fas fa-inbox"></i>
                            <h4>Suporte ANGONUEVE</h4>
                            <p>Selecione um ticket do lado esquerdo para ver a conversa.</p>
                        </div>
                    </div>
                    <div class="chat-input-area" id="chatInputArea" style="display:none;">
                        <input type="hidden" id="activeClientId">
                        <input type="hidden" id="activeClientName">
                        <input type="hidden" id="activeClientEmail">
                        <textarea id="msgInput" placeholder="Responder ao cliente..." rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg()}"></textarea>
                        <label class="btn-attach" title="Anexar ficheiro">
                            <i class="fas fa-paperclip"></i>
                            <input type="file" id="fileInput" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" style="display:none;" onchange="updateFileName()">
                        </label>
                        <button class="btn-send" onclick="sendMsg()"><i class="fas fa-reply"></i></button>
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
    if (!activeTicket) return;
    input.value = '';
    input.style.height = 'auto';

    const formData = new FormData();
    formData.append('ticket_id', activeTicket);
    formData.append('message', msg);
    formData.append('client_id', document.getElementById('activeClientId').value);
    formData.append('client_name', document.getElementById('activeClientName').value);
    formData.append('client_email', document.getElementById('activeClientEmail').value);
    formData.append('subject', document.getElementById('chatTitle').textContent);

    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    fileInput.value = '';
    document.getElementById('filePreview').style.display = 'none';

    try {
        await fetch(API + '?action=send', { method: 'POST', body: formData });
        await loadMessages();
        loadTickets();
    } catch(e) {}
}

async function loadMessages() {
    if (!activeTicket) return;
    try {
        const res = await fetch(API + '?action=history&ticket_id=' + encodeURIComponent(activeTicket));
        const data = await res.json();
        renderMessages(data.messages);
        if (data.messages.length > 0) {
            lastMsgId = Math.max(...data.messages.map(m => m.id));
            if (!document.getElementById('activeClientId').value) {
                const m = data.messages[0];
                document.getElementById('activeClientId').value = m.client_id || '';
                document.getElementById('activeClientName').value = m.client_name || '';
                document.getElementById('activeClientEmail').value = m.client_email || '';
                document.getElementById('clientInfo').textContent = (m.client_name || '') + ' • ' + (m.client_email || '');
            }
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
        const badge = document.getElementById('unreadTotal');
        if (data.unread > 0) {
            badge.innerHTML = '• <span style="color:var(--danger);">' + data.unread + ' não lidas</span>';
        } else {
            badge.innerHTML = '';
        }
        if (data.tickets) renderTickets(data.tickets);
    } catch(e) {}
}

async function loadTickets() {
    try {
        const res = await fetch(API + '?action=tickets');
        const data = await res.json();
        renderTickets(data.tickets || []);
        const unread = data.tickets ? data.tickets.reduce((sum, t) => sum + parseInt(t.unread_count||0), 0) : 0;
        document.getElementById('unreadTotal').innerHTML = unread > 0 ? '• <span style="color:var(--danger);">' + unread + ' não lidas</span>' : '';
    } catch(e) {}
}

function renderTickets(tickets) {
    const el = document.getElementById('ticketList');
    if (tickets.length === 0) {
        el.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:0.85rem;">Nenhum ticket de suporte</div>';
        return;
    }
    el.innerHTML = tickets.map(t => `
        <div class="chat-ticket ${t.ticket_id === activeTicket ? 'active' : ''}" onclick="openTicket('${t.ticket_id}','${escHtml(t.client_name)}','${escHtml(t.client_email)}','${t.client_id}','${escHtml(t.subject)}')">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div class="ticket-client">${escHtml(t.client_name)} ${parseInt(t.unread_count||0) > 0 ? '<span class="unread-count">' + t.unread_count + '</span>' : ''}</div>
                <div style="font-size:0.65rem;color:var(--text-muted);">${timeAgo(t.last_msg)}</div>
            </div>
            <div class="ticket-subject">${escHtml(t.subject)}</div>
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
            <div class="msg-sender">${m.sender_type === 'client' ? escHtml(m.client_name) : 'Você'}</div>
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
            <div class="msg-sender">${m.sender_type === 'client' ? escHtml(m.client_name) : 'Você'}</div>
            ${m.message ? escHtml(m.message) : ''}
            ${attachmentHtml(m)}
            <div class="msg-time">${formatTime(m.created_at)}</div>
        `;
        el.appendChild(div);
    });
    el.scrollTop = el.scrollHeight;
}

function openTicket(ticketId, clientName, clientEmail, clientId, subject) {
    activeTicket = ticketId;
    document.getElementById('chatTitle').textContent = subject;
    document.getElementById('clientInfo').textContent = clientName + ' • ' + clientEmail;
    document.getElementById('activeClientId').value = clientId;
    document.getElementById('activeClientName').value = clientName;
    document.getElementById('activeClientEmail').value = clientEmail;
    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatInputArea').style.display = 'flex';
    lastMsgId = 0;
    loadMessages();
    loadTickets();
    window.history.replaceState(null, '', '?ticket=' + ticketId);
    document.getElementById('msgInput').focus();
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

loadTickets();
if (activeTicket) { loadMessages(); }
pollTimer = setInterval(pollMessages, 3000);
</script>
</body>
</html>
