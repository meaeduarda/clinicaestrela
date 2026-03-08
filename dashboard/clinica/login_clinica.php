<?php
// login_clinica.php
// Caminho: C:\wamp64\www\clinicaestrela\dashboard\clinica\login_clinica.php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Administrativo - Clínica Estrela</title>
    
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
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .forgot-password a {
            text-decoration: none;
            color: #2A5C8F;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        
        .forgot-password a:hover {
            color: #1e3f61;
            transform: translateX(-5px);
        }
        
        .forgot-password a i {
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover i {
            transform: translateX(-3px);
        }
        
        .back-home {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-home a {
            text-decoration: none;
            color: #666;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-home a:hover {
            color: #2A5C8F;
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
            <h1>Painel Administrativo</h1>
            <p>Entre com suas credenciais para gerenciar a clínica</p>
        </div>
        
        <div class="login-content">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 12px 15px; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="processa_login_clinica.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">E-mail Profissional</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="nome@sistema.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar no Sistema
                </button>
            </form>
            
            <!-- Link para esqueci minha senha -->
            <div class="forgot-password">
                <a href="esqueci_senha.php">
                    <i class="fas fa-key"></i> Esqueceu sua senha?
                </a>
            </div>
        </div>
    </div>
</body>
</html>