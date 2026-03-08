<?php
// definir_nova_senha.php
session_start();

// Verificar se o acesso é válido (via sessão ou parâmetro temp)
$acesso_valido = false;
$email = '';

if (isset($_SESSION['reset_email']) && isset($_SESSION['reset_id'])) {
    $acesso_valido = true;
    $email = $_SESSION['reset_email'];
} elseif (isset($_GET['email']) && isset($_GET['temp']) && $_GET['temp'] == 1) {
    // Caso venha direto do link do e-mail (sem sessão)
    $email = $_GET['email'];
    $acesso_valido = true;
}

// Se não tiver acesso válido, redireciona para login
if (!$acesso_valido) {
    header("Location: login_clinica.php?error=Acesso inválido para troca de senha.");
    exit;
}

// Processar o formulário de nova senha
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    if (empty($nova_senha)) {
        $error = 'A nova senha é obrigatória.';
    } elseif (strlen($nova_senha) < 4) {
        $error = 'A senha deve ter pelo menos 4 caracteres.';
    } elseif ($nova_senha !== $confirmar_senha) {
        $error = 'As senhas não coincidem.';
    } else {
        // Caminho para o arquivo JSON
        $jsonPath = '../dados/users.json';
        
        if (file_exists($jsonPath)) {
            $usuarios = json_decode(file_get_contents($jsonPath), true);
            $usuarioEncontrado = false;
            $usuarioIndex = null;
            
            // Procurar o usuário pelo email
            foreach ($usuarios as $index => $user) {
                if ($user['email'] === $email) {
                    $usuarioEncontrado = true;
                    $usuarioIndex = $index;
                    break;
                }
            }
            
            if ($usuarioEncontrado) {
                // Atualizar a senha (usando password_hash para maior segurança)
                $usuarios[$usuarioIndex]['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                // Remover a marcação de senha temporária
                if (isset($usuarios[$usuarioIndex]['senha_temporaria'])) {
                    unset($usuarios[$usuarioIndex]['senha_temporaria']);
                }
                
                // Salvar no arquivo JSON
                if (file_put_contents($jsonPath, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                    $success = true;
                    
                    // Limpar as variáveis de sessão
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['reset_id']);
                    
                    // Redirecionar após 3 segundos para o login
                    header("refresh:3;url=login_clinica.php");
                } else {
                    $error = 'Erro ao salvar a nova senha. Tente novamente.';
                }
            } else {
                $error = 'Usuário não encontrado.';
            }
        } else {
            $error = 'Erro no sistema de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha - Clínica Estrela</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    
    <!-- Fontes e ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard/clinica/login_clinica.css">
    
    <style>
        /* Ajustes específicos para definir_nova_senha.php */
        .login-container {
            max-height: 95vh;
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        .login-header {
            margin-bottom: 0.8rem;
        }
        
        .login-header h1 {
            font-size: 1.2rem;
            margin: 0.3rem 0;
        }
        
        .login-header p {
            font-size: 0.8rem;
        }
        
        .info-box {
            padding: 10px;
            margin-bottom: 12px;
            font-size: 0.8rem;
        }
        
        .info-box i {
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 0.8rem;
        }
        
        .form-group label {
            margin-bottom: 3px;
            font-size: 0.8rem;
        }
        
        .input-with-icon input {
            padding: 8px 10px 8px 35px;
            font-size: 0.8rem;
        }
        
        .input-with-icon i {
            font-size: 0.8rem;
            left: 10px;
        }
        
        .password-requirements {
            padding: 10px;
            margin: 10px 0;
            font-size: 0.7rem;
        }
        
        .password-requirements p {
            margin: 2px 0;
        }
        
        .password-requirements i {
            font-size: 0.7rem;
            margin-right: 5px;
        }
        
        .btn-login {
            padding: 8px;
            font-size: 0.85rem;
            margin-top: 0;
        }
        
        .back-home {
            margin-top: 0.8rem;
        }
        
        .back-home a {
            font-size: 0.75rem;
        }
        
        /* Ajustes para mensagem de sucesso */
        .success-container {
            padding: 10px;
        }
        
        .success-container i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .success-container h2 {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .success-container p {
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        
        /* Esconder scrollbar mas manter funcionalidade */
        .login-container::-webkit-scrollbar {
            width: 3px;
        }
        
        .login-container::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Background com imagem e overlay -->
    <div class="login-background">
        <div class="background-overlay"></div>
    </div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="../../imagens/logo.png" alt="Logo Clínica Estrela" onerror="this.onerror=null; this.src='../../imagens/logo_clinica_estrela.png';">
                <span>Clínica <strong>Estrela</strong></span>
            </div>
            
            <?php if (!$success): ?>
                <h1>Definir Nova Senha</h1>
                <p>Crie uma senha segura</p>
            <?php else: ?>
                <h1>Senha Alterada!</h1>
                <p>Sua senha foi atualizada</p>
            <?php endif; ?>
        </div>
        
        <div class="login-content">
            
            <?php if ($success): ?>
                <!-- Mensagem de sucesso -->
                <div class="success-container">
                    <i class="fas fa-check-circle"></i>
                    <h2>Senha alterada!</h2>
                    <p>Redirecionando para o login...</p>
                    <p><small><a href="login_clinica.php">Clique aqui</a> se não for redirecionado.</small></p>
                </div>
                
            <?php else: ?>
            
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Informação de conta -->
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <span><strong><?php echo htmlspecialchars($email); ?></strong></span>
                </div>
                
                <form action="" method="POST" id="formNovaSenha">
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Nova senha" required minlength="4">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a senha" required minlength="4">
                        </div>
                    </div>
                    
                    <!-- Requisitos de senha -->
                    <div class="password-requirements">
                        <p><i class="fas fa-check-circle"></i> Mínimo 4 caracteres</p>
                        <p><i class="fas fa-check-circle"></i> Use letras e números</p>
                        <p><i class="fas fa-check-circle"></i> Evite informações pessoais</p>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-save"></i> Salvar Nova Senha
                    </button>
                </form>
                
                <div class="back-home">
                    <a href="login_clinica.php">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Validação em tempo real das senhas
        document.addEventListener('DOMContentLoaded', function() {
            const novaSenha = document.getElementById('nova_senha');
            const confirmarSenha = document.getElementById('confirmar_senha');
            
            function validarSenhas() {
                if (confirmarSenha.value.length > 0) {
                    if (novaSenha.value !== confirmarSenha.value) {
                        confirmarSenha.style.borderColor = '#dc3545';
                        confirmarSenha.style.backgroundColor = '#fff8f8';
                    } else {
                        confirmarSenha.style.borderColor = '#28a745';
                        confirmarSenha.style.backgroundColor = '#f8fff8';
                    }
                } else {
                    confirmarSenha.style.borderColor = '';
                    confirmarSenha.style.backgroundColor = '';
                }
            }
            
            novaSenha.addEventListener('keyup', validarSenhas);
            confirmarSenha.addEventListener('keyup', validarSenhas);
        });
        
        // Prevenir envio do formulário se as senhas não coincidirem
        document.getElementById('formNovaSenha')?.addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
            }
        });
    </script>
</body>
</html>