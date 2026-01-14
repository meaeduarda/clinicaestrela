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

    foreach ($usuarios as $user) {
        if ($user['email'] === $email) {
            $usuarioEncontrado = $user;
            break;
        }
    }

    if ($usuarioEncontrado) {
        // Valida senha hash e se o usuário está ativo
        // Nota: Para testes rápidos, use password_verify ou comparação direta se o hash for texto puro
        if (password_verify($senha, $usuarioEncontrado['senha']) || $senha === $usuarioEncontrado['senha']) {
            if ($usuarioEncontrado['ativo']) {
                $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
                $_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];
                $_SESSION['usuario_perfil'] = $usuarioEncontrado['perfil'];
                
                // Redireciona para o painel administrativo
                header("Location: painel_administrativo_pacientes.php");
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