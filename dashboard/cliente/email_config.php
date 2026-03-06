<?php
// Configuração de e-mail usando PHPMailer (versão sem Composer)

// Criar pasta de logs automaticamente
$base_path = dirname(dirname(__DIR__)) . '/';
$log_dir = $base_path . 'logs/';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Caminho do PHPMailer
$phpmailer_path = $base_path . 'PHPMailer/src/';
$phpmailer_instalado = file_exists($phpmailer_path . 'PHPMailer.php');

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

/**
 * Envia e-mail de verificação para o responsável (cadastro)
 */
function enviarEmailVerificacao($email, $nome, $token) {
    global $phpmailer_instalado, $smtp_config, $phpmailer_path, $log_dir;
    
    // Log de tentativa
    file_put_contents($log_dir . 'email_log.txt', 
        "[" . date('Y-m-d H:i:s') . "] Tentando enviar para: $email - PHPMailer instalado: " . ($phpmailer_instalado ? 'SIM' : 'NÃO') . "\n", 
        FILE_APPEND);
    
    // Se PHPMailer não estiver instalado, usar modo teste
    if (!$phpmailer_instalado) {
        file_put_contents($log_dir . 'email_log.txt', 
            "[" . date('Y-m-d H:i:s') . "] PHPMailer não encontrado em: $phpmailer_path - Usando modo teste\n", 
            FILE_APPEND);
        return enviarEmailVerificacaoTeste($email, $nome, $token);
    }
    
    // Incluir os arquivos necessários do PHPMailer
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = $smtp_config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_config['user'];
        $mail->Password   = $smtp_config['pass'];
        $mail->SMTPSecure = $smtp_config['secure'];
        $mail->Port       = $smtp_config['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';
        $mail->SMTPDebug  = 0;
        
        // Remetente e destinatário
        $mail->setFrom($smtp_config['from'], $smtp_config['from_name']);
        $mail->addAddress($email, $nome);
        $mail->addReplyTo($smtp_config['from'], $smtp_config['from_name']);
        
        // Configurar para HTML
        $mail->isHTML(true);
        $mail->Subject = 'Confirme seu cadastro - Clínica Estrela';
        
        // Criar link de verificação
        $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host_url = $_SERVER['HTTP_HOST'];
        $link = "{$protocolo}{$host_url}/clinicaestrela/dashboard/cliente/verificar_email.php?token={$token}";
        
        // Corpo do e-mail em HTML
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Confirme seu cadastro</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Poppins', Arial, sans-serif;
                    background-color: #f5f8ff;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 24px;
                    padding: 40px 30px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #eaeef7;
                    padding-bottom: 25px;
                    margin-bottom: 25px;
                }
                .logo {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 15px;
                    display: block;
                    border-radius: 50%;
                }
                h1 {
                    color: #2c3e50;
                    font-size: 28px;
                    font-weight: 700;
                    margin: 10px 0;
                }
                .greeting {
                    background: #f8fafd;
                    padding: 20px;
                    border-radius: 16px;
                    text-align: center;
                    margin-bottom: 25px;
                }
                .greeting p {
                    color: #2c3e50;
                    font-size: 18px;
                    line-height: 1.6;
                }
                .greeting strong {
                    color: #3498db;
                    font-size: 20px;
                }
                .message {
                    color: #444;
                    line-height: 1.8;
                    margin-bottom: 30px;
                    font-size: 16px;
                    text-align: center;
                }
                .button-container {
                    text-align: center;
                    margin: 35px 0;
                }
                .button {
                    display: inline-block;
                    background: linear-gradient(135deg, #3498db, #2980b9);
                    color: white;
                    text-decoration: none;
                    padding: 16px 40px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 18px;
                    box-shadow: 0 5px 15px rgba(52,152,219,0.4);
                    transition: all 0.3s ease;
                }
                .button:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 25px rgba(52,152,219,0.5);
                }
                .warning {
                    background-color: #fff3cd;
                    border: 1px solid #ffeeba;
                    color: #856404;
                    padding: 15px;
                    border-radius: 12px;
                    font-size: 14px;
                    margin: 25px 0;
                    text-align: center;
                }
                .footer {
                    margin-top: 35px;
                    padding-top: 25px;
                    border-top: 1px solid #eaeef7;
                    text-align: center;
                    color: #888;
                    font-size: 13px;
                }
                @media (max-width: 480px) {
                    .container { padding: 25px 20px; }
                    h1 { font-size: 24px; }
                    .button { padding: 14px 30px; font-size: 16px; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$protocolo}{$host_url}/clinicaestrela/imagens/logo_clinica_estrela.png' 
                         alt='Clínica Estrela' 
                         class='logo'>
                    <h1>Clínica Estrela</h1>
                </div>
                
                <div class='greeting'>
                    <p>Olá, <strong>" . htmlspecialchars($nome) . "</strong>!</p>
                </div>
                
                <div class='message'>
                    <p>Recebemos seu cadastro em nossa clínica. Para ativar sua conta e garantir a segurança dos seus dados, precisamos confirmar seu e-mail.</p>
                </div>
                
                <div class='button-container'>
                    <a href='{$link}' class='button'>
                        👉 CONFIRMAR E-MAIL
                    </a>
                </div>
                
                <div class='warning'>
                    <strong>⚠️ Link válido por 24 horas</strong><br>
                    Se você não realizou este cadastro, ignore este e-mail.
                </div>
                
                <div class='footer'>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>&copy; " . date('Y') . " Clínica Estrela. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Versão em texto puro (fallback)
        $mail->AltBody = "Olá {$nome},\n\n" .
                         "Recebemos seu cadastro na Clínica Estrela.\n\n" .
                         "Para ativar sua conta, acesse o link abaixo:\n" .
                         "{$link}\n\n" .
                         "Se você não realizou este cadastro, ignore este e-mail.\n\n" .
                         "Atenciosamente,\n" .
                         "Clínica Estrela";
        
        $mail->send();
        
        // Log de sucesso
        file_put_contents($log_dir . 'email_log.txt', 
            "[" . date('Y-m-d H:i:s') . "] ✅ E-mail enviado com sucesso para: $email\n", 
            FILE_APPEND);
        
        return true;
        
    } catch (Exception $e) {
        // Log de erro detalhado
        $erro = "[" . date('Y-m-d H:i:s') . "] ❌ ERRO ao enviar e-mail para: $email\n";
        $erro .= "Mensagem: " . $e->getMessage() . "\n";
        $erro .= "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        file_put_contents($log_dir . 'erros_smtp.txt', $erro, FILE_APPEND);
        
        return enviarEmailVerificacaoTeste($email, $nome, $token);
    }
}

/**
 * Função de teste que gera link sem enviar e-mail real
 */
function enviarEmailVerificacaoTeste($email, $nome, $token) {
    global $log_dir;
    
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $link = "{$protocolo}{$host}/clinicaestrela/dashboard/cliente/verificar_email.php?token={$token}";
    
    // Salvar em arquivo de log
    $log = "[" . date('Y-m-d H:i:s') . "] 🔧 MODO TESTE - E-mail para: $email - Nome: $nome - Link: $link\n";
    file_put_contents($log_dir . 'email_log.txt', $log, FILE_APPEND);
    
    // Mostrar alerta com o link
    echo "<script>
        alert('🔧 MODO DESENVOLVIMENTO\\n\\nE-mail de verificação gerado:\\n{$link}');
        window.open('{$link}', '_blank');
    </script>";
    
    return true;
}

/**
 * Envia código 
 */
function enviar2FANumberMatching($email, $nome, $numeros_email, $numero_site) {
    global $phpmailer_instalado, $smtp_config, $phpmailer_path, $log_dir;
    
    // Log de tentativa
    file_put_contents($log_dir . '2fa_log.txt', 
        "[" . date('Y-m-d H:i:s') . "] Tentando enviar 2FA para: $email - Números: " . implode(', ', $numeros_email) . " - Site: $numero_site\n", 
        FILE_APPEND);
    
    
    if (!$phpmailer_instalado) {
        return enviar2FANumberMatchingTeste($email, $nome, $numeros_email, $numero_site);
    }
    
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host       = $smtp_config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_config['user'];
        $mail->Password   = $smtp_config['pass'];
        $mail->SMTPSecure = $smtp_config['secure'];
        $mail->Port       = $smtp_config['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';
        
        // Remetente e destinatário
        $mail->setFrom($smtp_config['from'], $smtp_config['from_name']);
        $mail->addAddress($email, $nome);
        $mail->addReplyTo($smtp_config['from'], $smtp_config['from_name']);
        
        // Configurar para HTML
        $mail->isHTML(true);
        $mail->Subject = '🔐 Código de verificação - Clínica Estrela';
        
        // URL base
        $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host_url = $_SERVER['HTTP_HOST'];
        
        // Link de verificação (o usuário clica no número correspondente)
        $link_base = "{$protocolo}{$host_url}/clinicaestrela/dashboard/cliente/verificar_number_matching.php?numero=";
        
        // E-mail em HTML - MOSTRA OS 3 NÚMEROS
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verificação em duas etapas</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "Poppins", Arial, sans-serif;
                    background-color: #f4f7f9;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 24px;
                    padding: 40px 30px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 15px;
                    display: block;
                    border-radius: 50%;
                }
                h1 {
                    color: #2c3e50;
                    font-size: 28px;
                    font-weight: 700;
                    margin: 10px 0 5px;
                }
                h2 {
                    color: #3498db;
                    font-size: 20px;
                    font-weight: 500;
                    margin: 0 0 20px;
                }
                .greeting {
                    background: #f8fafd;
                    padding: 20px;
                    border-radius: 16px;
                    text-align: center;
                    margin-bottom: 30px;
                }
                .greeting p {
                    color: #2c3e50;
                    font-size: 18px;
                    line-height: 1.6;
                }
                .greeting strong {
                    color: #3498db;
                    font-size: 20px;
                }
                .numbers-container {
                    display: flex;
                    justify-content: center;
                    gap: 20px;
                    margin: 35px 0;
                    flex-wrap: wrap;
                }
                .number-box {
                    width: 100px;
                    height: 100px;
                    background: linear-gradient(145deg, #ffffff, #f5f9ff);
                    border: 3px solid #3498db;
                    border-radius: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 42px;
                    font-weight: 700;
                    color: #2c3e50;
                    box-shadow: 0 10px 20px rgba(52,152,219,0.15);
                }
                .instructions {
                    background: #e8f4fd;
                    border-left: 4px solid #3498db;
                    padding: 20px;
                    border-radius: 12px;
                    margin: 30px 0;
                }
                .instructions p {
                    color: #2c3e50;
                    margin: 12px 0;
                    font-size: 16px;
                    line-height: 1.6;
                }
                .instructions strong {
                    color: #3498db;
                    font-size: 18px;
                }
                .button-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .expiry {
                    text-align: center;
                    margin: 25px 0;
                    padding: 15px;
                    background: #fff3cd;
                    border: 1px solid #ffe69c;
                    border-radius: 12px;
                    color: #856404;
                    font-weight: 600;
                }
                .warning {
                    background: #f8d7da;
                    border: 1px solid #f5c2c7;
                    color: #842029;
                    padding: 15px;
                    border-radius: 12px;
                    font-size: 14px;
                    margin: 20px 0 0;
                    text-align: center;
                }
                .footer {
                    margin-top: 35px;
                    padding-top: 25px;
                    border-top: 1px solid #e9ecef;
                    text-align: center;
                    color: #8a9bb5;
                    font-size: 13px;
                }
                @media (max-width: 480px) {
                    .container { padding: 25px 20px; }
                    .number-box { width: 80px; height: 80px; font-size: 36px; }
                    h1 { font-size: 24px; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <img src="' . $protocolo . $host_url . '/clinicaestrela/imagens/logo_clinica_estrela.png" 
                         alt="Clínica Estrela" 
                         class="logo">
                    <h1>Clínica Estrela</h1>
                    <h2>🔐 Verificação em duas etapas</h2>
                </div>
                
                <div class="greeting">
                    <p>Olá, <strong>' . htmlspecialchars($nome) . '</strong>!</p>
                </div>
                
                <div class="message" style="text-align: center; margin: 20px 0;">
                    <p><strong>Clique no número que apareceu no site da Clínica:</strong></p>
                </div>
                
                <!-- Botões para cada número -->
                <div style="display: flex; justify-content: center; gap: 20px; margin: 30px 0; flex-wrap: wrap;">';
        
        // Adicionar botões 
        foreach ($numeros_email as $num) {
            $mail->Body .= '
                    <a href="' . $link_base . $num . '" 
                    style="display: inline-flex; 
                            align-items: center; 
                            justify-content: center; 
                            width: 100px; 
                            height: 100px; 
                            background: linear-gradient(135deg, #3498db, #2980b9); 
                            color: white; 
                            border-radius: 20px; 
                            text-decoration: none; 
                            font-size: 42px; 
                            font-weight: bold; 
                            box-shadow: 0 8px 15px rgba(52,152,219,0.3); 
                            transition: all 0.3s ease; 
                            margin: 5px;
                            line-height: 1;
                            text-align: center;
                            vertical-align: middle;
                            box-sizing: border-box;">
                        ' . $num . '
                    </a>';
        }
        
        $mail->Body .= '
                </div>
                
                <div class="instructions">
                    <p><strong>📋 Como verificar:</strong></p>
                    <p><strong>1.</strong> No site da Clínica Estrela apareceu um número </p>
                    <p><strong>2.</strong> Neste e-mail você vê 3 números</p>
                    <p><strong>3.</strong> Clique no número que é <strong>IGUAL</strong> ao que apareceu no site</p>
                </div>
                
                <div class="expiry">
                    <i>⏰</i> Este código expira em 5 minutos
                </div>
                
                <div class="warning">
                    <strong> Atenção:</strong> Se você não tentou fazer login, ignore este e-mail.
                </div>
                
                <div class="footer">
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>&copy; ' . date('Y') . ' Clínica Estrela. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        

        $mail->AltBody = "Clínica Estrela - Verificação em duas etapas\n\n" .
                         "Olá {$nome},\n\n" .
                         "Número que apareceu no site da Clínica: {$numero_site}\n\n" .
                         "Números disponíveis no e-mail:\n" .
                         "👉 {$numeros_email[0]}\n" .
                         "👉 {$numeros_email[1]}\n" .
                         "👉 {$numeros_email[2]}\n\n" .
                         "INSTRUÇÕES:\n" .
                         "1. No site da Clínica Estrela apareceu o número {$numero_site}\n" .
                         "2. Neste e-mail você vê 3 números\n" .
                         "3. Acesse o link do número que é IGUAL ao que apareceu no site\n\n" .
                         "Links:\n" .
                         "{$link_base}{$numeros_email[0]}\n" .
                         "{$link_base}{$numeros_email[1]}\n" .
                         "{$link_base}{$numeros_email[2]}\n\n" .
                         "Código expira em 5 minutos.\n\n" .
                         "Se não foi você, ignore este e-mail.\n\n" .
                         "Clínica Estrela";
        
        $mail->send();
        
        // Log de sucesso
        file_put_contents($log_dir . '2fa_log.txt', 
            "[" . date('Y-m-d H:i:s') . "] ✅ 2FA enviado com sucesso para: $email\n", 
            FILE_APPEND);
        
        return true;
        
    } catch (Exception $e) {
        file_put_contents($log_dir . 'erros_smtp.txt', 
            "[" . date('Y-m-d H:i:s') . "] ❌ ERRO 2FA: " . $e->getMessage() . "\n", 
            FILE_APPEND);
        
        return enviar2FANumberMatchingTeste($email, $nome, $numeros_email, $numero_site);
    }
}

/**
 * Modo teste para 2FA
 */
function enviar2FANumberMatchingTeste($email, $nome, $numeros_email, $numero_site) {
    global $log_dir;
    
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $link_base = "{$protocolo}{$host}/clinicaestrela/dashboard/cliente/verificar_number_matching.php?numero=";
    
    $log = "[" . date('Y-m-d H:i:s') . "] 🔧 MODO TESTE - 2FA para: $email - Números e-mail: " . implode(', ', $numeros_email) . " - Número site: $numero_site\n";
    file_put_contents($log_dir . '2fa_log.txt', $log, FILE_APPEND);
    
    // Popup com instruções claras
    echo "<div style='position: fixed; top: 20px; right: 20px; background: white; border: 3px solid #3498db; border-radius: 16px; padding: 25px; max-width: 450px; font-family: Poppins, sans-serif; box-shadow: 0 15px 35px rgba(0,0,0,0.2); z-index: 9999;'>";
    echo "<h3 style='color: #2c3e50; margin: 0 0 15px; text-align: center;'>🔐 MODO TESTE - 2FA</h3>";
    echo "<p style='text-align: center; margin: 10px 0;'><strong>Email:</strong> {$email}</p>";
    echo "<p style='text-align: center; margin: 10px 0;'><strong>Nome:</strong> {$nome}</p>";
    
    // Número do site em destaque
    echo "<div style='text-align: center; margin: 20px 0; padding: 20px; background: #e8f4fd; border-radius: 16px;'>";
    echo "<p style='font-weight: 600; color: #2c3e50; margin-bottom: 15px;'>NÚMERO NO SITE DA CLÍNICA:</p>";
    echo "<div style='width: 120px; height: 120px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 60px; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; color: white; margin: 0 auto; box-shadow: 0 10px 25px rgba(52,152,219,0.4);'>{$numero_site}</div>";
    echo "</div>";
    
    // Números do e-mail como botões clicáveis
    echo "<div style='text-align: center; margin: 25px 0;'>";
    echo "<p style='font-weight: 600; color: #2c3e50; margin-bottom: 15px;'>CLIQUE NO NÚMERO CORRESPONDENTE:</p>";
    echo "<div style='display: flex; justify-content: center; gap: 20px; margin: 20px 0; flex-wrap: wrap;'>";
    foreach ($numeros_email as $num) {
        $link = $link_base . $num;
        $destaque = ($num == $numero_site) ? 'border: 4px solid #27ae60; transform: scale(1.1);' : '';
        echo "<a href='{$link}' target='_blank' style='width: 90px; height: 90px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold; color: white; text-decoration: none; box-shadow: 0 8px 15px rgba(52,152,219,0.3); transition: all 0.3s ease; {$destaque}'>{$num}</a>";
    }
    echo "</div>";
    echo "</div>";
    
    // Instruções
    echo "<div style='background: #f0f7ff; padding: 20px; border-radius: 12px; margin: 20px 0;'>";
    echo "<p style='margin: 8px 0;'><span style='display: inline-block; width: 24px; height: 24px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 24px; margin-right: 10px;'>1</span> No site apareceu: <strong style='font-size: 22px; color: #3498db;'>{$numero_site}</strong></p>";
    echo "<p style='margin: 8px 0;'><span style='display: inline-block; width: 24px; height: 24px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 24px; margin-right: 10px;'>2</span> Clique no número <strong style='color: #27ae60;'>IGUAL</strong> acima</p>";
    echo "</div>";
    
    echo "<p style='text-align: center; margin: 15px 0 5px; padding: 10px; background: #fff3cd; border-radius: 8px; color: #856404; font-weight: 600;'><i class='fas fa-clock'></i> Código expira em 5 minutos</p>";
    echo "</div>";
    
    return true;
}
?>