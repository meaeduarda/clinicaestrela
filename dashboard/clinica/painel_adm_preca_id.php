<?php
// painel_adm_preca_id.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Dados mockados do paciente (serão substituídos por dados reais do banco)
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
    'foto' => '../../uploads/pacientes/joao_silva.jpg' // Caminho para a foto
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Menu Mobile Toggle -->
        <div class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Header Mobile -->
        <div class="mobile-header">
            <h1>Pré-Cadastro Clínico</h1>
            <div class="mobile-close" id="mobileClose">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-icon">
                    <img src="../../imagens/logo_clinica_estrela.png" alt="Logo" class="logo-img">
                </div>
                <h1>Clinica Estrela</h1>
                <div class="mobile-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <!-- Menu de Navegação - PADRÃO DO PROJETO -->
            <nav class="menu">
                <ul>
                    <li><a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a></li>
                    <li class="active"><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca_id.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
                    
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                        <li><a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                        <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <?php endif; ?>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                </ul>
            </nav>

            <!-- Usuário Logado -->
            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nomeLogado); ?>&background=random" alt="<?php echo htmlspecialchars($nomeLogado); ?>">
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($nomeLogado); ?></h3>
                    <p><?php echo htmlspecialchars(ucfirst($perfilLogado)); ?></p>
                </div>
            </div>
        </aside>

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
            <div class="patient-card">
                <div class="patient-photo-container">
                    <div class="patient-photo">
                        <img src="<?php echo $paciente['foto']; ?>" alt="Foto do paciente João Silva">
                        <div class="photo-upload-overlay">
                            <i class="fas fa-camera"></i>
                            <span>Alterar foto</span>
                        </div>
                    </div>
                </div>
                <div class="patient-info">
                    <div class="patient-header">
                        <h2 class="patient-name"><?php echo htmlspecialchars($paciente['nome_social']); ?></h2>
                        <span class="patient-age"><?php echo htmlspecialchars($paciente['idade']); ?></span>
                    </div>
                    <div class="patient-details">
                        <div class="patient-contact">
                            <span class="contact-label">Mãe:</span>
                            <span class="contact-value"><?php echo htmlspecialchars($paciente['nome_mae']); ?></span>
                        </div>
                        <div class="patient-phone">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars($paciente['telefone']); ?></span>
                            <i class="fas fa-comment message-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="patient-status">
                    <span class="status-badge">Status: <?php echo htmlspecialchars($paciente['status']); ?></span>
                </div>
            </div>

            <!-- Navegação por Abas -->
            <div class="navigation-tabs">
                <a href="#" class="tab active">
                    <i class="fas fa-id-card"></i>
                    <span>Identificação</span>
                </a>
                <a href="#" class="tab">
                    <i class="fas fa-comment-medical"></i>
                    <span>Queixa</span>
                </a>
                <a href="#" class="tab">
                    <i class="fas fa-history"></i>
                    <span>Antecedente</span>
                </a>
                <a href="#" class="tab">
                    <i class="fas fa-baby"></i>
                    <span>Desenvolvimento</span>
                </a>
                <a href="#" class="tab">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Observação Clínica</span>
                </a>
            </div>

            <!-- Card do Formulário -->
            <div class="form-card">
                <h3 class="form-title">Identificação</h3>
                
                <form id="form-identificacao" class="patient-form">
                    <!-- Linha 1 -->
                    <div class="form-row">
                        <div class="form-group large">
                            <label for="nome_completo">Nome Completo</label>
                            <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($paciente['nome_completo']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 2 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Sexo</label>
                            <div class="radio-group">
                                <label class="radio-option <?php echo $paciente['sexo'] === 'Masculino' ? 'selected' : ''; ?>">
                                    <input type="radio" name="sexo" value="masculino" <?php echo $paciente['sexo'] === 'Masculino' ? 'checked' : ''; ?> disabled>
                                    <span class="radio-label">Masculino</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nascimento">Nascimento</label>
                            <input type="text" id="nascimento" name="nascimento" value="<?php echo htmlspecialchars($paciente['data_nascimento']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 3 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_mae">Nome da Mãe</label>
                            <input type="text" id="nome_mae" name="nome_mae" value="<?php echo htmlspecialchars($paciente['nome_mae']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nome_pai">Nome do Pai</label>
                            <input type="text" id="nome_pai" name="nome_pai" value="<?php echo htmlspecialchars($paciente['nome_pai']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 4 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="responsavel">Responsável</label>
                            <select id="responsavel" name="responsavel" disabled>
                                <option value="<?php echo htmlspecialchars($paciente['responsavel']); ?>" selected><?php echo htmlspecialchars($paciente['responsavel']); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="parentesco">Parentesco</label>
                            <select id="parentesco" name="parentesco" disabled>
                                <option value="<?php echo htmlspecialchars($paciente['parentesco']); ?>" selected><?php echo htmlspecialchars($paciente['parentesco']); ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Linha 5 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($paciente['telefone']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($paciente['email']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 6 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cpf_responsavel">CPF</label>
                            <input type="text" id="cpf_responsavel" name="cpf_responsavel" value="<?php echo htmlspecialchars($paciente['cpf_responsavel']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="rg_responsavel">RG</label>
                            <input type="text" id="rg_responsavel" name="rg_responsavel" value="<?php echo htmlspecialchars($paciente['rg_responsavel']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 7 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="convenio">Convênio</label>
                            <select id="convenio" name="convenio" disabled>
                                <option value="<?php echo htmlspecialchars($paciente['convenio']); ?>" selected><?php echo htmlspecialchars($paciente['convenio']); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="telefone_convenio">Convênio</label>
                            <input type="text" id="telefone_convenio" name="telefone_convenio" value="<?php echo htmlspecialchars($paciente['telefone_convenio']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 8 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero_carteirinha">Nº Carteirinha</label>
                            <input type="text" id="numero_carteirinha" name="numero_carteirinha" value="<?php echo htmlspecialchars($paciente['numero_carteirinha']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="escolaridade">Escolaridade</label>
                            <input type="text" id="escolaridade" name="escolaridade" value="<?php echo htmlspecialchars($paciente['escolaridade']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 9 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cpf_paciente">CPF</label>
                            <input type="text" id="cpf_paciente" name="cpf_paciente" value="<?php echo htmlspecialchars($paciente['cpf_paciente']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="rg_paciente">RG</label>
                            <input type="text" id="rg_paciente" name="rg_paciente" value="<?php echo htmlspecialchars($paciente['rg_paciente']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Linha 10 -->
                    <div class="form-row">
                        <div class="form-group large">
                            <label for="escola">Escola</label>
                            <input type="text" id="escola" name="escola" value="<?php echo htmlspecialchars($paciente['escola']); ?>" readonly>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Botões de Ação -->
            <div class="action-buttons">
                <button type="button" class="btn btn-archive green">
                    <i class="fas fa-user-check"></i>
                    <span>Salvar Como Paciente Ativo</span>
                </button>
                <button type="button" class="btn btn-convert">
                    <i class="fas fa-archive"></i>
                    <span>Salvar Como Paciente Pendente</span>
                </button>
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

    <script>
        // Menu Mobile Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileClose = document.getElementById('mobileClose');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.querySelector('.sidebar .mobile-close');

        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
        });

        mobileClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
        
        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }

        // Navegação por abas
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Simular troca de conteúdo
                const tabName = tab.querySelector('span').textContent;
                console.log(`Carregando conteúdo da aba: ${tabName}`);
            });
        });

        // Modal de foto
        const photoModal = document.getElementById('photoModal');
        const photoUploadOverlay = document.querySelector('.photo-upload-overlay');
        const modalClose = document.querySelector('.modal-close');
        const cancelBtn = document.querySelector('.btn-cancel');
        const uploadBtn = document.querySelector('.btn-upload');
        const fileInput = document.getElementById('foto_paciente');
        const photoPreview = document.getElementById('photoPreview');
        const patientPhoto = document.querySelector('.patient-photo img');

        // Abrir modal
        photoUploadOverlay.addEventListener('click', () => {
            photoModal.style.display = 'block';
        });

        // Fechar modal
        function closeModal() {
            photoModal.style.display = 'none';
            fileInput.value = '';
        }

        modalClose.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Preview da foto selecionada
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Simular upload da foto
        uploadBtn.addEventListener('click', () => {
            if (fileInput.files.length > 0) {
                // Aqui seria o código PHP para upload real
                // Por enquanto, apenas atualizamos a preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    patientPhoto.src = e.target.result;
                    photoPreview.src = e.target.result;
                    
                    // Aqui você enviaria o form via AJAX para o PHP
                    console.log('Foto enviada para o servidor');
                    
                    closeModal();
                }
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                alert('Por favor, selecione uma foto.');
            }
        });

        // Botões de ação
        document.querySelector('.btn-save').addEventListener('click', () => {
            alert('Dados salvos com sucesso!');
        });

        document.querySelector('.btn-convert').addEventListener('click', () => {
            if (confirm('Tem certeza que deseja converter este pré-cadastro em paciente?')) {
                alert('Pré-cadastro convertido com sucesso!');
            }
        });

        document.querySelectorAll('.btn-archive').forEach(btn => {
            btn.addEventListener('click', function() {
                const isRed = this.classList.contains('red');
                const message = isRed 
                    ? 'Tem certeza que deseja arquivar (ação destrutiva)?' 
                    : 'Tem certeza que deseja arquivar este registro?';
                
                if (confirm(message)) {
                    alert(isRed ? 'Registro arquivado (destrutivo)!' : 'Registro arquivado!');
                }
            });
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', (e) => {
            if (e.target === photoModal) {
                closeModal();
            }
        });

        // Fechar menu ao clicar fora (mobile)
        document.addEventListener('click', (event) => {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnMobileToggle = mobileMenuToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnMobileToggle && window.innerWidth <= 768) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>