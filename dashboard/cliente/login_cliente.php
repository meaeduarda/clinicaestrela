<?php
// login_cliente.php
// Caminho: C:\wamp64\www\clinicaestrela\dashboard\cliente\login_cliente.php
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesse sua conta - Clínica Estrela</title>
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            background: linear-gradient(135deg, rgba(74, 123, 218, 0.95) 0%, rgba(95, 75, 139, 0.95) 100%);
        }
        
        /* Imagem de fundo com opacidade */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../../imagens/nossaclinica1500.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            opacity: 0.6;
            z-index: -1;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            border: none;
        }
        
        .login-header {
            background: #1d59a3c7;
            color: white;
            padding: 25px 25px 20px 25px;
            text-align: center;
            position: relative;
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .logo-container img {
            height: 50px;
            width: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        
        .logo-container span {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        
        .logo-container strong {
            color: #ffd700;
        }
        
        .login-header h1 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: white;
        }
        
        .login-header p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.4;
        }
        
        .login-content {
            padding: 30px 25px 25px 25px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 15px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #4a7bda;
            font-size: 16px;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 14px 18px 14px 48px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }
        
        .input-with-icon input:focus {
            border-color: #4a7bda;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 123, 218, 0.15);
        }
        
        .input-with-icon input::placeholder {
            color: #999;
            font-size: 14px;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #4a7bda;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-login:hover {
            background: #3a6bca;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(74, 123, 218, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .separator {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #666;
            font-size: 13px;
        }
        
        .separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #e0e0e0, transparent);
        }
        
        .separator span {
            background: white;
            padding: 0 12px;
            position: relative;
        }
        
        .login-options {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-options p {
            color: #333;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-secondary {
            display: block;
            width: 100%;
            padding: 14px;
            background: white;
            color: #4a7bda;
            border: 2px solid #4a7bda;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            background: #4a7bda;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(74, 123, 218, 0.2);
        }
        
        .back-home {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .back-home a {
            color: #4a7bda;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.3s ease;
            padding: 6px 12px;
            border-radius: 6px;
            background: #f8f9ff;
        }
        
        .back-home a:hover {
            color: #3a6bca;
            background: #edf2ff;
        }
        
        .back-home a i {
            color: #4a7bda;
        }
        
        .error-message {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
            display: none;
            font-size: 13px;
            font-weight: 500;
        }
        
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c8e6c9;
            font-size: 13px;
            font-weight: 500;
        }
        
        .message-icon {
            margin-right: 6px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
                border-radius: 10px;
            }
            
            .login-header {
                padding: 20px 20px 15px 20px;
            }
            
            .login-content {
                padding: 25px 20px 20px 20px;
            }
            
            .logo-container img {
                height: 45px;
                margin-bottom: 8px;
            }
            
            .logo-container span {
                font-size: 18px;
            }
            
            .login-header h1 {
                font-size: 16px;
            }
            
            .login-header p {
                font-size: 12px;
            }
            
            .input-with-icon input {
                padding: 13px 16px 13px 45px;
                font-size: 14px;
            }
            
            body {
                padding: 15px;
            }
        }
        
        @media (max-width: 360px) {
            .login-header {
                padding: 18px 15px 12px 15px;
            }
            
            .login-content {
                padding: 20px 15px 18px 15px;
            }
            
            .logo-container img {
                height: 40px;
            }
            
            .logo-container span {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="../../imagens/logo.png" alt="Logo Clínica Estrela" onerror="this.onerror=null; this.src='../../imagens/logo_clinica_estrela.png';">
                <span>Clínica <strong>Estrela</strong></span>
            </div>
            <h1>Acesse sua conta</h1>
            <p>Digite seu CPF para acessar sua área do cliente</p>
        </div>
        
        <div class="login-content">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" id="errorMessage" style="display: block;">
                    <i class="fas fa-exclamation-circle message-icon"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle message-icon"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="processa_login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="cpf">Digite seu CPF</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" 
                               id="cpf" 
                               name="cpf" 
                               placeholder="000.000.000-00" 
                               required
                               maxlength="14"
                               oninput="formatarCPF(this)">
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Acessar
                </button>
            </form>
            
            <div class="login-options">
                <p>É seu primeiro agendamento?</p>
                <a href="novo_agendamento.php" class="btn-secondary">
                    <i class="fas fa-calendar-plus"></i> Clique aqui para fazer um agendamento
                </a>
            </div>
            
            <div class="back-home">
                <a href="../../index.html">
                    <i class="fas fa-arrow-left"></i> Voltar para o site principal
                </a>
            </div>
        </div>
    </div>

    <script>
        // Função para formatar CPF
        function formatarCPF(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            
            input.value = value;
        }
        
        // Validação do formulário
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const cpf = document.getElementById('cpf').value;
            const cpfLimpo = cpf.replace(/\D/g, '');
            
            if (cpfLimpo.length !== 11) {
                e.preventDefault();
                let errorMsg = document.getElementById('errorMessage');
                if (!errorMsg) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.id = 'errorMessage';
                    document.querySelector('.login-content').insertBefore(errorMsg, document.getElementById('loginForm'));
                }
                errorMsg.innerHTML = '<i class="fas fa-exclamation-circle message-icon"></i> CPF deve conter 11 dígitos numéricos';
                errorMsg.style.display = 'block';
                
                // Scroll para a mensagem de erro
                errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Focar no campo CPF ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('cpf').focus();
        });
    </script>
</body>
</html>