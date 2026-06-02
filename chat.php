<?php
declare(strict_types=1);

session_name('slahpc_session');
session_start();

header('Content-Type: application/json; charset=utf-8');

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function env_value(string $key): string {
    $value = getenv($key);
    if (is_string($value) && trim($value) !== '') {
        return trim($value);
    }

    $envPath = __DIR__ . '/.env';
    if (!is_file($envPath)) {
        return '';
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $rawValue] = array_map('trim', explode('=', $line, 2));
        if ($name === $key) {
            return trim($rawValue, " \t\n\r\0\x0B\"'");
        }
    }

    return '';
}

function extract_text(array $data): string {
    if (isset($data['output_text']) && is_string($data['output_text'])) {
        return trim($data['output_text']);
    }

    foreach (($data['output'] ?? []) as $item) {
        foreach (($item['content'] ?? []) as $content) {
            if (($content['type'] ?? '') === 'output_text' && isset($content['text'])) {
                return trim((string)$content['text']);
            }
        }
    }

    return '';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Use POST to send chatbot messages.'], 405);
}

$now = time();
$_SESSION['chat_requests'] = array_values(array_filter(
    $_SESSION['chat_requests'] ?? [],
    static fn (int $timestamp): bool => $timestamp > $now - 60
));

if (count($_SESSION['chat_requests']) >= 20) {
    json_response(['error' => 'Please wait a moment before sending more messages.'], 429);
}

$_SESSION['chat_requests'][] = $now;

$rawBody = file_get_contents('php://input') ?: '';
$body = json_decode($rawBody, true);
if (!is_array($body)) {
    json_response(['error' => 'Invalid chatbot request.'], 400);
}

$message = trim((string)($body['message'] ?? ''));
if ($message === '') {
    json_response(['error' => 'Please enter a message.'], 400);
}

if (strlen($message) > 1000) {
    json_response(['error' => 'Please keep the message under 1000 characters.'], 400);
}

$apiKey = env_value('OPENAI_API_KEY');
if ($apiKey === '') {
    json_response([
        'error' => 'OpenAI API key is missing. Add OPENAI_API_KEY to a .env file or server environment.'
    ], 500);
}

$history = is_array($body['history'] ?? null) ? array_slice($body['history'], -8) : [];
$input = [];

foreach ($history as $item) {
    $role = ($item['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
    $content = trim((string)($item['content'] ?? ''));
    if ($content === '') {
        continue;
    }

    $input[] = [
        'role' => $role,
        'content' => substr($content, 0, 1000),
    ];
}

$input[] = [
    'role' => 'user',
    'content' => $message,
];

$payload = [
    'model' => env_value('OPENAI_MODEL') ?: 'gpt-5-mini',
    'instructions' => implode("\n", [
        'You are the Slahpc AI chatbot for a computer repair and PC parts website.',
        'Answer customers in a professional, friendly, concise style.',
        'You can help with laptop and desktop repair, diagnostics, virus removal, Wi-Fi/printer setup, data backup, PC parts, custom builds, appointments, account access, and dashboard guidance.',
        'Do not invent exact prices, stock, appointment availability, addresses, or phone numbers. Tell users to create an account or log in to the dashboard for requests, orders, and appointments.',
        'If the user describes a dangerous electrical issue, tell them to stop using the device and contact a qualified technician.',
        'Keep answers under 120 words unless the user asks for detail.',
    ]),
    'input' => $input,
    'max_output_tokens' => 320,
];

if (!function_exists('curl_init')) {
    json_response(['error' => 'The PHP curl extension is required for the AI chatbot.'], 500);
}

$ch = curl_init('https://api.openai.com/v1/responses');
if ($ch === false) {
    json_response(['error' => 'Unable to start the AI request.'], 500);
}

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
]);

$responseBody = curl_exec($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($responseBody === false) {
    json_response(['error' => 'AI service connection failed: ' . $curlError], 502);
}

$data = json_decode($responseBody, true);
if (!is_array($data)) {
    json_response(['error' => 'AI service returned an invalid response.'], 502);
}

if ($status < 200 || $status >= 300) {
    $error = $data['error']['message'] ?? 'AI service returned an error.';
    json_response(['error' => $error], 502);
}

$reply = extract_text($data);
if ($reply === '') {
    json_response(['error' => 'AI service returned an empty answer.'], 502);
}

json_response(['reply' => $reply]);
