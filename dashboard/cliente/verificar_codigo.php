<?php
session_start();

// Verificar se está em processo 2FA
if (!isset($_SESSION['2fa_email']) || !isset($_SESSION['2fa_codigo'])) {
    header("Location: login_cliente.php");
    exit();
}

// Verificar expiração
if (time() > $_SESSION['2fa_expira']) {
    session_destroy();
    header("Location: login_cliente.php?error=" . urlencode("Código expirado. Faça login novamente."));
    exit();
}

// Verificar tentativas
if ($_SESSION['2fa_tentativas'] >= 3) {
    session_destroy();
    header("Location: login_cliente.php?error=" . urlencode("Muitas tentativas. Faça login novamente."));
    exit();
}

// Obter código
$codigo_digitado = $_POST['codigo_completo'] ?? '';

// Validar
if (strlen($codigo_digitado) !== 6 || !ctype_digit($codigo_digitado)) {
    $_SESSION['2fa_tentativas']++;
    header("Location: login_cliente.php?error=" . urlencode("Código inválido"));
    exit();
}

// Verificar código
$codigo_correto = $_SESSION['2fa_codigo'];
$codigo_teste = $_SESSION['2fa_codigo_teste'] ?? null;

if ($codigo_digitado === $codigo_correto || ($codigo_teste && $codigo_digitado === $codigo_teste)) {
    // Código correto! Buscar dados do usuário
    $json_file = dirname(__DIR__) . '/dados_cliente/user.json';
    
    if (!file_exists($json_file)) {
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
            
            // Registrar último acesso
            $resp['ultimo_acesso'] = date('Y-m-d H:i:s');
            $usuario_encontrado = true;
            break;
        }
    }
    
    if ($usuario_encontrado) {
        // Salvar alterações
        file_put_contents($json_file, json_encode($responsaveis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Limpar dados 2FA
        unset($_SESSION['2fa_codigo']);
        unset($_SESSION['2fa_codigo_teste']);
        unset($_SESSION['2fa_expira']);
        unset($_SESSION['2fa_tentativas']);
        
        header("Location: painel_cliente.php");
        exit();
    } else {
        // Não encontrou usuário
        session_destroy();
        header("Location: login_cliente.php?error=" . urlencode("Erro ao carregar dados"));
        exit();
    }
} else {
    // Código errado
    $_SESSION['2fa_tentativas']++;
    header("Location: login_cliente.php?error=" . urlencode("Código incorreto"));
    exit();
}
?>