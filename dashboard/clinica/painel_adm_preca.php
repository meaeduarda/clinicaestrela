<?php
// painel_adm_preca.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Dados mockados do paciente
$paciente = [
    'nome_completo' => 'João Eduardo Souza da Silva',
    'nome_social' => 'João Silva',
    'idade' => '5 anos',
    'data_nascimento' => '15/02/2019',
    'sexo' => 'Masculino',
    'nome_mae' => 'Ana de Souza',
    'nome_pai' => 'Carlos Eduardo da Silva',
    'responsavel' => 'Ana de Souza',
    'parentesco' => 'Mãe',
    'telefone' => '(11) 98765-4321',
    'email' => 'ana.souza@email.com',
    'cpf_responsavel' => '123.456.789-00',
    'rg_responsavel' => '55.321.654-1',
    'cpf_paciente' => '123.456.789-00',
    'rg_paciente' => '55.321.654-1',
    'convenio' => 'Unimed Saúde',
    'telefone_convenio' => '(11) 98765-4321',
    'numero_carteirinha' => '65498721',
    'escolaridade' => 'Ensino Infantil',
    'escola' => 'Colégio Pingo de Luz',
    'status' => 'Aguardando',
    'foto' => '../../uploads/pacientes/joao_silva.jpg'
];

// Se não houver foto, usar padrão
if (!file_exists($paciente['foto'])) {
    $paciente['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($paciente['nome_social']) . '&size=200&background=4A7DFF&color=fff&bold=true';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Cadastro Clínico - Clinica Estrela</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaqueixa.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaantecedentes.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precadesenv.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca_observacao.css">
    
    <!-- Fontes e ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos de Notificação -->
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10000;
            transform: translateX(150%);
            transition: transform 0.3s ease;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            background-color: #10b981;
            border-left: 4px solid #059669;
        }
        
        .notification-error {
            background-color: #ef4444;
            border-left: 4px solid #dc2626;
        }
        
        .notification-info {
            background-color: #3b82f6;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes_precadastro/header_preca.php'; ?>
        
        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Topo Desktop -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-file-medical"></i> Pré-Cadastro Clínico</h2>
                <div class="top-icons">
                    <div class="icon-btn with-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge">2</span>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>

            <!-- Card do Paciente -->
            <?php include 'includes_precadastro/paciente_card.php'; ?>

            <!-- Navegação por Abas -->
            <div class="navigation-tabs">
                <a href="#" class="tab active" data-tab="identificacao">
                    <i class="fas fa-id-card"></i>
                    <span>Identificação</span>
                </a>
                <a href="#" class="tab" data-tab="queixa">
                    <i class="fas fa-comment-medical"></i>
                    <span>Queixa</span>
                </a>
                <a href="#" class="tab" data-tab="antecedente">
                    <i class="fas fa-history"></i>
                    <span>Antecedente</span>
                </a>
                <a href="#" class="tab" data-tab="desenvolvimento">
                    <i class="fas fa-baby"></i>
                    <span>Desenvolvimento</span>
                </a>
                <a href="#" class="tab" data-tab="observacao">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Observação Clínica</span>
                </a>
            </div>

            <!-- Cards dos Formulários -->
            <div id="form-identificacao" class="form-card tab-content active">
                <?php include 'includes_precadastro/formulario_identificacao.php'; ?>
            </div>

            <div id="form-queixa" class="form-card tab-content">
                <?php include 'includes_precadastro/formulario_queixa.php'; ?>
            </div>

            <div id="form-antecedente" class="form-card tab-content">
                <?php include 'includes_precadastro/formulario_antecedentes.php'; ?>
            </div>

            <div id="form-desenvolvimento" class="form-card tab-content">
                <?php include 'includes_precadastro/formulario_desenvolvimento.php'; ?>
            </div>

            <div id="form-observacao" class="form-card tab-content">
                <?php include 'includes_precadastro/formulario_observacao.php'; ?>
            </div>

            <!-- Rodapé -->
            <footer class="main-footer">
                <div class="footer-logo">
                    <i class="fas fa-star"></i>
                    <span>CLÍNICA ESTRELA</span>
                </div>
            </footer>
        </main>

        <!-- Modal para Upload de Foto -->
        <div class="modal" id="photoModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Alterar Foto do Paciente</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="photo-preview">
                        <img id="photoPreview" src="<?php echo $paciente['foto']; ?>" alt="Preview da foto">
                    </div>
                    <form id="photoUploadForm" class="upload-form">
                        <div class="form-group">
                            <label for="foto_paciente">Selecionar Foto</label>
                            <input type="file" id="foto_paciente" name="foto_paciente" accept="image/*">
                            <small>Formatos aceitos: JPG, PNG, GIF, WebP (máx. 2MB)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel">Cancelar</button>
                    <button type="button" class="btn btn-upload">Enviar Foto</button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- JavaScript -->
    <script src="/clinicaestrela/dashboard/js/painel_adm_preca.js" defer></script>
    <script src="/clinicaestrela/dashboard/js/tabs-navigation.js" defer></script>
    <script src="/clinicaestrela/dashboard/js/form-handlers.js" defer></script>
    <script src="/clinicaestrela/dashboard/js/file-upload.js" defer></script>
    <script src="/clinicaestrela/dashboard/js/modal-handler.js" defer></script>
    
    <!-- Dados passados do PHP para o JavaScript -->
    <script>
        const pacienteData = <?php echo json_encode($paciente); ?>;
        const usuarioLogado = {
            nome: "<?php echo htmlspecialchars($nomeLogado); ?>",
            perfil: "<?php echo htmlspecialchars($perfilLogado); ?>"
        };
        
        const config = {
            maxAnexos: 10,
            maxTamanhoAnexo: 5 * 1024 * 1024,
            maxTamanhoFoto: 2 * 1024 * 1024,
            tiposAnexoPermitidos: ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            tiposFotoPermitidos: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            storageKey: 'preCadastroDados'
        };
        
        // Funções globais para os botões
        window.salvarComoPacienteAtivo = function() {
            if (window.painelPreCadastro) {
                window.painelPreCadastro.salvarComoPacienteAtivo();
            }
        };
        
        window.salvarComoPacientePendente = function() {
            if (window.painelPreCadastro) {
                window.painelPreCadastro.salvarComoPacientePendente();
            }
        };
    </script>

</body>
</html>