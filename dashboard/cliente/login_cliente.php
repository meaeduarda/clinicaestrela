<?php
// Desabilitar notices
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// Verificar se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cancelar 2FA se solicitado
if (isset($_GET['cancelar_2fa']) && $_GET['cancelar_2fa'] == 1) {
    // Limpar apenas dados 2FA
    $keys_2fa = ['2fa_email', '2fa_numeros_email', '2fa_numero_site', 
                 '2fa_expira', '2fa_tentativas', '2fa_iniciado', '2fa_session_id', '2fa_teste'];
    
    foreach ($keys_2fa as $key) {
        unset($_SESSION[$key]);
    }
    
    // Mensagem amigável
    $_SESSION['mensagem_temporaria'] = "Verificação cancelada. Faça login novamente.";
    
    // Redirecionar para limpar URL
    header("Location: login_cliente.php");
    exit();
}

// Criar pasta de logs
$log_dir = dirname(dirname(__DIR__)) . '/logs/';
if (!file_exists($log_dir)) mkdir($log_dir, 0777, true);

// Redirecionar se já estiver logado
if (isset($_SESSION['responsavel_id']) && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    header("Location: painel_cliente.php");
    exit();
}

// Verificar se está aguardando 2FA
$aguardando_2fa = isset($_SESSION['2fa_email']) && !isset($_SESSION['2fa_verified']);

// Calcular tempo restante
$tempo_restante = 600;
if ($aguardando_2fa && isset($_SESSION['2fa_expira'])) {
    $tempo_restante = $_SESSION['2fa_expira'] - time();
    if ($tempo_restante < 0) $tempo_restante = 0;
}

// Se o tempo expirou, mostrar mensagem
$expirado = ($aguardando_2fa && $tempo_restante <= 0);
if ($expirado) {
    // Se expirou, limpar apenas 2FA mas manter mensagem
    $keys_2fa = ['2fa_email', '2fa_numeros_email', '2fa_numero_site', 
                 '2fa_expira', '2fa_tentativas', '2fa_iniciado', '2fa_session_id', '2fa_teste'];
    
    foreach ($keys_2fa as $key) {
        unset($_SESSION[$key]);
    }
    
    $aguardando_2fa = false;
    $_SESSION['mensagem_temporaria'] = "Código expirado. Faça login novamente.";
}

// Mostrar mensagem temporária se existir
$mensagem_temporaria = $_SESSION['mensagem_temporaria'] ?? '';
unset($_SESSION['mensagem_temporaria']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $aguardando_2fa ? 'Verificação em duas etapas' : 'Acesse sua conta'; ?> - Clínica Estrela</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    
    <!-- Fontes e ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="../../css/dashboard/cliente/login_cliente.css">
    
    <style>
        .info-message {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }
        
        .info-message i {
            font-size: 18px;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cancel-link {
            color: #666;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 6px;
        }
        
        .cancel-link:hover {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }
        
        .cancel-link i {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-background">
        <div class="background-overlay"></div>
    </div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="../../imagens/logo_clinica_estrela.png" alt="Logo Clínica Estrela">
                <span>Clínica <strong>Estrela</strong></span>
            </div>
            
            <?php if ($aguardando_2fa): ?>
                <h1>Verificação em duas etapas</h1>
                <p>Verifique seu e-mail para confirmar o login</p>
            <?php else: ?>
                <h1>Acesse sua conta</h1>
                <p>Digite seu e-mail e senha para acessar</p>
            <?php endif; ?>
        </div>
        
        <div class="login-content">
            <!-- Mensagem temporária -->
            <?php if ($mensagem_temporaria): ?>
                <div class="info-message">
                    <i class="fas fa-info-circle"></i> 
                    <?php echo htmlspecialchars($mensagem_temporaria); ?>
                </div>
            <?php endif; ?>
            
            <!-- Mensagens de erro/sucesso da URL -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-circle message-icon"></i> 
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle message-icon"></i> 
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($aguardando_2fa): ?>
                <!-- ===== TELA DE VERIFICAÇÃO 2FA ===== -->
                <div class="waiting-container"> 
                    <div class="waiting-message">
                        <p><i class="fas fa-envelope" style="color: #3498db;"></i> <strong>E-mail enviado para:</strong></p>
                        <div class="email-highlight">
                            <?php echo htmlspecialchars($_SESSION['2fa_email']); ?>
                        </div>
                        
                        <!-- NÚMERO QUE APARECE NO SITE -->
                        <div style="text-align: center; margin: 30px 0; padding: 20px; background: #e8f4fd; border-radius: 16px;">
                            <p style="font-weight: 600; color: #2c3e50; margin-bottom: 15px; font-size: 18px;">
                                <i class="fas fa-info-circle" style="color: #3498db;"></i> 
                                Seu número de verificação:
                            </p>
                            <div style="display: inline-block; width: 150px; height: 150px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 75px; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 15px 30px rgba(52,152,219,0.4);">
                                <span style="font-size: 64px; font-weight: 700; color: white;">
                                    <?php echo $_SESSION['2fa_numero_site']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- INSTRUÇÕES -->
                        <div style="background: #f8fafd; padding: 20px; border-radius: 12px; margin: 20px 0;">
                            <p style="font-weight: 600; color: #2c3e50; margin-bottom: 15px;">
                                <i class="fas fa-lightbulb" style="color: #f39c12;"></i> 
                                Como verificar:
                            </p>
                            <p style="margin-bottom: 12px; display: flex; align-items: center;">
                                <span style="display: inline-block; width: 26px; height: 26px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 26px; margin-right: 12px; font-size: 14px;">1</span>
                                Abra o e-mail que enviamos para você
                            </p>
                            <p style="margin-bottom: 12px; display: flex; align-items: center;">
                                <span style="display: inline-block; width: 26px; height: 26px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 26px; margin-right: 12px; font-size: 14px;">2</span>
                                No e-mail você verá 3 números
                            </p>
                             <p style="margin-bottom: 12px; display: flex; align-items: center;">
                                <span style="display: inline-block; width: 26px; height: 26px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 26px; margin-right: 12px; font-size: 14px;">3</span>
                                Clique no número que é igual ao que aparece no site
                            </p>
                        </div>
                        
                        <!-- TIMER -->
                        <div id="timerContainer" style="text-align: center; margin: 25px 0; padding: 15px; background: #fff3cd; border: 1px solid #ffe69c; border-radius: 12px; color: #856404; font-weight: 600;">
                            <i class="far fa-clock" style="margin-right: 8px;"></i>
                            Código expira em <span id="timer"><?php echo $tempo_restante; ?></span> segundos
                        </div>
                        
                        <!-- AVISO -->
                        <div style="background: #fff3cd; border-left: 4px solid #e67e22; padding: 15px; border-radius: 8px; margin: 20px 0;">
                            <p style="color: #856404; margin: 0;">
                                <i class="fas fa-exclamation-triangle" style="color: #e67e22; margin-right: 8px;"></i>
                                <strong>Não feche esta página!</strong> Você precisa dela para verificar o número.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Botão para reenviar código -->
                    <button class="btn-outline" onclick="reenviarCodigo()" style="margin-bottom: 15px;">
                        <i class="fas fa-redo-alt"></i> Não recebi o e-mail
                    </button>
                    
                    <!-- Link para cancelar e voltar ao login REAL -->
                    <div style="margin-top: 15px; text-align: center; border-top: 1px solid #eaeef7; padding-top: 20px;">
                        <p style="color: #666; font-size: 13px; margin-bottom: 8px;">
                            <i class="fas fa-info-circle"></i> 
                            Para tentar com outro e-mail:
                        </p>
                        <a href="?cancelar_2fa=1" 
                           class="cancel-link"
                           onclick="return confirm('Tem certeza? O código atual será cancelado e você voltará à tela de login.');">
                            <i class="fas fa-times-circle"></i> Cancelar verificação e voltar ao login
                        </a>
                    </div>
                </div>
                
                <script>
                // Timer regressivo
                let tempoRestante = <?php echo $tempo_restante; ?>;
                const timerElement = document.getElementById('timer');
                const timerContainer = document.getElementById('timerContainer');
                
                if (timerElement) {
                    const intervalo = setInterval(() => {
                        tempoRestante--;
                        timerElement.textContent = tempoRestante;
                        
                        // Mudar cor quando estiver acabando
                        if (tempoRestante <= 60) {
                            timerContainer.style.background = '#f8d7da';
                            timerContainer.style.color = '#721c24';
                            timerContainer.style.borderColor = '#f5c6cb';
                        }
                        
                        if (tempoRestante <= 0) {
                            clearInterval(intervalo);
                            timerContainer.innerHTML = '<i class="far fa-clock"></i> Código expirado! <a href="login_cliente.php" style="color: #721c24; font-weight: bold;">Clique aqui</a> para fazer login novamente.';
                            
                            // Auto-redirecionar após expirar
                            setTimeout(() => {
                                window.location.href = 'login_cliente.php';
                            }, 3000);
                        }
                    }, 1000);
                }
                
                function reenviarCodigo() {
                    const btn = event.target.closest('button');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                    btn.disabled = true;
                    
                    fetch('reenviar_number_matching.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ Novo código enviado para seu e-mail!');
                            location.reload();
                        } else {
                            alert('❌ Erro ao reenviar. Tente novamente.');
                        }
                    })
                    .catch(error => {
                        alert('❌ Erro de conexão. Tente novamente.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                }
                </script>
                
            <?php else: ?>
                <!-- FORMULÁRIO DE LOGIN -->
                <form action="processa_login.php" method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   placeholder="seu@email.com" 
                                   required
                                   value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="senha" 
                                   name="senha" 
                                   placeholder="Digite sua senha" 
                                   required>
                            <span class="toggle-password" onclick="togglePassword('senha', this)">
                                <i class="far fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Acessar
                    </button>
                </form>
            <?php endif; ?>

            <div class="back-home">
                <a href="../../index.html">
                    <i class="fas fa-arrow-left"></i> Voltar para o site principal
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, element) {
            const input = document.getElementById(inputId);
            const icon = element.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
            
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.opacity = '0';
                    errorMessage.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
        
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();
            
            if (!email || !senha) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos!');
            }
        });
    </script>
</body>
</html>