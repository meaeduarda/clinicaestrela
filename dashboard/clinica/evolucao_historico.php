<?php
// evolucao_historico.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'] ?? 'Maria Eduarda';
$perfilLogado = $_SESSION['usuario_perfil'] ?? 'Admin';

// Pega o nome do arquivo atual para o menu ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Dados do paciente
$paciente_id = isset($_GET['paciente_id']) ? $_GET['paciente_id'] : '';
$paciente_nome = isset($_GET['paciente_nome']) ? urldecode($_GET['paciente_nome']) : '';
$responsavel = isset($_GET['responsavel']) ? urldecode($_GET['responsavel']) : '';
$telefone = isset($_GET['telefone']) ? urldecode($_GET['telefone']) : '';

// Filtros
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$terapia = isset($_GET['terapia']) ? $_GET['terapia'] : '';
$terapeuta = isset($_GET['terapeuta']) ? $_GET['terapeuta'] : '';

// Mensagens de feedback
$success_message = isset($_GET['success']) ? $_GET['success'] : '';
$error_message = isset($_GET['error']) ? $_GET['error'] : '';

// Paginação
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$itens_por_pagina = 10;
$offset = ($pagina - 1) * $itens_por_pagina;

// ===== LEITURA DE VISITAS AGENDADAS NÃO CONFIRMADAS =====
$arquivoVisitas = __DIR__ . '/../../dashboard/dados/dados_visita_agendamento.json';
$totalVisitasNaoConfirmadas = 0;

if (file_exists($arquivoVisitas)) {
    $conteudoVisitas = file_get_contents($arquivoVisitas);
    if (!empty($conteudoVisitas)) {
        $agendamentos = json_decode($conteudoVisitas, true);
        if (is_array($agendamentos)) {
            foreach ($agendamentos as $agendamento) {
                if (isset($agendamento['confirmado']) && $agendamento['confirmado'] === false) {
                    $totalVisitasNaoConfirmadas++;
                }
            }
        }
    }
}

// Carregar evoluções do arquivo correto
$caminhoEvolucoes = __DIR__ . '/../../dashboard/dados/evolucao_pacientes.json';
$evolucoes = [];

if (file_exists($caminhoEvolucoes)) {
    $evolucoesJson = file_get_contents($caminhoEvolucoes);
    $evolucoes = json_decode($evolucoesJson, true) ?: [];
}

// Filtrar por paciente
$evolucoes_paciente = array_filter($evolucoes, function($e) use ($paciente_id) {
    return isset($e['paciente_id']) && $e['paciente_id'] === $paciente_id;
});

// Aplicar filtros adicionais
if ($data_inicio) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($data_inicio) {
        return isset($e['data_sessao']) && $e['data_sessao'] >= $data_inicio;
    });
}

if ($data_fim) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($data_fim) {
        return isset($e['data_sessao']) && $e['data_sessao'] <= $data_fim;
    });
}

if ($terapia) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($terapia) {
        return isset($e['terapia']) && $e['terapia'] === $terapia;
    });
}

if ($terapeuta) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($terapeuta) {
        return isset($e['terapeuta']) && stripos($e['terapeuta'], $terapeuta) !== false;
    });
}

// Reindexar e ordenar por data (mais recente primeiro)
$evolucoes_paciente = array_values($evolucoes_paciente);
usort($evolucoes_paciente, function($a, $b) {
    $dataA = isset($a['data_sessao']) ? strtotime($a['data_sessao']) : 0;
    $dataB = isset($b['data_sessao']) ? strtotime($b['data_sessao']) : 0;
    return $dataB - $dataA;
});

$total_evolucoes = count($evolucoes_paciente);
$evolucoes_paginadas = array_slice($evolucoes_paciente, $offset, $itens_por_pagina);
$total_paginas = ceil($total_evolucoes / $itens_por_pagina);

// Lista de terapias para o filtro
$terapias = [
    'ABA' => 'ABA',
    'Fonoaudiologia' => 'Fonoaudiologia',
    'Terapia Ocupacional' => 'Terapia Ocupacional',
    'Psicologia' => 'Psicologia',
    'Psicopedagogia' => 'Psicopedagogia',
    'Fisioterapia' => 'Fisioterapia',
    'Musicoterapia' => 'Musicoterapia',
    'Arteterapia' => 'Arteterapia',
    'Equoterapia' => 'Equoterapia',
    'Integração Sensorial' => 'Integração Sensorial',
    'Neuropsicologia' => 'Neuropsicologia',
    'Nutrição' => 'Nutrição'
];

// Função para formatar data
function formatarData($data) {
    if (empty($data)) return 'Data não informada';
    try {
        return date('d/m/Y', strtotime($data));
    } catch (Exception $e) {
        return $data;
    }
}

// Função para obter cor da terapia
function getTerapiaCor($terapia) {
    $cores = [
        'ABA' => '#3b82f6',
        'Fonoaudiologia' => '#10b981',
        'Terapia Ocupacional' => '#f59e0b',
        'Psicologia' => '#8b5cf6',
        'Psicopedagogia' => '#a855f7',
        'Fisioterapia' => '#ef4444',
        'Musicoterapia' => '#ec4899',
        'Arteterapia' => '#d946ef',
        'Equoterapia' => '#84cc16',
        'Integração Sensorial' => '#06b6d4',
        'Neuropsicologia' => '#6366f1',
        'Nutrição' => '#f97316'
    ];
    return $cores[$terapia] ?? '#64748b';
}

// Função para contar anexos
function contarAnexos($evolucao) {
    return isset($evolucao['anexos']) && is_array($evolucao['anexos']) ? count($evolucao['anexos']) : 0;
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
    <title>Histórico de Evoluções - <?php echo htmlspecialchars($paciente_nome); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_paciente.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_planoterapeutico.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_evolucoes.css">
    
    <!-- CSS específico para o histórico -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/evolucao_historico.css">
    
    <!-- Font Awesome e Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Badge de visitas */
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

        /* Estilo para mensagens de feedback */
        .alert-success, .alert-error {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #10b981;
            color: white;
        }

        .alert-error {
            background: #ef4444;
            color: white;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Badge de anexos */
        .anexos-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        .anexos-badge i {
            color: #3b82f6;
        }

        /* Modal de visualização */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 50px auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .modal-close:hover {
            opacity: 0.8;
        }

        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-section {
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .modal-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-section-title i {
            color: #3b82f6;
        }

        .modal-section-content {
            color: #334155;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .modal-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
        }

        .modal-info-item {
            display: flex;
            flex-direction: column;
        }

        .modal-info-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 4px;
        }

        .modal-info-value {
            font-weight: 600;
            color: #1e293b;
        }

        .anexos-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .anexo-item-modal {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .anexo-item-modal i {
            font-size: 1.2rem;
            color: #3b82f6;
        }

        .anexo-item-modal .anexo-nome {
            flex: 1;
            font-weight: 500;
            color: #1e293b;
        }

        .anexo-item-modal .anexo-link {
            color: #3b82f6;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .anexo-item-modal .anexo-link:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
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
                    <li><a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a></li>
                    <li><a href="painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
                    
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li><a href="painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                        <li><a href="painel_planoterapeutico.php"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                        <li><a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                        <li class="active"><a href="painel_evolucoes.php"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <?php endif; ?>
                    
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                    <li><a href="login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a></li>
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

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-history"></i> Histórico de Evoluções</h2>
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

            <!-- Breadcrumb -->
            <div class="breadcrumb-historico">
                <a href="painel_evolucoes.php">Evoluções</a> <i class="fas fa-chevron-right"></i>
                <span>Histórico de <?php echo htmlspecialchars($paciente_nome); ?></span>
            </div>

            <!-- Mensagens de feedback -->
            <?php if ($success_message): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Card de Identificação do Paciente -->
            <div class="card-identificacao-historico">
                <div class="identificacao-header-historico">
                    <div class="identificacao-avatar-historico">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($paciente_nome); ?>&background=3b82f6&color=fff" alt="<?php echo htmlspecialchars($paciente_nome); ?>">
                    </div>
                    <div class="identificacao-titulo-historico">
                        <h3><?php echo htmlspecialchars($paciente_nome); ?></h3>
                    </div>
                </div>
                
                <div class="identificacao-content-historico">
                    <p><i class="fas fa-user-tie"></i> <span class="label">Responsável:</span> <?php echo htmlspecialchars($responsavel ?: 'Não informado'); ?></p>
                    <p><i class="fas fa-phone"></i> <span class="label">Contato:</span> <?php echo htmlspecialchars($telefone ?: 'Não informado'); ?></p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filters-card">
                <div class="filters-title">
                    <i class="fas fa-filter"></i> Filtrar Evoluções
                </div>
                
                <form method="GET" action="evolucao_historico.php">
                    <input type="hidden" name="paciente_id" value="<?php echo htmlspecialchars($paciente_id); ?>">
                    <input type="hidden" name="paciente_nome" value="<?php echo htmlspecialchars($paciente_nome); ?>">
                    <input type="hidden" name="responsavel" value="<?php echo htmlspecialchars($responsavel); ?>">
                    <input type="hidden" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">
                    
                    <div class="filters-grid">
                        <div class="filter-item">
                            <label><i class="fas fa-calendar"></i> Data Início</label>
                            <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>">
                        </div>
                        
                        <div class="filter-item">
                            <label><i class="fas fa-calendar"></i> Data Fim</label>
                            <input type="date" name="data_fim" value="<?php echo $data_fim; ?>">
                        </div>
                        
                        <div class="filter-item">
                            <label><i class="fas fa-stethoscope"></i> Terapia</label>
                            <select name="terapia">
                                <option value="">Todas</option>
                                <?php foreach ($terapias as $valor => $nome): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $terapia == $valor ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label><i class="fas fa-user-md"></i> Terapeuta</label>
                            <input type="text" name="terapeuta" placeholder="Nome do terapeuta" value="<?php echo htmlspecialchars($terapeuta); ?>">
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn-filter">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="evolucao_historico.php?paciente_id=<?php echo urlencode($paciente_id); ?>&paciente_nome=<?php echo urlencode($paciente_nome); ?>&responsavel=<?php echo urlencode($responsavel); ?>&telefone=<?php echo urlencode($telefone); ?>" class="btn-clear-filter">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de Evoluções -->
            <?php if (empty($evolucoes_paginadas)): ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h3>Nenhuma evolução encontrada</h3>
                    <p>Não há registros de evoluções para este paciente com os filtros selecionados.</p>
                    <a href="painel_evolucoes.php?paciente_id=<?php echo urlencode($paciente_id); ?>" class="btn-primary" style="margin-top: 20px; display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none;">
                        <i class="fas fa-plus"></i> Nova Evolução
                    </a>
                </div>
            <?php else: ?>
                <div class="evolucoes-grid">
                    <?php foreach ($evolucoes_paginadas as $evolucao): ?>
                        <?php 
                            $cor_terapia = getTerapiaCor($evolucao['terapia'] ?? '');
                            $data_formatada = formatarData($evolucao['data_sessao'] ?? '');
                            $resumo = isset($evolucao['descricao']) ? substr($evolucao['descricao'], 0, 100) . (strlen($evolucao['descricao']) > 100 ? '...' : '') : '';
                            $total_anexos = contarAnexos($evolucao);
                        ?>
                        <div class="evolucao-card">
                            <div class="evolucao-header">
                                <span class="evolucao-data">
                                    <i class="fas fa-calendar-alt"></i> <?php echo $data_formatada; ?>
                                </span>
                                <span class="evolucao-terapia-badge" style="background-color: <?php echo $cor_terapia; ?>">
                                    <?php echo htmlspecialchars($evolucao['terapia'] ?? 'Não especificada'); ?>
                                </span>
                            </div>
                            
                            <div class="evolucao-detalhes">
                                <div class="evolucao-detalhe">
                                    <i class="fas fa-user-md"></i>
                                    <span><strong>Terapeuta:</strong> <?php echo htmlspecialchars($evolucao['terapeuta'] ?? 'Não informado'); ?></span>
                                </div>
                                <div class="evolucao-detalhe">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Sessão:</strong> <?php echo htmlspecialchars($evolucao['horario_inicio'] ?? ''); ?> - <?php echo htmlspecialchars($evolucao['horario_fim'] ?? ''); ?> (<?php echo htmlspecialchars($evolucao['turno'] ?? ''); ?>)</span>
                                </div>
                                <?php if ($total_anexos > 0): ?>
                                    <div class="evolucao-detalhe">
                                        <i class="fas fa-paperclip"></i>
                                        <span><strong>Anexos:</strong> <?php echo $total_anexos; ?> arquivo(s)</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="evolucao-resumo">
                                <i class="fas fa-quote-left" style="color: #3b82f6; opacity: 0.5; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($resumo ?: 'Sem descrição'); ?>
                            </div>
                            
                            <div class="evolucao-actions">
                                <a href="#" class="btn-evolucao btn-evolucao-view" onclick="visualizarEvolucao('<?php echo $evolucao['id']; ?>'); return false;">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>
                                <a href="#" class="btn-evolucao btn-evolucao-pdf" onclick="gerarPDF('<?php echo $evolucao['id']; ?>'); return false;">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($total_paginas > 1): ?>
                    <div class="pagination-historico">
                        <div class="pagination-info">
                            Mostrando <?php echo $offset + 1; ?> - <?php echo min($offset + $itens_por_pagina, $total_evolucoes); ?> de <?php echo $total_evolucoes; ?> evoluções
                        </div>
                        <div class="pagination-controls">
                            <?php
                            $url_base = "evolucao_historico.php?paciente_id=" . urlencode($paciente_id) . 
                                       "&paciente_nome=" . urlencode($paciente_nome) . 
                                       "&responsavel=" . urlencode($responsavel) . 
                                       "&telefone=" . urlencode($telefone) .
                                       ($data_inicio ? "&data_inicio=$data_inicio" : "") .
                                       ($data_fim ? "&data_fim=$data_fim" : "") .
                                       ($terapia ? "&terapia=$terapia" : "") .
                                       ($terapeuta ? "&terapeuta=" . urlencode($terapeuta) : "");
                            ?>
                            
                            <a href="<?php echo $url_base; ?>&pagina=1" class="pagination-btn <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $pagina - 1; ?>" class="pagination-btn <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <?php if ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                    <a href="<?php echo $url_base; ?>&pagina=<?php echo $i; ?>" class="pagination-btn <?php echo $i == $pagina ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $pagina + 1; ?>" class="pagination-btn <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $total_paginas; ?>" class="pagination-btn <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal de Visualização -->
    <div id="visualizarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-medical"></i> Detalhes da Evolução</h2>
                <span class="modal-close" onclick="fecharModal()">&times;</span>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Conteúdo carregado via JavaScript -->
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menu mobile
        document.addEventListener('DOMContentLoaded', function() {
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

            // Fechar modal com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    fecharModal();
                }
            });
        });

        // Dados das evoluções (para visualização)
        const evolucoesData = <?php echo json_encode($evolucoes_paciente); ?>;

        function visualizarEvolucao(id) {
            const evolucao = evolucoesData.find(e => e.id === id);
            if (!evolucao) {
                alert('Evolução não encontrada');
                return;
            }

            const modalBody = document.getElementById('modal-body');
            
            // Formatar anexos
            let anexosHtml = '';
            if (evolucao.anexos && evolucao.anexos.length > 0) {
                anexosHtml = '<div class="anexos-list">';
                evolucao.anexos.forEach(anexo => {
                    const icon = anexo.tipo && anexo.tipo.includes('pdf') ? 'fa-file-pdf' : 'fa-file-image';
                    anexosHtml += `
                        <div class="anexo-item-modal">
                            <i class="fas ${icon}"></i>
                            <span class="anexo-nome">${anexo.nome_original || anexo.nome_arquivo}</span>
                            <a href="../../${anexo.caminho}" target="_blank" class="anexo-link">
                                <i class="fas fa-download"></i> Visualizar
                            </a>
                        </div>
                    `;
                });
                anexosHtml += '</div>';
            } else {
                anexosHtml = '<p style="color: #64748b;">Nenhum anexo</p>';
            }

            modalBody.innerHTML = `
                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-calendar-alt"></i> Informações da Sessão
                    </div>
                    <div class="modal-info-grid">
                        <div class="modal-info-item">
                            <span class="modal-info-label">Data</span>
                            <span class="modal-info-value">${formatarData(evolucao.data_sessao)}</span>
                        </div>
                        <div class="modal-info-item">
                            <span class="modal-info-label">Horário</span>
                            <span class="modal-info-value">${evolucao.horario_inicio || ''} - ${evolucao.horario_fim || ''}</span>
                        </div>
                        <div class="modal-info-item">
                            <span class="modal-info-label">Turno</span>
                            <span class="modal-info-value">${evolucao.turno || ''}</span>
                        </div>
                        <div class="modal-info-item">
                            <span class="modal-info-label">Terapia</span>
                            <span class="modal-info-value">${evolucao.terapia || ''}</span>
                        </div>
                        <div class="modal-info-item">
                            <span class="modal-info-label">Terapeuta</span>
                            <span class="modal-info-value">${evolucao.terapeuta || ''}</span>
                        </div>
                        <div class="modal-info-item">
                            <span class="modal-info-label">Data Registro</span>
                            <span class="modal-info-value">${formatarDataHora(evolucao.data_registro)}</span>
                        </div>
                    </div>
                </div>

                ${evolucao.condicao ? `
                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-heartbeat"></i> Condição Apresentada
                    </div>
                    <div class="modal-section-content">${escapeHtml(evolucao.condicao)}</div>
                </div>
                ` : ''}

                ${evolucao.materiais ? `
                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-tools"></i> Materiais e Recursos
                    </div>
                    <div class="modal-section-content">${escapeHtml(evolucao.materiais)}</div>
                </div>
                ` : ''}

                ${evolucao.estrategias ? `
                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-brain"></i> Estratégias e Métodos
                    </div>
                    <div class="modal-section-content">${escapeHtml(evolucao.estrategias)}</div>
                </div>
                ` : ''}

                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-chart-line"></i> Descrição da Evolução
                    </div>
                    <div class="modal-section-content">${escapeHtml(evolucao.descricao || 'Sem descrição')}</div>
                </div>

                ${evolucao.observacoes ? `
                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-sticky-note"></i> Observações Complementares
                    </div>
                    <div class="modal-section-content">${escapeHtml(evolucao.observacoes)}</div>
                </div>
                ` : ''}

                <div class="modal-section">
                    <div class="modal-section-title">
                        <i class="fas fa-paperclip"></i> Anexos (${evolucao.anexos ? evolucao.anexos.length : 0})
                    </div>
                    <div class="modal-section-content">
                        ${anexosHtml}
                    </div>
                </div>
            `;

            document.getElementById('visualizarModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function fecharModal() {
            document.getElementById('visualizarModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        function gerarPDF(id) {
            window.location.href = 'gerar_pdf_evolucao.php?id=' + id;
        }

        function formatarData(data) {
            if (!data) return 'Não informada';
            try {
                const d = new Date(data);
                return d.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarDataHora(data) {
            if (!data) return 'Não informada';
            try {
                const d = new Date(data);
                return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Fechar modal clicando fora
        window.onclick = function(event) {
            const modal = document.getElementById('visualizarModal');
            if (event.target == modal) {
                fecharModal();
            }
        }
    </script>
</body>
</html>