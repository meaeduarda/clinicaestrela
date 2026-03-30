<?php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['responsavel_id'])) {
    header("Location: login_cliente.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Configurações de upload
define('MAX_FILE_SIZE', 3 * 1024 * 1024); // 3MB
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/clinicaestrela/uploads/');
define('FOTOS_PATH', UPLOAD_PATH . 'fotos/');
define('BACKGROUNDS_PATH', UPLOAD_PATH . 'backgrounds/');

// Criar pastas se não existirem
if (!file_exists(FOTOS_PATH)) {
    mkdir(FOTOS_PATH, 0777, true);
}
if (!file_exists(BACKGROUNDS_PATH)) {
    mkdir(BACKGROUNDS_PATH, 0777, true);
}

// Função para salvar dados do responsável no JSON
function salvarDadosResponsavel($responsavelId, $campo, $valor) {
    $jsonFile = __DIR__ . '/../dados_cliente/user.json';
    
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $responsaveis = json_decode($jsonContent, true) ?: [];
        
        foreach ($responsaveis as $key => $responsavel) {
            if ($responsavel['id'] == $responsavelId) {
                $responsaveis[$key][$campo] = $valor;
                break;
            }
        }
        
        file_put_contents($jsonFile, json_encode($responsaveis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    
    return false;
}

// Função para carregar dados do responsável do JSON
function carregarDadosResponsavel($responsavelId) {
    $jsonFile = __DIR__ . '/../dados_cliente/user.json';
    
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $responsaveis = json_decode($jsonContent, true) ?: [];
        
        foreach ($responsaveis as $responsavel) {
            if ($responsavel['id'] == $responsavelId) {
                return $responsavel;
            }
        }
    }
    
    return null;
}

function processarUploadFoto() {
    $response = ['success' => false, 'message' => '', 'foto' => ''];
    
    try {
        if (!isset($_FILES['foto'])) {
            throw new Exception('Nenhum arquivo enviado.');
        }
        
        $file = $_FILES['foto'];
        
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
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Arquivo muito grande. Máximo 3MB.');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception('Arquivo não é uma imagem válida.');
        }
        
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, WEBP ou GIF.');
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Extensão de arquivo não permitida.');
        }
        
        $responsavelId = isset($_SESSION['responsavel_id']) ? $_SESSION['responsavel_id'] : 'temp';
        $filename = 'foto_' . $responsavelId . '_' . uniqid() . '.' . $extension;
        $filepath = FOTOS_PATH . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $fotoUrl = '/clinicaestrela/uploads/fotos/' . $filename;
            $_SESSION['paciente_foto'] = $fotoUrl;
            salvarDadosResponsavel($responsavelId, 'paciente_foto', $fotoUrl);
            
            $response['success'] = true;
            $response['message'] = 'Foto atualizada com sucesso!';
            $response['foto'] = $fotoUrl;
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
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Arquivo muito grande. Máximo 3MB.');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception('Arquivo não é uma imagem válida.');
        }
        
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG ou WEBP.');
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Extensão de arquivo não permitida.');
        }
        
        $responsavelId = isset($_SESSION['responsavel_id']) ? $_SESSION['responsavel_id'] : 'temp';
        $filename = 'bg_' . $responsavelId . '_' . uniqid() . '.' . $extension;
        $filepath = BACKGROUNDS_PATH . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $bgUrl = '/clinicaestrela/uploads/backgrounds/' . $filename;
            $_SESSION['background_foto'] = $bgUrl;
            salvarDadosResponsavel($responsavelId, 'background_foto', $bgUrl);
            
            $response['success'] = true;
            $response['message'] = 'Background alterado com sucesso!';
            $response['background'] = $bgUrl;
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

// Carregar dados do responsável
$responsavelId = $_SESSION['responsavel_id'];
$dadosResponsavel = carregarDadosResponsavel($responsavelId);

// Carregar dados do paciente 
$jsonFilePaciente = __DIR__ . '/../dados/ativo-cad.json';
$pacientesData = [];
$pacienteAtivo = null;

if (file_exists($jsonFilePaciente)) {
    $jsonContent = file_get_contents($jsonFilePaciente);
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
$fotoPerfil = '';
if ($dadosResponsavel && !empty($dadosResponsavel['paciente_foto'])) {
    $fotoPerfil = $dadosResponsavel['paciente_foto'];
    $_SESSION['paciente_foto'] = $fotoPerfil;
} elseif (isset($_SESSION['paciente_foto']) && !empty($_SESSION['paciente_foto'])) {
    $fotoPerfil = $_SESSION['paciente_foto'];
} else {
    $fotoPerfil = 'https://ui-avatars.com/api/?name=' . urlencode($paciente['nome_completo']) . '&background=3b82f6&color=fff&size=200';
}

// Background
$backgroundAtual = '';
if ($dadosResponsavel && !empty($dadosResponsavel['background_foto'])) {
    $backgroundAtual = $dadosResponsavel['background_foto'];
    $_SESSION['background_foto'] = $backgroundAtual;
} elseif (isset($_SESSION['background_foto']) && !empty($_SESSION['background_foto'])) {
    $backgroundAtual = $_SESSION['background_foto'];
}

$nomeLogado = $_SESSION['responsavel_nome'] ?? ($dadosResponsavel['nome_completo'] ?? 'Maria Eduarda');
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
            <?php if ($secaoAtiva === 'painel'): ?>
                <div class="page-header">
                    <h1>
                        Dados Cadastrais
                    </h1>
                    <button class="btn-change-bg" id="btnChangeBg">
                        <i class="fas fa-palette"></i>
                        Alterar plano de fundo
                    </button>
                </div>

                <div class="content-area" id="contentArea" <?php echo !empty($backgroundAtual) ? 'style="background-image: url(\'' . $backgroundAtual . '\'); background-size: cover; background-position: center;"' : ''; ?>>
                    
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

            <?php elseif ($secaoAtiva === 'plano-terapeutico'): ?>
                <?php include 'plano_terapeutico_cliente.php'; ?>
            <?php elseif ($secaoAtiva === 'agenda'): ?>
                <div class="page-header">
                    <h1>Agenda da Criança</h1>
                </div>
                <div class="content-area" id="contentArea">
                    <div class="patient-card" style="text-align: center;">
                        <i class="fas fa-calendar-alt" style="font-size: 48px; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h2>Em breve</h2>
                        <p>A agenda da criança estará disponível em breve.</p>
                    </div>
                </div>
            <?php elseif ($secaoAtiva === 'documentos'): ?>
                <div class="page-header">
                    <h1>Documentos</h1>
                </div>
                <div class="content-area" id="contentArea">
                    <div class="patient-card" style="text-align: center;">
                        <i class="fas fa-folder-open" style="font-size: 48px; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h2>Em breve</h2>
                        <p>Os documentos estarão disponíveis em breve.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="clinic-footer">
                <i class="fas fa-star"></i> CLÍNICA ESTRELA <i class="fas fa-star"></i>
            </div>
        </main>
    </div>

    <!-- MODAL PARA FOTO -->
    <div class="modal" id="modalFoto">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-camera"></i>
                    Alterar Foto de Perfil
                </h3>
                <button class="modal-close" id="closeModalFoto">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-area" id="uploadAreaFoto">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Clique ou arraste uma imagem</p>
                    <span>Formatos: JPG, PNG, WEBP, GIF</span>
                    <small>Máximo 3MB</small>
                </div>
                <input type="file" id="fileInputFoto" accept="image/*" style="display: none;">
                <div class="preview-container" id="previewContainerFoto" style="display: none;">
                    <img class="preview-image" id="previewImageFoto" alt="Preview">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelFoto">Cancelar</button>
                <button class="btn-primary" id="saveFoto" disabled>Salvar foto</button>
            </div>
        </div>
    </div>

    <!-- MODAL PARA BACKGROUND -->
    <div class="modal" id="modalBackground">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-palette"></i>
                    Alterar Plano de Fundo
                </h3>
                <button class="modal-close" id="closeModalBg">&times;</button>
            </div>
            <div class="modal-body">
                <div class="background-options">
                    <div class="bg-option" data-bg="gradient1" data-gradient="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                        <div class="bg-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <span>Gradiente Roxo</span>
                    </div>
                    <div class="bg-option" data-bg="gradient2" data-gradient="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">
                        <div class="bg-preview" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
                        <span>Gradiente Rosa</span>
                    </div>
                    <div class="bg-option" data-bg="gradient3" data-gradient="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">
                        <div class="bg-preview" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
                        <span>Gradiente Azul</span>
                    </div>
                    <div class="bg-option" data-bg="gradient4" data-gradient="linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)">
                        <div class="bg-preview" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);"></div>
                        <span>Gradiente Verde</span>
                    </div>
                </div>
                <div class="upload-area" id="uploadAreaBg">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Ou envie sua própria imagem</p>
                    <span>Formatos: JPG, PNG, WEBP</span>
                    <small>Máximo 3MB</small>
                </div>
                <input type="file" id="fileInputBg" accept="image/*" style="display: none;">
                <div class="preview-container" id="previewContainerBg" style="display: none;">
                    <img class="preview-image" id="previewImageBg" alt="Preview">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancelBg">Cancelar</button>
                <button class="btn-primary" id="saveBg" disabled>Salvar fundo</button>
            </div>
        </div>
    </div>

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notificationMessage"></span>
    </div>

    <script src="../../dashboard/js/cliente/painel_cliente.js"></script>
</body>
</html>