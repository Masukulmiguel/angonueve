<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$prompt = sanitize($input['prompt'] ?? '');
$history = $input['history'] ?? [];

if (empty($prompt)) {
    jsonResponse(['error' => 'Prompt vazio'], 400);
}

$apiKey = getSetting('gemini_api_key', '');
if (empty($apiKey)) {
    jsonResponse(['error' => 'Gerador temporariamente indisponível'], 503);
}

$systemPrompt = "Tu és um gerador de sites da ANGONUEVE, uma empresa angolana de tecnologia.
Gera APENAS código HTML/CSS/JS completo para um site com base no pedido do utilizador.

REGRAS IMPORTANTES:
1. O código DEVE ser uma página HTML completa e funcional (DOCTYPE html, html, head, body).
2. Usa CSS embutido (style tag no head) - NÃO uses ficheiros CSS externos.
3. Podes usar Font Awesome CDN (https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css) e Google Fonts.
4. O design deve ser moderno, responsivo e seguir um tema escuro profissional (fundo escuro, accent color ciano/azul).
5. Inclui navegação básica, secções relevantes e rodapé.
6. TODO o código deve estar EM PORTUGUÊS DE ANGOLA (textos visíveis).
7. NÃO uses frameworks como Bootstrap, Tailwind, React, etc.
8. Gera APENAS o código HTML, sem markdown, sem explicações, sem ```html ... ```.
9. O código deve ser seguro e não conter scripts maliciosos.
10. Usa cores modernas: fundo #0a1628, texto #e0e6ed, accent #00d4ff.

O utilizador pediu: $prompt

Gera o código HTML completo agora:";

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
    'parts' => [['text' => $prompt]]
];

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'maxOutputTokens' => 8192,
        'temperature' => 0.4
    ]
];

$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . urlencode($apiKey));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 120,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    jsonResponse(['error' => 'Erro de conexão com o servidor AI'], 500);
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errMsg = $errorData['error']['message'] ?? 'Erro ao gerar site';
    if ($httpCode === 429) $errMsg = 'Limite de uso excedido. Tenta novamente mais tarde.';
    if ($httpCode === 403) $errMsg = 'Chave de API inválida.';
    jsonResponse(['error' => $errMsg], 502);
}

$data = json_decode($response, true);
$code = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

// Strip markdown code fences if present
$code = preg_replace('/^```(?:html|php)?\s*/i', '', $code);
$code = preg_replace('/\s*```$/', '', $code);

if (empty($code)) {
    jsonResponse(['error' => 'Não foi possível gerar o site. Tenta novamente.'], 500);
}

$tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;

// Save to DB
session_start();
$sessionId = session_id() ?: uniqid('gen_');
$userId = $_SESSION['user_id'] ?? null;

try {
    db()->insert('generated_sites', [
        'user_id' => $userId,
        'session_id' => $sessionId,
        'prompt_text' => $prompt,
        'generated_html' => $code,
        'tokens_used' => $tokens,
        'status' => 'draft'
    ]);
    $siteId = db()->lastInsertId();
} catch (Exception $e) {
    $siteId = 0;
}

jsonResponse([
    'success' => true,
    'html' => $code,
    'site_id' => $siteId,
    'session_id' => $sessionId,
    'tokens_used' => $tokens
]);
