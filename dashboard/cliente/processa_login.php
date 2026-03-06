<?php
// Configurações de sessão antes de session_start()
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();

// Incluir configurações
require_once 'email_config.php';

// Criar pasta de logs
$log_dir = dirname(dirname(__DIR__)) . '/logs/';
if (!file_exists($log_dir)) mkdir($log_dir, 0777, true);

// Log da tentativa de login
file_put_contents($log_dir . 'login_debug.txt', 
    "[" . date('Y-m-d H:i:s') . "] Tentativa de login - Session ID: " . session_id() . "\n", 
    FILE_APPEND);

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login_cliente.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Validar campos
if (empty($email) || empty($senha)) {
    header("Location: login_cliente.php?error=" . urlencode("E-mail e senha são obrigatórios"));
    exit();
}

// Carregar usuários
$json_file = dirname(__DIR__) . '/dados_cliente/user.json';
if (!file_exists($json_file)) {
    file_put_contents($log_dir . 'login_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: user.json não encontrado\n", 
        FILE_APPEND);
    header("Location: login_cliente.php?error=" . urlencode("Erro ao acessar dados"));
    exit();
}

$responsaveis = json_decode(file_get_contents($json_file), true) ?: [];

// Buscar usuário por email
$usuario = null;
foreach ($responsaveis as $resp) {
    if (strtolower($resp['email']) === strtolower($email)) {
        $usuario = $resp;
        break;
    }
}

if (!$usuario) {
    file_put_contents($log_dir . 'login_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: E-mail não encontrado: $email\n", 
        FILE_APPEND);
    header("Location: login_cliente.php?error=" . urlencode("E-mail não encontrado"));
    exit();
}

// Verificar se está ativo
if (isset($usuario['ativo']) && $usuario['ativo'] !== true) {
    header("Location: login_cliente.php?error=" . urlencode("Conta desativada. Contate o suporte."));
    exit();
}

// Verificar senha
if (isset($usuario['senha_hash'])) {
    if (!password_verify($senha, $usuario['senha_hash'])) {
        file_put_contents($log_dir . 'login_debug.txt', 
            "[" . date('Y-m-d H:i:s') . "] ERRO: Senha incorreta para: $email\n", 
            FILE_APPEND);
        header("Location: login_cliente.php?error=" . urlencode("Senha incorreta"));
        exit();
    }
} else {
    header("Location: login_cliente.php?error=" . urlencode("Conta antiga. Faça um novo cadastro ou recupere sua senha."));
    exit();
}

// Verificar se email foi verificado
if (isset($usuario['verified']) && $usuario['verified'] !== true) {
    header("Location: login_cliente.php?error=" . urlencode("E-mail não verificado. Verifique sua caixa de entrada."));
    exit();
}

// ===== SISTEMA DE NUMBER MATCHING =====
$numeros_email = [
    random_int(10, 99),
    random_int(10, 99),
    random_int(10, 99)
];

// Escolher UM número aleatório para aparecer NO SITE
$indice_site = random_int(0, 2);
$numero_site = $numeros_email[$indice_site];

// Limpar sessões antigas
$_SESSION = array();

// Salvar na sessão
$_SESSION['2fa_email'] = $email;
$_SESSION['2fa_numeros_email'] = $numeros_email;
$_SESSION['2fa_numero_site'] = $numero_site;
$_SESSION['2fa_expira'] = time() + 300; // 5 minutos
$_SESSION['2fa_tentativas'] = 0;
$_SESSION['2fa_iniciado'] = time();
$_SESSION['2fa_session_id'] = session_id(); 

// Log detalhado
file_put_contents($log_dir . '2fa_debug.txt', 
    str_repeat("=", 80) . "\n" .
    "[" . date('Y-m-d H:i:s') . "] 2FA INICIADO\n" .
    "Session ID: " . session_id() . "\n" .
    "Email: $email\n" .
    "Números e-mail: " . implode(', ', $numeros_email) . "\n" .
    "Número site: $numero_site\n" .
    "Expira em: " . date('Y-m-d H:i:s', time()+300) . "\n" .
    str_repeat("=", 80) . "\n", 
    FILE_APPEND);

// Enviar e-mail com os 3 números
$enviado = enviar2FANumberMatching($email, $usuario['nome_completo'], $numeros_email, $numero_site);

if ($enviado) {
    header("Location: login_cliente.php?2fa=aguardando");
    exit();
} else {
    // Fallback para modo teste
    $_SESSION['2fa_teste'] = true;
    header("Location: login_cliente.php?success=" . urlencode("Verifique seu e-mail (modo teste ativo)"));
    exit();
}
?>