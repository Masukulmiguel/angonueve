<?php
require_once __DIR__ . '/../includes/auth.php';
$user = currentUser();
$isLoggedIn = $user && $user['role'] === 'client';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Create your website with AI - Describe what you need and ANGONUEVE generates the site automatically.">
<title>Create Website with AI - ANGONUEVE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="icon" type="image/png" href="../images/logo.png">
<style>
.builder-layout{display:grid;grid-template-columns:380px 1fr;height:calc(100vh - 64px);margin-top:64px;overflow:hidden}
.builder-sidebar{background:rgba(10,22,40,0.98);border-right:1px solid var(--card-border);display:flex;flex-direction:column;overflow:hidden}
.builder-sidebar .sidebar-header{padding:20px 24px;border-bottom:1px solid var(--card-border);flex-shrink:0}
.builder-sidebar .sidebar-header h2{font-size:1.1rem;margin:0;display:flex;align-items:center;gap:10px}
.builder-sidebar .sidebar-header h2 i{color:var(--secondary)}
.builder-sidebar .sidebar-header p{font-size:0.78rem;color:var(--text-muted);margin:4px 0 0}
.prompt-section{padding:16px 24px;border-bottom:1px solid var(--card-border);flex-shrink:0}
.prompt-section textarea{width:100%;min-height:100px;padding:12px;border-radius:12px;border:1px solid var(--card-border);background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.88rem;resize:vertical;outline:none;transition:border-color 0.3s;box-sizing:border-box}
.prompt-section textarea:focus{border-color:var(--secondary)}
.prompt-section textarea::placeholder{color:var(--text-muted)}
.prompt-section .btn-generate{width:100%;padding:12px;border-radius:10px;border:none;background:var(--gradient-2);color:var(--primary);font-weight:600;font-size:0.9rem;cursor:pointer;margin-top:10px;transition:all 0.3s;display:flex;align-items:center;justify-content:center;gap:8px}
.prompt-section .btn-generate:hover{opacity:0.9;transform:translateY(-1px)}
.prompt-section .btn-generate:disabled{opacity:0.5;cursor:not-allowed;transform:none}
.prompt-section .btn-generate .spinner{display:none;width:16px;height:16px;border:2px solid var(--primary);border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite}
.prompt-section .btn-generate.loading .spinner{display:inline-block}
.prompt-section .btn-generate.loading .btn-text{display:none}
@keyframes spin{to{transform:rotate(360deg)}}
.examples{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
.examples span{padding:4px 12px;border-radius:50px;border:1px solid var(--card-border);font-size:0.72rem;color:var(--text-muted);cursor:pointer;transition:all 0.3s}
.examples span:hover{border-color:var(--secondary);color:var(--secondary);background:rgba(0,212,255,0.05)}
.chat-history{flex:1;overflow-y:auto;padding:16px 24px;display:flex;flex-direction:column;gap:12px}
.chat-history .msg{padding:12px 16px;border-radius:12px;font-size:0.85rem;line-height:1.6}
.chat-history .msg.user{background:rgba(0,212,255,0.06);border:1px solid rgba(0,212,255,0.1);margin-left:20px}
.chat-history .msg.assistant{background:rgba(255,255,255,0.03);border:1px solid var(--card-border);margin-right:20px}
.chat-history .msg .msg-label{font-size:0.7rem;color:var(--text-muted);margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
.chat-history .msg.user .msg-label{color:var(--secondary)}
.preview-section{display:flex;flex-direction:column;background:var(--primary);overflow:hidden}
.preview-toolbar{display:flex;align-items:center;justify-content:space-between;padding:10px 20px;background:rgba(10,22,40,0.95);border-bottom:1px solid var(--card-border);flex-shrink:0}
.preview-toolbar .device-btns{display:flex;gap:6px}
.preview-toolbar .device-btns button{padding:6px 12px;border-radius:6px;border:1px solid var(--card-border);background:transparent;color:var(--text-muted);cursor:pointer;font-size:0.78rem;transition:all 0.3s}
.preview-toolbar .device-btns button.active{border-color:var(--secondary);color:var(--secondary);background:rgba(0,212,255,0.06)}
.preview-toolbar .device-btns button:hover{border-color:var(--secondary);color:var(--secondary)}
.preview-toolbar .actions{display:flex;gap:8px}
.preview-toolbar .actions button,.preview-toolbar .actions a{padding:8px 16px;border-radius:8px;border:none;font-weight:600;font-size:0.82rem;cursor:pointer;text-decoration:none;transition:all 0.3s;display:flex;align-items:center;gap:6px}
.btn-download{background:var(--gradient-2);color:var(--primary)}
.btn-download:hover{opacity:0.9}
.btn-download:disabled{opacity:0.4;cursor:not-allowed}
.btn-refresh{background:rgba(255,255,255,0.05);border:1px solid var(--card-border)!important;color:var(--text-muted)}
.btn-refresh:hover{background:rgba(255,255,255,0.1)}
.preview-frame{flex:1;background:#fff;position:relative;overflow:hidden}
.preview-frame iframe{width:100%;height:100%;border:none;display:block}
.preview-frame .empty-state{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-muted);padding:40px;text-align:center}
.preview-frame .empty-state i{font-size:3.5rem;margin-bottom:16px;opacity:0.3}
.preview-frame .empty-state h3{font-size:1.3rem;margin-bottom:8px;color:var(--text)}
.preview-frame .empty-state p{max-width:360px;font-size:0.9rem}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:10000;align-items:center;justify-content:center}
.modal-overlay.active{display:flex}
.modal{background:var(--primary);border:1px solid var(--card-border);border-radius:16px;padding:32px;max-width:440px;width:90%;text-align:center}
.modal i{font-size:3rem;color:var(--secondary);margin-bottom:16px;display:block}
.modal h3{font-size:1.2rem;margin-bottom:8px}
.modal p{color:var(--text-muted);font-size:0.88rem;margin-bottom:24px;line-height:1.6}
.modal .btn{width:100%;padding:12px;border-radius:10px;font-weight:600;font-size:0.9rem;cursor:pointer;text-decoration:none;display:block;transition:all 0.3s;margin-bottom:10px;border:none}
.modal .btn-primary{background:var(--gradient-2);color:var(--primary)}
.modal .btn-primary:hover{opacity:0.9}
.modal .btn-secondary{background:rgba(255,255,255,0.05);border:1px solid var(--card-border);color:var(--text)}
.modal .btn-secondary:hover{background:rgba(255,255,255,0.1)}
@media(max-width:768px){
.builder-layout{grid-template-columns:1fr;grid-template-rows:auto 1fr;height:calc(100vh - 60px);margin-top:60px}
.builder-sidebar{max-height:50vh}
.preview-toolbar{flex-wrap:wrap;gap:8px}
}
</style>
</head>
<body>
<a href="#previewArea" class="skip-link">Skip to preview</a>
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="index.html" class="nav-logo"><img src="../images/logo.png" alt="ANGONUEVE"><span>ANGONUEVE</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="../criar-site.php" class="lang-switch" title="Versão Portuguesa">PT</a></li>
            <li><a href="index.html">Home</a></li>
            <li><a href="services.html">Services</a></li>
            <li><a href="criar-site.php" class="active">Create Site</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="../client/login.php" class="btn btn-secondary"><i class="fas fa-user-circle"></i> Client Area</a></li>
            <li><a href="orcamento.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Quote</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
</nav>
<div class="nav-overlay" id="navOverlay"></div>

<div class="builder-layout">
    <aside class="builder-sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-magic"></i> Create Site with AI</h2>
            <p>Describe your ideal website for your business.</p>
        </div>
        <div class="prompt-section">
            <textarea id="promptInput" placeholder="E.g.: I want a modern business website with services, team and contact sections. Dark design with blue accents."></textarea>
            <div class="examples">
                <span data-prompt="I want a modern business website with services, team and contact sections">Business</span>
                <span data-prompt="I want a creative portfolio for a photographer with gallery and testimonials">Portfolio</span>
                <span data-prompt="I want a landing page for selling online courses">Landing Page</span>
                <span data-prompt="I want a restaurant website with menu, gallery and reservations">Restaurant</span>
                <span data-prompt="I want a simple e-commerce store with products and cart">Online Store</span>
                <span data-prompt="I want a personal blog with posts, categories and sidebar">Blog</span>
            </div>
            <button class="btn-generate" id="btnGenerate" onclick="generateSite()">
                <span class="spinner"></span>
                <span class="btn-text"><i class="fas fa-wand-magic-sparkles"></i> Generate Site</span>
            </button>
        </div>
        <div class="chat-history" id="chatHistory"></div>
    </aside>
    <section class="preview-section" id="previewArea">
        <div class="preview-toolbar">
            <div class="device-btns">
                <button class="active" data-device="desktop" onclick="setDevice('desktop')" title="Desktop"><i class="fas fa-desktop"></i></button>
                <button data-device="tablet" onclick="setDevice('tablet')" title="Tablet"><i class="fas fa-tablet-alt"></i></button>
                <button data-device="mobile" onclick="setDevice('mobile')" title="Mobile"><i class="fas fa-mobile-alt"></i></button>
            </div>
            <div class="actions">
                <button class="btn-refresh" onclick="refreshPreview()" title="Refresh preview"><i class="fas fa-sync-alt"></i></button>
                <button class="btn-download" id="btnDownload" onclick="handleDownload()"><i class="fas fa-download"></i> Download</button>
            </div>
        </div>
        <div class="preview-frame" id="previewFrame">
            <div class="empty-state" id="emptyState">
                <i class="fas fa-wand-magic-sparkles"></i>
                <h3>Ready to create your site?</h3>
                <p>Describe the website you need in the panel on the left and click "Generate Site". Our AI creates the site automatically for you!</p>
            </div>
            <div class="loading-state" id="loadingState" style="display:none;position:absolute;inset:0;display:none;flex-direction:column;align-items:center;justify-content:center;background:var(--primary);z-index:10;">
                <div style="width:48px;height:48px;border:3px solid var(--card-border);border-top-color:var(--secondary);border-radius:50%;animation:builderSpin 0.8s linear infinite;margin-bottom:20px;"></div>
                <h3 style="font-size:1.1rem;margin-bottom:6px">Generating your site...</h3>
                <p style="color:var(--text-muted);font-size:0.85rem;">The AI is creating your site based on your request. This may take a few seconds.</p>
            </div>
            <style>@keyframes builderSpin{to{transform:rotate(360deg)}}.loading-state{display:none!important}.loading-state.active{display:flex!important}</style>
            <iframe id="previewIframe" srcdoc="" style="display:none;"></iframe>
        </div>
    </section>
</div>

<!-- Login modal -->
<div class="modal-overlay" id="loginModal">
    <div class="modal">
        <i class="fas fa-user-lock"></i>
        <h3>Login to Download</h3>
        <p>To download the generated site, you need an account. If you don't have one, you can register for free.</p>
        <a href="../client/login.php?redirect=<?= urlencode('en/criar-site.php') ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="../client/register.php?redirect=<?= urlencode('en/criar-site.php') ?>" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Create Account</a>
    </div>
</div>

<!-- Payment modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal">
        <i class="fas fa-shopping-cart"></i>
        <h3>Download Site</h3>
        <p id="paymentInfo">The generated site can be yours with a one-time payment. After payment confirmation, you can download the complete HTML file.</p>
        <button class="btn btn-primary" onclick="proceedToPayment()"><i class="fas fa-credit-card"></i> Pay & Download</button>
        <button class="btn btn-secondary" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i> Not now</button>
    </div>
</div>

<!-- Download ready modal -->
<div class="modal-overlay" id="downloadModal">
    <div class="modal">
        <i class="fas fa-check-circle" style="color:#00e676"></i>
        <h3>Site Ready to Download!</h3>
        <p>Your site has been generated successfully. Click below to download the HTML file.</p>
        <a href="#" class="btn btn-primary" id="downloadLink"><i class="fas fa-download"></i> Download Site</a>
        <button class="btn btn-secondary" onclick="closeModal('downloadModal')">Close</button>
    </div>
</div>

<script src="../js/script.js"></script>
<script>
let currentHtml = '';
let currentSiteId = 0;
let chatHistory = [];
let isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
let hasPaid = false;

function generateSite() {
    const input = document.getElementById('promptInput');
    const btn = document.getElementById('btnGenerate');
    const prompt = input.value.trim();
    if (!prompt) { input.focus(); return; }

    btn.classList.add('loading');
    btn.disabled = true;
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('loadingState').classList.add('active');
    document.getElementById('previewIframe').style.display = 'none';

    chatHistory.push({ role: 'user', content: prompt });
    renderChat();

    fetch('../api/generate-site.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt, history: chatHistory.slice(0, -1) })
    })
    .then(r => r.json().catch(() => ({ error: 'Invalid server response (HTTP ' + r.status + ')' })))
    .then(data => {
        btn.classList.remove('loading');
        btn.disabled = false;
        document.getElementById('loadingState').classList.remove('active');
        if (data.success && data.html) {
            currentHtml = data.html;
            currentSiteId = data.site_id || 0;
            chatHistory.push({ role: 'assistant', content: '✅ Site generated successfully! You can preview it below.' });
            renderChat();
            showPreview(data.html);
        } else {
            const err = data.error || 'Unknown error generating the site';
            chatHistory.push({ role: 'assistant', content: '❌ ' + err });
            renderChat();
        }
    })
    .catch(err => {
        btn.classList.remove('loading');
        btn.disabled = false;
        document.getElementById('loadingState').classList.remove('active');
        const msg = err.message || 'Connection error. Check your internet.';
        chatHistory.push({ role: 'assistant', content: '❌ ' + msg });
        renderChat();
    });
}

function renderChat() {
    const container = document.getElementById('chatHistory');
    container.innerHTML = chatHistory.map(msg =>
        '<div class="msg ' + msg.role + '"><div class="msg-label">' + (msg.role === 'user' ? 'You' : 'ANGONUEVE AI') + '</div>' + msg.content + '</div>'
    ).join('');
    container.scrollTop = container.scrollHeight;
}

function showPreview(html) {
    document.getElementById('emptyState').style.display = 'none';
    const iframe = document.getElementById('previewIframe');
    iframe.style.display = 'block';
    iframe.srcdoc = html;
    document.getElementById('btnDownload').disabled = false;
}

function refreshPreview() {
    if (currentHtml) {
        const iframe = document.getElementById('previewIframe');
        iframe.srcdoc = currentHtml;
    }
}

function setDevice(device) {
    const iframe = document.getElementById('previewIframe');
    const widths = { desktop: '100%', tablet: '768px', mobile: '375px' };
    iframe.style.maxWidth = widths[device];
    iframe.style.margin = '0 auto';
    iframe.style.display = 'block';
    document.querySelectorAll('.device-btns button').forEach(b => b.classList.remove('active'));
    document.querySelector('[data-device="' + device + '"]').classList.add('active');
}

function handleDownload() {
    if (!currentHtml) return;
    if (!isLoggedIn) {
        document.getElementById('loginModal').classList.add('active');
        return;
    }
    if (!hasPaid) {
        document.getElementById('paymentModal').classList.add('active');
        return;
    }
    doDownload();
}

function proceedToPayment() {
    closeModal('paymentModal');
    fetch('../api/create-site-invoice.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ site_id: currentSiteId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.invoice_id) {
            window.location.href = '../client/invoice-view.php?id=' + data.invoice_id + '&pay=1';
        } else {
            alert('Error creating invoice. Please try again.');
        }
    })
    .catch(() => alert('Connection error.'));
}

function doDownload() {
    if (!currentHtml) return;
    const blob = new Blob([currentHtml], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.getElementById('downloadLink');
    a.href = url;
    a.download = 'site-angonueve.html';
    document.getElementById('downloadModal').classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.examples span').forEach(el => {
    el.addEventListener('click', () => {
        document.getElementById('promptInput').value = el.dataset.prompt;
        document.getElementById('promptInput').focus();
    });
});

document.getElementById('promptInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); generateSite(); }
});
</script>
<div id="pageLoader" class="page-loader"><div class="loader-spinner"><i class="fas fa-circle-notch fa-spin"></i></div></div>
</body>
</html>
