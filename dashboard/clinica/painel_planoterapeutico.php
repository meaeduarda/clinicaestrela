<?php
// painel_planoterapeutico.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Pega o nome do arquivo atual para o menu ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Filtros
$anoSelecionado = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$mesSelecionado = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$paginaAtual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// ===== ANOS DISPONÍVEIS (1960 ATÉ ANO ATUAL + 1) =====
$anosDisponiveis = [];
$anoInicial = 1960;
$anoAtual = date('Y');
$anoFinal = $anoAtual + 1; // Inclui o próximo ano

for ($ano = $anoFinal; $ano >= $anoInicial; $ano--) {
    $anosDisponiveis[] = [
        'valor' => $ano,
        'texto' => $ano,
        'selecionado' => $ano == $anoSelecionado
    ];
}

// Meses disponíveis
$mesesDisponiveis = [
    ['valor' => '01', 'texto' => 'Janeiro', 'selecionado' => '01' == $mesSelecionado],
    ['valor' => '02', 'texto' => 'Fevereiro', 'selecionado' => '02' == $mesSelecionado],
    ['valor' => '03', 'texto' => 'Março', 'selecionado' => '03' == $mesSelecionado],
    ['valor' => '04', 'texto' => 'Abril', 'selecionado' => '04' == $mesSelecionado],
    ['valor' => '05', 'texto' => 'Maio', 'selecionado' => '05' == $mesSelecionado],
    ['valor' => '06', 'texto' => 'Junho', 'selecionado' => '06' == $mesSelecionado],
    ['valor' => '07', 'texto' => 'Julho', 'selecionado' => '07' == $mesSelecionado],
    ['valor' => '08', 'texto' => 'Agosto', 'selecionado' => '08' == $mesSelecionado],
    ['valor' => '09', 'texto' => 'Setembro', 'selecionado' => '09' == $mesSelecionado],
    ['valor' => '10', 'texto' => 'Outubro', 'selecionado' => '10' == $mesSelecionado],
    ['valor' => '11', 'texto' => 'Novembro', 'selecionado' => '11' == $mesSelecionado],
    ['valor' => '12', 'texto' => 'Dezembro', 'selecionado' => '12' == $mesSelecionado],
];

// Formatar mês atual
$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
$mesFormatado = $meses[$mesSelecionado] . " $anoSelecionado";

// ===== DADOS DOS PACIENTES - INICIALMENTE VAZIO =====
// Aqui você vai integrar com seu banco de dados ou JSON posteriormente
$todosPacientes = []; // Array vazio - sem dados fictícios

// Exemplo de como será quando integrado:
// $todosPacientes = json_decode(file_get_contents('caminho/do/arquivo.json'), true) ?? [];

// Aplicar busca (quando houver dados)
if ($busca && !empty($todosPacientes)) {
    $todosPacientes = array_filter($todosPacientes, function($paciente) use ($busca) {
        return stripos($paciente['nome'] ?? '', $busca) !== false || 
               stripos($paciente['responsavel'] ?? '', $busca) !== false;
    });
}

// Paginação (quando houver dados)
$totalPacientes = count($todosPacientes);
$pacientesPorPagina = 10;
$totalPaginas = $totalPacientes > 0 ? ceil($totalPacientes / $pacientesPorPagina) : 1;
$paginaAtual = min($paginaAtual, $totalPaginas);

// Pegar pacientes da página atual (quando houver dados)
$indiceInicio = ($paginaAtual - 1) * $pacientesPorPagina;
$pacientesPagina = !empty($todosPacientes) ? array_slice($todosPacientes, $indiceInicio, $pacientesPorPagina) : [];

// Contar PEIs anexados (quando houver dados)
$count_pei = 0;
foreach ($todosPacientes as $paciente) {
    if (!empty($paciente['pei_anexado'])) $count_pei++;
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
    <title>Plano Terapêutico - Clínica Estrela</title>

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
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_planoterapeutico-modals.css">
    
    <!-- Font Awesome e Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Ajustes para o dropdown de anos com muitos itens */
        #anoDropdown {
            max-height: 300px;
            overflow-y: auto;
            width: 100px;
        }
        
        .dropdown-item {
            white-space: nowrap;
            padding: 8px 12px;
        }

        /* Estado vazio melhorado */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background-color: #f8fafc;
            border-radius: 12px;
            margin: 20px 0;
        }

        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto 20px;
        }

        .empty-state .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .empty-state .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .kpi-card .kpi-content h3 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
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

            <!-- Menu de Navegação -->
            <nav class="menu">
                <ul>
                    <li <?php echo ($pagina_atual == 'painel_adm_pacientes.php') ? 'class="active"' : ''; ?>>
                        <a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'painel_pacientes_pendentes.php') ? 'class="active"' : ''; ?>>
                        <a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a>
                    </li>
                    
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li <?php echo ($pagina_atual == 'painel_adm_preca.php') ? 'class="active"' : ''; ?>>
                            <a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_planoterapeutico.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_planoterapeutico.php"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_adm_grade.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_evolucoes.php') ? 'class="active"' : ''; ?>>
                            <a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a>
                        </li>
                    <?php endif; ?>
                    
                    <li <?php echo ($pagina_atual == 'painel_agenda.php') ? 'class="active"' : ''; ?>>
                        <a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'visita_agendamento.php') ? 'class="active"' : ''; ?>>
                        <a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'painel_salas.php') ? 'class="active"' : ''; ?>>
                        <a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'login_cadastro_clinica.php') ? 'class="active"' : ''; ?>>
                        <a href="http://localhost/clinicaestrela/dashboard/clinica/login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a>
                    </li>
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
                <a href="logout.php" title="Sair" style="color: #ef4444; margin-left: 10px; text-decoration: none;">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-calendar-check"></i> Plano Terapêutico</h2>
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


            <!-- Filtros e Busca -->
            <div class="plan-filters">
                <div class="filters-left">
                    <!-- Filtro por Ano -->
                    <div class="filter-group">
                        <div class="filter-label">
                            <i class="fas fa-calendar"></i>
                            <span>Ano:</span>
                        </div>
                        <div class="filter-dropdown">
                            <button class="dropdown-btn" id="anoDropdownBtn">
                                <span><?php echo $anoSelecionado; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="anoDropdown">
                                <?php foreach ($anosDisponiveis as $ano): ?>
                                    <a href="?ano=<?php echo $ano['valor']; ?>&mes=<?php echo $mesSelecionado; ?>&busca=<?php echo urlencode($busca); ?>"
                                       class="dropdown-item <?php echo $ano['selecionado'] ? 'active' : ''; ?>">
                                        <?php echo $ano['texto']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro por Mês -->
                    <div class="filter-group">
                        <div class="filter-label">
                            <i class="fas fa-filter"></i>
                            <span>Mês:</span>
                        </div>
                        <div class="filter-dropdown">
                            <button class="dropdown-btn" id="mesDropdownBtn">
                                <span><?php echo $meses[$mesSelecionado]; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="mesDropdown">
                                <?php foreach ($mesesDisponiveis as $mes): ?>
                                    <a href="?ano=<?php echo $anoSelecionado; ?>&mes=<?php echo $mes['valor']; ?>&busca=<?php echo urlencode($busca); ?>"
                                       class="dropdown-item <?php echo $mes['selecionado'] ? 'active' : ''; ?>">
                                        <?php echo $mes['texto']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filters-right">
                    <form method="GET" class="search-form">
                        <input type="hidden" name="ano" value="<?php echo $anoSelecionado; ?>">
                        <input type="hidden" name="mes" value="<?php echo $mesSelecionado; ?>">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text"
                                   name="busca"
                                   placeholder="Buscar paciente..."
                                   value="<?php echo htmlspecialchars($busca); ?>"
                                   id="searchPatient">
                            <?php if ($busca): ?>
                                <a href="?ano=<?php echo $anoSelecionado; ?>&mes=<?php echo $mesSelecionado; ?>"
                                   class="clear-search" title="Limpar busca">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Pacientes -->
            <div class="patients-table-container">
                <div class="table-header">
                    <h3>Plano Terapêutico - PEI Mensal</h3>
                    <div class="table-actions">
                        <button class="btn-export" <?php echo empty($todosPacientes) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                            <i class="fas fa-file-export"></i>
                            Exportar
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="patients-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Responsável</th>
                                <th>Contato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <?php if (empty($todosPacientes)): ?>
                                <!-- Estado vazio - sem dados fictícios -->
                                <tr>
                                    <td colspan="4" class="no-results">
                                        <div class="empty-state">
                                            <i class="fas fa-file-medical"></i>
                                            <h3>Nenhum paciente encontrado</h3>
                                            <p>Os pacientes com PEI anexado aparecerão aqui quando você integrar com sua fonte de dados.</p>
                                            <button class="btn-primary" onclick="alert('Funcionalidade de importação será implementada em breve!')">
                                                <i class="fas fa-database"></i>
                                                Importar Dados
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php elseif (count($pacientesPagina) > 0): ?>
                                <?php foreach ($pacientesPagina as $paciente): ?>
                                    <tr class="patient-row">
                                        <td>
                                            <div class="patient-cell">
                                                <div class="patient-avatar-small">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($paciente['nome'] ?? 'Paciente'); ?>&background=random&color=fff"
                                                         alt="<?php echo htmlspecialchars($paciente['nome'] ?? 'Paciente'); ?>">
                                                </div>
                                                <div class="patient-name">
                                                    <strong><?php echo htmlspecialchars($paciente['nome'] ?? 'Nome não informado'); ?></strong>
                                                    <span class="patient-age"><?php echo htmlspecialchars($paciente['idade'] ?? 'Idade n/d'); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="responsible-info">
                                                <span class="responsible-text"><?php echo htmlspecialchars($paciente['responsavel'] ?? 'Responsável não informado'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="contact-phone"><?php echo htmlspecialchars($paciente['telefone'] ?? 'Telefone não informado'); ?></span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-attach-pei"
                                                        data-patient-id="<?php echo $paciente['id'] ?? ''; ?>"
                                                        data-patient-name="<?php echo htmlspecialchars($paciente['nome'] ?? 'Paciente'); ?>">
                                                    <i class="fas fa-paperclip"></i>
                                                    Anexar PEI
                                                </button>
                                                <button class="btn-view-pei"
                                                        data-patient-id="<?php echo $paciente['id'] ?? ''; ?>"
                                                        data-patient-name="<?php echo htmlspecialchars($paciente['nome'] ?? 'Paciente'); ?>"
                                                        data-pei-anexado="<?php echo !empty($paciente['pei_anexado']) ? 'true' : 'false'; ?>"
                                                        data-pei-arquivo="<?php echo htmlspecialchars($paciente['pei_arquivo'] ?? ''); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-results">
                                        <i class="fas fa-search"></i>
                                        <p>Nenhum paciente encontrado para "<?php echo htmlspecialchars($busca); ?>"</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação (só mostra se houver dados) -->
                <?php if (!empty($todosPacientes) && $totalPaginas > 1): ?>
                    <div class="pagination">
                        <!-- Botão Anterior -->
                        <?php if ($paginaAtual > 1): ?>
                            <a href="?ano=<?php echo $anoSelecionado; ?>&mes=<?php echo $mesSelecionado; ?>&busca=<?php echo urlencode($busca); ?>&pagina=<?php echo $paginaAtual - 1; ?>"
                               class="pagination-btn prev">
                                <i class="fas fa-chevron-left"></i>
                                Anterior
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn prev disabled">
                                <i class="fas fa-chevron-left"></i>
                                Anterior
                            </span>
                        <?php endif; ?>

                        <!-- Informação da página -->
                        <div class="pagination-info">
                            <span class="current-page"><?php echo $paginaAtual; ?></span>
                            <span class="total-pages">de <?php echo $totalPaginas; ?></span>
                        </div>

                        <!-- Botão Próximo -->
                        <?php if ($paginaAtual < $totalPaginas): ?>
                            <a href="?ano=<?php echo $anoSelecionado; ?>&mes=<?php echo $mesSelecionado; ?>&busca=<?php echo urlencode($busca); ?>&pagina=<?php echo $paginaAtual + 1; ?>"
                               class="pagination-btn next">
                                Próximo
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn next disabled">
                                Próximo
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal para Anexar PEI -->
    <div id="attachPEIModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-paperclip"></i> Anexar PEI</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-patient-info">
                    <p><strong>Paciente:</strong> <span id="modalPatientName"></span></p>
                    <p><strong>Mês referência:</strong> <span id="modalMonth"><?php echo $mesFormatado; ?></span></p>
                </div>

                <div class="file-upload-area">
                    <input type="file" id="peiFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="file-input">
                    <label for="peiFile" class="upload-label">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <span class="upload-text">Clique para selecionar arquivo</span>
                        <span class="upload-hint">ou arraste e solte aqui</span>
                    </label>
                    <div class="selected-file" id="selectedFile" style="display: none;">
                        <i class="fas fa-file"></i>
                        <span id="selectedFileName"></span>
                        <button class="btn-remove-file" id="removeFile">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="file-formats">
                        Formatos aceitos: PDF, DOC, DOCX, JPG, PNG<br>
                        Tamanho máximo: 10MB
                    </p>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-primary save-btn" id="btnSavePEI">
                        <i class="fas fa-save"></i>
                        Salvar PEI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Visualizar PEI -->
    <div id="viewPEIModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Visualizar PEI</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-patient-info">
                    <p><strong>Paciente:</strong> <span id="viewPatientName"></span></p>
                    <p><strong>Mês referência:</strong> <span id="viewMonth"><?php echo $mesFormatado; ?></span></p>
                </div>

                <div id="peiContent">
                    <!-- Conteúdo será carregado dinamicamente -->
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary close-btn">
                        <i class="fas fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/dashboard/clinica/painel_planoterapeutico.js"></script>
    
    <script>
        // Scripts para menu mobile e dropdowns
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

            // Dropdowns de filtro
            const anoDropdownBtn = document.getElementById('anoDropdownBtn');
            const anoDropdown = document.getElementById('anoDropdown');
            const mesDropdownBtn = document.getElementById('mesDropdownBtn');
            const mesDropdown = document.getElementById('mesDropdown');

            // Ano dropdown
            if (anoDropdownBtn && anoDropdown) {
                anoDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    anoDropdown.classList.toggle('show');
                    if (mesDropdown) mesDropdown.classList.remove('show');
                });
            }

            // Mês dropdown
            if (mesDropdownBtn && mesDropdown) {
                mesDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    mesDropdown.classList.toggle('show');
                    if (anoDropdown) anoDropdown.classList.remove('show');
                });
            }

            // Fechar dropdowns ao clicar fora
            document.addEventListener('click', function(e) {
                if (anoDropdown && !anoDropdownBtn?.contains(e.target) && !anoDropdown.contains(e.target)) {
                    anoDropdown.classList.remove('show');
                }
                if (mesDropdown && !mesDropdownBtn?.contains(e.target) && !mesDropdown.contains(e.target)) {
                    mesDropdown.classList.remove('show');
                }
            });

            // Desabilitar botões de ação se não houver pacientes
            <?php if (empty($todosPacientes)): ?>
                document.querySelectorAll('.btn-attach-pei, .btn-view-pei').forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.style.cursor = 'not-allowed';
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>