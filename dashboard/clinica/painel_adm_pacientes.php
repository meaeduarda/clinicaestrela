<?php
// painel_adm_pacientes.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Função para calcular idade a partir da data de nascimento
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) {
        return 'Idade n/d';
    }
    
    try {
        // Tenta converter a data para objeto DateTime
        // Primeiro tenta o formato Y-m-d (que está no JSON)
        $dataNasc = DateTime::createFromFormat('Y-m-d', $dataNascimento);
        
        // Se não funcionar, tenta outros formatos comuns
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
        return 'Data inválida';
    }
}

// --- LÓGICA DE DADOS REAIS ---
$arquivoAtivos = __DIR__ . '/../../dashboard/dados/ativo-cad.json';
$pacientesAtivos = [];
$totalAtivos = 0;

if (file_exists($arquivoAtivos)) {
    $conteudo = file_get_contents($arquivoAtivos);
    $dados = json_decode($conteudo, true);
    
    if (is_array($dados)) {
        $pacientesAtivos = $dados;
        $totalAtivos = count($pacientesAtivos);
    }
}

// --- LEITURA DE PACIENTES PENDENTES ---
$arquivoPendentes = __DIR__ . '/../../dashboard/dados/pre-cad.json';
$totalPendentes = 0;

if (file_exists($arquivoPendentes)) {
    $conteudoPendentes = file_get_contents($arquivoPendentes);
    $dadosPendentes = json_decode($conteudoPendentes, true);
    
    if (is_array($dadosPendentes)) {
        $totalPendentes = count($dadosPendentes);
    }
}

// --- LEITURA DE VISITAS AGENDADAS NÃO CONFIRMADAS ---
$arquivoVisitas = __DIR__ . '/../../dashboard/dados/dados_visita_agendamento.json';
$totalVisitasNaoConfirmadas = 0;
$agendamentos = [];

if (file_exists($arquivoVisitas)) {
    $conteudoVisitas = file_get_contents($arquivoVisitas);
    if (!empty($conteudoVisitas)) {
        $agendamentos = json_decode($conteudoVisitas, true);
        if (is_array($agendamentos)) {
            // Contar apenas agendamentos não confirmados
            foreach ($agendamentos as $agendamento) {
                if (isset($agendamento['confirmado']) && $agendamento['confirmado'] === false) {
                    $totalVisitasNaoConfirmadas++;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title> Painel Administrativo Clinica Estrela </title>

    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_paciente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .action-buttons a.btn-action.edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background-color: #3b82f6;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .action-buttons a.btn-action.edit:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
        
        /* Modal de confirmação */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .confirmation-modal.active {
            display: flex;
        }
        .confirmation-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .confirmation-modal-header {
            padding: 20px;
            background: #fef2f2;
            border-bottom: 1px solid #fecaca;
        }
        .confirmation-modal-header h3 {
            margin: 0;
            color: #dc2626;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .confirmation-modal-body {
            padding: 20px;
        }
        .confirmation-modal-body p {
            margin: 0 0 15px;
            color: #4b5563;
            line-height: 1.5;
        }
        .patient-info-confirm {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .patient-info-confirm strong {
            color: #1e293b;
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        .patient-info-confirm span {
            color: #64748b;
            font-size: 0.9rem;
        }
        .confirmation-modal-footer {
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-confirm, .btn-cancel, .btn-pdf {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-confirm {
            background: #dc2626;
            color: white;
        }
        .btn-confirm:hover {
            background: #b91c1c;
        }
        .btn-cancel {
            background: #6b7280;
            color: white;
        }
        .btn-cancel:hover {
            background: #4b5563;
        }
        .btn-pdf {
            background: #10b981;
            color: white;
        }
        .btn-pdf:hover {
            background: #059669;
        }
        .btn-pdf:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        
        /* Notificações */
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
            z-index: 10001;
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

        /* Modal de Visualização de Paciente */
        .view-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .view-modal.active {
            display: flex;
        }
        .view-modal-content {
            background: white;
            border-radius: 12px;
            width: 95%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .view-modal-header {
            padding: 20px 25px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }
        .view-modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .view-modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .view-modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .view-modal-body {
            padding: 25px;
        }
        .view-modal-footer {
            padding: 15px 25px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .view-modal-footer-left {
            display: flex;
            gap: 10px;
        }
        .view-modal-footer-right {
            display: flex;
            gap: 10px;
        }

        /* Layout dos detalhes do paciente */
        .patient-details-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .patient-photo-section {
            text-align: center;
        }
        .patient-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e7eb;
            margin-bottom: 15px;
        }
        .patient-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .patient-info-item {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #3b82f6;
        }
        .patient-info-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.875rem;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .patient-info-value {
            color: #1f2937;
            font-size: 1rem;
        }
        .patient-section-title {
            font-size: 1.25rem;
            color: #1e40af;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #dbeafe;
            grid-column: 1 / -1;
        }

        /* Botões de navegação */
        .nav-section-btn {
            padding: 8px 16px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .nav-section-btn:hover {
            background-color: #e2e8f0;
            transform: translateY(-2px);
        }

        .nav-section-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        @media (max-width: 768px) {
            .nav-section-btn {
                padding: 6px 12px;
                font-size: 0.75rem;
                flex: 1;
                min-width: 120px;
            }
            .view-modal-footer {
                flex-direction: column;
            }
            .view-modal-footer-left,
            .view-modal-footer-right {
                width: 100%;
                justify-content: center;
            }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .patient-details-container {
                grid-template-columns: 1fr;
            }
            .patient-info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Badges para status */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-inactive {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Loading para PDF */
        .pdf-loading {
            display: none;
            align-items: center;
            gap: 8px;
            color: #10b981;
        }
        .pdf-loading.show {
            display: flex;
        }
        .pdf-loading .fa-spinner {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* KPI card amarelo com link */
        .kpi-card.yellow {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .kpi-card.yellow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2);
        }
        .kpi-card.yellow .kpi-content a {
            color: inherit;
            text-decoration: none;
        }
        .kpi-card.yellow .kpi-content a:hover {
            text-decoration: underline;
        }
        
        /* Estilo para busca rápida */
        .search-info {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 8px;
            display: none;
        }
        .search-info.show {
            display: block;
        }
        .no-results {
            text-align: center;
            padding: 30px;
            color: #64748b;
            font-style: italic;
        }
        
        /* Adicionar estilo para badge de visitas */
        .visitas-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .icon-btn.with-badge {
            position: relative;
            text-decoration: none;
            color: inherit;
        }
        
        .icon-btn.with-badge:hover {
            color: inherit;
        }
    </style>
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
                <h1>Clinica Estrela</h1>
                <div class="mobile-close">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <nav class="menu">
                <ul>
                    <li class="active"><a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a></li>
                    
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
                                            
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                        <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                        <li><a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                        <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <?php endif; ?>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                </ul>
            </nav>

            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nomeLogado); ?>&background=random" alt="<?php echo htmlspecialchars($nomeLogado); ?>">
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($nomeLogado); ?></h3>
                    <p><?php echo htmlspecialchars(ucfirst($perfilLogado)); ?></p>
                </div>
                <a href="logout.php" title="Sair" style="color: #ef4444; margin-left: 10px; text-decoration: none;">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <div class="main-top desktop-only">
                <h2><i class="fas fa-user-check"></i> Pacientes Ativos</h2>
                <div class="top-icons">
                    <a href="visita_agendamento.php" class="icon-btn with-badge" title="Visitas Agendadas não confirmadas">
                        <i class="fas fa-calendar-check"></i>
                        <?php if ($totalVisitasNaoConfirmadas > 0): ?>
                            <span class="visitas-badge"><?php echo $totalVisitasNaoConfirmadas; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="icon-btn">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>

             <div class="kpi-cards">
                <div class="kpi-card blue">
                    <div class="kpi-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 id="total-ativos-count"><?php echo $totalAtivos; ?></h3>
                        <p>Pacientes Ativos</p>
                    </div>
                </div>
                
                <div class="kpi-card green">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="kpi-content">
                        <h3>0</h3> <p>Sessões Hoje</p>
                    </div>
                </div>
                
                <div class="kpi-card yellow" onclick="window.location.href='http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php';" title="Clique para ver pacientes pendentes">
                    <div class="kpi-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="kpi-content">
                        <h3><?php echo $totalPendentes; ?></h3>
                        <p><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php" style="text-decoration: none; color: inherit;">Pacientes Pendentes</a></p>
                    </div>
                </div>
                
                <div class="kpi-card pink">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="kpi-content">
                        <h3><?php echo $totalVisitasNaoConfirmadas; ?></h3>
                        <p>Visitas Pendentes</p>
                    </div>
                </div>
            </div>

            <div class="patients-search-container">
                <div class="patients-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscar-paciente" placeholder="Buscar paciente por nome, telefone ou idade...">
                </div>
                <div class="patients-actions">
                    <a href="painel_adm_preca.php" class="btn-add-patient" style="text-decoration:none;">
                        <i class="fas fa-plus"></i> Novo Paciente
                    </a>
                    <button class="btn-export">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                </div>
            </div>
            
            <div class="search-info" id="search-info">
                <!-- Informações da busca serão inseridas aqui via JavaScript -->
            </div>

            <div class="patients-table-container">
                <div class="table-header">
                    <h3>Tabela de Pacientes</h3>
                    <div class="table-actions">                       
                        <button class="btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="patients-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Idade</th>
                                <th>Contato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="pacientes-tbody">
                            <?php if ($totalAtivos === 0): ?>
                                <tr id="linha-sem-pacientes">
                                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">
                                        Nenhum paciente ativo encontrado. Vá em "Pacientes Pendentes" para ativar um cadastro.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pacientesAtivos as $index => $p): ?>
                                    <?php 
                                        // Calcular idade a partir da data de nascimento
                                        $dataNascimento = isset($p['nascimento']) ? $p['nascimento'] : (isset($p['data_nascimento']) ? $p['data_nascimento'] : '');
                                        $idade = calcularIdade($dataNascimento);
                                        
                                        // Preparar dados para busca
                                        $nome = $p['nome_completo'] ?? 'Paciente';
                                        $telefone = $p['telefone'] ?? '-';
                                        $dataCadastro = isset($p['data_ativacao']) ? date('d/m/Y', strtotime($p['data_ativacao'])) : '-';
                                    ?>
                                    <tr id="paciente-row-<?php echo $index; ?>" class="paciente-row" 
                                        data-nome="<?php echo htmlspecialchars(strtolower($nome)); ?>"
                                        data-telefone="<?php echo htmlspecialchars(strtolower($telefone)); ?>"
                                        data-idade="<?php echo htmlspecialchars(strtolower($idade)); ?>"
                                        data-cadastro="<?php echo htmlspecialchars($dataCadastro); ?>">
                                        <td>
                                            <div class="patient-cell">
                                                <div class="patient-avatar-small">
                                                    <?php 
                                                        $foto = $p['foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($nome).'&background=random';
                                                    ?>
                                                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="<?php echo htmlspecialchars($nome); ?>">
                                                </div>
                                                <div class="patient-name">
                                                    <strong><?php echo htmlspecialchars($nome); ?></strong>
                                                    <span class="patient-age">Cadastrado em: <?php echo $dataCadastro; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="diagnostic-tags">
                                                <span class="diagnostic-badge" style="background-color: #d1fae5; color: #065f46;">Ativo</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="next-session">
                                                <span class="session-date"><?php echo htmlspecialchars($idade); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($telefone); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action view" 
                                                        title="Visualizar" 
                                                        data-index="<?php echo $index; ?>" 
                                                        data-nome="<?php echo htmlspecialchars($nome); ?>"
                                                        data-origem="ativo">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <!-- Botão Editar -->
                                                <a href="painel_adm_preca.php?index=<?php echo $index; ?>&origem=ativo" 
                                                   class="btn-action edit" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Botão Excluir (modificado) -->
                                                <button class="btn-action delete" 
                                                        title="Excluir" 
                                                        data-index="<?php echo $index; ?>" 
                                                        data-nome="<?php echo htmlspecialchars($nome); ?>"
                                                        data-origem="ativo">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <div class="pagination-info">
                        <span class="current-page">Total: <span id="total-pacientes"><?php echo $totalAtivos; ?></span></span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h3>
            </div>
            <div class="confirmation-modal-body">
                <p>Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.</p>
                <div class="patient-info-confirm">
                    <strong id="patient-name-confirm"></strong>
                    <span>ID: <span id="patient-id-confirm"></span></span>
                </div>
                <p><small><i class="fas fa-info-circle"></i> Todos os dados do paciente serão removidos permanentemente.</small></p>
            </div>
            <div class="confirmation-modal-footer">
                <button class="btn-cancel" id="cancelDelete">Cancelar</button>
                <button class="btn-confirm" id="confirmDelete">Excluir Permanentemente</button>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização de Paciente -->
    <div class="view-modal" id="viewPatientModal">
        <div class="view-modal-content">
            <div class="view-modal-header">
                <h3><i class="fas fa-user-injured"></i> Detalhes do Paciente</h3>
                <button class="view-modal-close" id="closeViewModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="view-modal-body" id="viewPatientBody">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
            <div class="view-modal-footer">
                <div class="view-modal-footer-left">
                    <div class="pdf-loading" id="pdfLoading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Gerando PDF...</span>
                    </div>
                </div>
                <div class="view-modal-footer-right">
                    <button class="btn-pdf" id="gerarPdfBtn" disabled>
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </button>
                    <button class="btn-cancel" id="closeViewModalBtn">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Dados globais
    let pacienteParaExcluir = {
        index: null,
        nome: '',
        origem: ''
    };
    
    let pacienteVisualizado = {
        index: null,
        origem: null
    };
    
    // Variáveis para controle da busca
    let todosPacientes = <?php echo json_encode($pacientesAtivos); ?>;
    let termoBusca = '';
    let resultadosVisiveis = <?php echo $totalAtivos; ?>;

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
            
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(event.target) && 
                    !mobileMenuToggle.contains(event.target) && 
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }

        const kpiCards = document.querySelectorAll('.kpi-card');
        kpiCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Efeitos visuais na tabela
        const tableRows = document.querySelectorAll('.patients-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8fafc';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Adicionar confirmação antes de editar
        const editButtons = document.querySelectorAll('.btn-action.edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Deseja editar este paciente?')) {
                    e.preventDefault();
                }
            });
        });

        // Configurar botões de exclusão
        const deleteButtons = document.querySelectorAll('.btn-action.delete');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const nome = this.getAttribute('data-nome');
                const origem = this.getAttribute('data-origem');
                
                // Preencher modal de confirmação
                pacienteParaExcluir = { index, nome, origem };
                document.getElementById('patient-name-confirm').textContent = nome;
                document.getElementById('patient-id-confirm').textContent = `#${index}`;
                
                // Mostrar modal
                document.getElementById('confirmationModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Botão cancelar exclusão
        document.getElementById('cancelDelete').addEventListener('click', function() {
            closeConfirmationModal();
        });

        // Botão confirmar exclusão
        document.getElementById('confirmDelete').addEventListener('click', function() {
            excluirPaciente();
        });

        // Fechar modal ao clicar fora
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmationModal();
            }
        });

        // Botão gerar PDF
        document.getElementById('gerarPdfBtn').addEventListener('click', function() {
            gerarPDF(pacienteVisualizado.index, pacienteVisualizado.origem);
        });

        // Configurar campo de busca
        const campoBusca = document.getElementById('buscar-paciente');
        if (campoBusca) {
            campoBusca.addEventListener('input', function() {
                termoBusca = this.value.trim().toLowerCase();
                buscarPacientes(termoBusca);
            });
            
            // Adicionar atalho de teclado (Ctrl+F para focar no campo de busca)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    campoBusca.focus();
                }
                
                // Esc para limpar busca
                if (e.key === 'Escape' && campoBusca.value) {
                    campoBusca.value = '';
                    termoBusca = '';
                    buscarPacientes('');
                    campoBusca.focus();
                }
            });
        }

        function adjustMenuForMobile() {
            const menuItems = document.querySelectorAll('.menu li a span');
            if (window.innerWidth <= 767) {
                menuItems.forEach(span => {
                    span.style.fontSize = '11px';
                    span.style.lineHeight = '1.2';
                });
            }
        }

        adjustMenuForMobile();
        window.addEventListener('resize', adjustMenuForMobile);
    });

    // Função de busca de pacientes
    function buscarPacientes(termo) {
        const linhasPacientes = document.querySelectorAll('.paciente-row');
        const linhaSemPacientes = document.getElementById('linha-sem-pacientes');
        const infoBusca = document.getElementById('search-info');
        const totalPacientesElement = document.getElementById('total-pacientes');
        const totalAtivosCount = document.getElementById('total-ativos-count');
        
        let resultados = 0;
        let temResultados = false;
        
        // Se não há termo de busca, mostrar todos os pacientes
        if (!termo) {
            linhasPacientes.forEach(linha => {
                linha.style.display = '';
            });
            
            // Mostrar/ocultar linha "sem pacientes" conforme necessário
            if (linhaSemPacientes) {
                linhaSemPacientes.style.display = linhasPacientes.length === 0 ? '' : 'none';
            }
            
            // Atualizar contadores
            resultados = linhasPacientes.length;
            totalPacientesElement.textContent = resultados;
            totalAtivosCount.textContent = resultados;
            
            // Ocultar informações de busca
            infoBusca.classList.remove('show');
            infoBusca.innerHTML = '';
            
            return;
        }
        
        // Filtrar pacientes
        linhasPacientes.forEach(linha => {
            const nome = linha.getAttribute('data-nome') || '';
            const telefone = linha.getAttribute('data-telefone') || '';
            const idade = linha.getAttribute('data-idade') || '';
            
            // Verificar se o termo está em algum dos campos
            const corresponde = 
                nome.includes(termo) || 
                telefone.includes(termo) || 
                idade.includes(termo);
            
            if (corresponde) {
                linha.style.display = '';
                resultados++;
                temResultados = true;
                
                // Destacar o termo encontrado
                destacarTermo(linha, termo);
            } else {
                linha.style.display = 'none';
            }
        });
        
        // Atualizar contadores
        totalPacientesElement.textContent = resultados;
        totalAtivosCount.textContent = resultados;
        
        // Mostrar informações da busca
        if (termo) {
            infoBusca.innerHTML = `
                <i class="fas fa-search"></i> 
                ${resultados} ${resultados === 1 ? 'paciente encontrado' : 'pacientes encontrados'} 
                para "${termo}"
                ${resultados === 0 ? '<span class="no-results">(Nenhum resultado encontrado)</span>' : ''}
            `;
            infoBusca.classList.add('show');
        } else {
            infoBusca.classList.remove('show');
        }
        
        // Se não há resultados e há linha "sem pacientes", ocultá-la
        if (linhaSemPacientes) {
            linhaSemPacientes.style.display = 'none';
        }
        
        // Se não há resultados, mostrar mensagem
        if (!temResultados && linhasPacientes.length > 0) {
            // Verificar se já existe uma linha de "sem resultados"
            let linhaSemResultados = document.getElementById('linha-sem-resultados');
            if (!linhaSemResultados) {
                const tbody = document.getElementById('pacientes-tbody');
                linhaSemResultados = document.createElement('tr');
                linhaSemResultados.id = 'linha-sem-resultados';
                linhaSemResultados.innerHTML = `
                    <td colspan="5" class="no-results">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        <h4>Nenhum paciente encontrado</h4>
                        <p>Não encontramos resultados para "<strong>${termo}</strong>"</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">
                            <i class="fas fa-lightbulb"></i> Tente buscar por nome, telefone ou idade
                        </p>
                    </td>
                `;
                tbody.appendChild(linhaSemResultados);
            }
        } else {
            // Remover linha de "sem resultados" se existir
            const linhaSemResultados = document.getElementById('linha-sem-resultados');
            if (linhaSemResultados) {
                linhaSemResultados.remove();
            }
        }
    }
    
    // Função para destacar o termo encontrado nas células da linha
    function destacarTermo(linha, termo) {
        // Obter todas as células da linha (exceto a última que tem ações)
        const celulas = linha.querySelectorAll('td:not(:last-child)');
        
        celulas.forEach(celula => {
            const textoOriginal = celula.textContent || celula.innerText;
            const regex = new RegExp(`(${termo.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            
            // Verificar se o termo está no texto (ignorando case)
            if (regex.test(textoOriginal)) {
                // Restaurar o texto original primeiro
                celula.innerHTML = textoOriginal;
                
                // Aplicar o destaque
                celula.innerHTML = celula.innerHTML.replace(
                    regex, 
                    '<mark style="background-color: #fef3c7; padding: 2px 4px; border-radius: 4px;">$1</mark>'
                );
            }
        });
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.remove('active');
        document.body.style.overflow = '';
        pacienteParaExcluir = { index: null, nome: '', origem: '' };
    }

    function excluirPaciente() {
        const { index, nome, origem } = pacienteParaExcluir;
        
        if (!index) return;
        
        // Desabilitar botões durante a exclusão
        const confirmBtn = document.getElementById('confirmDelete');
        const cancelBtn = document.getElementById('cancelDelete');
        const originalConfirmText = confirmBtn.textContent;
        
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        
        // Enviar requisição para excluir
        fetch('excluir_paciente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                index: parseInt(index),
                origem: origem
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Remover linha da tabela
                const row = document.getElementById(`paciente-row-${index}`);
                if (row) {
                    row.style.backgroundColor = '#fee2e2';
                    setTimeout(() => {
                        row.remove();
                        
                        // Atualizar contador
                        const totalElement = document.getElementById('total-pacientes');
                        const totalAtivosCount = document.getElementById('total-ativos-count');
                        if (totalElement && totalAtivosCount) {
                            const currentTotal = parseInt(totalElement.textContent);
                            totalElement.textContent = currentTotal - 1;
                            totalAtivosCount.textContent = currentTotal - 1;
                        }
                        
                        // Atualizar informações de busca se houver termo ativo
                        const campoBusca = document.getElementById('buscar-paciente');
                        if (campoBusca && campoBusca.value) {
                            buscarPacientes(campoBusca.value.trim().toLowerCase());
                        }
                        
                        // Mostrar notificação de sucesso
                        mostrarNotificacao('sucesso', data.message || 'Paciente excluído com sucesso!');
                    }, 300);
                }
                
                // Verificar se não há mais pacientes
                const tbody = document.getElementById('pacientes-tbody');
                if (tbody) {
                    const linhasPacientes = tbody.querySelectorAll('.paciente-row');
                    if (linhasPacientes.length === 0) {
                        // Adicionar linha de "nenhum paciente"
                        if (!document.getElementById('linha-sem-pacientes')) {
                            const noPatientsRow = document.createElement('tr');
                            noPatientsRow.id = 'linha-sem-pacientes';
                            noPatientsRow.innerHTML = `
                                <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">
                                    Nenhum paciente ativo encontrado. Vá em "Pacientes Pendentes" para ativar um cadastro.
                                </td>
                            `;
                            tbody.appendChild(noPatientsRow);
                        }
                        
                        // Remover linha de busca se existir
                        const linhaBuscaSemResultados = document.getElementById('linha-sem-resultados');
                        if (linhaBuscaSemResultados) {
                            linhaBuscaSemResultados.remove();
                        }
                        
                        // Ocultar informações de busca
                        const infoBusca = document.getElementById('search-info');
                        if (infoBusca) {
                            infoBusca.classList.remove('show');
                        }
                    }
                }
            } else {
                mostrarNotificacao('erro', data.message || 'Erro ao excluir paciente.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarNotificacao('erro', 'Erro de comunicação com o servidor.');
        })
        .finally(() => {
            closeConfirmationModal();
            confirmBtn.innerHTML = originalConfirmText;
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
        });
    }

    function mostrarNotificacao(tipo, mensagem) {
        const div = document.createElement('div');
        let classeTipo = 'notification-info';
        let icone = 'fa-info-circle';
        
        if (tipo === 'sucesso') { 
            classeTipo = 'notification-success'; 
            icone = 'fa-check-circle'; 
        } else if (tipo === 'erro') { 
            classeTipo = 'notification-error'; 
            icone = 'fa-exclamation-circle'; 
        }
        
        div.className = `notification show ${classeTipo}`;
        div.innerHTML = `<i class="fas ${icone}"></i><span>${mensagem}</span>`;
        document.body.appendChild(div);
        
        setTimeout(() => { 
            div.classList.remove('show'); 
            setTimeout(() => div.remove(), 300); 
        }, 4000);
    }

    // Modal de Visualização de Paciente
    const viewPatientModal = document.getElementById('viewPatientModal');
    const closeViewModal = document.getElementById('closeViewModal');
    const closeViewModalBtn = document.getElementById('closeViewModalBtn');

    // Configurar botões de visualização
    const viewButtons = document.querySelectorAll('.btn-action.view');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            const nome = this.getAttribute('data-nome');
            const origem = this.getAttribute('data-origem');
            
            if (index && origem) {
                carregarDetalhesPaciente(index, origem, nome);
            }
        });
    });

    // Fechar modal de visualização
    function fecharModalVisualizacao() {
        viewPatientModal.classList.remove('active');
        document.body.style.overflow = '';
        // Desabilitar botão PDF ao fechar
        document.getElementById('gerarPdfBtn').disabled = true;
        pacienteVisualizado = { index: null, origem: null };
    }

    closeViewModal.addEventListener('click', fecharModalVisualizacao);
    closeViewModalBtn.addEventListener('click', fecharModalVisualizacao);

    // Fechar modal ao clicar fora
    viewPatientModal.addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalVisualizacao();
        }
    });

    // Função para carregar detalhes do paciente
    function carregarDetalhesPaciente(index, origem, nome) {
        // Desabilitar botão PDF enquanto carrega
        document.getElementById('gerarPdfBtn').disabled = true;
        
        // Armazenar dados do paciente visualizado
        pacienteVisualizado = { index, origem };
        
        // Mostrar loading
        document.getElementById('viewPatientBody').innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: #3b82f6;"></i>
                <p style="margin-top: 15px; color: #6b7280;">Carregando dados do paciente...</p>
            </div>
        `;
        
        // Abrir modal
        viewPatientModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Fazer requisição para buscar dados
        fetch(`detalhes_paciente.php?index=${index}&origem=${origem}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    exibirDetalhesPaciente(data.paciente);
                    // Habilitar botão PDF após carregar dados
                    document.getElementById('gerarPdfBtn').disabled = false;
                } else {
                    document.getElementById('viewPatientBody').innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-exclamation-triangle fa-2x" style="color: #ef4444;"></i>
                            <p style="margin-top: 15px; color: #6b7280;">Erro: ${data.message}</p>
                        </div>
                    `;
                    document.getElementById('gerarPdfBtn').disabled = true;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('viewPatientBody').innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-exclamation-circle fa-2x" style="color: #ef4444;"></i>
                        <p style="margin-top: 15px; color: #6b7280;">Erro ao carregar dados do paciente.</p>
                    </div>
                `;
                document.getElementById('gerarPdfBtn').disabled = true;
            });
    }

    // Função para gerar PDF
    function gerarPDF(index, origem) {
        if (!index || !origem) {
            mostrarNotificacao('erro', 'Dados do paciente não encontrados.');
            return;
        }
        
        // Mostrar loading
        document.getElementById('pdfLoading').classList.add('show');
        const pdfBtn = document.getElementById('gerarPdfBtn');
        const originalText = pdfBtn.innerHTML;
        pdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando...';
        pdfBtn.disabled = true;
        
        // Criar formulário temporário para enviar dados
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'gerar_pdf_paciente.php';
        form.target = '_blank'; // Abrir em nova aba
        
        // Adicionar campos
        const inputIndex = document.createElement('input');
        inputIndex.type = 'hidden';
        inputIndex.name = 'index';
        inputIndex.value = index;
        form.appendChild(inputIndex);
        
        const inputOrigem = document.createElement('input');
        inputOrigem.type = 'hidden';
        inputOrigem.name = 'origem';
        inputOrigem.value = origem;
        form.appendChild(inputOrigem);
        
        // Adicionar formulário ao body e submeter
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Restaurar botão após alguns segundos (caso o PDF não abra automaticamente)
        setTimeout(() => {
            document.getElementById('pdfLoading').classList.remove('show');
            pdfBtn.innerHTML = originalText;
            pdfBtn.disabled = false;
        }, 3000);
    }

    // Função para exibir os detalhes do paciente no modal    
    function exibirDetalhesPaciente(paciente) {
        // Formatar dados para exibição
        const formatarData = (dataString) => {
            if (!dataString) return '-';
            try {
                const data = new Date(dataString);
                return data.toLocaleDateString('pt-BR');
            } catch {
                return dataString;
            }
        };
        
        const formatarLista = (lista) => {
            if (!lista) return '-';
            if (Array.isArray(lista)) {
                // Se for array vazio
                if (lista.length === 0) return '-';
                
                // Se for array de objetos vazios (como anexos)
                if (lista.length > 0 && Array.isArray(lista[0]) && lista[0].length === 0) return 'Nenhum';
                
                // Converter para string legível
                const items = lista.map(item => {
                    if (typeof item === 'object') {
                        return JSON.stringify(item);
                    }
                    return item;
                });
                return items.join(', ') || '-';
            }
            return lista;
        };
        
        const formatarBooleano = (valor) => {
            if (valor === 'sim' || valor === 'Sim' || valor === true || valor === 'true') return 'Sim';
            if (valor === 'nao' || valor === 'Não' || valor === false || valor === 'false') return 'Não';
            return valor || '-';
        };

        // Montar HTML dos detalhes
        let html = `
            <div class="patient-details-container">
                <div class="patient-photo-section">
                    <img src="${paciente.foto || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(paciente.nome_completo || 'Paciente') + '&background=random'}" 
                         alt="${paciente.nome_completo || 'Paciente'}" 
                         class="patient-photo">
                    <h3 style="margin: 10px 0 5px 0;">${paciente.nome_completo || 'Paciente sem nome'}</h3>
                    <div class="status-badge ${paciente.status === 'Ativo' ? 'status-active' : 'status-pending'}">
                        ${paciente.status || 'Ativo'}
                    </div>
                </div>
                
                <!-- Botões para navegação rápida -->
                <div style="grid-column: 1 / -1; margin: 20px 0 10px 0;">
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                        <button class="nav-section-btn active" data-section="dados-pessoais">Dados Pessoais</button>
                        <button class="nav-section-btn" data-section="contato">Contato</button>
                        <button class="nav-section-btn" data-section="convenio">Convênio</button>
                        <button class="nav-section-btn" data-section="identificacao">Identificação</button>
                        <button class="nav-section-btn" data-section="historico">Histórico</button>
                        <button class="nav-section-btn" data-section="gestacao">Gestação/Nascimento</button>
                        <button class="nav-section-btn" data-section="desenvolvimento">Desenvolvimento</button>
                        <button class="nav-section-btn" data-section="sistema">Sistema</button>
                    </div>
                </div>
        `;
        
        // ================= DADOS PESSOAIS =================
        html += `<div class="patient-section-title" id="dados-pessoais">Dados Pessoais</div>`;
        html += `<div class="patient-info-grid">`;
        
        // Mapear todos os campos do paciente
        const camposExibidos = new Set();
        
        // Função para adicionar campo se existir
        const adicionarCampo = (label, valor, valorFormatado = null) => {
            if (valor !== undefined && valor !== null && valor !== '') {
                camposExibidos.add(label.toLowerCase());
                html += `
                    <div class="patient-info-item">
                        <div class="patient-info-label">${label}</div>
                        <div class="patient-info-value">${valorFormatado !== null ? valorFormatado : valor}</div>
                    </div>
                `;
            }
        };
        
        // Dados básicos
        adicionarCampo('Nome Completo', paciente.nome_completo);
        adicionarCampo('Sexo', paciente.sexo);
        adicionarCampo('Data de Nascimento', paciente.nascimento, formatarData(paciente.nascimento) + ' (' + (paciente.idade_calculada || 'Idade n/d') + ')');
        adicionarCampo('Nome da Mãe', paciente.nome_mae);
        adicionarCampo('Nome do Pai', paciente.nome_pai);
        adicionarCampo('Responsável', paciente.responsavel);
        adicionarCampo('Data de Nascimento (alternativa)', paciente.data_nascimento, formatarData(paciente.data_nascimento));
        
        html += `</div>`;
        
        // ================= CONTATO =================
        html += `<div class="patient-section-title" id="contato">Contato</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Telefone', paciente.telefone);
        adicionarCampo('E-mail', paciente.email);
        adicionarCampo('CPF do Responsável', paciente.cpf_responsavel);
        
        html += `</div>`;
        
        // ================= CONVÊNIO =================
        html += `<div class="patient-section-title" id="convenio">Convênio</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Convênio', paciente.convenio);
        adicionarCampo('Telefone do Convênio', paciente.telefone_convenio);
        adicionarCampo('Número da Carteirinha', paciente.numero_carteirinha);
        
        html += `</div>`;
        
        // ================= IDENTIFICAÇÃO/ESCOLARIDADE =================
        html += `<div class="patient-section-title" id="identificacao">Identificação e Escolaridade</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Escolaridade', paciente.escolaridade);
        adicionarCampo('CPF do Paciente', paciente.cpf_paciente);
        adicionarCampo('RG do Paciente', paciente.rg_paciente);
        adicionarCampo('Escola', paciente.escola);
        
        html += `</div>`;
        
        // ================= IDENTIFICAÇÃO DA NECESSIDADE =================
        html += `<div class="patient-section-title" id="identificacao">Identificação da Necessidade</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Motivo Principal', paciente.motivo_principal);
        adicionarCampo('Quem Identificou', formatarLista(paciente.quem_identificou));
        adicionarCampo('Encaminhado', formatarBooleano(paciente.encaminhado));
        adicionarCampo('Nome do Profissional', paciente.nome_profissional);
        adicionarCampo('Especialidade', paciente.especialidade_profissional);
        adicionarCampo('Possui Relatório', formatarBooleano(paciente.possui_relatorio));
        adicionarCampo('Sinais Observados', formatarLista(paciente.sinais_observados));
        adicionarCampo('Descrição de Outros Sinais', paciente.sinal_outro_descricao);
        adicionarCampo('Descrição dos Sinais', paciente.descricao_sinais);
        adicionarCampo('Expectativas da Família', paciente.expectativas_familia);
        
        html += `</div>`;
        
        // ================= HISTÓRICO DE TRATAMENTO =================
        html += `<div class="patient-section-title" id="historico">Histórico de Tratamento</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Tratamento Anterior', formatarBooleano(paciente.tratamento_anterior));
        adicionarCampo('Tipo de Tratamento', paciente.tipo_tratamento);
        adicionarCampo('Local do Tratamento', paciente.local_tratamento);
        adicionarCampo('Período do Tratamento', paciente.periodo_tratamento);
        
        html += `</div>`;
        
        // ================= GESTAÇÃO E NASCIMENTO =================
        html += `<div class="patient-section-title" id="gestacao">Gestação e Nascimento</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Duração da Gestação', paciente.duracao_gestacao);
        adicionarCampo('Tipo de Parto', paciente.tipo_parto);
        adicionarCampo('Problemas na Gestação', formatarBooleano(paciente.problemas_gestacao));
        adicionarCampo('Quais Problemas na Gestação', paciente.quais_problemas_gestacao);
        adicionarCampo('Problemas Pós-Nascimento', formatarBooleano(paciente.problemas_pos_nascimento));
        adicionarCampo('Quais Problemas Pós-Nascimento', paciente.quais_problemas_pos_nascimento);
        
        html += `</div>`;
        
        // ================= HISTÓRICO MÉDICO =================
        html += `<div class="patient-section-title" id="historico">Histórico Médico</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Complicações Graves', formatarBooleano(paciente.complicacoes_graves));
        adicionarCampo('Quais Complicações', paciente.quais_complicacoes);
        adicionarCampo('Hospitalizações', paciente.hospitalizacoes);
        adicionarCampo('Motivo da Hospitalização', paciente.motivo_hospitalizacao);
        adicionarCampo('Idade da Hospitalização', paciente.idade_hospitalizacao);
        adicionarCampo('Convulsões', formatarBooleano(paciente.convulsoes));
        adicionarCampo('Detalhes das Convulsões', paciente.detalhes_convulsoes);
        adicionarCampo('Alergias', paciente.alergias);
        adicionarCampo('Quais Alergias', paciente.quais_alergias);
        adicionarCampo('Histórico Familiar', formatarLista(paciente.historico_familiar));
        adicionarCampo('Outros Históricos Familiares', paciente.familia_outros_descricao);
        
        html += `</div>`;
        
        // ================= DESENVOLVIMENTO =================
        html += `<div class="patient-section-title" id="desenvolvimento">Desenvolvimento</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Crescimento Similar', formatarBooleano(paciente.crescimento_similar));
        adicionarCampo('Diferença no Crescimento', paciente.diferenca_crescimento);
        adicionarCampo('Sentou sem Apoio', formatarBooleano(paciente.sentou_sem_apoio));
        adicionarCampo('Idade que Sentou', paciente.idade_sentou);
        adicionarCampo('Engatinhou', formatarBooleano(paciente.engatinhou));
        adicionarCampo('Idade que Engatinhou', paciente.idade_engatinhou);
        adicionarCampo('Começou a Andar', formatarBooleano(paciente.comecou_andar));
        adicionarCampo('Idade que Andou', paciente.idade_andou);
        adicionarCampo('Controle de Esfíncteres', formatarBooleano(paciente.controle_esfincteres));
        adicionarCampo('Idade do Controle', paciente.idade_controle);
        adicionarCampo('Balbuciou', formatarBooleano(paciente.balbuciou));
        adicionarCampo('Idade do Balbucio', paciente.idade_balbucio);
        adicionarCampo('Primeiras Palavras', formatarBooleano(paciente.primeiras_palavras));
        adicionarCampo('Idade das Primeiras Palavras', paciente.idade_primeiras_palavras);
        adicionarCampo('Montou Frases', formatarBooleano(paciente.montou_frases));
        adicionarCampo('Idade das Frases', paciente.idade_frases);
        adicionarCampo('Frases Completas', formatarBooleano(paciente.frases_completas));
        adicionarCampo('Sorriu em Interações', formatarBooleano(paciente.sorriu_interacoes));
        adicionarCampo('Interage com Crianças', formatarBooleano(paciente.interage_criancas));
        
        html += `</div>`;
        
        // ================= ALIMENTAÇÃO =================
        html += `<div class="patient-section-title">Alimentação</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Introdução Alimentar', formatarBooleano(paciente.introducao_alimentar));
        adicionarCampo('Alimenta Sozinho', formatarBooleano(paciente.alimenta_sozinho));
        adicionarCampo('Hábitos Alimentares', paciente.habitos_alimentares);
        
        html += `</div>`;
        
        // ================= OBSERVAÇÕES =================
        html += `<div class="patient-section-title">Observações</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Observações Clínicas', paciente.observacoes_clinicas);
        
        // Verificar anexos
        if (paciente.anexos && paciente.anexos.length > 0) {
            const temAnexos = paciente.anexos.some(anexo => anexo && anexo.length > 0);
            if (temAnexos) {
                adicionarCampo('Anexos', 'Sim (' + paciente.anexos.length + ' arquivos)');
            } else {
                adicionarCampo('Anexos', 'Nenhum');
            }
        } else {
            adicionarCampo('Anexos', 'Nenhum');
        }
        
        html += `</div>`;
        
        // ================= SISTEMA =================
        html += `<div class="patient-section-title" id="sistema">Informações do Sistema</div>`;
        html += `<div class="patient-info-grid">`;
        
        adicionarCampo('Status do Paciente', paciente.status_paciente);
        adicionarCampo('Data de Registro', paciente.data_registro_formatada || formatarData(paciente.data_registro));
        adicionarCampo('Status', paciente.status);
        adicionarCampo('Data de Ativação', paciente.data_ativacao_formatada || formatarData(paciente.data_ativacao));
        adicionarCampo('Última Atualização', formatarData(paciente.data_atualizacao));
        
        html += `</div>`;
        
        // ================= CAMPOS NÃO MAPEADOS =================
        // Capturar todos os campos que não foram exibidos
        const todosCampos = Object.keys(paciente);
        const camposNaoExibidos = todosCampos.filter(campo => 
            !camposExibidos.has(campo.toLowerCase()) && 
            !['foto', 'idade_calculada', 'data_ativacao_formatada', 'data_registro_formatada'].includes(campo)
        );
        
        if (camposNaoExibidos.length > 0) {
            html += `<div class="patient-section-title">Outras Informações</div>`;
            html += `<div class="patient-info-grid">`;
            
            camposNaoExibidos.forEach(campo => {
                if (paciente[campo] !== undefined && paciente[campo] !== null && paciente[campo] !== '') {
                    html += `
                        <div class="patient-info-item">
                            <div class="patient-info-label">${campo.replace(/_/g, ' ')}</div>
                            <div class="patient-info-value">${formatarLista(paciente[campo])}</div>
                        </div>
                    `;
                }
            });
            
            html += `</div>`;
        }
        
        html += `</div>`; // Fechar container
        
        // Inserir no modal
        document.getElementById('viewPatientBody').innerHTML = html;
        
        // Adicionar funcionalidade aos botões de navegação
        const navButtons = document.querySelectorAll('.nav-section-btn');
        navButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remover classe active de todos
                navButtons.forEach(b => b.classList.remove('active'));
                // Adicionar classe active ao clicado
                this.classList.add('active');
                
                // Rolar para a seção correspondente
                const sectionId = this.getAttribute('data-section');
                const sectionElement = document.getElementById(sectionId);
                if (sectionElement) {
                    sectionElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }
    
    // Função para atualizar contador de visitas
    function atualizarContadorVisitas() {
        fetch('atualizar_contador_visitas.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Atualizar o badge do sino
                    const badgeElement = document.querySelector('.visitas-badge');
                    const kpiCardCount = document.querySelector('.kpi-card.pink h3');
                    
                    if (data.count > 0) {
                        if (!badgeElement) {
                            // Criar badge se não existir
                            const iconBtn = document.querySelector('.icon-btn.with-badge');
                            if (iconBtn) {
                                const badge = document.createElement('span');
                                badge.className = 'visitas-badge';
                                badge.textContent = data.count;
                                iconBtn.appendChild(badge);
                            }
                        } else {
                            badgeElement.textContent = data.count;
                        }
                        
                        // Adicionar animação de pulso
                        if (badgeElement) {
                            badgeElement.style.animation = 'pulse 2s infinite';
                        }
                    } else {
                        // Remover badge se count for 0
                        if (badgeElement) {
                            badgeElement.remove();
                        }
                    }
                    
                    // Atualizar KPI card
                    if (kpiCardCount) {
                        kpiCardCount.textContent = data.count;
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar contador de visitas:', error);
            });
    }

    // Atualizar a cada 30 segundos
    setInterval(atualizarContadorVisitas, 30000);

    // Atualizar ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        atualizarContadorVisitas();
    });
    </script>
</body>
</html>