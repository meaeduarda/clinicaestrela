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
                // Função para enviar e-mail de boas-vindas (versão silenciosa para desenvolvimento)
                function enviarEmailBoasVindas($email, $nome, $senha) {
                    // Em ambiente de desenvolvimento, apenas retorna false simulando falha
                    // e a senha será mostrada na tela
                    
                    // Descomente as linhas abaixo quando estiver em produção com servidor de email configurado
                    /*
                    $assunto = "Clínica Estrela - Seu cadastro foi realizado";
                    
                    $mensagem = "
                    <html>
                    <head>
                        <title>Cadastro realizado</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background-color: #2A5C8F; color: white; padding: 20px; text-align: center; }
                            .content { padding: 20px; background-color: #f9f9f9; }
                            .info-box { background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
                            .botao { background-color: #2A5C8F; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                            .footer { font-size: 12px; color: #666; text-align: center; padding: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>Clínica Estrela</h2>
                            </div>
                            <div class='content'>
                                <h3>Olá, $nome!</h3>
                                <p>Seu cadastro no sistema da Clínica Estrela foi realizado com sucesso.</p>
                                
                                <div class='info-box'>
                                    <p><strong>Suas credenciais de acesso:</strong></p>
                                    <p><strong>E-mail:</strong> $email</p>
                                    <p><strong>Senha temporária:</strong> $senha</p>
                                </div>
                                
                                <p>Por questões de segurança, você deve alterar sua senha no primeiro acesso.</p>
                                
                                <div style='text-align: center;'>
                                    <a href='http://localhost/clinicaestrela/dashboard/clinica/definir_nova_senha.php?email=" . urlencode($email) . "&temp=1' class='botao'>Definir nova senha</a>
                                </div>
                                
                                <p><small>Se você não solicitou este cadastro, por favor ignore este e-mail.</small></p>
                            </div>
                            <div class='footer'>
                                <p>Este é um e-mail automático, por favor não responda.</p>
                                <p>&copy; " . date('Y') . " Clínica Estrela. Todos os direitos reservados.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: naoresponda@clinicaestrela.com.br" . "\r\n";
                    
                    return @mail($email, $assunto, $mensagem, $headers);
                    */
                    
                    // Para ambiente de desenvolvimento, retorna false (não envia e-mail)
                    return false;
                }
                
                // Inicializar variáveis
                $nome = $email = $senha = $perfil = '';
                $nome_error = $email_error = $senha_error = $perfil_error = '';
                $success = false;
                $form_submitted = false;
                $email_enviado = false;
                $senha_salva = ''; // Para mostrar na tela em caso de falha no email
                
                // Suprimir warnings de mail() em desenvolvimento
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
                                'senha' => $senha, // Em produção, use password_hash($senha, PASSWORD_DEFAULT)
                                'perfil' => $perfil,
                                'ativo' => true,
                                'senha_temporaria' => true, // Marca que a senha atual é temporária
                                'criado_em' => date('Y-m-d H:i:s')
                            ];
                            
                            $users[] = $novo_usuario;
                            
                            // Salvar no arquivo JSON
                            file_put_contents($json_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            
                            // Tentar enviar e-mail de boas-vindas com a senha temporária
                            $email_enviado = enviarEmailBoasVindas($email, $nome, $senha);
                            
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
                            <br><small>Um e-mail com as instruções foi enviado para <?php echo htmlspecialchars($email); ?></small>
                        <?php else: ?>
                            <br><small style="color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; display: block; margin-top: 10px;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Ambiente de Desenvolvimento:</strong> O envio de e-mail não está configurado.<br>
                                <strong>Senha temporária:</strong> <?php echo htmlspecialchars($senha_salva); ?><br>
                                <a href="definir_nova_senha.php?email=<?php echo urlencode($email); ?>&temp=1" style="color: #2A5C8F; text-decoration: underline;">Clique aqui para definir sua nova senha</a>
                            </small>
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