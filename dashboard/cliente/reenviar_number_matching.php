<?php
session_start();
header('Content-Type: application/json');

// Incluir configurações
require_once 'email_config.php';

// Log para debug
$log_dir = dirname(dirname(__DIR__)) . '/logs/';
if (!file_exists($log_dir)) mkdir($log_dir, 0777, true);

// Verificar se está em processo 2FA
if (!isset($_SESSION['2fa_email'])) {
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] REENVIAR: Sessão expirada\n", 
        FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Sessão expirada']);
    exit();
}

// Gerar novos números
$numeros_email = [
    random_int(10, 99),
    random_int(10, 99),
    random_int(10, 99)
];

$indice_site = random_int(0, 2);
$numero_site = $numeros_email[$indice_site];

// Atualizar sessão
$_SESSION['2fa_numeros_email'] = $numeros_email;
$_SESSION['2fa_numero_site'] = $numero_site;
$_SESSION['2fa_expira'] = time() + 600; // 10 MINUTOS
$_SESSION['2fa_tentativas'] = 0;

file_put_contents($log_dir . '2fa_debug.txt', 
    "[" . date('Y-m-d H:i:s') . "] REENVIAR: Novos números gerados - Site: $numero_site, Email: " . implode(', ', $numeros_email) . "\n", 
    FILE_APPEND);

// Buscar nome do usuário
$json_file = dirname(__DIR__) . '/dados_cliente/user.json';
$responsaveis = [];
$nome = '';

if (file_exists($json_file)) {
    $responsaveis = json_decode(file_get_contents($json_file), true) ?: [];
    
    foreach ($responsaveis as $resp) {
        if (strtolower($resp['email']) === strtolower($_SESSION['2fa_email'])) {
            $nome = $resp['nome_completo'];
            break;
        }
    }
}

// Enviar novo e-mail
$enviado = enviar2FANumberMatching($_SESSION['2fa_email'], $nome, $numeros_email, $numero_site);

echo json_encode(['success' => $enviado]);
?>