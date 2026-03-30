<?php
// Iniciar sessão para mensagens flash
session_start();

// Verificar se o usuário acabou de se registrar e precisa ver e-mail
$email_enviado = $_SESSION['email_enviado'] ?? false;
$email_cadastrado = $_SESSION['email_cadastrado'] ?? '';
unset($_SESSION['email_enviado'], $_SESSION['email_cadastrado']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Estrela - Cadastro de Responsável</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">

    <!-- Importação da fonte Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard/cliente/cadastro_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="page-container">
        <div class="main-wrapper">
            <!-- Mascote à esquerda (FORA DO container branco) - VISÍVEL APENAS NO DESKTOP -->
            <div class="mascote-container desktop-only">
                <img src="../../imagens/mascote_up.png" alt="Mascote" class="mascote-image">
            </div>
            
            <!-- Container branco do formulário -->
            <div class="form-card">
                <!-- Logo DENTRO DO container (APENAS LOGO NO CARD) -->
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
                    <h2 class="form-title">Cadastro de Responsável</h2>
                </div>
                
                <!-- PAINEL DE DIAGNÓSTICO - REMOVA DEPOIS -->
                <div style="background: #e8f4fd; border-left: 4px solid #1a5fce; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px;">
                    <strong><i class="fas fa-info-circle"></i> Diagnóstico do Sistema</strong>
                    <?php
                    // Verificar PHPMailer
                    $base_path = dirname(dirname(__DIR__)) . '/';
                    $phpmailer_path = $base_path . 'PHPMailer/src/';
                    if (file_exists($phpmailer_path . 'PHPMailer.php')) {
                        echo "<div style='color: #28a745; margin-top: 5px;'>✅ PHPMailer encontrado</div>";
                    } else {
                        echo "<div style='color: #dc3545; margin-top: 5px;'>❌ PHPMailer NÃO encontrado em: $phpmailer_path</div>";
                        echo "<div style='margin-top: 5px;'>📥 Baixe de: <a href='https://github.com/PHPMailer/PHPMailer' target='_blank'>github.com/PHPMailer/PHPMailer</a></div>";
                    }
                    
                    // Verificar pasta de logs
                    $log_dir = $base_path . 'logs/';
                    if (!file_exists($log_dir)) {
                        if (@mkdir($log_dir, 0777, true)) {
                            echo "<div style='color: #28a745; margin-top: 5px;'>✅ Pasta de logs criada</div>";
                        } else {
                            echo "<div style='color: #dc3545; margin-top: 5px;'>❌ Falha ao criar pasta de logs</div>";
                        }
                    } else {
                        echo "<div style='color: #28a745; margin-top: 5px;'>✅ Pasta de logs existe</div>";
                    }
                    
                    // Verificar permissões de escrita
                    $test_file = $log_dir . 'test.txt';
                    if (@file_put_contents($test_file, 'test')) {
                        echo "<div style='color: #28a745; margin-top: 5px;'>✅ Pasta de logs tem permissão de escrita</div>";
                        @unlink($test_file);
                    } else {
                        echo "<div style='color: #dc3545; margin-top: 5px;'>❌ Pasta de logs SEM permissão de escrita</div>";
                    }
                    ?>
                </div>
                
                <!-- MENSAGEM DE CONFIRMAÇÃO DE E-MAIL -->
                <?php if ($email_enviado): ?>
                <div class="info-section">
                    <div class="info-message" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                        <i class="fas fa-envelope" style="margin-right: 10px;"></i>
                        <strong>E-mail de confirmação enviado!</strong><br>
                        Verifique sua caixa de entrada em <strong><?php echo htmlspecialchars($email_cadastrado); ?></strong>.<br>
                        Clique no link enviado para ativar seu cadastro.
                        <?php if (strpos($email_cadastrado, 'teste') !== false): ?>
                            <br><small>(Modo de teste ativo - link aparecerá em popup)</small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php
                // Inicializar variáveis
                $nome_completo = $email = $celular = $parentesco = $nome_crianca = $cpf_crianca = $senha = '';
                $nome_completo_error = $email_error = $celular_error = $parentesco_error = $nome_crianca_error = $cpf_crianca_error = $senha_error = '';
                $form_submitted = false;
                
                // Verificar se o formulário foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $form_submitted = true;
                    
                    // Obter dados do formulário
                    $nome_completo = trim($_POST['nome_completo'] ?? '');
                    $email = trim($_POST['email'] ?? '');
                    $celular = trim($_POST['celular'] ?? '');
                    $parentesco = $_POST['parentesco'] ?? '';
                    $nome_crianca = trim($_POST['nome_crianca'] ?? '');
                    $cpf_crianca = preg_replace('/[^0-9]/', '', $_POST['cpf_crianca'] ?? '');
                    $senha = $_POST['senha'] ?? '';
                    
                    // Validar campos obrigatórios
                    $erro_geral = false;
                    
                    if (empty($nome_completo)) {
                        $nome_completo_error = 'Nome completo é obrigatório';
                        $erro_geral = true;
                    }
                    
                    if (empty($email)) {
                        $email_error = 'E-mail é obrigatório';
                        $erro_geral = true;
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email_error = 'E-mail inválido';
                        $erro_geral = true;
                    }
                    
                    if (empty($celular)) {
                        $celular_error = 'Celular é obrigatório';
                        $erro_geral = true;
                    }
                    
                    if (empty($parentesco)) {
                        $parentesco_error = 'Selecione o parentesco';
                        $erro_geral = true;
                    }
                    
                    if (empty($nome_crianca)) {
                        $nome_crianca_error = 'Nome da criança é obrigatório';
                        $erro_geral = true;
                    }
                    
                    // Validar CPF da criança
                    if (empty($cpf_crianca)) {
                        $cpf_crianca_error = 'CPF da criança é obrigatório';
                        $erro_geral = true;
                    } elseif (strlen($cpf_crianca) != 11) {
                        $cpf_crianca_error = 'CPF deve ter 11 dígitos';
                        $erro_geral = true;
                    } elseif (!validarCPF($cpf_crianca)) {
                        $cpf_crianca_error = 'CPF inválido';
                        $erro_geral = true;
                    }
                    
                    // Validar senha
                    if (empty($senha)) {
                        $senha_error = 'Senha é obrigatória';
                        $erro_geral = true;
                    } elseif (strlen($senha) < 8) {
                        $senha_error = 'Senha deve ter pelo menos 8 caracteres';
                        $erro_geral = true;
                    } elseif (!preg_match('/[0-9]/', $senha)) {
                        $senha_error = 'Senha deve conter pelo menos um número';
                        $erro_geral = true;
                    } elseif (!preg_match('/[a-z]/', $senha)) {
                        $senha_error = 'Senha deve conter pelo menos uma letra minúscula';
                        $erro_geral = true;
                    } elseif (!preg_match('/[A-Z]/', $senha)) {
                        $senha_error = 'Senha deve conter pelo menos uma letra maiúscula';
                        $erro_geral = true;
                    }
                    
                    // Se não houver erros de validação, verificar no arquivo JSON
                    if (!$erro_geral) {
                        // Caminho para o arquivo JSON - USANDO CAMINHO RELATIVO
                        $json_file = dirname(__DIR__) . '/dados_cliente/user.json';
                        
                        // Criar diretório se não existir
                        $json_dir = dirname($json_file);
                        if (!file_exists($json_dir)) {
                            mkdir($json_dir, 0777, true);
                        }
                        
                        // Ler responsáveis existentes
                        $responsaveis = [];
                        if (file_exists($json_file)) {
                            $json_content = file_get_contents($json_file);
                            $responsaveis = json_decode($json_content, true) ?: [];
                        }
                        
                        // Verificar se email já existe
                        $email_exists = false;
                        // Verificar se CPF já está cadastrado
                        $cpf_exists = false;
                        
                        foreach ($responsaveis as $responsavel) {
                            if (strtolower($responsavel['email']) === strtolower($email)) {
                                $email_exists = true;
                            }
                            if (isset($responsavel['cpf_crianca']) && $responsavel['cpf_crianca'] === $cpf_crianca) {
                                $cpf_exists = true;
                            }
                        }
                        
                        if ($email_exists) {
                            $email_error = 'E-mail já cadastrado';
                            $erro_geral = true;
                        }
                        
                        if ($cpf_exists) {
                            $cpf_crianca_error = 'CPF já cadastrado para outra criança';
                            $erro_geral = true;
                        }
                        
                        
                        if (!$erro_geral) {
                            // Gerar token de verificação único
                            $verification_token = bin2hex(random_bytes(32));
                            
                            // Calcular força da senha
                            $forca = 0;
                            if (strlen($senha) >= 8) $forca++;
                            if (preg_match('/[0-9]/', $senha)) $forca++;
                            if (preg_match('/[a-z]/', $senha)) $forca++;
                            if (preg_match('/[A-Z]/', $senha)) $forca++;
                            if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha)) $forca++;
                            
                            if ($forca <= 2) $nivel_senha = 'fraca';
                            elseif ($forca <= 4) $nivel_senha = 'media';
                            else $nivel_senha = 'forte';
                            
                            $novo_responsavel = [
                                'id' => count($responsaveis) + 1,
                                'nome_completo' => $nome_completo,
                                'email' => $email,
                                'celular' => $celular,
                                'parentesco' => $parentesco,
                                'nome_crianca' => $nome_crianca,
                                'cpf_crianca' => $cpf_crianca,
                                'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                                'nivel_senha' => $nivel_senha,
                                'verification_token' => $verification_token,
                                'verified' => false,
                                'data_cadastro' => date('Y-m-d H:i:s'),
                                'ativo' => true
                            ];
                            
                            $responsaveis[] = $novo_responsavel;
                            
                            // Salvar no arquivo JSON
                            file_put_contents($json_file, json_encode($responsaveis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            
                            // Incluir arquivo de configuração de e-mail
                            require_once 'email_config.php';
                            
                            // Enviar e-mail de verificação
                            $email_enviado = enviarEmailVerificacao($email, $nome_completo, $verification_token);
                            
                            if ($email_enviado) {
                                $_SESSION['email_enviado'] = true;
                                $_SESSION['email_cadastrado'] = $email;
                                
                                // Redirecionar para evitar reenvio do formulário
                                header('Location: ' . $_SERVER['PHP_SELF']);
                                exit;
                            } else {
                                echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                                echo "<strong>Erro:</strong> Não foi possível enviar o e-mail de confirmação. ";
                                echo "Verifique os logs em C:/wamp64/www/clinicaestrela/logs/";
                                echo "</div>";
                            }
                        }
                    }
                }
                
                // Função para validar CPF
                function validarCPF($cpf) {
                    // Remove caracteres não numéricos
                    $cpf = preg_replace('/[^0-9]/', '', $cpf);
                    
                    // Verifica se tem 11 dígitos
                    if (strlen($cpf) != 11) {
                        return false;
                    }
                    
                    // Verifica se todos os dígitos são iguais (CPF inválido)
                    if (preg_match('/(\d)\1{10}/', $cpf)) {
                        return false;
                    }
                    
                    // Calcula o primeiro dígito verificador
                    $soma = 0;
                    for ($i = 0; $i < 9; $i++) {
                        $soma += intval($cpf[$i]) * (10 - $i);
                    }
                    $resto = $soma % 11;
                    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
                    
                    // Calcula o segundo dígito verificador
                    $soma = 0;
                    for ($i = 0; $i < 10; $i++) {
                        $soma += intval($cpf[$i]) * (11 - $i);
                    }
                    $resto = $soma % 11;
                    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
                    
                    // Verifica se os dígitos calculados são iguais aos informados
                    return ($cpf[9] == $digito1 && $cpf[10] == $digito2);
                }
                ?>
                
                <form method="POST" action="" class="cadastro-form" id="cadastroForm">
                    <!-- Seção: Dados do Responsável -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3>Dados do Responsável</h3>
                        </div>
                        
                        <div class="form-fields">
                            <!-- Campo Nome Completo -->
                            <div class="field-row">
                                <label for="nome_completo" class="field-label required">Nome Completo:</label>
                                <div class="field-input-wrapper">
                                    <input type="text" id="nome_completo" name="nome_completo" class="field-input" placeholder="Maria da Silva" value="<?php echo htmlspecialchars($nome_completo); ?>">
                                    <?php if ($form_submitted && $nome_completo_error): ?>
                                        <div class="error-message"><?php echo $nome_completo_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo E-mail -->
                            <div class="field-row">
                                <label for="email" class="field-label required">E-mail:</label>
                                <div class="field-input-wrapper">
                                    <input type="email" id="email" name="email" class="field-input" placeholder="maria@email.com" value="<?php echo htmlspecialchars($email); ?>">
                                    <?php if ($form_submitted && $email_error): ?>
                                        <div class="error-message"><?php echo $email_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo Celular -->
                            <div class="field-row">
                                <label for="celular" class="field-label required">Celular:</label>
                                <div class="field-input-wrapper">
                                    <input type="tel" id="celular" name="celular" class="field-input" placeholder="(11) 98765-4321" value="<?php echo htmlspecialchars($celular); ?>" maxlength="15">
                                    <?php if ($form_submitted && $celular_error): ?>
                                        <div class="error-message"><?php echo $celular_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo Senha -->
                            <div class="field-row">
                                <label for="senha" class="field-label required">Senha:</label>
                                <div class="field-input-wrapper" style="position: relative; width: 100%;">
                                    <input type="password" id="senha" name="senha" class="field-input" 
                                           placeholder="Crie uma senha forte" 
                                           value="<?php echo htmlspecialchars($senha); ?>"
                                           oninput="avaliarForcaSenhaCadastro(this.value)">
                                    <span class="toggle-password" onclick="togglePasswordCadastro('senha', this)">
                                        <i class="far fa-eye"></i>
                                    </span>
                                    <?php if ($form_submitted && $senha_error): ?>
                                        <div class="error-message"><?php echo $senha_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Requisitos da senha -->
                            <div class="password-requirements-container">
                                <div class="password-requirements">
                                    <span class="requirement" id="req-length-cad">
                                        <i class="fas fa-circle"></i> 8+ caracteres
                                    </span>
                                    <span class="requirement" id="req-number-cad">
                                        <i class="fas fa-circle"></i> Números
                                    </span>
                                    <span class="requirement" id="req-lower-cad">
                                        <i class="fas fa-circle"></i> Minúsculas
                                    </span>
                                    <span class="requirement" id="req-upper-cad">
                                        <i class="fas fa-circle"></i> Maiúsculas
                                    </span>
                                    <span class="requirement" id="req-special-cad">
                                        <i class="fas fa-circle"></i> Especial
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Campo Parentesco -->
                            <div class="field-row">
                                <label for="parentesco" class="field-label required">Parentesco:</label>
                                <div class="field-input-wrapper">
                                    <select id="parentesco" name="parentesco" class="field-select">
                                        <option value="">Selecione o parentesco...</option>
                                        <option value="Pai" <?php echo ($parentesco === 'Pai') ? 'selected' : ''; ?>>Pai</option>
                                        <option value="Mãe" <?php echo ($parentesco === 'Mãe') ? 'selected' : ''; ?>>Mãe</option>
                                        <option value="Avô" <?php echo ($parentesco === 'Avô') ? 'selected' : ''; ?>>Avô</option>
                                        <option value="Avó" <?php echo ($parentesco === 'Avó') ? 'selected' : ''; ?>>Avó</option>
                                        <option value="Tio" <?php echo ($parentesco === 'Tio') ? 'selected' : ''; ?>>Tio</option>
                                        <option value="Tia" <?php echo ($parentesco === 'Tia') ? 'selected' : ''; ?>>Tia</option>
                                        <option value="Responsável Legal" <?php echo ($parentesco === 'Responsável Legal') ? 'selected' : ''; ?>>Responsável Legal</option>
                                        <option value="Outro" <?php echo ($parentesco === 'Outro') ? 'selected' : ''; ?>>Outro</option>
                                    </select>
                                    <?php if ($form_submitted && $parentesco_error): ?>
                                        <div class="error-message"><?php echo $parentesco_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção: Dados da Criança -->
                    <div class="form-section">            
                        <div class="form-fields">
                            <!-- Campo Nome da Criança -->
                            <div class="field-row">
                                <label for="nome_crianca" class="field-label required">Nome da Criança:</label>
                                <div class="field-input-wrapper">
                                    <input type="text" id="nome_crianca" name="nome_crianca" class="field-input" placeholder="João Silva" value="<?php echo htmlspecialchars($nome_crianca); ?>">
                                    <?php if ($form_submitted && $nome_crianca_error): ?>
                                        <div class="error-message"><?php echo $nome_crianca_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Campo CPF da Criança -->
                            <div class="field-row">
                                <label for="cpf_crianca" class="field-label required">CPF da Criança:</label>
                                <div class="field-input-wrapper">
                                    <input type="text" id="cpf_crianca" name="cpf_crianca" class="field-input" placeholder="000.000.000-00" value="<?php echo htmlspecialchars($cpf_crianca); ?>" maxlength="14">
                                    <?php if ($form_submitted && $cpf_crianca_error): ?>
                                        <div class="error-message"><?php echo $cpf_crianca_error; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botão Cadastrar -->
                    <div class="form-actions">
                        <button type="submit" class="submit-button">Cadastrar Responsável</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Função para alternar visualização da senha
        function togglePasswordCadastro(inputId, element) {
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
        
        // Função para avaliar força da senha
        function avaliarForcaSenhaCadastro(senha) {
            // Requisitos
            const length = senha.length >= 8;
            const number = /[0-9]/.test(senha);
            const lower = /[a-z]/.test(senha);
            const upper = /[A-Z]/.test(senha);
            const special = /[!@#$%^&*(),.?":{}|<>]/.test(senha);
            
            // Atualizar ícones e cores dos requisitos
            const reqs = [
                { id: 'req-length-cad', valido: length },
                { id: 'req-number-cad', valido: number },
                { id: 'req-lower-cad', valido: lower },
                { id: 'req-upper-cad', valido: upper },
                { id: 'req-special-cad', valido: special }
            ];
            
            reqs.forEach(req => {
                const elemento = document.getElementById(req.id);
                if (elemento) {
                    const icon = elemento.querySelector('i');
                    if (req.valido) {
                        elemento.classList.add('valid');
                        elemento.classList.remove('invalid');
                        icon.classList.remove('fa-circle');
                        icon.classList.add('fa-check-circle');
                    } else {
                        elemento.classList.add('invalid');
                        elemento.classList.remove('valid');
                        icon.classList.remove('fa-check-circle');
                        icon.classList.add('fa-circle');
                    }
                }
            });
            
            // Calcular força e atualizar barra
            const pontos = [length, number, lower, upper, special].filter(Boolean).length;
            const strengthBar = document.getElementById('strengthBarCadastro');
            
            if (strengthBar) {
                strengthBar.className = 'password-strength-bar';
                
                if (senha.length === 0) {
                    strengthBar.style.width = '0';
                } else if (pontos <= 2) {
                    strengthBar.style.width = '33.33%';
                    strengthBar.classList.add('fraca');
                } else if (pontos <= 4) {
                    strengthBar.style.width = '66.66%';
                    strengthBar.classList.add('media');
                } else {
                    strengthBar.style.width = '100%';
                    strengthBar.classList.add('forte');
                }
            }
        }
        
        // Máscara para celular
        function mascaraCelular(input) {
            let v = input.value.replace(/\D/g, '');
            v = v.replace(/^(\d{2})(\d{5})(\d{0,4})$/, '($1) $2-$3');
            input.value = v;
        }
        
        // Máscara para CPF
        function mascaraCPF(input) {
            let v = input.value.replace(/\D/g, '');
            if (v.length <= 11) {
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            input.value = v;
        }
        
        // Aplicar máscaras quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const celular = document.getElementById('celular');
            if (celular) {
                celular.addEventListener('input', function() {
                    mascaraCelular(this);
                });
            }
            
            const cpf = document.getElementById('cpf_crianca');
            if (cpf) {
                cpf.addEventListener('input', function() {
                    mascaraCPF(this);
                });
            }
            
            
            const senha = document.getElementById('senha');
            if (senha && senha.value.length > 0) {
                avaliarForcaSenhaCadastro(senha.value);
            }
        });
    </script>
</body>
</html>