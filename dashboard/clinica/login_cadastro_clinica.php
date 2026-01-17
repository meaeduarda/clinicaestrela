<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Estrela - Cadastro de Colaborador</title>
    <!-- Importação da fonte Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard/clinica/login_cadastro_clinica.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="page-container">
        <div class="main-wrapper">
            <!-- Mascote à esquerda (FORA do container branco) - VISÍVEL APENAS NO DESKTOP -->
            <div class="mascote-container desktop-only">
                <img src="../../imagens/mascote_up.png" alt="Mascote" class="mascote-image">
            </div>
            
            <!-- Container branco do formulário -->
            <div class="form-card">
                <!-- Logo DENTRO do container (APENAS LOGO NO CARD) -->
                <div class="card-header">
                    <div class="logo-container">
                        <img src="../../imagens/logo_marca_aside.png" alt="Clínica Estrela" class="logo-image">
                    </div>
                </div>
                
                <!-- MASCOTE MOBILE (aparece apenas no mobile, DENTRO DO CARD, depois da logo) -->
                <div class="mascote-mobile-container">
                    <img src="../../imagens/mascote_down.png" alt="Mascote" class="mascote-mobile">
                </div>
                
                <!-- TÍTULO (DENTRO DO CARD, depois do mascote no mobile) -->
                <div class="form-title-container">
                    <h2 class="form-title">Cadastro de Colaborador</h2>
                </div>
                
                <?php
                // Inicializar variáveis
                $nome = $email = $senha = $perfil = '';
                $nome_error = $email_error = $senha_error = $perfil_error = '';
                $success = false;
                $form_submitted = false;
                
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
                        // Caminho para o arquivo JSON - AJUSTE CONFORME SEU CAMINHO
                        // Se estiver no WAMP, use o caminho absoluto:
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
                                'criado_em' => date('Y-m-d H:i:s')
                            ];
                            
                            $users[] = $novo_usuario;
                            
                            // Salvar no arquivo JSON
                            file_put_contents($json_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            
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
                                    <input type="text" id="nome" name="nome" class="field-input" placeholder="Ana Silva" value="<?php echo htmlspecialchars($nome); ?>">
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
                                <label for="senha" class="field-label required">Senha:</label>
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
                    <div class="success-message">Usuário cadastrado com sucesso!</div>
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