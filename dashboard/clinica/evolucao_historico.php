<?php
// evolucao_historico.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Evoluções - <?php echo htmlspecialchars($paciente_nome); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_paciente.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_evolucoes.css">
    
    <!-- Font Awesome e Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos específicos para o histórico */
        .historico-paciente-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .historico-paciente-nome {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .historico-paciente-info {
            display: flex;
            gap: 20px;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .historico-paciente-info i {
            margin-right: 8px;
        }
        
        .historico-filters-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .historico-filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .historico-filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .historico-filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .historico-filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .historico-filter-input {
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            background: #f8fafc;
        }
        
        .historico-filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
        }
        
        .historico-filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        
        .historico-btn-filter {
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 46px;
        }
        
        .historico-btn-clear {
            padding: 12px 24px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            height: 46px;
        }
        
        .evolucoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .evolucao-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
        }
        
        .evolucao-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }
        
        .evolucao-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .evolucao-data {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        
        .evolucao-terapia {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        
        .evolucao-detalhes {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .evolucao-detalhe-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #475569;
            font-size: 14px;
        }
        
        .evolucao-detalhe-item i {
            width: 20px;
            color: #3b82f6;
            font-size: 16px;
        }
        
        .evolucao-detalhe-item strong {
            color: #1e293b;
            font-weight: 600;
            margin-right: 5px;
        }
        
        .evolucao-resumo {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
            color: #334155;
            line-height: 1.5;
            border-left: 3px solid #3b82f6;
        }
        
        .evolucao-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .evolucao-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        
        .evolucao-btn-view {
            background: #3b82f6;
            color: white;
        }
        
        .evolucao-btn-edit {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .evolucao-btn-pdf {
            background: #ef4444;
            color: white;
        }
        
        .pagination-historico {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .pagination-info-historico {
            color: #64748b;
            font-size: 14px;
        }
        
        .pagination-controls-historico {
            display: flex;
            gap: 8px;
        }
        
        .pagination-btn-historico {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .pagination-btn-historico:hover:not(.disabled) {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .pagination-btn-historico.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .pagination-btn-historico.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .btn-voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .btn-voltar:hover {
            background: #e2e8f0;
        }
        
        .empty-state-historico {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            color: #64748b;
        }
        
        .empty-state-historico i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .empty-state-historico h3 {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .historico-paciente-header {
                padding: 20px;
            }
            
            .historico-paciente-nome {
                font-size: 22px;
            }
            
            .historico-paciente-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .historico-filters-grid {
                grid-template-columns: 1fr;
            }
            
            .historico-filter-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .evolucoes-grid {
                grid-template-columns: 1fr;
            }
            
            .pagination-historico {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar (igual ao painel_evolucoes.php) -->
        <aside class="sidebar">
            <!-- ... conteúdo da sidebar igual ao painel_evolucoes.php ... -->
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="main-top">
                <h2><i class="fas fa-history"></i> Histórico de Evoluções</h2>
            </div>

            <!-- Botão Voltar -->
            <a href="painel_evolucoes.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar para Lista de Pacientes
            </a>

            <!-- Header do Paciente -->
            <div class="historico-paciente-header">
                <div class="historico-paciente-nome">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($paciente_nome); ?>
                </div>
                <div class="historico-paciente-info">
                    <span><i class="fas fa-user-tie"></i> Responsável: <?php echo htmlspecialchars($responsavel); ?></span>
                    <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($telefone); ?></span>
                </div>
            </div>

            <!-- Filtros -->
            <div class="historico-filters-card">
                <div class="historico-filters-title">
                    <i class="fas fa-filter"></i> Filtrar Evoluções
                </div>
                
                <form method="GET" action="evolucao_historico.php">
                    <input type="hidden" name="paciente_id" value="<?php echo htmlspecialchars($paciente_id); ?>">
                    <input type="hidden" name="paciente_nome" value="<?php echo htmlspecialchars($paciente_nome); ?>">
                    <input type="hidden" name="responsavel" value="<?php echo htmlspecialchars($responsavel); ?>">
                    <input type="hidden" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">
                    
                    <div class="historico-filters-grid">
                        <div class="historico-filter-group">
                            <label class="historico-filter-label">Data Início</label>
                            <input type="date" name="data_inicio" class="historico-filter-input" value="<?php echo $data_inicio; ?>">
                        </div>
                        
                        <div class="historico-filter-group">
                            <label class="historico-filter-label">Data Fim</label>
                            <input type="date" name="data_fim" class="historico-filter-input" value="<?php echo $data_fim; ?>">
                        </div>
                        
                        <div class="historico-filter-group">
                            <label class="historico-filter-label">Terapia</label>
                            <select name="terapia" class="historico-filter-input">
                                <option value="">Todas</option>
                                <?php foreach ($terapias as $valor => $nome): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $terapia == $valor ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="historico-filter-group">
                            <label class="historico-filter-label">Terapeuta</label>
                            <input type="text" name="terapeuta" class="historico-filter-input" placeholder="Nome do terapeuta" value="<?php echo htmlspecialchars($terapeuta); ?>">
                        </div>
                        
                        <div class="historico-filter-actions">
                            <button type="submit" class="historico-btn-filter">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="evolucao_historico.php?paciente_id=<?php echo urlencode($paciente_id); ?>&paciente_nome=<?php echo urlencode($paciente_nome); ?>&responsavel=<?php echo urlencode($responsavel); ?>&telefone=<?php echo urlencode($telefone); ?>" class="historico-btn-clear">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de Evoluções -->
            <?php if (empty($evolucoes_paginadas)): ?>
                <div class="empty-state-historico">
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
                                <span class="evolucao-data"><?php echo $data_formatada; ?></span>
                                <span class="evolucao-terapia" style="background-color: <?php echo $cor_terapia; ?>">
                                    <?php echo $evolucao['terapia']; ?>
                                </span>
                            </div>
                            
                            <div class="evolucao-detalhes">
                                <div class="evolucao-detalhe-item">
                                    <i class="fas fa-user-md"></i>
                                    <span><strong>Terapeuta:</strong> <?php echo htmlspecialchars($evolucao['terapeuta']); ?></span>
                                </div>
                                <div class="evolucao-detalhe-item">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Sessão:</strong> <?php echo $evolucao['horario_inicio']; ?> - <?php echo $evolucao['horario_fim']; ?> (<?php echo $evolucao['turno']; ?>)</span>
                                </div>
                            </div>
                            
                            <div class="evolucao-resumo">
                                <i class="fas fa-quote-left" style="color: #3b82f6; opacity: 0.5; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($resumo); ?>
                            </div>
                            
                            <div class="evolucao-actions">
                                <a href="evolucao_visualizar.php?id=<?php echo $evolucao['id']; ?>" class="evolucao-btn evolucao-btn-view">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>
                                <a href="evolucao_editar.php?id=<?php echo $evolucao['id']; ?>" class="evolucao-btn evolucao-btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="evolucao_pdf.php?id=<?php echo $evolucao['id']; ?>" class="evolucao-btn evolucao-btn-pdf" target="_blank">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($total_paginas > 1): ?>
                    <div class="pagination-historico">
                        <div class="pagination-info-historico">
                            Mostrando <?php echo $offset + 1; ?> - <?php echo min($offset + $itens_por_pagina, $total_evolucoes); ?> de <?php echo $total_evolucoes; ?> evoluções
                        </div>
                        <div class="pagination-controls-historico">
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
                            
                            <a href="<?php echo $url_base; ?>&pagina=1" class="pagination-btn-historico <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $pagina - 1; ?>" class="pagination-btn-historico <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <?php if ($i >= $pagina - 2 && $i <= $pagina + 2): ?>
                                    <a href="<?php echo $url_base; ?>&pagina=<?php echo $i; ?>" class="pagination-btn-historico <?php echo $i == $pagina ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $pagina + 1; ?>" class="pagination-btn-historico <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            
                            <a href="<?php echo $url_base; ?>&pagina=<?php echo $total_paginas; ?>" class="pagination-btn-historico <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
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
    </script>
</body>
</html>