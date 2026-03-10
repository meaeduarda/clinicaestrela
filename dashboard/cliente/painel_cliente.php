<?php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['responsavel_id'])) {
    header("Location: login_cliente.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Configurações de upload
define('MAX_FILE_SIZE', 3 * 1024 * 1024); // 3MB
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');
define('FOTOS_PATH', UPLOAD_PATH . 'fotos/');
define('BACKGROUNDS_PATH', UPLOAD_PATH . 'backgrounds/');

// Criar pastas se não existirem
if (!file_exists(FOTOS_PATH)) {
    mkdir(FOTOS_PATH, 0777, true);
}
if (!file_exists(BACKGROUNDS_PATH)) {
    mkdir(BACKGROUNDS_PATH, 0777, true);
}

// Função para processar upload de foto
function processarUploadFoto() {
    $response = ['success' => false, 'message' => '', 'foto' => ''];
    
    try {
        if (!isset($_FILES['foto'])) {
            throw new Exception('Nenhum arquivo enviado.');
        }
        
        $file = $_FILES['foto'];
        
        // Validar arquivo
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido.',
                UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido.',
                UPLOAD_ERR_PARTIAL => 'O upload foi apenas parcialmente concluído.',
                UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente.',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo em disco.',
                UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload.'
            ];
            $errorMessage = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Erro desconhecido no upload.';
            throw new Exception($errorMessage);
        }
        
        // Validar tamanho
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Arquivo muito grande. Máximo 3MB.');
        }
        
        // Validar tipo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception('Arquivo não é uma imagem válida.');
        }
        
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, WEBP ou GIF.');
        }
        
        // Validar extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Extensão de arquivo não permitida.');
        }
        
        // Gerar nome único
        $pacienteId = isset($_SESSION['paciente_id']) ? $_SESSION['paciente_id'] : 'temp';
        $filename = 'foto_' . $pacienteId . '_' . uniqid() . '.' . $extension;
        $filepath = FOTOS_PATH . $filename;
        
        // Mover arquivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $_SESSION['paciente_foto'] = '/clinicaestrela/uploads/fotos/' . $filename;
            
            $response['success'] = true;
            $response['message'] = 'Foto atualizada com sucesso!';
            $response['foto'] = $_SESSION['paciente_foto'];
        } else {
            throw new Exception('Erro ao salvar o arquivo. Verifique permissões de pasta.');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

// Função para processar upload de background
function processarUploadBackground() {
    $response = ['success' => false, 'message' => '', 'background' => ''];
    
    try {
        if (!isset($_FILES['background'])) {
            throw new Exception('Nenhum arquivo enviado.');
        }
        
        $file = $_FILES['background'];
        
        // Validar arquivo
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido.',
                UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido.',
                UPLOAD_ERR_PARTIAL => 'O upload foi apenas parcialmente concluído.',
                UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente.',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo em disco.',
                UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload.'
            ];
            $errorMessage = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Erro desconhecido no upload.';
            throw new Exception($errorMessage);
        }
        
        // Validar tamanho
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Arquivo muito grande. Máximo 3MB.');
        }
        
        // Validar tipo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception('Arquivo não é uma imagem válida.');
        }
        
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG ou WEBP.');
        }
        
        // Validar extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Extensão de arquivo não permitida.');
        }
        
        // Gerar nome único
        $responsavelId = isset($_SESSION['responsavel_id']) ? $_SESSION['responsavel_id'] : 'temp';
        $filename = 'bg_' . $responsavelId . '_' . uniqid() . '.' . $extension;
        $filepath = BACKGROUNDS_PATH . $filename;
        
        // Mover arquivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $_SESSION['background_foto'] = '/clinicaestrela/uploads/backgrounds/' . $filename;
            
            $response['success'] = true;
            $response['message'] = 'Background alterado com sucesso!';
            $response['background'] = $_SESSION['background_foto'];
        } else {
            throw new Exception('Erro ao salvar o arquivo. Verifique permissões de pasta.');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

// Processar requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_FILES['foto'])) {
        $response = processarUploadFoto();
        echo json_encode($response);
        exit();
    }
    
    if (isset($_FILES['background'])) {
        $response = processarUploadBackground();
        echo json_encode($response);
        exit();
    }
    
    echo json_encode(['success' => false, 'message' => 'Tipo de upload não reconhecido.']);
    exit();
}

// Carregar dados do JSON
$jsonFile = __DIR__ . '/../dados/ativo-cad.json';
$pacientesData = [];
$pacienteAtivo = null;

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $pacientesData = json_decode($jsonContent, true);
    
    if (!empty($pacientesData)) {
        foreach ($pacientesData as $paciente) {
            if (isset($paciente['status']) && $paciente['status'] === 'Ativo') {
                $pacienteAtivo = $paciente;
                break;
            }
        }
    }
}

// Dados do paciente
$paciente = [
    'id' => $_SESSION['paciente_id'] ?? 1,
    'nome_completo' => 'Igor Souza',
    'nome_mae' => 'Marta Souza',
    'telefone' => '(81) 99325-1967',
    'convenio' => 'Unimed'
];

if ($pacienteAtivo) {
    $telefone = isset($pacienteAtivo['telefone']) ? $pacienteAtivo['telefone'] : '';
    if (!empty($telefone) && strlen($telefone) === 11) {
        $telefone = '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    }
    
    $paciente['nome_completo'] = $pacienteAtivo['nome_completo'] ?? 'Igor Souza';
    $paciente['nome_mae'] = $pacienteAtivo['nome_mae'] ?? 'Marta Souza';
    $paciente['telefone'] = $telefone ?: '(81) 99325-1967';
    $paciente['convenio'] = $pacienteAtivo['convenio'] ?? 'Unimed';
}

// Foto de perfil
$fotoPerfil = $_SESSION['paciente_foto'] ?? '../../imagens/avatar-default.png';
$backgroundAtual = $_SESSION['background_foto'] ?? '';
$nomeLogado = $_SESSION['responsavel_nome'] ?? 'Maria Eduarda';
$secaoAtiva = $_GET['secao'] ?? 'painel';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - PuzzleCare</title>
    
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="../../css/dashboard/cliente/painel_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../../imagens/logo_saas.png" alt="Logo PuzzleCare" class="logo-img">
                </div>
                <div class="logo-text-container">
                    <h1>
                        <span class="puzzle-azul">Puzzle</span><span class="care-verde">Care</span>
                    </h1>
                    <span class="logo-subtitle">by Cronos Solutions Tech</span>
                </div>
                <div class="mobile-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <nav class="menu cliente-menu">
                <ul>
                    <li class="<?php echo $secaoAtiva === 'painel' ? 'active' : ''; ?>">
                        <a href="?secao=painel">
                            <i class="fas fa-home"></i>
                            <span>Dados Cadastrais</span>
                        </a>
                    </li>
                    <li class="<?php echo $secaoAtiva === 'agenda' ? 'active' : ''; ?>">
                        <a href="?secao=agenda">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Agenda da Criança</span>
                        </a>
                    </li>
                    <li class="<?php echo $secaoAtiva === 'plano-terapeutico' ? 'active' : ''; ?>">
                        <a href="?secao=plano-terapeutico">
                            <i class="fas fa-file-medical"></i>
                            <span>Plano Terapêutico</span>
                        </a>
                    </li>
                    <li class="<?php echo $secaoAtiva === 'documentos' ? 'active' : ''; ?>">
                        <a href="?secao=documentos">
                            <i class="fas fa-folder-open"></i>
                            <span>Documentos</span>
                        </a>
                    </li>        
                </ul>
            </nav>

            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nomeLogado); ?>&background=3b82f6&color=fff&size=128" alt="<?php echo htmlspecialchars($nomeLogado); ?>">
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($nomeLogado); ?></h3>
                    <p>Responsável</p>
                </div>
                <a href="login_cliente.php" title="Sair" style="color: #ef4444; margin-left: 10px;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <main class="main-content cliente-main">
            <div class="page-header">
                <h1>
                    Dados Cadastrais
                </h1>
                <button class="btn-change-bg" id="btnChangeBg">
                    <i class="fas fa-palette"></i>
                    Alterar plano de fundo
                </button>
            </div>

            <div class="content-area" id="contentArea" <?php echo !empty($backgroundAtual) ? 'style="background-image: url(\'' . $backgroundAtual . '\');"' : ''; ?>>
                
                <div class="patient-card">
                    <div class="patient-photo-wrapper">
                        <div class="photo-container">
                            <img src="<?php echo $fotoPerfil; ?>" alt="<?php echo htmlspecialchars($paciente['nome_completo']); ?>" class="patient-photo" id="patientPhoto">
                            <button class="btn-edit-photo" id="btnEditPhoto" title="Alterar foto">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="patient-info-header">
                            <h2><?php echo htmlspecialchars($paciente['nome_completo']); ?></h2>
                            <p class="patient-role">Paciente</p>
                        </div>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span class="label">Responsável:</span>
                            <span class="value"><?php echo htmlspecialchars($paciente['nome_mae']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span class="value"><?php echo htmlspecialchars($paciente['telefone']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-notes-medical"></i>
                            <span class="label">Convênio:</span>
                            <span class="value"><?php echo htmlspecialchars($paciente['convenio']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clinic-footer">
                <i class="fas fa-star"></i> CLÍNICA ESTRELA <i class="fas fa-star"></i>
            </div>
        </main>
    </div>

    <div class="modal" id="modalFoto">
    </div>

    <div class="modal" id="modalBackground">
    </div>

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notificationMessage"></span>
    </div>

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

            // Função de notificação
            function mostrarNotificacao(mensagem, tipo = 'success') {
                const notification = document.getElementById('notification');
                const messageSpan = document.getElementById('notificationMessage');
                const icon = notification.querySelector('i');
                
                notification.className = 'notification show';
                if (tipo === 'success') {
                    notification.classList.add('success');
                    icon.className = 'fas fa-check-circle';
                } else {
                    notification.classList.add('error');
                    icon.className = 'fas fa-exclamation-circle';
                }
                
                messageSpan.textContent = mensagem;
                
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            }

            // Upload de foto
            const btnEditPhoto = document.getElementById('btnEditPhoto');
            const modalFoto = document.getElementById('modalFoto');
            const closeModalFoto = document.getElementById('closeModalFoto');
            const cancelFoto = document.getElementById('cancelFoto');
            const uploadAreaFoto = document.getElementById('uploadAreaFoto');
            const fileInputFoto = document.getElementById('fileInputFoto');
            const previewContainerFoto = document.getElementById('previewContainerFoto');
            const previewImageFoto = document.getElementById('previewImageFoto');
            const saveFotoBtn = document.getElementById('saveFoto');
            const patientPhoto = document.getElementById('patientPhoto');

            let selectedFotoFile = null;

            btnEditPhoto.addEventListener('click', function() {
                modalFoto.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            function closeFotoModal() {
                modalFoto.classList.remove('active');
                document.body.style.overflow = '';
                resetFotoUpload();
            }

            closeModalFoto.addEventListener('click', closeFotoModal);
            cancelFoto.addEventListener('click', closeFotoModal);

            function resetFotoUpload() {
                uploadAreaFoto.style.display = 'block';
                previewContainerFoto.style.display = 'none';
                previewImageFoto.src = '';
                saveFotoBtn.disabled = true;
                fileInputFoto.value = '';
                selectedFotoFile = null;
            }

            uploadAreaFoto.addEventListener('click', function() {
                fileInputFoto.click();
            });

            uploadAreaFoto.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = '#3b82f6';
                this.style.backgroundColor = '#eef2ff';
            });

            uploadAreaFoto.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '#e2e8f0';
                this.style.backgroundColor = '#f8fafc';
            });

            uploadAreaFoto.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = '#e2e8f0';
                this.style.backgroundColor = '#f8fafc';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFotoFile(files[0]);
                }
            });

            fileInputFoto.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFotoFile(this.files[0]);
                }
            });

            function handleFotoFile(file) {
                if (!file.type.match('image.*')) {
                    mostrarNotificacao('Por favor, selecione uma imagem válida.', 'error');
                    return;
                }
                
                if (file.size > 3 * 1024 * 1024) {
                    mostrarNotificacao('A imagem deve ter no máximo 3MB.', 'error');
                    return;
                }

                selectedFotoFile = file;

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImageFoto.src = e.target.result;
                    uploadAreaFoto.style.display = 'none';
                    previewContainerFoto.style.display = 'block';
                    saveFotoBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }

            saveFotoBtn.addEventListener('click', function() {
                if (!selectedFotoFile) return;

                saveFotoBtn.disabled = true;
                saveFotoBtn.textContent = 'Enviando...';

                const formData = new FormData();
                formData.append('foto', selectedFotoFile);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        patientPhoto.src = data.foto + '?t=' + new Date().getTime();
                        mostrarNotificacao(data.message, 'success');
                        closeFotoModal();
                    } else {
                        mostrarNotificacao(data.message, 'error');
                        saveFotoBtn.disabled = false;
                        saveFotoBtn.textContent = 'Salvar foto';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('Erro ao enviar a imagem. Tente novamente.', 'error');
                    saveFotoBtn.disabled = false;
                    saveFotoBtn.textContent = 'Salvar foto';
                });
            });

            // Upload de background
            const btnChangeBg = document.getElementById('btnChangeBg');
            const modalBg = document.getElementById('modalBackground');
            const closeModalBg = document.getElementById('closeModalBg');
            const cancelBg = document.getElementById('cancelBg');
            const uploadAreaBg = document.getElementById('uploadAreaBg');
            const fileInputBg = document.getElementById('fileInputBg');
            const previewContainerBg = document.getElementById('previewContainerBg');
            const previewImageBg = document.getElementById('previewImageBg');
            const saveBgBtn = document.getElementById('saveBg');
            const bgOptions = document.querySelectorAll('.bg-option');
            const contentArea = document.getElementById('contentArea');

            let selectedBgFile = null;
            let selectedBgType = 'default';
            let selectedGradient = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

            btnChangeBg.addEventListener('click', function() {
                modalBg.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            function closeBgModal() {
                modalBg.classList.remove('active');
                document.body.style.overflow = '';
                resetBgUpload();
            }

            closeModalBg.addEventListener('click', closeBgModal);
            cancelBg.addEventListener('click', closeBgModal);

            function resetBgUpload() {
                uploadAreaBg.style.display = 'block';
                previewContainerBg.style.display = 'none';
                previewImageBg.src = '';
                saveBgBtn.disabled = true;
                fileInputBg.value = '';
                selectedBgFile = null;
                
                bgOptions.forEach(opt => opt.classList.remove('selected'));
            }

            bgOptions.forEach(option => {
                option.addEventListener('click', function() {
                    bgOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    selectedBgType = this.getAttribute('data-bg');
                    selectedGradient = this.getAttribute('data-gradient');
                    
                    contentArea.style.background = selectedGradient;
                    contentArea.style.backgroundSize = 'cover';
                    contentArea.style.backgroundPosition = 'center';
                    
                    localStorage.setItem('clienteBgType', selectedBgType);
                    localStorage.setItem('clienteBgGradient', selectedGradient);
                    
                    selectedBgFile = null;
                    saveBgBtn.disabled = false;
                });
            });

            uploadAreaBg.addEventListener('click', function() {
                fileInputBg.click();
            });

            uploadAreaBg.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = '#3b82f6';
                this.style.backgroundColor = '#eef2ff';
            });

            uploadAreaBg.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '#e2e8f0';
                this.style.backgroundColor = '#f8fafc';
            });

            uploadAreaBg.addEventListener('drop', function(e) {
                e.preventDefault();
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleBgFile(files[0]);
                }
                this.style.borderColor = '#e2e8f0';
                this.style.backgroundColor = '#f8fafc';
            });

            fileInputBg.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleBgFile(this.files[0]);
                }
            });

            function handleBgFile(file) {
                if (!file.type.match('image.*')) {
                    mostrarNotificacao('Por favor, selecione uma imagem válida.', 'error');
                    return;
                }
                
                if (file.size > 3 * 1024 * 1024) {
                    mostrarNotificacao('A imagem deve ter no máximo 3MB.', 'error');
                    return;
                }

                selectedBgFile = file;
                selectedBgType = 'custom';

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImageBg.src = e.target.result;
                    uploadAreaBg.style.display = 'none';
                    previewContainerBg.style.display = 'block';
                    saveBgBtn.disabled = false;
                    
                    bgOptions.forEach(opt => opt.classList.remove('selected'));
                };
                reader.readAsDataURL(file);
            }

            saveBgBtn.addEventListener('click', function() {
                if (selectedBgType === 'custom' && selectedBgFile) {
                    saveBgBtn.disabled = true;
                    saveBgBtn.textContent = 'Enviando...';

                    const formData = new FormData();
                    formData.append('background', selectedBgFile);

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            contentArea.style.backgroundImage = `url('${data.background}?t=${new Date().getTime()}')`;
                            contentArea.style.backgroundSize = 'cover';
                            contentArea.style.backgroundPosition = 'center';
                            
                            localStorage.removeItem('clienteBgGradient');
                            
                            mostrarNotificacao(data.message, 'success');
                            closeBgModal();
                        } else {
                            mostrarNotificacao(data.message, 'error');
                            saveBgBtn.disabled = false;
                            saveBgBtn.textContent = 'Salvar fundo';
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        mostrarNotificacao('Erro ao enviar a imagem. Tente novamente.', 'error');
                        saveBgBtn.disabled = false;
                        saveBgBtn.textContent = 'Salvar fundo';
                    });
                } else {
                    closeBgModal();
                }
            });

            const savedBgGradient = localStorage.getItem('clienteBgGradient');
            if (savedBgGradient) {
                contentArea.style.background = savedBgGradient;
                contentArea.style.backgroundSize = 'cover';
                contentArea.style.backgroundPosition = 'center';
            }

            window.addEventListener('click', function(e) {
                if (e.target === modalFoto) {
                    closeFotoModal();
                }
                if (e.target === modalBg) {
                    closeBgModal();
                }
            });
        });
    </script>
</body>
</html>