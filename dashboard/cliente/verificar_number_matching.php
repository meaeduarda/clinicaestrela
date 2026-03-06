<?php
// Configurações de sessão
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();

// LOG PARA DEBUG
$log_dir = dirname(dirname(__DIR__)) . '/logs/';
if (!file_exists($log_dir)) mkdir($log_dir, 0777, true);

// Registrar TODOS os dados da sessão e requisição
file_put_contents($log_dir . '2fa_debug.txt', 
    str_repeat("=", 80) . "\n" .
    "[" . date('Y-m-d H:i:s') . "] VERIFICAÇÃO 2FA INICIADA\n" .
    "Session ID: " . session_id() . "\n" .
    "Cookie PHPSESSID: " . ($_COOKIE['PHPSESSID'] ?? 'NÃO DEFINIDO') . "\n" .
    "GET: " . print_r($_GET, true) . "\n" .
    "SESSION KEYS: " . implode(', ', array_keys($_SESSION)) . "\n" .
    "SESSION DATA: " . print_r($_SESSION, true) . "\n" .
    str_repeat("=", 80) . "\n", 
    FILE_APPEND);

// Verificar se está em processo 2FA
if (!isset($_SESSION['2fa_email']) || !isset($_SESSION['2fa_numero_site'])) {
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: Sessão 2FA não encontrada\n" .
        "Variáveis disponíveis: " . print_r(array_keys($_SESSION), true) . "\n", 
        FILE_APPEND);
    
    header("Location: login_cliente.php?error=" . urlencode("Sessão expirada. Faça login novamente."));
    exit();
}

// Verificar expiração
$agora = time();
$expira = $_SESSION['2fa_expira'];
$restante = $expira - $agora;

file_put_contents($log_dir . '2fa_debug.txt', 
    "[" . date('Y-m-d H:i:s') . "] Tempo restante: {$restante} segundos\n", 
    FILE_APPEND);

if ($agora > $expira) {
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: Código expirado. Agora: $agora, Expira: $expira\n", 
        FILE_APPEND);
    session_destroy();
    header("Location: login_cliente.php?error=" . urlencode("Código expirado. Faça login novamente."));
    exit();
}

// Verificar tentativas
if ($_SESSION['2fa_tentativas'] >= 3) {
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: Muitas tentativas\n", 
        FILE_APPEND);
    session_destroy();
    header("Location: login_cliente.php?error=" . urlencode("Muitas tentativas. Faça login novamente."));
    exit();
}

// Obter número escolhido
$escolha = isset($_GET['numero']) ? (int)$_GET['numero'] : 0;
$numero_site = (int)$_SESSION['2fa_numero_site'];

file_put_contents($log_dir . '2fa_debug.txt', 
    "[" . date('Y-m-d H:i:s') . "] Comparando: Escolha=$escolha, Correto=$numero_site\n", 
    FILE_APPEND);

// Verificar se o número está na lista válida
if (!in_array($escolha, $_SESSION['2fa_numeros_email'])) {
    $_SESSION['2fa_tentativas']++;
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ERRO: Número $escolha não está na lista válida: " . implode(', ', $_SESSION['2fa_numeros_email']) . "\n", 
        FILE_APPEND);
    header("Location: login_cliente.php?error=" . urlencode("Número inválido"));
    exit();
}

// VERIFICAR SE O NÚMERO ESCOLHIDO É IGUAL AO DO SITE
if ($escolha === $numero_site) {
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ✅ SUCESSO! Número correto\n", 
        FILE_APPEND);
    
    // Buscar dados do usuário
    $json_file = dirname(__DIR__) . '/dados_cliente/user.json';
    
    if (!file_exists($json_file)) {
        file_put_contents($log_dir . '2fa_debug.txt', 
            "[" . date('Y-m-d H:i:s') . "] ERRO: user.json não encontrado\n", 
            FILE_APPEND);
        session_destroy();
        header("Location: login_cliente.php?error=" . urlencode("Erro ao carregar dados"));
        exit();
    }
    
    $responsaveis = json_decode(file_get_contents($json_file), true) ?: [];
    $usuario_encontrado = false;
    
    foreach ($responsaveis as &$resp) {
        if (strtolower($resp['email']) === strtolower($_SESSION['2fa_email'])) {
            // Criar sessão completa
            $_SESSION['responsavel_id'] = $resp['id'];
            $_SESSION['responsavel_nome'] = $resp['nome_completo'];
            $_SESSION['responsavel_email'] = $resp['email'];
            $_SESSION['responsavel_celular'] = $resp['celular'] ?? '';
            $_SESSION['responsavel_parentesco'] = $resp['parentesco'] ?? '';
            $_SESSION['responsavel_nome_crianca'] = $resp['nome_crianca'] ?? '';
            $_SESSION['2fa_verified'] = true;
            $_SESSION['login_time'] = time();
            
            // Registrar último acesso
            $resp['ultimo_acesso'] = date('Y-m-d H:i:s');
            $usuario_encontrado = true;
            break;
        }
    }
    
    if ($usuario_encontrado) {
        // Salvar alterações
        file_put_contents($json_file, json_encode($responsaveis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Limpar dados 2FA (manter apenas os dados do usuário)
        unset($_SESSION['2fa_numero_site']);
        unset($_SESSION['2fa_numeros_email']);
        unset($_SESSION['2fa_email']);
        unset($_SESSION['2fa_expira']);
        unset($_SESSION['2fa_tentativas']);
        unset($_SESSION['2fa_iniciado']);
        unset($_SESSION['2fa_session_id']);
        
        file_put_contents($log_dir . '2fa_debug.txt', 
            "[" . date('Y-m-d H:i:s') . "] ✅ Redirecionando para painel_cliente.php\n", 
            FILE_APPEND);
        
        header("Location: painel_cliente.php");
        exit();
    } else {
        file_put_contents($log_dir . '2fa_debug.txt', 
            "[" . date('Y-m-d H:i:s') . "] ERRO: Usuário não encontrado no JSON\n", 
            FILE_APPEND);
        session_destroy();
        header("Location: login_cliente.php?error=" . urlencode("Erro ao carregar dados"));
        exit();
    }
} else {
    // ERROU - número incorreto
    $_SESSION['2fa_tentativas']++;
    $tentativas_restantes = 3 - $_SESSION['2fa_tentativas'];
    
    file_put_contents($log_dir . '2fa_debug.txt', 
        "[" . date('Y-m-d H:i:s') . "] ❌ ERRO: Número incorreto. Tentativa {$_SESSION['2fa_tentativas']} de 3\n", 
        FILE_APPEND);
    
    header("Location: login_cliente.php?error=" . urlencode("Número incorreto. Tentativas restantes: $tentativas_restantes"));
    exit();
}
?>