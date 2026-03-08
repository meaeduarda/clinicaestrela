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
        /* Estilos adicionais específicos para a página de definição de senha */
        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 13px;
            color: #666;
        }
        
        .password-requirements i {
            color: #2A5C8F;
            margin-right: 8px;
            width: 18px;
        }
        
        .password-requirements p {
            margin: 5px 0;
        }
        
        .success-container {
            text-align: center;
            padding: 20px;
        }
        
        .success-container i {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-container h2 {
            color: #28a745;
            margin-bottom: 15px;
        }
        
        .success-container p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .login-link {
            display: inline-block;
            background-color: #2A5C8F;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .login-link:hover {
            background-color: #1e3f61;
        }
        
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2A5C8F;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-box i {
            color: #2A5C8F;
            margin-right: 10px;
        }
        
        .info-box span {
            color: #333;
            font-size: 14px;
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
                <p>Crie uma senha segura para seu acesso</p>
            <?php else: ?>
                <h1>Senha Alterada!</h1>
                <p>Sua senha foi atualizada com sucesso</p>
            <?php endif; ?>
        </div>
        
        <div class="login-content">
            
            <?php if ($success): ?>
                <!-- Mensagem de sucesso -->
                <div class="success-container">
                    <i class="fas fa-check-circle"></i>
                    <h2>Senha alterada com sucesso!</h2>
                    <p>Você será redirecionado para a página de login em instantes.</p>
                    <p>Se não for redirecionado automaticamente, <a href="login_clinica.php" style="color: #2A5C8F;">clique aqui</a>.</p>
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
                    <span>Definindo nova senha para: <strong><?php echo htmlspecialchars($email); ?></strong></span>
                </div>
                
                <form action="" method="POST" id="formNovaSenha">
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" required minlength="4">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite novamente a senha" required minlength="4">
                        </div>
                    </div>
                    
                    <!-- Requisitos de senha -->
                    <div class="password-requirements">
                        <p><i class="fas fa-check-circle"></i> Mínimo de 4 caracteres</p>
                        <p><i class="fas fa-check-circle"></i> Recomendamos usar letras e números</p>
                        <p><i class="fas fa-check-circle"></i> Evite usar informações pessoais</p>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-save"></i> Salvar Nova Senha
                    </button>
                </form>
                
                <div class="back-home" style="margin-top: 15px;">
                    <a href="login_clinica.php">
                        <i class="fas fa-arrow-left"></i> Voltar para o login
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