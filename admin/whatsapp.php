<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
checkSessionTimeout();
requirePermission('whatsapp');

$user = currentUser();
$action = $_GET['action'] ?? 'list';
$convId = intval($_GET['id'] ?? 0);

// Mark conversation as read
if ($action === 'read' && $convId) {
    db()->query("UPDATE whatsapp_conversations SET unread = 0 WHERE id = ?", [$convId]);
    header('Location: whatsapp.php?id=' . $convId);
    exit;
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $convId = intval($_POST['conv_id']);
    $message = sanitize($_POST['message'] ?? '');

    if ($convId && $message) {
        $conv = db()->fetchOne("SELECT * FROM whatsapp_conversations WHERE id = ?", [$convId]);
        if ($conv) {
            require_once __DIR__ . '/../api/whatsapp.php';
            $wa = new WhatsAppCloudAPI();
            $result = $wa->sendText($conv['client_phone'], $message);

            if (isset($result['success'])) {
                db()->insert('whatsapp_messages', [
                    'conversation_id' => $convId,
                    'wa_message_id' => $result['wa_message_id'] ?? '',
                    'direction' => 'outgoing',
                    'content' => $message,
                    'content_type' => 'text'
                ]);
                db()->query("UPDATE whatsapp_conversations SET last_message = ?, last_time = NOW() WHERE id = ?", [$message, $convId]);
                $success = 'Mensagem enviada!';
            } else {
                $error = 'Erro: ' . ($result['error'] ?? 'Falha ao enviar');
            }
        }
    }
}

$conversations = db()->fetchAll("SELECT * FROM whatsapp_conversations ORDER BY last_time DESC");
$selectedConv = null;
$messages = [];

if ($convId) {
    $selectedConv = db()->fetchOne("SELECT * FROM whatsapp_conversations WHERE id = ?", [$convId]);
    if ($selectedConv) {
        $messages = db()->fetchAll("SELECT * FROM whatsapp_messages WHERE conversation_id = ? ORDER BY created_at ASC", [$convId]);
        db()->query("UPDATE whatsapp_conversations SET unread = 0 WHERE id = ?", [$convId]);
    }
}

$totalUnread = db()->count('whatsapp_conversations', "unread > 0");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .wa-layout{display:grid;grid-template-columns:360px 1fr;gap:0;height:calc(100vh - 140px);border:1px solid var(--card-border);border-radius:12px;overflow:hidden;background:var(--card-bg)}
        .wa-sidebar{border-right:1px solid var(--card-border);overflow-y:auto;background:var(--dark)}
        .wa-sidebar-header{padding:16px 20px;border-bottom:1px solid var(--card-border);font-weight:600;font-size:0.95rem;display:flex;align-items:center;gap:8px}
        .wa-conv{padding:14px 20px;border-bottom:1px solid var(--card-border);cursor:pointer;transition:background 0.2s;display:flex;align-items:center;gap:12px}
        .wa-conv:hover{background:rgba(255,255,255,0.03)}
        .wa-conv.active{background:rgba(0,212,255,0.06);border-left:3px solid var(--secondary)}
        .wa-conv-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#0d1f3c,#162d50);border:1px solid var(--card-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;color:var(--secondary)}
        .wa-conv-info{flex:1;min-width:0}
        .wa-conv-name{font-size:0.88rem;font-weight:600;display:flex;align-items:center;gap:8px}
        .wa-conv-name .badge-unread{background:var(--secondary);color:var(--primary);font-size:0.65rem;padding:2px 8px;border-radius:10px;font-weight:700;margin-left:auto}
        .wa-conv-preview{font-size:0.78rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
        .wa-conv-time{font-size:0.7rem;color:var(--text-muted);flex-shrink:0}

        .wa-main{display:flex;flex-direction:column}
        .wa-main-header{padding:16px 24px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:12px}
        .wa-main-header h3{font-size:1rem}
        .wa-main-header p{font-size:0.78rem;color:var(--text-muted)}
        .wa-messages{flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:10px}
        .wa-msg{max-width:75%;padding:10px 16px;border-radius:12px;font-size:0.88rem;line-height:1.5;word-wrap:break-word}
        .wa-msg.incoming{align-self:flex-start;background:rgba(255,255,255,0.06);border:1px solid var(--card-border);border-bottom-left-radius:4px}
        .wa-msg.outgoing{align-self:flex-end;background:rgba(0,212,255,0.1);border:1px solid rgba(0,212,255,0.15);border-bottom-right-radius:4px}
        .wa-msg-time{font-size:0.65rem;color:var(--text-muted);margin-top:4px;text-align:right}
        .wa-msg .wa-msg-status{font-size:0.65rem;margin-left:4px}
        .wa-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--text-muted);gap:12px}
        .wa-empty i{font-size:3rem;opacity:0.3}
        .wa-input-area{padding:16px 24px;border-top:1px solid var(--card-border);display:flex;gap:12px}
        .wa-input-area textarea{flex:1;padding:12px 16px;border-radius:24px;border:1px solid var(--card-border);background:var(--dark);color:var(--text);font-size:0.88rem;font-family:'Inter',sans-serif;resize:none;outline:none;max-height:100px}
        .wa-input-area textarea:focus{border-color:var(--secondary)}
        .wa-input-area .btn-send{width:44px;height:44px;border-radius:50%;border:none;background:var(--secondary);color:var(--primary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:opacity 0.3s;flex-shrink:0}
        .wa-input-area .btn-send:hover{opacity:0.9}
        .wa-input-area .btn-send:disabled{opacity:0.3;cursor:not-allowed}
        .wa-not-configured{padding:40px;text-align:center}
        .wa-not-configured i{font-size:3rem;color:var(--text-muted);margin-bottom:16px;opacity:0.3}
        .wa-not-configured h3{margin-bottom:8px}
        .wa-not-configured p{color:var(--text-muted);max-width:500px;margin:0 auto 20px;line-height:1.6}
        .wa-not-configured .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;font-weight:600;text-decoration:none;background:var(--gradient-2);color:var(--primary)}
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fab fa-whatsapp" style="color:#25d366;"></i> <span>WhatsApp <?= $totalUnread > 0 ? "<span class=\"badge badge-danger\" style=\"font-size:0.7rem;padding:2px 10px;\">{$totalUnread}</span>" : '' ?></span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content" style="padding:0;">

<?php
require_once __DIR__ . '/../api/whatsapp.php';
$waTest = new WhatsAppCloudAPI();
if (!$waTest->isConfigured()):
?>
    <div class="wa-not-configured">
        <i class="fab fa-whatsapp"></i>
        <h3>WhatsApp Cloud API não configurada</h3>
        <p>Para começar a enviar e receber mensagens pelo sistema, configura as credenciais da API nos campos abaixo. 
        Precisas de um <strong>Token de Acesso Permanente</strong> e do <strong>ID do Número de Telefone</strong> da Meta Business Platform.</p>
        <a href="settings.php#whatsapp" class="btn"><i class="fas fa-cog"></i> Configurar Agora</a>
    </div>
<?php else: ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" style="margin:16px;"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success" style="margin:16px;"><?= $success ?></div>
    <?php endif; ?>

    <div class="wa-layout">
        <div class="wa-sidebar">
            <div class="wa-sidebar-header"><i class="fab fa-whatsapp" style="color:#25d366;"></i> Conversas <?= $totalUnread > 0 ? "<span class=\"badge badge-danger\" style=\"margin-left:auto;font-size:0.65rem;\">{$totalUnread}</span>" : '' ?></div>
            <?php if (empty($conversations)): ?>
                <div style="padding:40px;text-align:center;color:var(--text-muted);font-size:0.85rem;">
                    <i class="fab fa-whatsapp" style="font-size:2rem;display:block;margin-bottom:12px;opacity:0.3;"></i>
                    Nenhuma conversa ainda.
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $c): ?>
                <a href="whatsapp.php?id=<?= $c['id'] ?>" class="wa-conv <?= $c['id'] === $convId ? 'active' : '' ?>">
                    <div class="wa-conv-avatar"><i class="fas fa-user"></i></div>
                    <div class="wa-conv-info">
                        <div class="wa-conv-name">
                            <?= sanitize($c['client_name'] ?: $c['client_phone']) ?>
                            <?php if ($c['unread'] > 0): ?>
                                <span class="badge-unread"><?= $c['unread'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="wa-conv-preview"><?= sanitize(mb_substr($c['last_message'] ?? '', 0, 50)) ?></div>
                    </div>
                    <div class="wa-conv-time"><?= $c['last_time'] ? date('d/m', strtotime($c['last_time'])) : '' ?></div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="wa-main">
            <?php if ($selectedConv): ?>
                <div class="wa-main-header">
                    <div class="wa-conv-avatar" style="width:40px;height:40px;font-size:0.85rem;"><i class="fas fa-user"></i></div>
                    <div>
                        <h3><?= sanitize($selectedConv['client_name'] ?: $selectedConv['client_phone']) ?></h3>
                        <p><i class="fas fa-phone" style="font-size:0.7rem;"></i> <?= sanitize($selectedConv['client_phone']) ?></p>
                    </div>
                </div>
                <div class="wa-messages" id="waMessages">
                    <?php if (empty($messages)): ?>
                        <div style="text-align:center;color:var(--text-muted);padding:40px;font-size:0.85rem;">Nenhuma mensagem ainda. Envia a primeira mensagem!</div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="wa-msg <?= $msg['direction'] ?>">
                                <?= nl2br(sanitize($msg['content'])) ?>
                                <div class="wa-msg-time">
                                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                                    <?php if ($msg['direction'] === 'outgoing'): ?>
                                        <span class="wa-msg-status"><i class="fas fa-check<?= $msg['status'] === 'read' ? '-double' : '' ?>" style="color:<?= $msg['status'] === 'read' ? 'var(--secondary)' : 'var(--text-muted)' ?>"></i></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <form method="POST" class="wa-input-area" onsubmit="return validateForm()">
                    <input type="hidden" name="conv_id" value="<?= $convId ?>">
                    <textarea name="message" id="waInput" placeholder="Escreve a tua mensagem..." rows="1" required></textarea>
                    <button type="submit" name="send" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                </form>
            <?php else: ?>
                <div class="wa-empty">
                    <i class="fab fa-whatsapp"></i>
                    <p>Selecciona uma conversa para começar</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        function validateForm() {
            const input = document.getElementById('waInput');
            if (!input.value.trim()) return false;
            const btn = document.querySelector('.btn-send');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            return true;
        }
        var msgContainer = document.getElementById('waMessages');
        if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;
    </script>
</body>
</html>
