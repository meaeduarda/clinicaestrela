<?php
// dashboard/cliente/painel_cliente.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['responsavel_id'])) {
    header("Location: login_cliente.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados da sessão
$nomeLogado = $_SESSION['responsavel_nome'] ?? 'Responsável';
$pacienteNome = $_SESSION['paciente_nome'] ?? 'Paciente';
$pacienteId = $_SESSION['paciente_id'] ?? 1;

// Função para calcular idade
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) {
        return 'Idade n/d';
    }
    
    try {
        // Tenta converter a data para objeto DateTime
        $dataNasc = DateTime::createFromFormat('Y-m-d', $dataNascimento);
        if (!$dataNasc) {
            $dataNasc = DateTime::createFromFormat('d/m/Y', $dataNascimento);
        }
        if (!$dataNasc) {
            $dataNasc = new DateTime($dataNascimento);
        }
        
        $hoje = new DateTime();
        $diferenca = $hoje->diff($dataNasc);
        
        return $diferenca->y . ' anos';
    } catch (Exception $e) {
        return 'Idade n/d';
    }
}

// Dados do paciente (vindos da SESSÃO - NÃO SOBRESCREVER)
$paciente = [
    'id' => $pacienteId,
    'nome_completo' => $_SESSION['paciente_nome'] ?? 'Renato Oliveira Jr.',
    'nome_mae' => $_SESSION['paciente_nome_mae'] ?? 'Ana Paula Oliveira',
    'nome_pai' => $_SESSION['paciente_nome_pai'] ?? 'Renato Oliveira',
    'data_nascimento' => $_SESSION['paciente_data_nascimento'] ?? '2019-02-14',
    'convenio' => $_SESSION['paciente_convenio'] ?? 'Unimed',
    'foto' => $_SESSION['paciente_foto'] ?? '../../imagens/pacientes/paciente_default.jpg',
    'telefone' => $_SESSION['paciente_telefone'] ?? '(11) 99999-9999',
    'email' => $_SESSION['paciente_email'] ?? $_SESSION['responsavel_email'] ?? 'contato@email.com'
];

// Calcular idade
$idade = calcularIdade($paciente['data_nascimento']);

// Seção ativa (dados pessoais é a padrão)
$secaoAtiva = 'dados-pessoais';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title>Painel do Responsável - Clínica Estrela</title>

    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="../../css/dashboard/cliente/painel_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="mobile-header">
    </div>
    
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../../imagens/logo_clinica_estrela.png" alt="Logo Clínica Estrela" class="logo-img">
                </div>
                <h1>Clínica Estrela</h1>
                <div class="mobile-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <nav class="menu cliente-menu">
                <ul>
                    <li class="<?php echo $secaoAtiva === 'dados-pessoais' ? 'active' : ''; ?>">
                        <a href="?secao=dados-pessoais">
                            <i class="fas fa-user-circle"></i> 
                            <span>Dados Pessoais</span>
                        </a>
                    </li>
                    <li>
                        <a href="?secao=grade-sessao">
                            <i class="fas fa-calendar-alt"></i> 
                            <span>Grade de Sessão</span>
                        </a>
                    </li>
                    <li>
                        <a href="?secao=galeria">
                            <i class="fas fa-images"></i> 
                            <span>Galeria de Fotos</span>
                        </a>
                    </li>
                    <li>
                        <a href="?secao=pei">
                            <i class="fas fa-file-signature"></i> 
                            <span>Plano de Ensino (PEI)</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nomeLogado); ?>&background=random" alt="<?php echo htmlspecialchars($nomeLogado); ?>">
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($nomeLogado); ?></h3>
                    <p>Responsável</p>
                </div>
                <a href="login_cliente.php" title="Sair" style="color: #ef4444; margin-left: 10px; text-decoration: none;">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </aside>

        <main class="main-content cliente-main" id="mainContent">
            <!-- Cabeçalho da Página -->
            <div class="page-header">
                <h2><i class="fas fa-user-circle"></i> Dados Pessoais</h2>
                <button class="btn-change-bg" id="changeBgBtn" title="Alterar plano de fundo">
                    <i class="fas fa-palette"></i>
                    <span>Alterar Fundo</span>
                </button>
            </div>

            <!-- Conteúdo Principal -->
            <div class="cliente-content">
                <!-- Card do Paciente -->
                <div class="patient-profile-card">
                    <div class="patient-photo-container">
                        <img src="<?php echo $paciente['foto']; ?>" 
                             alt="<?php echo $paciente['nome_completo']; ?>" 
                             class="patient-photo" 
                             id="patientPhoto">
                        <button class="btn-change-photo" id="changePhotoBtn" title="Alterar foto">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div class="patient-name-header">
                        <h3><?php echo $paciente['nome_completo']; ?></h3>
                        <span class="patient-age-badge"><?php echo $idade; ?></span>
                    </div>
                </div>

                <!-- Grid de Informações -->
                <div class="info-grid">
                    <!-- Nome da Mãe -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-female"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Nome da Mãe</span>
                            <span class="info-value"><?php echo $paciente['nome_mae']; ?></span>
                        </div>
                    </div>

                    <!-- Nome do Pai -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-male"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Nome do Pai</span>
                            <span class="info-value"><?php echo $paciente['nome_pai']; ?></span>
                        </div>
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Data de Nascimento</span>
                            <span class="info-value">
                                <?php 
                                    // Formatar data para exibição
                                    $dataExibicao = $paciente['data_nascimento'];
                                    try {
                                        $dataObj = new DateTime($paciente['data_nascimento']);
                                        $dataExibicao = $dataObj->format('d/m/Y');
                                    } catch (Exception $e) {
                                        // Mantém o valor original se não conseguir formatar
                                    }
                                    echo $dataExibicao;
                                ?>
                                <small>(<?php echo $idade; ?>)</small>
                            </span>
                        </div>
                    </div>

                    <!-- Convênio -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Convênio</span>
                            <span class="info-value"><?php echo $paciente['convenio']; ?></span>
                        </div>
                    </div>

                    <!-- Telefone -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Telefone</span>
                            <span class="info-value"><?php echo $paciente['telefone']; ?></span>
                        </div>
                    </div>

                    <!-- E-mail -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">E-mail</span>
                            <span class="info-value"><?php echo $paciente['email']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Seção de Resumo (inspirado na imagem) -->
                <div class="resumo-section">
                    <h4><i class="fas fa-file-alt"></i> Resumo de Evolução</h4>
                    <div class="resumo-content">
                        <p>Resumo de evolução do paciente (visual, não técnico) - Em breve disponível.</p>
                    </div>
                </div>

                <!-- Documentos (inspirado na imagem) -->
                <div class="documentos-section">
                    <h4><i class="fas fa-folder-open"></i> Documentos</h4>
                    <div class="documentos-list">
                        <div class="documento-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>Relatório de Evolução - Fev/2026</span>
                        </div>
                        <div class="documento-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>Plano Terapêutico - 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para selecionar cor de fundo -->
    <div class="modal" id="bgColorModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-palette"></i> Escolha uma cor de fundo</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="color-options">
                    <div class="color-option" data-color="#f0f9ff" style="background: #f0f9ff;">Azul Claro</div>
                    <div class="color-option" data-color="#f0fdf4" style="background: #f0fdf4;">Verde Claro</div>
                    <div class="color-option" data-color="#fef2f2" style="background: #fef2f2;">Vermelho Claro</div>
                    <div class="color-option" data-color="#fffbeb" style="background: #fffbeb;">Amarelo Claro</div>
                    <div class="color-option" data-color="#faf5ff" style="background: #faf5ff;">Roxo Claro</div>
                    <div class="color-option" data-color="#fff1f2" style="background: #fff1f2;">Rosa Claro</div>
                    <div class="color-option" data-color="#f4f4f5" style="background: #f4f4f5;">Cinza Claro</div>
                    <div class="color-option" data-color="#ffffff" style="background: #ffffff; border: 1px solid #e2e8f0;">Branco</div>
                </div>
                <div class="custom-color">
                    <label for="customBgColor">Ou escolha uma cor personalizada:</label>
                    <input type="color" id="customBgColor" value="#f0f9ff">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="cancelColor">Cancelar</button>
                <button class="btn-confirm" id="applyColor">Aplicar</button>
            </div>
        </div>
    </div>

    <!-- Modal para upload de foto -->
    <div class="modal" id="photoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-camera"></i> Alterar Foto do Paciente</h3>
                <button class="modal-close" id="closePhotoModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="photo-upload-area" id="photoUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Clique para selecionar ou arraste uma imagem</p>
                    <span>Formatos aceitos: JPG, PNG, GIF (max. 5MB)</span>
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                </div>
                <div class="photo-preview" id="photoPreview" style="display: none;">
                    <img src="" alt="Preview" id="previewImage">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="cancelPhoto">Cancelar</button>
                <button class="btn-confirm" id="savePhoto" disabled>Salvar</button>
            </div>
        </div>
    </div>

    <!-- Notificações -->
    <div class="notification" id="notification"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu Mobile
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mobileClose = document.querySelector('.mobile-close');
            
            if (mobileMenuToggle && sidebar && mobileClose) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
                
                mobileClose.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // ===== FUNCIONALIDADE DE ALTERAR FUNDO =====
            const changeBgBtn = document.getElementById('changeBgBtn');
            const bgModal = document.getElementById('bgColorModal');
            const closeModal = document.getElementById('closeModal');
            const cancelColor = document.getElementById('cancelColor');
            const applyColor = document.getElementById('applyColor');
            const colorOptions = document.querySelectorAll('.color-option');
            const customBgColor = document.getElementById('customBgColor');
            const mainContent = document.getElementById('mainContent');
            
            let selectedColor = mainContent.style.backgroundColor || '#f8fafc';

            changeBgBtn.addEventListener('click', function() {
                bgModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            function closeBgModal() {
                bgModal.classList.remove('active');
                document.body.style.overflow = '';
            }

            closeModal.addEventListener('click', closeBgModal);
            cancelColor.addEventListener('click', closeBgModal);

            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    colorOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedColor = this.getAttribute('data-color');
                });
            });

            applyColor.addEventListener('click', function() {
                if (selectedColor) {
                    mainContent.style.backgroundColor = selectedColor;
                    // Salvar preferência no localStorage
                    localStorage.setItem('clienteBgColor', selectedColor);
                    closeBgModal();
                    mostrarNotificacao('Fundo alterado com sucesso!', 'sucesso');
                }
            });

            // Carregar cor salva
            const savedColor = localStorage.getItem('clienteBgColor');
            if (savedColor) {
                mainContent.style.backgroundColor = savedColor;
            }

            // ===== FUNCIONALIDADE DE ALTERAR FOTO =====
            const changePhotoBtn = document.getElementById('changePhotoBtn');
            const photoModal = document.getElementById('photoModal');
            const closePhotoModal = document.getElementById('closePhotoModal');
            const cancelPhoto = document.getElementById('cancelPhoto');
            const photoUploadArea = document.getElementById('photoUploadArea');
            const photoInput = document.getElementById('photoInput');
            const photoPreview = document.getElementById('photoPreview');
            const previewImage = document.getElementById('previewImage');
            const savePhotoBtn = document.getElementById('savePhoto');
            const patientPhoto = document.getElementById('patientPhoto');

            changePhotoBtn.addEventListener('click', function() {
                photoModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            function closePhotoModalFunc() {
                photoModal.classList.remove('active');
                document.body.style.overflow = '';
                // Reset
                photoUploadArea.style.display = 'block';
                photoPreview.style.display = 'none';
                savePhotoBtn.disabled = true;
                photoInput.value = '';
            }

            closePhotoModal.addEventListener('click', closePhotoModalFunc);
            cancelPhoto.addEventListener('click', closePhotoModalFunc);

            photoUploadArea.addEventListener('click', function() {
                photoInput.click();
            });

            photoUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = '#3b82f6';
                this.style.backgroundColor = '#eff6ff';
            });

            photoUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '#cbd5e1';
                this.style.backgroundColor = '';
            });

            photoUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = '#cbd5e1';
                this.style.backgroundColor = '';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handlePhotoFile(files[0]);
                }
            });

            photoInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handlePhotoFile(this.files[0]);
                }
            });

            function handlePhotoFile(file) {
                // Verificar tipo
                if (!file.type.match('image.*')) {
                    mostrarNotificacao('Por favor, selecione uma imagem válida.', 'erro');
                    return;
                }
                
                // Verificar tamanho (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    mostrarNotificacao('A imagem deve ter no máximo 5MB.', 'erro');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    photoUploadArea.style.display = 'none';
                    photoPreview.style.display = 'block';
                    savePhotoBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }

            savePhotoBtn.addEventListener('click', function() {
                // Simular upload (em produção, enviaria para o servidor)
                patientPhoto.src = previewImage.src;
                mostrarNotificacao('Foto atualizada com sucesso!', 'sucesso');
                closePhotoModalFunc();
                
                // Salvar no localStorage como exemplo (em produção, salvaria no servidor)
                localStorage.setItem('patientPhoto', previewImage.src);
            });

            // Carregar foto salva
            const savedPhoto = localStorage.getItem('patientPhoto');
            if (savedPhoto) {
                patientPhoto.src = savedPhoto;
            }

            // Fechar modais ao clicar fora
            window.addEventListener('click', function(e) {
                if (e.target === bgModal) {
                    closeBgModal();
                }
                if (e.target === photoModal) {
                    closePhotoModalFunc();
                }
            });

            // Função de notificação
            function mostrarNotificacao(mensagem, tipo = 'info') {
                const notification = document.getElementById('notification');
                let classeTipo = 'notification-info';
                let icone = 'fa-info-circle';
                
                if (tipo === 'sucesso') { 
                    classeTipo = 'notification-success'; 
                    icone = 'fa-check-circle'; 
                } else if (tipo === 'erro') { 
                    classeTipo = 'notification-error'; 
                    icone = 'fa-exclamation-circle'; 
                }
                
                notification.className = `notification show ${classeTipo}`;
                notification.innerHTML = `<i class="fas ${icone}"></i><span>${mensagem}</span>`;
                
                setTimeout(() => { 
                    notification.classList.remove('show'); 
                }, 3000);
            }

            // Ajustes para mobile
            function adjustForMobile() {
                if (window.innerWidth <= 768) {
                    document.querySelectorAll('.menu.cliente-menu li a span').forEach(span => {
                        span.style.fontSize = '11px';
                    });
                }
            }

            adjustForMobile();
            window.addEventListener('resize', adjustForMobile);
        });
    </script>
</body>
</html>