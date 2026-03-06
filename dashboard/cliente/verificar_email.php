<?php
session_start();

// Caminho para o arquivo JSON - USAR RELATIVO
$json_file = dirname(__DIR__) . '/dados_cliente/user.json';

// Inicializar variáveis
$mensagem = '';
$tipo_mensagem = ''; // 'success', 'error', 'info'

// Obter token da URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $mensagem = 'Token de verificação não fornecido.';
    $tipo_mensagem = 'error';
} else {
    // Ler usuários existentes
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        $responsaveis = json_decode($json_content, true) ?: [];
        
        $usuario_encontrado = false;
        
        // Procurar usuário com o token
        foreach ($responsaveis as &$responsavel) {
            if (isset($responsavel['verification_token']) && $responsavel['verification_token'] === $token) {
                $usuario_encontrado = true;
                
                // Verificar se já estava verificado
                if (isset($responsavel['verified']) && $responsavel['verified'] === true) {
                    $mensagem = 'Este e-mail já foi verificado anteriormente.';
                    $tipo_mensagem = 'info';
                } else {
                    // Marcar como verificado
                    $responsavel['verified'] = true;
                    $responsavel['verified_at'] = date('Y-m-d H:i:s');
                    
                    $mensagem = 'E-mail verificado com sucesso! Seu cadastro está ativo.';
                    $tipo_mensagem = 'success';
                }
                break;
            }
        }
        
        if ($usuario_encontrado) {
            // Salvar alterações
            file_put_contents($json_file, json_encode($responsaveis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $mensagem = 'Token de verificação inválido ou expirado.';
            $tipo_mensagem = 'error';
        }
    } else {
        $mensagem = 'Erro ao processar verificação.';
        $tipo_mensagem = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Estrela - Verificação de E-mail</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .verification-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('../../imagens/telalogincadastro.png');
            background-size: cover;
            background-position: center;
            padding: 20px;
        }
        
        .verification-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-width: 100%;
            padding: 40px;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .verification-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .success-icon { color: #28a745; }
        .error-icon { color: #dc3545; }
        .info-icon { color: #17a2b8; }
        
        .verification-title {
            color: #4A6FAE;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .verification-message {
            color: <?php echo $tipo_mensagem === 'success' ? '#155724' : ($tipo_mensagem === 'error' ? '#721c24' : '#0c5460'); ?>;
            background-color: <?php echo $tipo_mensagem === 'success' ? '#d4edda' : ($tipo_mensagem === 'error' ? '#f8d7da' : '#d1ecf1'); ?>;
            border: 1px solid <?php echo $tipo_mensagem === 'success' ? '#c3e6cb' : ($tipo_mensagem === 'error' ? '#f5c6cb' : '#bee5eb'); ?>;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .verification-button {
            background-color: #1a5fce;
            color: white;
            border: none;
            border-radius: 8px;
            height: 46px;
            padding: 0 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            text-decoration: none;
            display: inline-block;
            line-height: 46px;
        }
        
        .verification-button:hover {
            background-color: #0f4db5;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-link {
            margin-top: 20px;
        }
        
        .login-link a {
            color: #1a5fce;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-icon">
                <?php if ($tipo_mensagem === 'success'): ?>
                    <i class="fas fa-check-circle success-icon"></i>
                <?php elseif ($tipo_mensagem === 'error'): ?>
                    <i class="fas fa-times-circle error-icon"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle info-icon"></i>
                <?php endif; ?>
            </div>
            
            <h1 class="verification-title">Verificação de E-mail</h1>
            
            <div class="verification-message">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
            
            <?php if ($tipo_mensagem === 'success'): ?>
                <a href="login_cliente.php" class="verification-button">
                    Ir para Login
                </a>
            <?php else: ?>
                <a href="cadastro_cliente.php" class="verification-button">
                    Voltar para Cadastro
                </a>
            <?php endif; ?>
            
            <?php if ($tipo_mensagem === 'success'): ?>
            <div class="login-link">
                <small>Já pode <a href="login_cliente.php">fazer login</a> na sua conta.</small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>