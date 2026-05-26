<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$message = sanitize($input['message'] ?? '');
$history = $input['history'] ?? [];

if (empty($message)) {
    jsonResponse(['error' => 'Mensagem vazia'], 400);
}

$apiKey = getSetting('gemini_api_key', '');

if (empty($apiKey)) {
    jsonResponse(['error' => 'Chatbot temporariamente indisponível'], 503);
}

$systemPrompt = "Tu és o assistente virtual da ANGONUEVE, uma empresa angolana de tecnologia.
Responde sempre em português de Angola, de forma profissional, simpática e objetiva.

INFORMAÇÃO SOBRE A EMPRESA:
- Nome: ANGONUEVE
- Localização: Luanda, Angola
- Email: geral@angonueve.co
- Telefone/WhatsApp: 935 603 163
- Website: angonueve.com
- Horário: Seg-Sex 08:00-18:00, Sáb 08:00-13:00

SERVIÇOS DISPONÍVEIS:
1. Hospedagem de Sites - Planos desde 5.000 Kz/mês (10GB SSD, 1 site, SSL grátis)
2. Registo de Domínios - .com, .ao, .co.ao, .net, .org
3. Email Corporativo - Email profissional com o seu domínio, desde 5.000 Kz/mês
4. Criação de Sites Profissionais - Sites personalizados e responsivos, desde 50.000 Kz

REGRAS:
- Mantém as respostas curtas e diretas (máx 3 parágrafos).
- Se perguntarem por preços, menciona que os valores variam conforme o plano e sugere contacto via WhatsApp ou formulário.
- Se não souberes a resposta, pede desculpa e sugere falar com um humano via WhatsApp (935 603 163).
- NUNCA inventes informações. Se não tiveres a informação, diz que vais transferir para um atendente.
- Usa emojis moderadamente para tornar a conversa mais amigável.
- No final de cada resposta, se for uma pergunta sobre serviços, pergunta se precisa de mais alguma ajuda.";

$contents = [];

foreach ($history as $msg) {
    $role = $msg['role'] ?? 'user';
    if (in_array($role, ['user', 'assistant'])) {
        $geminiRole = ($role === 'assistant') ? 'model' : 'user';
        $contents[] = [
            'role' => $geminiRole,
            'parts' => [['text' => sanitize($msg['content'] ?? '')]]
        ];
    }
}

$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $message]]
];

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'maxOutputTokens' => 500,
        'temperature' => 0.7
    ]
];

$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . urlencode($apiKey));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    jsonResponse(['error' => 'Erro de conexão com o servidor AI'], 500);
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errMsg = $errorData['error']['message'] ?? 'Erro ao processar resposta';
    if (strpos($errMsg, 'quota') !== false || strpos($errMsg, 'rate') !== false || $httpCode === 429) {
        $errMsg = 'Limite de uso excedido. Tenta novamente mais tarde.';
    } elseif ($httpCode === 403) {
        $errMsg = 'Chave de API inválida. Verifica as configurações.';
    }
    jsonResponse(['error' => $errMsg], 502);
}

$data = json_decode($response, true);
$reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Desculpa, não consegui processar a tua mensagem. Podes tentar de novo?';

$tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;

try {
    $sessionId = session_id() ?: uniqid('chat_');
    db()->insert('chat_conversations', [
        'session_id' => $sessionId,
        'user_message' => $message,
        'bot_reply' => $reply,
        'tokens_used' => $tokens,
        'ip_address' => getIP()
    ]);
} catch (Exception $e) {
    // silently fail - chat still works
}

jsonResponse([
    'success' => true,
    'reply' => $reply,
    'tokens_used' => $tokens
]);
