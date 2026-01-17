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
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard/clinica/login_clinica.css">
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
            
            <div class="back-home">
                <a href="../../index.html">
                    <i class="fas fa-arrow-left"></i> Voltar ao site principal
                </a>
                <a href="http://localhost/clinicaestrela/dashboard/clinica/login_cadastro_clinica.php">
                    <i class="fas fa-user-plus"></i> Faça seu cadastro aqui
                </a>
            </div>
        </div>
    </div>
</body>
</html>