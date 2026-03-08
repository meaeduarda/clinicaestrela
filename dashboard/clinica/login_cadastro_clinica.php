<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Estrela - Cadastro de Colaborador</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">

    <!-- Importação da fonte Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard/clinica/login_cadastro_clinica.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="page-container">
        <div class="main-wrapper">
            <div class="mascote-container desktop-only">
                <img src="../../imagens/mascote_up.png" alt="Mascote" class="mascote-image">
            </div>
            
            <!-- Container branco do formulário -->
            <div class="form-card">
                <div class="card-header">
                    <div class="logo-container">
                        <img src="../../imagens/logo_marca_aside.png" alt="Clínica Estrela" class="logo-image">
                    </div>
                </div>
                
                <div class="mascote-mobile-container">
                    <img src="../../imagens/mascote_down.png" alt="Mascote" class="mascote-mobile">
                </div>
                
                <div class="form-title-container">
                    <h2 class="form-title">Cadastro de Colaborador</h2>
                </div>
                
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
                
                // Verificar se o PHPMailer está instalado
                $phpmailer_instalado = file_exists($phpmailer_path . 'PHPMailer.php');
                
                // Se o PHPMailer estiver instalado, incluir as classes
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
                
                // Função para enviar e-mail de boas-vindas usando SMTP do Gmail
                function enviarEmailBoasVindas($email, $nome, $senha, $smtp_config, $phpmailer_instalado, $phpmailer_path) {
                    
                    // Verificar se o PHPMailer está instalado
                    if (!$phpmailer_instalado) {
                        error_log("PHPMailer não encontrado em: " . $phpmailer_path);
                        return false;
                    }
                    
                    try {
                        // Usar as classes do PHPMailer
                        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                        
                        // Configurações do servidor SMTP do Gmail
                        $mail->isSMTP();
                        $mail->Host       = $smtp_config['host'];
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $smtp_config['user'];
                        $mail->Password   = $smtp_config['pass'];
                        $mail->SMTPSecure = $smtp_config['secure'];
                        $mail->Port       = $smtp_config['port'];
                        
                        // Configuração do charset para acentos
                        $mail->CharSet = 'UTF-8';
                        
                        // Remetente e destinatário
                        $mail->setFrom($smtp_config['from'], $smtp_config['from_name']);
                        $mail->addAddress($email, $nome);
                        $mail->addReplyTo($smtp_config['from'], $smtp_config['from_name']);
                        
                        // Conteúdo do e-mail
                        $mail->isHTML(true);
                        $mail->Subject = 'Clínica Estrela - Seu cadastro foi realizado';
                        
                        // Corpo do e-mail em HTML
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
                                .info-box p { margin: 10px 0; }
                                .info-box strong { color: #2A5C8F; }
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
                                    <p>Bem-vindo à nossa equipe!</p>
                                </div>
                                <div class='content'>
                                    <h2>Olá, $nome!</h2>
                                    <p>Seu cadastro no sistema da Clínica Estrela foi realizado com sucesso.</p>
                                    
                                    <div class='info-box'>
                                        <h3 style='margin-top: 0; color: #2A5C8F;'>Suas credenciais de acesso:</h3>
                                        <p><strong>E-mail:</strong> $email</p>
                                        <p><strong>Senha temporária:</strong> <span style='font-size: 18px; font-weight: bold; color: #2A5C8F;'>$senha</span></p>
                                    </div>
                                    
                                    <p><strong>⚠️ Importante:</strong> Por questões de segurança, você deve alterar sua senha no primeiro acesso.</p>
                                    
                                    <div style='text-align: center;'>
                                        <a href='http://localhost/clinicaestrela/dashboard/clinica/definir_nova_senha.php?email=" . urlencode($email) . "&temp=1' class='button'>Definir nova senha</a>
                                    </div>
                                    
                                    <p>Se você não solicitou este cadastro, por favor ignore este e-mail.</p>
                                    
                                    <div class='warning'>
                                        <strong>🔒 Segurança:</strong> Nunca compartilhe sua senha com ninguém.
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
                        
                        // Versão em texto plano (para clientes de e-mail que não suportam HTML)
                        $mail->AltBody = "Olá $nome,\n\nSeu cadastro no sistema da Clínica Estrela foi realizado com sucesso.\n\nSuas credenciais de acesso:\nE-mail: $email\nSenha temporária: $senha\n\nPor questões de segurança, você deve alterar sua senha no primeiro acesso.\n\nAcesse: http://localhost/clinicaestrela/dashboard/clinica/definir_nova_senha.php?email=" . urlencode($email) . "&temp=1\n\nAtenciosamente,\nEquipe Clínica Estrela";
                        
                        $mail->send();
                        
                        // Registrar log de sucesso
                        $log_file = $log_dir . 'email_success.log';
                        $log_message = date('Y-m-d H:i:s') . " - E-mail enviado com sucesso. Verifique sua caixa de entrada. E-mail: $email\n";
                        file_put_contents($log_file, $log_message, FILE_APPEND);
                        
                        return true;
                        
                    } catch (Exception $e) {
                        // Registrar log de erro
                        $log_file = $log_dir . 'email_errors.log';
                        $log_message = date('Y-m-d H:i:s') . " - Erro ao enviar e-mail para $email: " . $e->getMessage() . "\n";
                        file_put_contents($log_file, $log_message, FILE_APPEND);
                        
                        return false;
                    }
                }
                
                // Inicializar variáveis
                $nome = $email = $senha = $perfil = '';
                $nome_error = $email_error = $senha_error = $perfil_error = '';
                $success = false;
                $form_submitted = false;
                $email_enviado = false;
                $erro_email = '';
                $senha_salva = ''; // Para mostrar na tela em caso de falha no email
                
                // Suprimir warnings
                error_reporting(E_ALL & ~E_WARNING);
                
                // Verificar se o formulário foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $form_submitted = true;
                    
                    // Obter dados do formulário
                    $nome = trim($_POST['nome'] ?? '');
                    $email = trim($_POST['email'] ?? '');
                    $senha = $_POST['senha'] ?? '';
                    $perfil = $_POST['perfil'] ?? '';
                    
                    // Validar campos obrigatórios
                    $erro_geral = false;
                    
                    if (empty($nome)) {
                        $nome_error = 'Nome é obrigatório';
                        $erro_geral = true;
                    }
                    
                    if (empty($email)) {
                        $email_error = 'E-mail é obrigatório';
                        $erro_geral = true;
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email_error = 'E-mail inválido';
                        $erro_geral = true;
                    }
                    
                    if (empty($senha)) {
                        $senha_error = 'Senha é obrigatória';
                        $erro_geral = true;
                    } elseif (strlen($senha) < 4) {
                        $senha_error = 'Senha deve ter pelo menos 4 caracteres';
                        $erro_geral = true;
                    }
                    
                    if (empty($perfil) || $perfil === '') {
                        $perfil_error = 'Selecione um perfil válido';
                        $erro_geral = true;
                    }
                    
                    // Se não houver erros de validação, verificar no arquivo JSON
                    if (!$erro_geral) {
                        // Caminho para o arquivo JSON
                        $json_file = 'C:/wamp64/www/clinicaestrela/dashboard/dados/users.json';
                        
                        // Ler usuários existentes
                        $users = [];
                        if (file_exists($json_file)) {
                            $json_content = file_get_contents($json_file);
                            $users = json_decode($json_content, true) ?: [];
                        }
                        
                        // Verificar se usuário já existe (por nome ou email)
                        $user_exists = false;
                        $email_exists = false;
                        
                        foreach ($users as $user) {
                            if (strtolower($user['nome']) === strtolower($nome)) {
                                $user_exists = true;
                            }
                            if (strtolower($user['email']) === strtolower($email)) {
                                $email_exists = true;
                            }
                        }
                        
                        if ($user_exists) {
                            $nome_error = 'Nome de usuário já cadastrado';
                            $erro_geral = true;
                        }
                        
                        if ($email_exists) {
                            $email_error = 'E-mail já cadastrado';
                            $erro_geral = true;
                        }
                        
                        // Se não houver erros, adicionar novo usuário
                        if (!$erro_geral) {
                            $novo_usuario = [
                                'id' => count($users) + 1,
                                'nome' => $nome,
                                'email' => $email,
                                'senha' => $senha,
                                'perfil' => $perfil,
                                'ativo' => true,
                                'senha_temporaria' => true,
                                'criado_em' => date('Y-m-d H:i:s')
                            ];
                            
                            $users[] = $novo_usuario;
                            
                            // Salvar no arquivo JSON
                            file_put_contents($json_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            
                            // Tentar enviar e-mail de boas-vindas com a senha temporária
                            $email_enviado = enviarEmailBoasVindas($email, $nome, $senha, $smtp_config, $phpmailer_instalado, $phpmailer_path);
                            
                            // Guardar a senha para mostrar em caso de falha no email
                            $senha_salva = $senha;
                            
                            // Limpar campos para novo cadastro
                            $nome = $email = $senha = $perfil = '';
                            $success = true;
                        }
                    }
                }
                ?>
                
                <form method="POST" action="" class="cadastro-form">
                    <!-- Seção: Dados Pessoais -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3>Dados de Acesso</h3>
                        </div>
                        
                        <div class="form-fields">
                            <!-- Campo Nome -->
                            <div class="field-row">
                                <label for="nome" class="field-label required">Nome:</label>
                                <div class="field-input-wrapper">
                                    <input type="text" id="nome" name="nome" class="field-input" placeholder="Nome Completo" value="<?php echo htmlspecialchars($nome); ?>">
                                    <?php if ($form_submitted && $nome_error): ?>
                                        <div class="error-message"><?php echo $nome_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo E-mail -->
                            <div class="field-row">
                                <label for="email" class="field-label required">E-mail:</label>
                                <div class="field-input-wrapper">
                                    <input type="email" id="email" name="email" class="field-input" placeholder="ana@clinica.com" value="<?php echo htmlspecialchars($email); ?>">
                                    <?php if ($form_submitted && $email_error): ?>
                                        <div class="error-message"><?php echo $email_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção: Dados de Acesso -->
                    <div class="form-section">                        
                        <div class="form-fields">
                            <!-- Campo Senha -->
                            <div class="field-row">
                                <label for="senha" class="field-label required">Senha Temporária:</label>
                                <div class="field-input-wrapper password-wrapper">
                                    <input type="password" id="senha" name="senha" class="field-input password-input" value="<?php echo htmlspecialchars($senha); ?>">
                                    <button type="button" class="toggle-password" onclick="togglePassword()">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <?php if ($form_submitted && $senha_error): ?>
                                        <div class="error-message"><?php echo $senha_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo Perfil -->
                            <div class="field-row">
                                <label for="perfil" class="field-label required">Perfil de Acesso:</label>
                                <div class="field-input-wrapper">
                                    <select id="perfil" name="perfil" class="field-select">
                                        <option value="">Selecione um perfil...</option>
                                        <option value="Nutricionista" <?php echo ($perfil === 'Nutricionista') ? 'selected' : ''; ?>>Nutricionista</option>
                                        <option value="Fisioterapeuta" <?php echo ($perfil === 'Fisioterapeuta') ? 'selected' : ''; ?>>Fisioterapeuta</option>
                                        <option value="Coordenador Clinico" <?php echo ($perfil === 'Coordenador Clinico') ? 'selected' : ''; ?>>Coordenador Clínico</option>
                                        <option value="Psicopedagogia" <?php echo ($perfil === 'Psicopedagogia') ? 'selected' : ''; ?>>Psicopedagogia</option>
                                        <option value="Musicoterapeuta" <?php echo ($perfil === 'Musicoterapeuta') ? 'selected' : ''; ?>>Musicoterapeuta</option>
                                        <option value="Fonoterapeuta" <?php echo ($perfil === 'Fonoterapeuta') ? 'selected' : ''; ?>>Fonoterapeuta</option>
                                        <option value="Direção Clinica" <?php echo ($perfil === 'Direção Clinica') ? 'selected' : ''; ?>>Direção Clínica</option>
                                        <option value="Recepcionista" <?php echo ($perfil === 'Recepcionista') ? 'selected' : ''; ?>>Recepcionista</option>
                                        <option value="Terapeuta Ocupacional" <?php echo ($perfil === 'Terapeuta Ocupacional') ? 'selected' : ''; ?>>Terapeuta Ocupacional</option>
                                    </select>
                                    <?php if ($form_submitted && $perfil_error): ?>
                                        <div class="error-message"><?php echo $perfil_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botão Cadastrar -->
                    <div class="form-actions">
                        <button type="submit" class="submit-button">Cadastrar</button>
                    </div>
                </form>
                
                <!-- Mensagem de sucesso -->
                <?php if ($success): ?>
                <div class="feedback-section">
                    <div class="success-separator"></div>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> Usuário cadastrado com sucesso!
                        <?php if ($email_enviado): ?>
                            <br><small style="color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; display: block; margin-top: 10px;">
                                <i class="fas fa-envelope"></i> 
                                <strong>E-mail enviado!</strong> As instruções foram enviadas para <?php echo htmlspecialchars($email); ?>
                            </small>
                        <?php else: ?>
                            <br><small style="color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; display: block; margin-top: 10px;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Não foi possível enviar o e-mail.</strong><br>
                                <strong>Senha temporária:</strong> <?php echo htmlspecialchars($senha_salva); ?><br>
                                <a href="definir_nova_senha.php?email=<?php echo urlencode($email); ?>&temp=1" style="color: #2A5C8F; text-decoration: underline;">Clique aqui para definir sua nova senha</a>
                            </small>
                            
                            <?php if (!$phpmailer_instalado): ?>
                                <br><small style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; display: block; margin-top: 10px;">
                                    <i class="fas fa-times-circle"></i> 
                                    <strong>Erro:</strong> PHPMailer não encontrado. Verifique se a pasta está em: <strong><?php echo $phpmailer_path; ?></strong>
                                </small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('senha');
            const toggleButton = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('fa-eye');
                toggleButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('fa-eye-slash');
                toggleButton.classList.add('fa-eye');
            }
        }
        
        // Verificar se há mensagem de sucesso e rolar até ela
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>