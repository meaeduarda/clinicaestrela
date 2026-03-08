<?php
// processa_login_clinica.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    // Caminho para o arquivo JSON (um nível acima na pasta dados)
    $jsonPath = '../dados/users.json';
    
    if (!file_exists($jsonPath)) {
        header("Location: login_clinica.php?error=Erro interno no sistema de dados.");
        exit;
    }

    $usuarios = json_decode(file_get_contents($jsonPath), true);
    $usuarioEncontrado = null;
    $usuarioIndex = null;

    foreach ($usuarios as $index => $user) {
        if ($user['email'] === $email) {
            $usuarioEncontrado = $user;
            $usuarioIndex = $index;
            break;
        }
    }

    if ($usuarioEncontrado) {
        // Valida senha
        if (password_verify($senha, $usuarioEncontrado['senha']) || $senha === $usuarioEncontrado['senha']) {
            if ($usuarioEncontrado['ativo']) {
                
                // Verifica se a senha é temporária
                if (isset($usuarioEncontrado['senha_temporaria']) && $usuarioEncontrado['senha_temporaria'] === true) {
                    // Salva informações na sessão para a troca de senha
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_id'] = $usuarioEncontrado['id'];
                    
                    // Redireciona para página de definição de nova senha
                    header("Location: definir_nova_senha.php?temp=1");
                    exit;
                }
                
                // Login normal (senha já foi alterada)
                $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
                $_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];
                $_SESSION['usuario_perfil'] = $usuarioEncontrado['perfil'];
                
                // Remove a marcação de senha temporária se existir (por segurança)
                if (isset($usuarioEncontrado['senha_temporaria'])) {
                    unset($usuarios[$usuarioIndex]['senha_temporaria']);
                    file_put_contents($jsonPath, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
                
                // Redireciona para o painel administrativo
                header("Location: painel_adm_pacientes.php");
                exit;
            } else {
                header("Location: login_clinica.php?error=Usuário desativado. Contate o administrador.");
            }
        } else {
            header("Location: login_clinica.php?error=Senha incorreta.");
        }
    } else {
        header("Location: login_clinica.php?error=E-mail não cadastrado.");
    }
} else {
    header("Location: login_clinica.php");
}
?>