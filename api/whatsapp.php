<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

class WhatsAppCloudAPI {
    private $token;
    private $phoneNumberId;
    private $apiVersion;
    private $baseUrl;

    public function __construct() {
        $this->token = getSetting('whatsapp_api_token', '');
        $this->phoneNumberId = getSetting('whatsapp_phone_number_id', '');
        $this->apiVersion = getSetting('whatsapp_api_version', 'v22.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
    }

    public function isConfigured() {
        return !empty($this->token) && !empty($this->phoneNumberId);
    }

    public function sendText($to, $text) {
        if (!$this->isConfigured()) return ['error' => 'API não configurada'];

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $text]
        ];

        return $this->post($data);
    }

    public function sendTemplate($to, $templateName, $lang = 'pt', $components = []) {
        if (!$this->isConfigured()) return ['error' => 'API não configurada'];

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $lang],
            ]
        ];

        if (!empty($components)) {
            $data['template']['components'] = $components;
        }

        return $this->post($data);
    }

    public function sendOrderNotification($to, $orderId, $customerName, $serviceName) {
        $msg = "🆕 *Nova Encomenda #{$orderId}*\n\n";
        $msg .= "Cliente: {$customerName}\n";
        $msg .= "Serviço: {$serviceName}\n\n";
        $msg .= "Acede ao painel para mais detalhes.";
        return $this->sendText($to, $msg);
    }

    public function sendContactNotification($to, $name, $email, $phone, $subject, $message) {
        $msg = "📬 *Nova Mensagem de Contacto*\n\n";
        $msg .= "Nome: {$name}\n";
        $msg .= "Email: {$email}\n";
        $msg .= "Tel: {$phone}\n";
        $msg .= "Assunto: {$subject}\n";
        $msg .= "Mensagem: {$message}";
        return $this->sendText($to, $msg);
    }

    public function sendTemplateOrderNotification($to, $name, $templateName) {
        $msg = "🎨 *Novo Pedido de Template*\n\n";
        $msg .= "Cliente: {$name}\n";
        $msg .= "Template: {$templateName}\n\n";
        $msg .= "Acede ao painel para mais detalhes.";
        return $this->sendText($to, $msg);
    }

    public function markAsRead($messageId) {
        if (!$this->isConfigured()) return ['error' => 'API não configurada'];

        $data = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId
        ];

        return $this->post($data);
    }

    public function verifyWebhook($mode, $token, $verifyToken) {
        if ($mode === 'subscribe' && $token === $verifyToken) {
            return true;
        }
        return false;
    }

    public function processWebhook($payload) {
        $entries = $payload['entry'] ?? [];
        $results = [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $messages = $value['messages'] ?? [];
                $contacts = $value['contacts'] ?? [];

                foreach ($messages as $msg) {
                    $from = $msg['from'] ?? '';
                    $waMsgId = $msg['id'] ?? '';
                    $type = $msg['type'] ?? 'text';
                    $content = '';

                    if ($type === 'text') {
                        $content = $msg['text']['body'] ?? '';
                    } elseif ($type === 'interactive') {
                        $interactive = $msg['interactive'] ?? [];
                        $buttonReply = $interactive['button_reply'] ?? [];
                        $listReply = $interactive['list_reply'] ?? [];
                        $content = $buttonReply['title'] ?? $listReply['title'] ?? '';
                    }

                    $name = '';
                    foreach ($contacts as $c) {
                        if ($c['wa_id'] === $from) {
                            $profile = $c['profile'] ?? [];
                            $name = $profile['name'] ?? '';
                        }
                    }

                    $results[] = [
                        'from' => $from,
                        'wa_message_id' => $waMsgId,
                        'type' => $type,
                        'content' => $content,
                        'name' => $name
                    ];

                    $this->storeIncomingMessage($from, $name, $content, $waMsgId);
                }
            }
        }

        return $results;
    }

    private function storeIncomingMessage($phone, $name, $content, $waMsgId) {
        try {
            $conv = db()->fetchOne("SELECT id FROM whatsapp_conversations WHERE client_phone = ?", [$phone]);
            if ($conv) {
                $convId = $conv['id'];
                db()->query("UPDATE whatsapp_conversations SET last_message = ?, last_time = NOW(), unread = unread + 1, client_name = COALESCE(NULLIF(?, ''), client_name) WHERE id = ?", [$content, $name, $convId]);
            } else {
                $convId = db()->insert('whatsapp_conversations', [
                    'client_phone' => $phone,
                    'client_name' => $name ?: 'Desconhecido',
                    'last_message' => $content,
                    'last_time' => date('Y-m-d H:i:s'),
                    'unread' => 1
                ]);
            }

            db()->insert('whatsapp_messages', [
                'conversation_id' => $convId,
                'wa_message_id' => $waMsgId,
                'direction' => 'incoming',
                'content' => $content,
                'content_type' => 'text'
            ]);
        } catch (Exception $e) {
            error_log("WhatsApp store error: " . $e->getMessage());
        }
    }

    private function post($data) {
        $ch = curl_init($this->baseUrl);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => "cURL error: {$error}"];
        }

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && isset($result['messages'])) {
            $waMsgId = $result['messages'][0]['id'] ?? '';
            return ['success' => true, 'wa_message_id' => $waMsgId];
        }

        $errorMsg = $result['error']['message'] ?? 'Erro desconhecido';
        return ['error' => $errorMsg];
    }
}
