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
    <!-- CSS da Página Login do Cliente -->
    <link rel="stylesheet" href="../../css/dashboard/cliente/login_cliente.css">
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