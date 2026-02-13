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

// Anos disponíveis (dos últimos 5 anos)
$anosDisponiveis = [];
for ($i = 0; $i < 5; $i++) {
    $ano = date('Y') - $i;
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

// Dados dos pacientes (em produção viriam do banco com paginação)
$todosPacientes = [
    ['id' => 1, 'nome' => 'João Silva', 'idade' => '5 anos', 'responsavel' => 'Ana de Souza', 'telefone' => '(11) 98765-4321', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Joao_Silva_2024_04.pdf'],
    ['id' => 2, 'nome' => 'Guilherme Souza', 'idade' => '4 anos', 'responsavel' => 'Regina Souza', 'telefone' => '(11) 98711-4572', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 3, 'nome' => 'Helena Alves', 'idade' => '6 anos', 'responsavel' => 'Carla Alves', 'telefone' => '(11) 99987-4403', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Helena_Alves_2024_04.pdf'],
    ['id' => 4, 'nome' => 'Guilherme Martins', 'idade' => '5 anos', 'responsavel' => 'Patricia Martins', 'telefone' => '(11) 91020-5556', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Guilherme_Martins_2024_04.pdf'],
    ['id' => 5, 'nome' => 'Mariana Santos', 'idade' => '8 anos', 'responsavel' => 'Renata Santos', 'telefone' => '(11) 93456-7810', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 6, 'nome' => 'Luisa Souza', 'idade' => '7 anos', 'responsavel' => 'Ana Souza', 'telefone' => '(11) 90215-0090', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Luisa_Souza_2024_04.pdf'],
    ['id' => 7, 'nome' => 'Henrique Costa', 'idade' => '6 anos', 'responsavel' => 'Fernanda Costa', 'telefone' => '(11) 9000-4192', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 8, 'nome' => 'Sofia Santos', 'idade' => '7 anos', 'responsavel' => 'Marcos Santos', 'telefone' => '(11) 91234-5050', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Sofia_Santos_2024_04.pdf'],
    ['id' => 9, 'nome' => 'Pedro Almeida', 'idade' => '6 anos', 'responsavel' => 'Fernanda Almeida', 'telefone' => '(11) 94676-1112', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 10, 'nome' => 'Lucas Pereira', 'idade' => '4 anos', 'responsavel' => 'Joana Pereira', 'telefone' => '(11) 98876-6543', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Lucas_Pereira_2024_04.pdf'],
    ['id' => 11, 'nome' => 'Carlos Oliveira', 'idade' => '5 anos', 'responsavel' => 'Maria Oliveira', 'telefone' => '(11) 95555-4444', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 12, 'nome' => 'Ana Beatriz', 'idade' => '6 anos', 'responsavel' => 'Cláudia Santos', 'telefone' => '(11) 97777-8888', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Ana_Beatriz_2024_04.pdf'],
    ['id' => 13, 'nome' => 'Rafael Lima', 'idade' => '7 anos', 'responsavel' => 'Patrícia Lima', 'telefone' => '(11) 93333-2222', 'pei_anexado' => false, 'pei_arquivo' => null],
    ['id' => 14, 'nome' => 'Fernanda Costa', 'idade' => '5 anos', 'responsavel' => 'Roberto Costa', 'telefone' => '(11) 94444-5555', 'pei_anexado' => true, 'pei_arquivo' => 'PEI_Fernanda_Costa_2024_04.pdf'],
    ['id' => 15, 'nome' => 'Miguel Santos', 'idade' => '6 anos', 'responsavel' => 'André Santos', 'telefone' => '(11) 96666-7777', 'pei_anexado' => false, 'pei_arquivo' => null],
];

// Aplicar busca
if ($busca) {
    $todosPacientes = array_filter($todosPacientes, function($paciente) use ($busca) {
        return stripos($paciente['nome'], $busca) !== false || 
               stripos($paciente['responsavel'], $busca) !== false;
    });
}

// Paginação
$totalPacientes = count($todosPacientes);
$pacientesPorPagina = 10;
$totalPaginas = ceil($totalPacientes / $pacientesPorPagina);
$paginaAtual = min($paginaAtual, $totalPaginas);

// Pegar pacientes da página atual
$indiceInicio = ($paginaAtual - 1) * $pacientesPorPagina;
$pacientesPagina = array_slice($todosPacientes, $indiceInicio, $pacientesPorPagina);

// Contar PEIs anexados
$count_pei = 0;
foreach ($todosPacientes as $paciente) {
    if ($paciente['pei_anexado']) $count_pei++;
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
                        <button class="btn-export">
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
                            <?php if (count($pacientesPagina) > 0): ?>
                                <?php foreach ($pacientesPagina as $paciente): ?>
                                    <tr class="patient-row">
                                        <td>
                                            <div class="patient-cell">
                                                <div class="patient-avatar-small">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($paciente['nome']); ?>&background=random&color=fff"
                                                         alt="<?php echo htmlspecialchars($paciente['nome']); ?>">
                                                </div>
                                                <div class="patient-name">
                                                    <strong><?php echo htmlspecialchars($paciente['nome']); ?></strong>
                                                    <span class="patient-age"><?php echo htmlspecialchars($paciente['idade']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="responsible-info">
                                                <span class="responsible-text"><?php echo htmlspecialchars($paciente['responsavel']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="contact-phone"><?php echo htmlspecialchars($paciente['telefone']); ?></span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-attach-pei"
                                                        data-patient-id="<?php echo $paciente['id']; ?>"
                                                        data-patient-name="<?php echo htmlspecialchars($paciente['nome']); ?>">
                                                    <i class="fas fa-paperclip"></i>
                                                    Anexar PEI
                                                </button>
                                                <button class="btn-view-pei"
                                                        data-patient-id="<?php echo $paciente['id']; ?>"
                                                        data-patient-name="<?php echo htmlspecialchars($paciente['nome']); ?>"
                                                        data-pei-anexado="<?php echo $paciente['pei_anexado'] ? 'true' : 'false'; ?>"
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

                <!-- Paginação -->
                <?php if ($totalPaginas > 1): ?>
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
        });
    </script>
</body>
</html>