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
    <!-- Estilos CSS IDENTIFICACAO -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <!-- Estilos CSS QUEIXA -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaqueixa.css">
    <!-- Estilos CSS ANTECEDENTES -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaantecedentes.css">
    <!-- Estilos CSS DESENVOLVIMENTO -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precadesenv.css">
    <!-- Estilos CSS OBSERVACAO CLINICA -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca_observacao.css">

    <!-- Fontes e ícones -->
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
                    <li class><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
                    
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                        <li><a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                        <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <?php endif; ?>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a></li>
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

            <!-- Card do Formulário - IDENTIFICAÇÃO -->
            <div id="form-identificacao" class="form-card tab-content active">
                <h3 class="form-title">Identificação</h3>
                
                <form id="form-identificacao-data" class="patient-form">
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

            <!-- Card do Formulário - QUEIXA -->
            <div id="form-queixa" class="form-card tab-content">
                <h3 class="form-title">Queixa Principal e Demanda Atual</h3>
                
                <form id="form-queixa-data" class="patient-form">
                    <!-- Seção 1: Motivo da Procura -->
                    <div class="form-section">
                        <h4 class="section-title">Motivo da Procura</h4>
                        
                        <!-- Motivo principal da procura -->
                        <div class="form-row">
                            <div class="form-group large">
                                <label for="motivo_principal">Motivo principal da procura</label>
                                <textarea id="motivo_principal" name="motivo_principal" rows="3" placeholder="Descreva com suas próprias palavras, por exemplo: 'Dificuldade na fala, comportamento agitado, atraso no desenvolvimento...'"></textarea>
                            </div>
                        </div>

                        <!-- Quem identificou a necessidade? (CHECKBOXES) -->
                        <div class="form-row">
                            <div class="form-group large">
                                <label>Quem identificou a necessidade?</label>
                                <div class="checkbox-grid">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_pais" name="quem_identificou[]" value="pais">
                                        <label for="identificou_pais">Pais / Responsáveis</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_escola" name="quem_identificou[]" value="escola">
                                        <label for="identificou_escola">Escola</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_pediatra" name="quem_identificou[]" value="pediatra">
                                        <label for="identificou_pediatra">Pediatra</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_neurologista" name="quem_identificou[]" value="neurologista">
                                        <label for="identificou_neurologista">Neurologista</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_outro_prof" name="quem_identificou[]" value="outro_profissional">
                                        <label for="identificou_outro_prof">Outro profissional</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="identificou_outro" name="quem_identificou[]" value="outro">
                                        <label for="identificou_outro">Outro</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Encaminhado por algum profissional? (RADIO) -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Encaminhado por algum profissional?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="encaminhado" value="sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="encaminhado" value="nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do profissional (aparece se SIM for selecionado) -->
                        <div id="dados-profissional" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nome_profissional">Nome do profissional</label>
                                    <input type="text" id="nome_profissional" name="nome_profissional" placeholder="Digite o nome">
                                </div>
                                <div class="form-group">
                                    <label for="especialidade_profissional">Especialidade</label>
                                    <input type="text" id="especialidade_profissional" name="especialidade_profissional" placeholder="Digite a especialidade">
                                </div>
                            </div>
                        </div>

                        <!-- Possui relatório? (RADIO) -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Possui relatório?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="possui_relatorio" value="sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="possui_relatorio" value="nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Seção 2: Sinais Observados -->
                    <div class="form-section">
                        <h4 class="section-title">Sinais Observados</h4>
                        
                        <!-- Checkboxes de sinais -->
                        <div class="form-row">
                            <div class="form-group large">
                                <div class="checkbox-grid two-columns">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_atraso_fala" name="sinais_observados[]" value="atraso_fala">
                                        <label for="sinal_atraso_fala">Atraso na fala</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_dificuldade_comunicacao" name="sinais_observados[]" value="dificuldade_comunicacao">
                                        <label for="sinal_dificuldade_comunicacao">Dificuldade de comunicação</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_pouco_contato_visual" name="sinais_observados[]" value="pouco_contato_visual">
                                        <label for="sinal_pouco_contato_visual">Pouco contato visual</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_nao_responde_chamado" name="sinais_observados[]" value="nao_responde_chamado">
                                        <label for="sinal_nao_responde_chamado">Não responde quando chamado</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_comportamentos_repetitivos" name="sinais_observados[]" value="comportamentos_repetitivos">
                                        <label for="sinal_comportamentos_repetitivos">Comportamentos repetitivos</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_sensibilidade_sons" name="sinais_observados[]" value="sensibilidade_sons">
                                        <label for="sinal_sensibilidade_sons">Sensibilidade a sons / texturas</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_agitacao" name="sinais_observados[]" value="agitacao">
                                        <label for="sinal_agitacao">Agitação / hiperatividade</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_dificuldade_interacao" name="sinais_observados[]" value="dificuldade_interacao">
                                        <label for="sinal_dificuldade_interacao">Dificuldade de interação social</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_agressividade" name="sinais_observados[]" value="agressividade">
                                        <label for="sinal_agressividade">Agressividade</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_dificuldade_aprendizagem" name="sinais_observados[]" value="dificuldade_aprendizagem">
                                        <label for="sinal_dificuldade_aprendizagem">Dificuldade de aprendizagem</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="sinal_outro" name="sinais_observados[]" value="outro">
                                        <label for="sinal_outro">Outro:</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo "Outro" detalhado (aparece se "Outro" for selecionado) -->
                        <div id="sinal-outro-detalhe" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <label for="sinal_outro_descricao">Especificar outro sinal</label>
                                    <input type="text" id="sinal_outro_descricao" name="sinal_outro_descricao" placeholder="Descreva o sinal observado">
                                </div>
                            </div>
                        </div>

                        <!-- Descrição dos sinais observados -->
                        <div class="form-row">
                            <div class="form-group large">
                                <label for="descricao_sinais">Descrever os sinais observados</label>
                                <textarea id="descricao_sinais" name="descricao_sinais" rows="3" placeholder="A criança fala poucas palavras e se irrita quando não é compreendida."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 3: Expectativas da Família -->
                    <div class="form-section">
                        <h4 class="section-title">Expectativas da Família</h4>
                        
                        <!-- Expectativas -->
                        <div class="form-row">
                            <div class="form-group large">
                                <label for="expectativas_familia">O que a família espera do atendimento?</label>
                                <textarea id="expectativas_familia" name="expectativas_familia" rows="3" placeholder="Esperamos que ela consiga se comunicar melhor e interagir com outras crianças..."></textarea>
                            </div>
                        </div>

                        <!-- Tratamento anterior (RADIO) -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Já realizou algum tratamento anteriormente?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="tratamento_anterior" value="sim" id="tratamento_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="tratamento_anterior" value="nao" id="tratamento_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do tratamento anterior (aparece se SIM for selecionado) -->
                        <div id="dados-tratamento" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="tipo_tratamento">Tipo de tratamento:</label>
                                    <input type="text" id="tipo_tratamento" name="tipo_tratamento" placeholder="Ex: Fonoaudiologia, Psicologia, TO...">
                                </div>
                                <div class="form-group">
                                    <label for="local_tratamento">Local:</label>
                                    <input type="text" id="local_tratamento" name="local_tratamento" placeholder="Nome da clínica/hospital">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="periodo_tratamento">Período aproximado:</label>
                                    <input type="text" id="periodo_tratamento" name="periodo_tratamento" placeholder="Ex: 6 meses em 2023">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Card do Formulário - ANTECEDENTES -->
            <div id="form-antecedente" class="form-card tab-content">
                <h3 class="form-title">Antecedentes</h3>
                
                <form id="form-antecedente-data" class="patient-form">
                    <!-- Seção 1: Gestação e Nascimento -->
                    <div class="form-section">
                        <h4 class="section-title">Gestação e Nascimento</h4>
                        
                        <!-- Duração da gestação -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Duração da gestação</label>
                                <div class="checkbox-group-grid">
                                    <div class="checkbox-item">
                                        <input type="radio" name="duracao_gestacao" id="gestacao_normal" value="normal">
                                        <label for="gestacao_normal">Normal (37-41 sem)</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="duracao_gestacao" id="gestacao_prematura" value="prematura">
                                        <label for="gestacao_prematura">Prematura (&lt;37 sem)</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="duracao_gestacao" id="gestacao_prolongada" value="prolongada">
                                        <label for="gestacao_prolongada">Prolongada (42+ sem)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de parto -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipo de parto</label>
                                <div class="checkbox-group-grid">
                                    <div class="checkbox-item">
                                        <input type="radio" name="tipo_parto" id="parto_normal" value="normal" checked>
                                        <label for="parto_normal">Normal</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="tipo_parto" id="parto_cesarea" value="cesarea">
                                        <label for="parto_cesarea">Cesárea</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="tipo_parto" id="parto_complicado" value="complicado">
                                        <label for="parto_complicado">Complicado</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Problemas durante a gestação ou parto -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Houve problemas durante a gestação ou parto?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="problemas_gestacao" value="sim" id="problemas_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="problemas_gestacao" value="nao" id="problemas_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar problemas -->
                        <div id="detalhes-problemas-gestacao" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="quais_problemas_gestacao">Quais problemas houve?</label>
                                        <textarea id="quais_problemas_gestacao" name="quais_problemas_gestacao" rows="3" placeholder="Descreva os problemas ocorridos durante a gestação ou parto..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Problemas após o nascimento -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teve algum problema após o nascimento?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="problemas_pos_nascimento" value="sim" id="pos_nascimento_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="problemas_pos_nascimento" value="nao" id="pos_nascimento_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar problemas pós-nascimento -->
                        <div id="detalhes-problemas-pos-nascimento" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="quais_problemas_pos_nascimento">Quais problemas houve?</label>
                                        <textarea id="quais_problemas_pos_nascimento" name="quais_problemas_pos_nascimento" rows="3" placeholder="Descreva os problemas ocorridos após o nascimento..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 2: História Médica Pessoal -->
                    <div class="form-section medical-history-section">
                        <h4 class="section-title">História Médica Pessoal</h4>
                        
                        <!-- Complicações graves de saúde -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teve alguma complicação grave de saúde?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="complicacoes_graves" value="sim" id="complicacoes_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="complicacoes_graves" value="nao" id="complicacoes_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar complicações -->
                        <div id="detalhes-complicacoes" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="quais_complicacoes">Qual(is)?</label>
                                        <textarea id="quais_complicacoes" name="quais_complicacoes" rows="3" placeholder="Descreva as complicações de saúde..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hospitalizações -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Hospitalizações</label>
                                <div class="checkbox-group-grid">
                                    <div class="checkbox-item">
                                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_nunca" value="nunca">
                                        <label for="hospitalizacao_nunca">Nunca</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_1vez" value="1vez">
                                        <label for="hospitalizacao_1vez">1 vez</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_2mais" value="2mais">
                                        <label for="hospitalizacao_2mais">2 ou mais</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes hospitalizações (sempre visível para preenchimento) -->
                        <div class="form-row two-col">
                            <div class="form-group">
                                <div class="details-field">
                                    <label for="motivo_hospitalizacao">Motivo:</label>
                                    <textarea id="motivo_hospitalizacao" name="motivo_hospitalizacao" rows="2" placeholder="Motivo da hospitalização..."></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="details-field">
                                    <label for="idade_hospitalizacao">Idade:</label>
                                    <textarea id="idade_hospitalizacao" name="idade_hospitalizacao" rows="2" placeholder="Idade na hospitalização..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico de convulsões -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Histórico de convulsões?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="convulsoes" value="sim" id="convulsoes_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="convulsoes" value="nao" id="convulsoes_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar convulsões -->
                        <div id="detalhes-convulsoes" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="detalhes_convulsoes">Detalhes das crises</label>
                                        <textarea id="detalhes_convulsoes" name="detalhes_convulsoes" rows="3" placeholder="Descreva o tipo, frequência e características das crises..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico de alergias -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Histórico de alergias ou restrições alimentares</label>
                                <div class="checkbox-group-grid">
                                    <div class="checkbox-item">
                                        <input type="radio" name="alergias" id="alergias_nenhuma" value="nenhuma" checked>
                                        <label for="alergias_nenhuma">Sem alergias</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="alergias" id="alergias_restricoes" value="restricoes">
                                        <label for="alergias_restricoes">Tem algumas restrições</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar alergias -->
                        <div id="detalhes-alergias" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="quais_alergias">Qual(is) alergia / restrição?</label>
                                        <textarea id="quais_alergias" name="quais_alergias" rows="3" placeholder="Descreva as alergias ou restrições alimentares..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 3: História Médica Familiar -->
                    <div class="form-section">
                        <h4 class="section-title">História Médica Familiar</h4>
                        
                        <!-- Histórico familiar -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Alguém na família tem histórico de:</label>
                                <div class="checkbox-group-grid">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_autismo" name="historico_familiar[]" value="autismo">
                                        <label for="familia_autismo">Autismo</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_tdah" name="historico_familiar[]" value="tdah">
                                        <label for="familia_tdah">Transtorno de Déficit de Atenção (TDAH)</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_atraso" name="historico_familiar[]" value="atraso">
                                        <label for="familia_atraso">Atraso no desenvolvimento</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_epilepsia" name="historico_familiar[]" value="epilepsia">
                                        <label for="familia_epilepsia">Epilepsia / Convulsões</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_esquizofrenia" name="historico_familiar[]" value="esquizofrenia">
                                        <label for="familia_esquizofrenia">Esquizofrenia</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_aprendizagem" name="historico_familiar[]" value="aprendizagem">
                                        <label for="familia_aprendizagem">Transtorno de Aprendizagem</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="familia_outros" name="historico_familiar[]" value="outros">
                                        <label for="familia_outros">Outros:</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar "outros" -->
                        <div id="detalhes-outros-familia" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <input type="text" id="familia_outros_descricao" name="familia_outros_descricao" placeholder="Especificar outros históricos familiares...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 4: Sobre o crescimento da criança -->
                    <div class="form-section">
                        <h4 class="section-title">Sobre o crescimento da criança</h4>
                        
                        <!-- Crescimento similar aos irmãos -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>O crescimento da criança é similar ao de seus irmãos?</label>
                                <div class="radio-group horizontal">
                                    <label class="radio-option">
                                        <input type="radio" name="crescimento_similar" value="sim" id="crescimento_sim">
                                        <span class="radio-label">Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="crescimento_similar" value="nao" id="crescimento_nao">
                                        <span class="radio-label">Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para detalhar diferenças -->
                        <div id="detalhes-diferenca-crescimento" style="display: none;">
                            <div class="form-row">
                                <div class="form-group large">
                                    <div class="details-field">
                                        <label for="diferenca_crescimento">Descreva a diferença:</label>
                                        <textarea id="diferenca_crescimento" name="diferenca_crescimento" rows="3" placeholder="Descreva as diferenças no crescimento em relação aos irmãos..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

                        <!-- Card do Formulário - DESENVOLVIMENTO -->
            <div id="form-desenvolvimento" class="form-card tab-content">
                <h3 class="form-title">Desenvolvimento</h3>
                
                <form id="form-desenvolvimento-data" class="patient-form">
                    <!-- Seção 1: Motor -->
                    <div class="form-section">
                        <h4 class="section-title">Motor</h4>
                        
                        <!-- Sentou-se sem apoio? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Sentou-se sem apoio?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="sentou_sem_apoio" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="sentou_sem_apoio" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_sentou" name="idade_sentou" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Engatinhou? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Engatinhou?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="engatinhou" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="engatinhou" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_engatinhou" name="idade_engatinhou" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Começou a andar? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Começou a andar?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="comecou_andar" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="comecou_andar" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_andou" name="idade_andou" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Controle dos esfíncteres -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Controle dos esfíncteres</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="controle_esfincteres" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="controle_esfincteres" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_controle" name="idade_controle" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 2: Fala e Linguagem -->
                    <div class="form-section">
                        <h4 class="section-title">Fala e Linguagem</h4>
                        
                        <!-- Balbuciou? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Balbuciou?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="balbuciou" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="balbuciou" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_balbucio" name="idade_balbucio" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Falou as primeiras palavras? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Falou as primeiras palavras?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="primeiras_palavras" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="primeiras_palavras" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_primeiras_palavras" name="idade_primeiras_palavras" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Montou frases? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Montou frases?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="montou_frases" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="montou_frases" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                    <div class="age-input-container">
                                        <input type="text" class="age-input" id="idade_frases" name="idade_frases" placeholder="Idade (meses)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Atualmente conversa com frases completas? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Atualmente conversa com frases completas?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="frases_completas" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="frases_completas" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 3: Social -->
                    <div class="form-section">
                        <h4 class="section-title">Social</h4>
                        
                        <!-- Sorriu em resposta a interações? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Sorriu em resposta a interações?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="sorriu_interacoes" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="sorriu_interacoes" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Interage com outras crianças? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Interage com outras crianças?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="interage_criancas" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="interage_criancas" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Seção 4: Alimentação -->
                    <div class="form-section">
                        <h4 class="section-title">Alimentação</h4>
                        
                        <!-- Aceitou bem a introdução alimentar? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Aceitou bem a introdução alimentar?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="introducao_alimentar" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="introducao_alimentar" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alimenta-se sozinho? -->
                        <div class="form-row question-row">
                            <div class="question-item">
                                <div class="question-text">
                                    <span class="question-label">Alimenta-se sozinho?</span>
                                </div>
                                <div class="question-controls">
                                    <div class="radio-group compact">
                                        <label class="radio-option">
                                            <input type="radio" name="alimenta_sozinho" value="sim">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Sim</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="alimenta_sozinho" value="nao">
                                            <span class="radio-dot"></span>
                                            <span class="radio-label">Não</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hábitos alimentares / Observações -->
                        <div class="form-row">
                            <div class="form-group large">
                                <div class="textarea-container">
                                    <label for="habitos_alimentares">Hábitos alimentares / Observações</label>
                                    <div class="textarea-wrapper">
                                        <textarea id="habitos_alimentares" name="habitos_alimentares" rows="4" placeholder="Descreva os hábitos alimentares, preferências, dificuldades..."></textarea>
                                        <div class="char-counter">
                                            <span class="char-count">0</span>/400
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

                        <!-- Card do Formulário - OBSERVAÇÃO CLÍNICA -->
            <div id="form-observacao" class="form-card tab-content">
                <h3 class="form-title">Observação Clínica</h3>
                
                <form id="form-observacao-data" class="patient-form">
                    <!-- Seção 1: Observações Clínicas -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Observações Clínicas</span>
                        </h4>
                        
                        <!-- Campo principal de observações -->
                        <div class="form-row">
                            <div class="form-group large">
                                <div class="textarea-container">
                                    <label for="observacoes_clinicas">Observações Clínicas</label>
                                    <div class="textarea-wrapper">
                                        <textarea id="observacoes_clinicas" name="observacoes_clinicas" rows="6" 
                                                  placeholder="Neste espaço, registre observações relevantes sobre a criança, interações durante a avaliação inicial ou outros pontos importantes."></textarea>
                                        <div class="char-counter">
                                            <span class="char-count" id="observacoes_counter">0</span>/500
                                        </div>
                                    </div>
                                    <small class="field-info">Este campo é visível ao prontuário clínico</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 2: Anexos -->
                    <div class="form-section collapsible-section">
                        <div class="section-header collapsible-header">
                            <h4 class="section-title">
                                <i class="fas fa-folder"></i>
                                <span>Anexos</span>
                            </h4>
                        </div>
                        
                        <div class="collapsible-content">
                            <!-- Botão para adicionar anexo -->
                            <div class="form-row">
                                <div class="form-group large">
                                    <button type="button" class="btn-add-attachment" id="btn-add-attachment">
                                        <i class="fas fa-plus"></i>
                                        <span>Adicionar Anexo</span>
                                    </button>
                                    <input type="file" id="file-upload" name="anexos[]" multiple accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                </div>
                            </div>

                            <!-- Lista de anexos (inicialmente vazia) -->
                            <div class="attachments-list" id="attachments-list">
                                <!-- Anexos serão adicionados dinamicamente aqui -->
                                <div class="no-attachments">
                                    <i class="fas fa-paperclip"></i>
                                    <p>Nenhum anexo adicionado</p>
                                </div>
                            </div>

                            <!-- Regras de upload -->
                            <div class="upload-rules">
                                <p class="rules-title">Regras de upload:</p>
                                <ul class="rules-list">
                                    <li><i class="fas fa-check-circle"></i> Máx.: <strong>10 arquivos</strong></li>
                                    <li><i class="fas fa-check-circle"></i> Tipos aceitos: <strong>PDF, JPG, PNG</strong></li>
                                    <li><i class="fas fa-check-circle"></i> Tamanho máximo: <strong>até 5MB por arquivo</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                   <!-- Botões de ação -->
                <div class="action-buttons observacao-buttons">
                    <div class="button-group">
                        <button type="button" class="btn btn-convert" onclick="salvarComoPacienteAtivo()">
                            <i class="fas fa-user-check"></i>
                            <span>Salvar Como Paciente Ativo</span>
                        </button>
                        <button type="button" class="btn btn-archive" onclick="salvarComoPacientePendente()">
                            <i class="fas fa-user-clock"></i>
                            <span>Salvar Como Paciente Pendente</span>
                        </button>
                    </div>
                </div>
                </form>
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

        // Controle das abas do formulário
        const tabs = document.querySelectorAll('.tab');
        const tabContents = {
            'Identificação': document.getElementById('form-identificacao'),
            'Queixa': document.getElementById('form-queixa'),
            'Antecedente': document.getElementById('form-antecedente'),
            'Desenvolvimento': document.getElementById('form-desenvolvimento'),
            'Observação Clínica': document.getElementById('form-observacao')
        };

        // Dados do formulário (em memória para simulação)
        const formData = {
            identificacao: {},
            queixa: {},
            antecedentes: {},
            desenvolvimento: {},
            observacao: {}
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Nome da aba clicada
                const tabName = tab.querySelector('span').textContent.trim();
                
                // Salvar dados da aba atual antes de trocar
                saveCurrentTabData();
                
                // Remover classe active de todas as abas
                tabs.forEach(t => t.classList.remove('active'));
                
                // Adicionar classe active à aba clicada
                tab.classList.add('active');
                
                // Ocultar todos os conteúdos
                Object.values(tabContents).forEach(content => {
                    if (content) {
                        content.classList.remove('active');
                        content.style.display = 'none';
                    }
                });
                
                // Mostrar conteúdo da aba clicada
                const targetContent = tabContents[tabName];
                if (targetContent) {
                    targetContent.style.display = 'block';
                    setTimeout(() => {
                        targetContent.classList.add('active');
                    }, 10);
                    
                    // Carregar dados salvos para esta aba
                    loadTabData(tabName);
                }
                
                console.log(`Aba ativa: ${tabName}`);
            });
        });

        // Funções específicas para a aba QUEIXA

        // Mostrar/ocultar campos condicionais
        function toggleCamposCondicionais() {
            // Mostrar dados do profissional se "Encaminhado por algum profissional? Sim" estiver selecionado
            const encaminhadoSim = document.querySelector('input[name="encaminhado"][value="sim"]');
            const dadosProfissional = document.getElementById('dados-profissional');
            
            if (encaminhadoSim && dadosProfissional) {
                if (encaminhadoSim.checked) {
                    dadosProfissional.style.display = 'block';
                } else {
                    dadosProfissional.style.display = 'none';
                }
            }
            
            // Mostrar campo "Outro detalhe" se "Outro" em sinais estiver selecionado
            const sinalOutro = document.getElementById('sinal_outro');
            const sinalOutroDetalhe = document.getElementById('sinal-outro-detalhe');
            
            if (sinalOutro && sinalOutroDetalhe) {
                if (sinalOutro.checked) {
                    sinalOutroDetalhe.style.display = 'block';
                } else {
                    sinalOutroDetalhe.style.display = 'none';
                }
            }
            
            // Mostrar dados do tratamento se "Tratamento anterior Sim" estiver selecionado
            const tratamentoSim = document.getElementById('tratamento_sim');
            const dadosTratamento = document.getElementById('dados-tratamento');
            
            if (tratamentoSim && dadosTratamento) {
                if (tratamentoSim.checked) {
                    dadosTratamento.style.display = 'block';
                } else {
                    dadosTratamento.style.display = 'none';
                }
            }
        }

        // Funções específicas para a aba ANTECEDENTES

        // Mostrar/ocultar campos condicionais da aba Antecedentes
        function toggleCamposCondicionaisAntecedentes() {
            // Problemas durante gestação
            const problemasGestacaoSim = document.getElementById('problemas_sim');
            const detalhesProblemasGestacao = document.getElementById('detalhes-problemas-gestacao');
            
            if (problemasGestacaoSim && detalhesProblemasGestacao) {
                detalhesProblemasGestacao.style.display = problemasGestacaoSim.checked ? 'block' : 'none';
            }
            
            // Problemas pós-nascimento
            const posNascimentoSim = document.getElementById('pos_nascimento_sim');
            const detalhesPosNascimento = document.getElementById('detalhes-problemas-pos-nascimento');
            
            if (posNascimentoSim && detalhesPosNascimento) {
                detalhesPosNascimento.style.display = posNascimentoSim.checked ? 'block' : 'none';
            }
            
            // Complicações graves
            const complicacoesSim = document.getElementById('complicacoes_sim');
            const detalhesComplicacoes = document.getElementById('detalhes-complicacoes');
            
            if (complicacoesSim && detalhesComplicacoes) {
                detalhesComplicacoes.style.display = complicacoesSim.checked ? 'block' : 'none';
            }
            
            // Convulsões
            const convulsoesSim = document.getElementById('convulsoes_sim');
            const detalhesConvulsoes = document.getElementById('detalhes-convulsoes');
            
            if (convulsoesSim && detalhesConvulsoes) {
                detalhesConvulsoes.style.display = convulsoesSim.checked ? 'block' : 'none';
            }
            
            // Alergias
            const alergiasRestricoes = document.getElementById('alergias_restricoes');
            const detalhesAlergias = document.getElementById('detalhes-alergias');
            
            if (alergiasRestricoes && detalhesAlergias) {
                detalhesAlergias.style.display = alergiasRestricoes.checked ? 'block' : 'none';
            }
            
            // Outros históricos familiares
            const familiaOutros = document.getElementById('familia_outros');
            const detalhesOutrosFamilia = document.getElementById('detalhes-outros-familia');
            
            if (familiaOutros && detalhesOutrosFamilia) {
                detalhesOutrosFamilia.style.display = familiaOutros.checked ? 'block' : 'none';
            }
            
            // Diferença no crescimento
            const crescimentoNao = document.getElementById('crescimento_nao');
            const detalhesCrescimento = document.getElementById('detalhes-diferenca-crescimento');
            
            if (crescimentoNao && detalhesCrescimento) {
                detalhesCrescimento.style.display = crescimentoNao.checked ? 'block' : 'none';
            }
        }

        // Função para mostrar/ocultar campos de idade dos marcos
        function toggleCamposIdadeMarcos() {
            const idadeMarcos = document.querySelector('.idade-marcos');
            const idadeMarcosLinguagem = document.querySelector('.idade-marcos-linguagem');
            
            // Verificar marcos motores
            const sentouSim = document.querySelector('input[name="sentou_sem_apoio"][value="sim"]');
            const engatinhouSim = document.querySelector('input[name="engatinhou"][value="sim"]');
            const andouSim = document.querySelector('input[name="comecou_andar"][value="sim"]');
            const controleSim = document.querySelector('input[name="controle_esfincteres"][value="sim"]');
            
            // Verificar marcos de linguagem
            const balbuciouSim = document.querySelector('input[name="balbuciou"][value="sim"]');
            const palavrasSim = document.querySelector('input[name="primeiras_palavras"][value="sim"]');
            const frasesSim = document.querySelector('input[name="montou_frases"][value="sim"]');
            
            // Mostrar campos de idade se algum marco motor foi alcançado
            if (sentouSim?.checked || engatinhouSim?.checked || andouSim?.checked || controleSim?.checked) {
                idadeMarcos.style.display = 'grid';
            } else {
                idadeMarcos.style.display = 'none';
            }
            
            // Mostrar campos de idade se algum marco de linguagem foi alcançado
            if (balbuciouSim?.checked || palavrasSim?.checked || frasesSim?.checked) {
                idadeMarcosLinguagem.style.display = 'grid';
            } else {
                idadeMarcosLinguagem.style.display = 'none';
            }
        }

        // Adicionar event listeners para os campos condicionais
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners para radio buttons da aba queixa
            const radiosEncaminhado = document.querySelectorAll('input[name="encaminhado"]');
            radiosEncaminhado.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionais);
            });
            
            // Event listener para checkbox "Outro" em sinais
            const sinalOutroCheckbox = document.getElementById('sinal_outro');
            if (sinalOutroCheckbox) {
                sinalOutroCheckbox.addEventListener('change', toggleCamposCondicionais);
            }
            
            // Event listeners para radio buttons de tratamento anterior
            const radiosTratamento = document.querySelectorAll('input[name="tratamento_anterior"]');
            radiosTratamento.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionais);
            });
            
            // Event listeners para radio buttons da aba antecedentes
            const radiosGestacao = document.querySelectorAll('input[name="problemas_gestacao"]');
            radiosGestacao.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            const radiosPosNascimento = document.querySelectorAll('input[name="problemas_pos_nascimento"]');
            radiosPosNascimento.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            const radiosComplicacoes = document.querySelectorAll('input[name="complicacoes_graves"]');
            radiosComplicacoes.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            const radiosConvulsoes = document.querySelectorAll('input[name="convulsoes"]');
            radiosConvulsoes.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            const radiosAlergias = document.querySelectorAll('input[name="alergias"]');
            radiosAlergias.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            const radiosCrescimento = document.querySelectorAll('input[name="crescimento_similar"]');
            radiosCrescimento.forEach(radio => {
                radio.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            });
            
            // Checkbox "outros" em histórico familiar
            const familiaOutrosCheckbox = document.getElementById('familia_outros');
            if (familiaOutrosCheckbox) {
                familiaOutrosCheckbox.addEventListener('change', toggleCamposCondicionaisAntecedentes);
            }
            
            // Adicionar event listeners para todos os radio buttons de desenvolvimento
            const desenvolvimentoRadios = document.querySelectorAll('#form-desenvolvimento input[type="radio"]');
            desenvolvimentoRadios.forEach(radio => {
                radio.addEventListener('change', toggleCamposIdadeMarcos);
            });
            
            // Contadores de caracteres para observação
            const observacoesTextarea = document.getElementById('observacoes_clinicas');
            const internaTextarea = document.getElementById('observacao_interna');
            const observacoesCounter = document.getElementById('observacoes_counter');
            const internaCounter = document.getElementById('interna_counter');
            
            if (observacoesTextarea && observacoesCounter) {
                // Atualizar contador inicial
                observacoesCounter.textContent = observacoesTextarea.value.length;
                
                observacoesTextarea.addEventListener('input', function() {
                    observacoesCounter.textContent = this.value.length;
                    observacoesCounter.style.color = this.value.length > 500 ? '#ef4444' : '#3b82f6';
                });
            }
            
            if (internaTextarea && internaCounter) {
                // Atualizar contador inicial
                internaCounter.textContent = internaTextarea.value.length;
                
                internaTextarea.addEventListener('input', function() {
                    internaCounter.textContent = this.value.length;
                    internaCounter.style.color = this.value.length > 300 ? '#ef4444' : '#3b82f6';
                });
            }
            
            // Contador de caracteres para o textarea de hábitos alimentares
            const habitosTextarea = document.getElementById('habitos_alimentares');
            const charCount = document.querySelector('.char-count');
            
            if (habitosTextarea && charCount) {
                // Atualizar contador inicial
                charCount.textContent = habitosTextarea.value.length;
                
                // Atualizar contador ao digitar
                habitosTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    charCount.textContent = length;
                    
                    // Mudar cor se passar de 400 caracteres
                    if (length > 400) {
                        charCount.style.color = '#ef4444';
                    } else {
                        charCount.style.color = '#3b82f6';
                    }
                });
            }
            
            // Controle de colapso da seção de anexos
            const collapsibleHeader = document.querySelector('.collapsible-header');
            const collapsibleSection = document.querySelector('.collapsible-section');
            
            if (collapsibleHeader && collapsibleSection) {
                collapsibleHeader.addEventListener('click', function() {
                    collapsibleSection.classList.toggle('active');
                });
            }
            
            // Controle de upload de anexos
            const btnAddAttachment = document.getElementById('btn-add-attachment');
            const fileUpload = document.getElementById('file-upload');
            const attachmentsList = document.getElementById('attachments-list');
            
            if (btnAddAttachment && fileUpload) {
                btnAddAttachment.addEventListener('click', function() {
                    fileUpload.click();
                });
                
                fileUpload.addEventListener('change', function(e) {
                    handleFileUpload(e.target.files);
                });
            }
            
            // Drag and drop para upload
            if (attachmentsList) {
                attachmentsList.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '#f0f9ff';
                    this.style.borderColor = '#3b82f6';
                });
                
                attachmentsList.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '';
                    this.style.borderColor = '';
                });
                
                attachmentsList.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '';
                    this.style.borderColor = '';
                    
                    if (e.dataTransfer.files.length) {
                        handleFileUpload(e.dataTransfer.files);
                    }
                });
            }
            
            // Inicializar estado dos campos condicionais
            setTimeout(() => {
                toggleCamposCondicionais();
                toggleCamposCondicionaisAntecedentes();
                toggleCamposIdadeMarcos();
            }, 100);
        });

        // Função para salvar dados da aba atual
        function saveCurrentTabData() {
            const activeTab = document.querySelector('.tab.active span').textContent.trim();
            
            switch(activeTab) {
                case 'Queixa':
                    // Coletar dados do novo formulário
                    const formDataQueixa = {
                        motivo_principal: document.getElementById('motivo_principal')?.value || '',
                        quem_identificou: Array.from(document.querySelectorAll('input[name="quem_identificou[]"]:checked')).map(cb => cb.value),
                        encaminhado: document.querySelector('input[name="encaminhado"]:checked')?.value || '',
                        nome_profissional: document.getElementById('nome_profissional')?.value || '',
                        especialidade_profissional: document.getElementById('especialidade_profissional')?.value || '',
                        possui_relatorio: document.querySelector('input[name="possui_relatorio"]:checked')?.value || '',
                        sinais_observados: Array.from(document.querySelectorAll('input[name="sinais_observados[]"]:checked')).map(cb => cb.value),
                        sinal_outro_descricao: document.getElementById('sinal_outro_descricao')?.value || '',
                        descricao_sinais: document.getElementById('descricao_sinais')?.value || '',
                        expectativas_familia: document.getElementById('expectativas_familia')?.value || '',
                        tratamento_anterior: document.querySelector('input[name="tratamento_anterior"]:checked')?.value || '',
                        tipo_tratamento: document.getElementById('tipo_tratamento')?.value || '',
                        local_tratamento: document.getElementById('local_tratamento')?.value || '',
                        periodo_tratamento: document.getElementById('periodo_tratamento')?.value || ''
                    };
                    
                    formData.queixa = formDataQueixa;
                    console.log('Dados da queixa salvos (novo modelo):', formDataQueixa);
                    break;
                    
                case 'Antecedente':
                    // Coletar dados do formulário de antecedentes
                    const formDataAntecedentes = {
                        // Gestação e Nascimento
                        duracao_gestacao: document.querySelector('input[name="duracao_gestacao"]:checked')?.value || '',
                        tipo_parto: document.querySelector('input[name="tipo_parto"]:checked')?.value || '',
                        problemas_gestacao: document.querySelector('input[name="problemas_gestacao"]:checked')?.value || '',
                        quais_problemas_gestacao: document.getElementById('quais_problemas_gestacao')?.value || '',
                        problemas_pos_nascimento: document.querySelector('input[name="problemas_pos_nascimento"]:checked')?.value || '',
                        quais_problemas_pos_nascimento: document.getElementById('quais_problemas_pos_nascimento')?.value || '',
                        
                        // História Médica Pessoal
                        complicacoes_graves: document.querySelector('input[name="complicacoes_graves"]:checked')?.value || '',
                        quais_complicacoes: document.getElementById('quais_complicacoes')?.value || '',
                        hospitalizacoes: document.querySelector('input[name="hospitalizacoes"]:checked')?.value || '',
                        motivo_hospitalizacao: document.getElementById('motivo_hospitalizacao')?.value || '',
                        idade_hospitalizacao: document.getElementById('idade_hospitalizacao')?.value || '',
                        convulsoes: document.querySelector('input[name="convulsoes"]:checked')?.value || '',
                        detalhes_convulsoes: document.getElementById('detalhes_convulsoes')?.value || '',
                        alergias: document.querySelector('input[name="alergias"]:checked')?.value || '',
                        quais_alergias: document.getElementById('quais_alergias')?.value || '',
                        
                        // História Médica Familiar
                        historico_familiar: Array.from(document.querySelectorAll('input[name="historico_familiar[]"]:checked')).map(cb => cb.value),
                        familia_outros_descricao: document.getElementById('familia_outros_descricao')?.value || '',
                        
                        // Crescimento
                        crescimento_similar: document.querySelector('input[name="crescimento_similar"]:checked')?.value || '',
                        diferenca_crescimento: document.getElementById('diferenca_crescimento')?.value || ''
                    };
                    
                    formData.antecedentes = formDataAntecedentes;
                    console.log('Dados dos antecedentes salvos:', formDataAntecedentes);
                    break;
                    
                case 'Desenvolvimento':
                    const formDataDesenvolvimento = {
                        // Motor
                        sentou_sem_apoio: document.querySelector('input[name="sentou_sem_apoio"]:checked')?.value || '',
                        idade_sentou: document.getElementById('idade_sentou')?.value || '',
                        engatinhou: document.querySelector('input[name="engatinhou"]:checked')?.value || '',
                        idade_engatinhou: document.getElementById('idade_engatinhou')?.value || '',
                        comecou_andar: document.querySelector('input[name="comecou_andar"]:checked')?.value || '',
                        idade_andou: document.getElementById('idade_andou')?.value || '',
                        controle_esfincteres: document.querySelector('input[name="controle_esfincteres"]:checked')?.value || '',
                        idade_controle: document.getElementById('idade_controle')?.value || '',
                        
                        // Fala e Linguagem
                        balbuciou: document.querySelector('input[name="balbuciou"]:checked')?.value || '',
                        idade_balbucio: document.getElementById('idade_balbucio')?.value || '',
                        primeiras_palavras: document.querySelector('input[name="primeiras_palavras"]:checked')?.value || '',
                        idade_primeiras_palavras: document.getElementById('idade_primeiras_palavras')?.value || '',
                        montou_frases: document.querySelector('input[name="montou_frases"]:checked')?.value || '',
                        idade_frases: document.getElementById('idade_frases')?.value || '',
                        frases_completas: document.querySelector('input[name="frases_completas"]:checked')?.value || '',
                        
                        // Social
                        sorriu_interacoes: document.querySelector('input[name="sorriu_interacoes"]:checked')?.value || '',
                        interage_criancas: document.querySelector('input[name="interage_criancas"]:checked')?.value || '',
                        descricao_interacao: document.getElementById('descricao_interacao')?.value || '',
                        
                        // Alimentação
                        introducao_alimentar: document.querySelector('input[name="introducao_alimentar"]:checked')?.value || '',
                        alimenta_sozinho: document.querySelector('input[name="alimenta_sozinho"]:checked')?.value || '',
                        preferencias_alimentares: document.getElementById('preferencias_alimentares')?.value || ''
                    };
                    
                    formData.desenvolvimento = formDataDesenvolvimento;
                    console.log('Dados do desenvolvimento salvos:', formDataDesenvolvimento);
                    break;
                    
                case 'Observação Clínica':
                    const formDataObservacao = {
                        // Observações clínicas
                        observacoes_clinicas: document.getElementById('observacoes_clinicas')?.value || '',
                        
                        // Classificação de prioridade
                        classificacao_prioridade: document.getElementById('classificacao_prioridade')?.value || '',
                        
                        // Observação interna
                        observacao_interna: document.getElementById('observacao_interna')?.value || '',
                        
                        // Anexos (lista de nomes dos arquivos)
                        anexos: Array.from(document.querySelectorAll('.attachment-name')).map(el => el.textContent)
                    };
                    
                    formData.observacao = formDataObservacao;
                    console.log('Dados da observação clínica salvos:', formDataObservacao);
                    break;
            }
        }

        // Função para carregar dados salvos na aba
        function loadTabData(tabName) {
            switch(tabName) {
                case 'Queixa':
                    if (formData.queixa && Object.keys(formData.queixa).length > 0) {
                        // Preencher campos com dados salvos
                        const dados = formData.queixa;
                        
                        // Campos de texto
                        if (dados.motivo_principal) document.getElementById('motivo_principal').value = dados.motivo_principal;
                        if (dados.nome_profissional) document.getElementById('nome_profissional').value = dados.nome_profissional;
                        if (dados.especialidade_profissional) document.getElementById('especialidade_profissional').value = dados.especialidade_profissional;
                        if (dados.sinal_outro_descricao) document.getElementById('sinal_outro_descricao').value = dados.sinal_outro_descricao;
                        if (dados.descricao_sinais) document.getElementById('descricao_sinais').value = dados.descricao_sinais;
                        if (dados.expectativas_familia) document.getElementById('expectativas_familia').value = dados.expectativas_familia;
                        if (dados.tipo_tratamento) document.getElementById('tipo_tratamento').value = dados.tipo_tratamento;
                        if (dados.local_tratamento) document.getElementById('local_tratamento').value = dados.local_tratamento;
                        if (dados.periodo_tratamento) document.getElementById('periodo_tratamento').value = dados.periodo_tratamento;
                        
                        // Checkboxes - quem identificou
                        if (dados.quem_identificou && Array.isArray(dados.quem_identificou)) {
                            document.querySelectorAll('input[name="quem_identificou[]"]').forEach(cb => {
                                cb.checked = dados.quem_identificou.includes(cb.value);
                            });
                        }
                        
                        // Checkboxes - sinais observados
                        if (dados.sinais_observados && Array.isArray(dados.sinais_observados)) {
                            document.querySelectorAll('input[name="sinais_observados[]"]').forEach(cb => {
                                cb.checked = dados.sinais_observados.includes(cb.value);
                            });
                        }
                        
                        // Radio buttons
                        if (dados.encaminhado) {
                            document.querySelector(`input[name="encaminhado"][value="${dados.encaminhado}"]`).checked = true;
                        }
                        if (dados.possui_relatorio) {
                            document.querySelector(`input[name="possui_relatorio"][value="${dados.possui_relatorio}"]`).checked = true;
                        }
                        if (dados.tratamento_anterior) {
                            document.querySelector(`input[name="tratamento_anterior"][value="${dados.tratamento_anterior}"]`).checked = true;
                        }
                        
                        // Atualizar campos condicionais
                        setTimeout(toggleCamposCondicionais, 50);
                    }
                    break;
                    
                case 'Antecedente':
                    if (formData.antecedentes && Object.keys(formData.antecedentes).length > 0) {
                        const dados = formData.antecedentes;
                        
                        // Preencher radio buttons
                        if (dados.duracao_gestacao) {
                            document.querySelector(`input[name="duracao_gestacao"][value="${dados.duracao_gestacao}"]`).checked = true;
                        }
                        if (dados.tipo_parto) {
                            document.querySelector(`input[name="tipo_parto"][value="${dados.tipo_parto}"]`).checked = true;
                        }
                        if (dados.problemas_gestacao) {
                            document.querySelector(`input[name="problemas_gestacao"][value="${dados.problemas_gestacao}"]`).checked = true;
                        }
                        if (dados.problemas_pos_nascimento) {
                            document.querySelector(`input[name="problemas_pos_nascimento"][value="${dados.problemas_pos_nascimento}"]`).checked = true;
                        }
                        if (dados.complicacoes_graves) {
                            document.querySelector(`input[name="complicacoes_graves"][value="${dados.complicacoes_graves}"]`).checked = true;
                        }
                        if (dados.hospitalizacoes) {
                            document.querySelector(`input[name="hospitalizacoes"][value="${dados.hospitalizacoes}"]`).checked = true;
                        }
                        if (dados.convulsoes) {
                            document.querySelector(`input[name="convulsoes"][value="${dados.convulsoes}"]`).checked = true;
                        }
                        if (dados.alergias) {
                            document.querySelector(`input[name="alergias"][value="${dados.alergias}"]`).checked = true;
                        }
                        if (dados.crescimento_similar) {
                            document.querySelector(`input[name="crescimento_similar"][value="${dados.crescimento_similar}"]`).checked = true;
                        }
                        
                        // Preencher checkboxes (histórico familiar)
                        if (dados.historico_familiar && Array.isArray(dados.historico_familiar)) {
                            document.querySelectorAll('input[name="historico_familiar[]"]').forEach(cb => {
                                cb.checked = dados.historico_familiar.includes(cb.value);
                            });
                        }
                        
                        // Preencher campos de texto
                        if (dados.quais_problemas_gestacao) document.getElementById('quais_problemas_gestacao').value = dados.quais_problemas_gestacao;
                        if (dados.quais_problemas_pos_nascimento) document.getElementById('quais_problemas_pos_nascimento').value = dados.quais_problemas_pos_nascimento;
                        if (dados.quais_complicacoes) document.getElementById('quais_complicacoes').value = dados.quais_complicacoes;
                        if (dados.motivo_hospitalizacao) document.getElementById('motivo_hospitalizacao').value = dados.motivo_hospitalizacao;
                        if (dados.idade_hospitalizacao) document.getElementById('idade_hospitalizacao').value = dados.idade_hospitalizacao;
                        if (dados.detalhes_convulsoes) document.getElementById('detalhes_convulsoes').value = dados.detalhes_convulsoes;
                        if (dados.quais_alergias) document.getElementById('quais_alergias').value = dados.quais_alergias;
                        if (dados.familia_outros_descricao) document.getElementById('familia_outros_descricao').value = dados.familia_outros_descricao;
                        if (dados.diferenca_crescimento) document.getElementById('diferenca_crescimento').value = dados.diferenca_crescimento;
                        
                        // Atualizar campos condicionais
                        setTimeout(toggleCamposCondicionaisAntecedentes, 50);
                    }
                    break;
                    
                case 'Desenvolvimento':
                    if (formData.desenvolvimento && Object.keys(formData.desenvolvimento).length > 0) {
                        const dados = formData.desenvolvimento;
                        
                        // Preencher campos motor
                        if (dados.sentou_sem_apoio) document.querySelector(`input[name="sentou_sem_apoio"][value="${dados.sentou_sem_apoio}"]`).checked = true;
                        if (dados.idade_sentou) document.getElementById('idade_sentou').value = dados.idade_sentou;
                        if (dados.engatinhou) document.querySelector(`input[name="engatinhou"][value="${dados.engatinhou}"]`).checked = true;
                        if (dados.idade_engatinhou) document.getElementById('idade_engatinhou').value = dados.idade_engatinhou;
                        if (dados.comecou_andar) document.querySelector(`input[name="comecou_andar"][value="${dados.comecou_andar}"]`).checked = true;
                        if (dados.idade_andou) document.getElementById('idade_andou').value = dados.idade_andou;
                        if (dados.controle_esfincteres) document.querySelector(`input[name="controle_esfincteres"][value="${dados.controle_esfincteres}"]`).checked = true;
                        if (dados.idade_controle) document.getElementById('idade_controle').value = dados.idade_controle;
                        
                        // Preencher campos fala e linguagem
                        if (dados.balbuciou) document.querySelector(`input[name="balbuciou"][value="${dados.balbuciou}"]`).checked = true;
                        if (dados.idade_balbucio) document.getElementById('idade_balbucio').value = dados.idade_balbucio;
                        if (dados.primeiras_palavras) document.querySelector(`input[name="primeiras_palavras"][value="${dados.primeiras_palavras}"]`).checked = true;
                        if (dados.idade_primeiras_palavras) document.getElementById('idade_primeiras_palavras').value = dados.idade_primeiras_palavras;
                        if (dados.montou_frases) document.querySelector(`input[name="montou_frases"][value="${dados.montou_frases}"]`).checked = true;
                        if (dados.idade_frases) document.getElementById('idade_frases').value = dados.idade_frases;
                        if (dados.frases_completas) document.querySelector(`input[name="frases_completas"][value="${dados.frases_completas}"]`).checked = true;
                        
                        // Preencher campos social
                        if (dados.sorriu_interacoes) document.querySelector(`input[name="sorriu_interacoes"][value="${dados.sorriu_interacoes}"]`).checked = true;
                        if (dados.interage_criancas) document.querySelector(`input[name="interage_criancas"][value="${dados.interage_criancas}"]`).checked = true;
                        if (dados.descricao_interacao) document.getElementById('descricao_interacao').value = dados.descricao_interacao;
                        
                        // Preencher campos alimentação
                        if (dados.introducao_alimentar) document.querySelector(`input[name="introducao_alimentar"][value="${dados.introducao_alimentar}"]`).checked = true;
                        if (dados.alimenta_sozinho) document.querySelector(`input[name="alimenta_sozinho"][value="${dados.alimenta_sozinho}"]`).checked = true;
                        if (dados.preferencias_alimentares) document.getElementById('preferencias_alimentares').value = dados.preferencias_alimentares;
                        
                        // Atualizar campos condicionais
                        setTimeout(toggleCamposIdadeMarcos, 50);
                    }
                    break;
                    
                case 'Observação Clínica':
                    if (formData.observacao && Object.keys(formData.observacao).length > 0) {
                        const dados = formData.observacao;
                        
                        // Preencher campos de texto
                        if (dados.observacoes_clinicas) {
                            document.getElementById('observacoes_clinicas').value = dados.observacoes_clinicas;
                            document.getElementById('observacoes_counter').textContent = dados.observacoes_clinicas.length;
                        }
                        
                        if (dados.classificacao_prioridade) {
                            document.getElementById('classificacao_prioridade').value = dados.classificacao_prioridade;
                        }
                        
                        if (dados.observacao_interna) {
                            document.getElementById('observacao_interna').value = dados.observacao_interna;
                            document.getElementById('interna_counter').textContent = dados.observacao_interna.length;
                        }
                        
                        // Preencher anexos (simulação)
                        // Em um sistema real, você buscaria os arquivos do servidor
                    }
                    break;
            }
        }

        // Função para lidar com upload de arquivos
        function handleFileUpload(files) {
            const attachmentsList = document.getElementById('attachments-list');
            const noAttachments = attachmentsList.querySelector('.no-attachments');
            
            if (!attachmentsList) return;
            
            // Remover mensagem "nenhum anexo" se existir
            if (noAttachments) {
                noAttachments.style.display = 'none';
            }
            
            // Limitar a 10 arquivos
            const existingFiles = attachmentsList.querySelectorAll('.attachment-item').length;
            const remainingSlots = 10 - existingFiles;
            
            if (files.length > remainingSlots) {
                alert(`Você só pode adicionar mais ${remainingSlots} arquivo(s). Limite máximo: 10 arquivos.`);
                files = Array.from(files).slice(0, remainingSlots);
            }
            
            // Processar cada arquivo
            Array.from(files).forEach(file => {
                // Validar tipo de arquivo
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert(`Arquivo "${file.name}" não suportado. Use PDF, JPG ou PNG.`);
                    return;
                }
                
                // Validar tamanho (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`Arquivo "${file.name}" muito grande. Tamanho máximo: 5MB.`);
                    return;
                }
                
                // Criar elemento de anexo
                const attachmentItem = createAttachmentElement(file);
                attachmentsList.appendChild(attachmentItem);
            });
            
            // Resetar input de arquivo
            const fileUpload = document.getElementById('file-upload');
            if (fileUpload) {
                fileUpload.value = '';
            }
        }

        // Função para criar elemento de anexo
        function createAttachmentElement(file) {
            const div = document.createElement('div');
            div.className = 'attachment-item';
            
            // Formatar tamanho do arquivo
            const fileSize = formatFileSize(file.size);
            
            // Determinar ícone baseado no tipo
            let iconClass = 'fa-file';
            if (file.type === 'application/pdf') {
                iconClass = 'fa-file-pdf';
            } else if (file.type.startsWith('image/')) {
                iconClass = 'fa-file-image';
            }
            
            div.innerHTML = `
                <div class="attachment-info">
                    <i class="fas ${iconClass} attachment-icon"></i>
                    <div>
                        <span class="attachment-name">${file.name}</span>
                        <span class="attachment-size">(${fileSize})</span>
                    </div>
                </div>
                <div class="attachment-actions">
                    <button type="button" class="btn-remove-attachment" title="Remover arquivo">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            // Adicionar evento para remover anexo
            const removeBtn = div.querySelector('.btn-remove-attachment');
            removeBtn.addEventListener('click', function() {
                div.remove();
                
                // Mostrar mensagem "nenhum anexo" se lista estiver vazia
                const attachmentsList = document.getElementById('attachments-list');
                if (attachmentsList && attachmentsList.querySelectorAll('.attachment-item').length === 0) {
                    const noAttachments = attachmentsList.querySelector('.no-attachments');
                    if (noAttachments) {
                        noAttachments.style.display = 'block';
                    }
                }
            });
            
            return div;
        }

        // Função para formatar tamanho do arquivo
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Função principal para salvar como paciente ativo
        function salvarComoPacienteAtivo() {
            if (confirm('Deseja salvar este pré-cadastro como paciente ativo?')) {
                // Salvar dados de todas as abas primeiro
                saveCurrentTabData();
                
                console.log('Enviando dados para salvar como paciente ativo:', formData);
                
                // Aqui você faria a requisição AJAX para salvar no backend
                // Exemplo de requisição AJAX:
                /*
                fetch('salvar_paciente.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ...formData,
                        status: 'ativo',
                        data_cadastro: new Date().toISOString()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Paciente salvo como ativo com sucesso!', 'success');
                        setTimeout(() => {
                            window.location.href = 'painel_adm_pacientes.php';
                        }, 2000);
                    } else {
                        showNotification('Erro ao salvar paciente: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao conectar com o servidor', 'error');
                });
                */
                
                // Simulação do sucesso
                showNotification('Paciente salvo como ativo com sucesso!', 'success');
                
                // Limpar localStorage após salvar
                localStorage.removeItem('preCadastroDados');
                
                // Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = 'painel_adm_pacientes.php';
                }, 2000);
            }
        }

        // Função para salvar como paciente pendente
        function salvarComoPacientePendente() {
            if (confirm('Deseja salvar este pré-cadastro como paciente pendente?')) {
                // Salvar dados de todas as abas primeiro
                saveCurrentTabData();
                
                console.log('Enviando dados para salvar como paciente pendente:', formData);
                
                // Aqui você faria a requisição AJAX para salvar no backend
                // Similar à função acima, mas com status: 'pendente'
                
                // Simulação do sucesso
                showNotification('Paciente salvo como pendente com sucesso!', 'success');
                
                // Limpar localStorage após salvar
                localStorage.removeItem('preCadastroDados');
                
                // Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = 'painel_pacientes_pendentes.php';
                }, 2000);
            }
        }

        // Função para cancelar desenvolvimento
        function cancelarDesenvolvimento() {
            if (confirm('Tem certeza que deseja cancelar o preenchimento do desenvolvimento?')) {
                const radios = document.querySelectorAll('#form-desenvolvimento input[type="radio"]');
                radios.forEach(radio => radio.checked = false);
                
                const inputs = document.querySelectorAll('#form-desenvolvimento input[type="text"], #form-desenvolvimento textarea');
                inputs.forEach(input => input.value = '');
                
                formData.desenvolvimento = {};
                
                const charCount = document.querySelector('#form-desenvolvimento .char-count');
                if (charCount) {
                    charCount.textContent = '0';
                    charCount.style.color = '#3b82f6';
                }
                
                showNotification('Desenvolvimento cancelado!', 'info');
            }
        }

        // Função para cancelar observação
        function cancelarObservacao() {
            if (confirm('Tem certeza que deseja cancelar as observações clínicas?')) {
                document.getElementById('observacoes_clinicas').value = '';
                document.getElementById('classificacao_prioridade').value = '';
                document.getElementById('observacao_interna').value = '';
                
                const attachmentsList = document.getElementById('attachments-list');
                if (attachmentsList) {
                    attachmentsList.innerHTML = `
                        <div class="no-attachments">
                            <i class="fas fa-paperclip"></i>
                            <p>Nenhum anexo adicionado</p>
                        </div>
                    `;
                }
                
                document.getElementById('observacoes_counter').textContent = '0';
                document.getElementById('interna_counter').textContent = '0';
                
                formData.observacao = {};
                
                showNotification('Observações clínicas canceladas!', 'info');
            }
        }

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
                if (file.size > 2 * 1024 * 1024) {
                    alert('A foto deve ter no máximo 2MB.');
                    fileInput.value = '';
                    return;
                }
                
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Formato de imagem não suportado. Use JPG, PNG, GIF ou WebP.');
                    fileInput.value = '';
                    return;
                }
                
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
                const file = fileInput.files[0];
                
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                
                setTimeout(() => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        patientPhoto.src = e.target.result;
                        photoPreview.src = e.target.result;
                        
                        console.log('Foto enviada para o servidor:', file.name);
                        
                        showNotification('Foto atualizada com sucesso!', 'success');
                        
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = 'Enviar Foto';
                        closeModal();
                    };
                    reader.readAsDataURL(file);
                }, 1500);
            } else {
                alert('Por favor, selecione uma foto.');
            }
        });

        // Notificação
        function showNotification(message, type = 'success') {
            document.querySelectorAll('.notification').forEach(n => n.remove());
            
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

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

        // Salvar automaticamente ao sair da aba
        window.addEventListener('beforeunload', (e) => {
            saveCurrentTabData();
            localStorage.setItem('preCadastroDados', JSON.stringify(formData));
        });

        // Tentar recuperar dados do localStorage
        window.addEventListener('load', () => {
            const savedData = localStorage.getItem('preCadastroDados');
            if (savedData) {
                try {
                    const parsedData = JSON.parse(savedData);
                    Object.assign(formData, parsedData);
                    console.log('Dados recuperados do localStorage:', formData);
                } catch (e) {
                    console.error('Erro ao recuperar dados do localStorage:', e);
                }
            }
        });
    </script>
</body>
</html>