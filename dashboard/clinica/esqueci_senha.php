<?php
// esqueci_senha.php
session_start();

// Configuração de e-mail usando PHPMailer
$base_path = dirname(dirname(__DIR__)) . '/';
$log_dir = $base_path . 'logs/';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Caminho do PHPMailer
$phpmailer_path = $base_path . 'PHPMailer/src/';
$phpmailer_instalado = file_exists($phpmailer_path . 'PHPMailer.php');

if ($phpmailer_instalado) {
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
}

// Configurações de email - GMAIL
$smtp_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'secure' => 'tls',
    'user' => 'clinicaestrela2026@gmail.com',
    'pass' => 'rozsjxkdjulukyhh',
    'from' => 'clinicaestrela2026@gmail.com',
    'from_name' => 'Clínica Estrela'
];

// Função para enviar e-mail de redefinição de senha
function enviarEmailRedefinicao($email, $nome, $smtp_config, $phpmailer_instalado, $phpmailer_path, $log_dir) {
    
    if (!$phpmailer_instalado) {
        error_log("PHPMailer não encontrado em: " . $phpmailer_path);
        return false;
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host       = $smtp_config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_config['user'];
        $mail->Password   = $smtp_config['pass'];
        $mail->SMTPSecure = $smtp_config['secure'];
        $mail->Port       = $smtp_config['port'];
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom($smtp_config['from'], $smtp_config['from_name']);
        $mail->addAddress($email, $nome);
        $mail->addReplyTo($smtp_config['from'], $smtp_config['from_name']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Clínica Estrela - Redefinição de senha';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2A5C8F 0%, #1e3f61 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: #e3f2fd; border-left: 4px solid #2A5C8F; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .button { display: inline-block; background: #2A5C8F; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                .button:hover { background: #1e3f61; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
                .warning { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Clínica Estrela</h1>
                    <p>Redefinição de senha</p>
                </div>
                <div class='content'>
                    <h2>Olá, $nome!</h2>
                    <p>Recebemos uma solicitação para redefinir sua senha de acesso ao sistema da Clínica Estrela.</p>
                    
                    <div class='info-box'>
                        <p><strong>E-mail:</strong> $email</p>
                    </div>
                    
                    <p>Para criar uma nova senha, clique no botão abaixo:</p>
                    
                    <div style='text-align: center;'>
                        <a href='http://localhost/clinicaestrela/dashboard/clinica/definir_nova_senha.php?email=" . urlencode($email) . "&temp=1' class='button'>Redefinir minha senha</a>
                    </div>
                    
                    <p>Se você não solicitou esta redefinição, por favor ignore este e-mail.</p>
                    
                    <div class='warning'>
                        <strong>🔒 Segurança:</strong> Este link é válido apenas para esta solicitação.
                    </div>
                </div>
                <div class='footer'>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>&copy; " . date('Y') . " Clínica Estrela. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Olá $nome,\n\nRecebemos uma solicitação para redefinir sua senha de acesso ao sistema da Clínica Estrela.\n\nPara criar uma nova senha, acesse: http://localhost/clinicaestrela/dashboard/clinica/definir_nova_senha.php?email=" . urlencode($email) . "&temp=1\n\nSe você não solicitou esta redefinição, por favor ignore este e-mail.\n\nAtenciosamente,\nEquipe Clínica Estrela";
        
        $mail->send();
        
        // Registrar log de sucesso
        $log_file = $log_dir . 'email_success.log';
        $log_message = date('Y-m-d H:i:s') . " - E-mail de redefinição enviado para: $email\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        return true;
        
    } catch (Exception $e) {
        // Registrar log de erro
        $log_file = $log_dir . 'email_errors.log';
        $log_message = date('Y-m-d H:i:s') . " - Erro ao enviar e-mail de redefinição para $email: " . $e->getMessage() . "\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        return false;
    }
}

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $mensagem = 'Por favor, informe seu e-mail.';
        $tipo_mensagem = 'erro';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'E-mail inválido.';
        $tipo_mensagem = 'erro';
    } else {
        // Verificar se o e-mail existe no JSON
        $jsonPath = '../dados/users.json';
        
        if (file_exists($jsonPath)) {
            $usuarios = json_decode(file_get_contents($jsonPath), true);
            $usuarioEncontrado = null;
            
            foreach ($usuarios as $user) {
                if ($user['email'] === $email && $user['ativo']) {
                    $usuarioEncontrado = $user;
                    break;
                }
            }
            
            if ($usuarioEncontrado) {
                // Enviar e-mail de redefinição
                $email_enviado = enviarEmailRedefinicao($email, $usuarioEncontrado['nome'], $smtp_config, $phpmailer_instalado, $phpmailer_path, $log_dir);
                
                if ($email_enviado) {
                    $mensagem = 'E-mail de redefinição enviado com sucesso! Verifique sua caixa de entrada.';
                    $tipo_mensagem = 'sucesso';
                } else {
                    $mensagem = 'Erro ao enviar e-mail. Tente novamente mais tarde.';
                    $tipo_mensagem = 'erro';
                }
            } else {
                $mensagem = 'Este e-mail não está cadastrado no sistema da Clínica Estrela.';
                $tipo_mensagem = 'erro';
            }
        } else {
            $mensagem = 'Erro no sistema. Tente novamente mais tarde.';
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Clínica Estrela</title>
    
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
            <h1>Recuperar Senha</h1>
            <p>Digite seu e-mail para receber as instruções</p>
        </div>
        
        <div class="login-content">
            
            <?php if ($mensagem): ?>
                <div class="<?php echo $tipo_mensagem; ?>-message">
                    <i class="fas fa-<?php echo $tipo_mensagem === 'sucesso' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">Seu E-mail Profissional</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="nome@sistema.com" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-paper-plane"></i> Enviar instruções
                </button>
            </form>
            
            <div class="back-home">
                <a href="login_clinica.php">
                    <i class="fas fa-arrow-left"></i> Voltar para o login
                </a>
            </div>
        </div>
    </div>
</body>
</html>