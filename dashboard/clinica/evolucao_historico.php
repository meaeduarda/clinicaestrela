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

// Carregar evoluções
$caminhoEvolucoes = __DIR__ . '/../../dashboard/dados/evolucoes.json';
$evolucoes = [];

if (file_exists($caminhoEvolucoes)) {
    $evolucoesJson = file_get_contents($caminhoEvolucoes);
    $evolucoes = json_decode($evolucoesJson, true) ?: [];
}

// Filtrar por paciente
$evolucoes_paciente = array_filter($evolucoes, function($e) use ($paciente_id) {
    return $e['paciente_id'] === $paciente_id;
});

// Aplicar filtros adicionais
if ($data_inicio) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($data_inicio) {
        return $e['data_sessao'] >= $data_inicio;
    });
}

if ($data_fim) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($data_fim) {
        return $e['data_sessao'] <= $data_fim;
    });
}

if ($terapia) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($terapia) {
        return $e['terapia'] === $terapia;
    });
}

if ($terapeuta) {
    $evolucoes_paciente = array_filter($evolucoes_paciente, function($e) use ($terapeuta) {
        return stripos($e['terapeuta'], $terapeuta) !== false;
    });
}

// Reindexar e ordenar por data (mais recente primeiro)
$evolucoes_paciente = array_values($evolucoes_paciente);
usort($evolucoes_paciente, function($a, $b) {
    return strtotime($b['data_sessao']) - strtotime($a['data_sessao']);
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
    'Fisioterapia' => 'Fisioterapia',
    'Musicoterapia' => 'Musicoterapia'
];

// Função para formatar data
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Função para obter cor da terapia
function getTerapiaCor($terapia) {
    $cores = [
        'ABA' => '#3b82f6',
        'Fonoaudiologia' => '#10b981',
        'Terapia Ocupacional' => '#f59e0b',
        'Psicologia' => '#8b5cf6',
        'Fisioterapia' => '#ef4444',
        'Musicoterapia' => '#ec4899'
    ];
    return $cores[$terapia] ?? '#64748b';
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

    <!-- Estilos CSS (mesmos do painel) -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_paciente.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_planoterapeutico.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_evolucoes.css">
    
    <!-- CSS específico para o histórico -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/evolucao_historico.css">
    
    <!-- Font Awesome e Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar COMPLETA (igual ao painel_evolucoes.php) -->
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

            <!-- Card de Identificação do Paciente (igual ao formulário) -->
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
                </div>
            <?php else: ?>
                <div class="evolucoes-grid">
                    <?php foreach ($evolucoes_paginadas as $evolucao): ?>
                        <?php 
                            $cor_terapia = getTerapiaCor($evolucao['terapia']);
                            $data_formatada = formatarData($evolucao['data_sessao']);
                            $resumo = substr($evolucao['descricao'], 0, 100) . (strlen($evolucao['descricao']) > 100 ? '...' : '');
                        ?>
                        <div class="evolucao-card">
                            <div class="evolucao-header">
                                <span class="evolucao-data">
                                    <i class="fas fa-calendar-alt"></i> <?php echo $data_formatada; ?>
                                </span>
                                <span class="evolucao-terapia-badge" style="background-color: <?php echo $cor_terapia; ?>">
                                    <?php echo $evolucao['terapia']; ?>
                                </span>
                            </div>
                            
                            <div class="evolucao-detalhes">
                                <div class="evolucao-detalhe">
                                    <i class="fas fa-user-md"></i>
                                    <span><strong>Terapeuta:</strong> <?php echo htmlspecialchars($evolucao['terapeuta']); ?></span>
                                </div>
                                <div class="evolucao-detalhe">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Sessão:</strong> <?php echo $evolucao['horario_inicio']; ?> - <?php echo $evolucao['horario_fim']; ?> (<?php echo $evolucao['turno']; ?>)</span>
                                </div>
                            </div>
                            
                            <div class="evolucao-resumo">
                                <i class="fas fa-quote-left" style="color: #3b82f6; opacity: 0.5; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($resumo); ?>
                            </div>
                            
                            <div class="evolucao-actions">
                                <a href="#" class="btn-evolucao btn-evolucao-view" onclick="visualizarEvolucao(<?php echo $evolucao['id']; ?>)">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>
                                <a href="#" class="btn-evolucao btn-evolucao-edit" onclick="editarEvolucao(<?php echo $evolucao['id']; ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="#" class="btn-evolucao btn-evolucao-pdf" onclick="gerarPDF(<?php echo $evolucao['id']; ?>)">
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
        });

        // Funções para ações (placeholder)
        function visualizarEvolucao(id) {
            alert('Visualizar evolução ID: ' + id);
        }

        function editarEvolucao(id) {
            alert('Editar evolução ID: ' + id);
        }

        function gerarPDF(id) {
            alert('Gerar PDF da evolução ID: ' + id);
        }
    </script>
</body>
</html>