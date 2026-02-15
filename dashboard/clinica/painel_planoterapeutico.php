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
$anoFinal = $anoAtual + 1;

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

// ===== FUNÇÃO PARA CARREGAR PACIENTES =====
function carregarPacientes() {
    $caminhoPacientes = __DIR__ . '/../dados/ativo-cad.json';
    $caminhoPEISalvo = __DIR__ . '/../dados/pei_salvo/pei_salvo.json';
    $pacientesCarregados = [];
    
    if (file_exists($caminhoPacientes)) {
        $pacientesJson = file_get_contents($caminhoPacientes);
        $pacientes = json_decode($pacientesJson, true);
        
        if (is_array($pacientes)) {
            // Carregar PEIs salvos
            $peisSalvos = [];
            if (file_exists($caminhoPEISalvo)) {
                $peisSalvos = json_decode(file_get_contents($caminhoPEISalvo), true) ?: [];
            }
            
            foreach ($pacientes as $paciente) {
                // Filtrar apenas pacientes com status "Ativo"
                if (isset($paciente['status']) && $paciente['status'] !== 'Ativo') {
                    continue;
                }
                
                // Gerar ID único baseado no nome + telefone
                $pacienteId = md5(($paciente['nome_completo'] ?? '') . ($paciente['telefone'] ?? ''));
                $peiInfo = $peisSalvos[$pacienteId] ?? null;
                
                // Calcular idade
                $idade = 'Idade n/d';
                if (!empty($paciente['nascimento'])) {
                    try {
                        $nascimento = new DateTime($paciente['nascimento']);
                        $hoje = new DateTime();
                        $idade = $nascimento->diff($hoje)->y . ' anos';
                    } catch (Exception $e) {
                        $idade = 'Idade n/d';
                    }
                }
                
                // Determinar responsável
                $responsavel = 'Responsável não informado';
                $tipoResponsavel = $paciente['responsavel'] ?? '';
                
                if (!empty($paciente['nome_mae'])) {
                    $responsavel = $paciente['nome_mae'];
                    if (!empty($tipoResponsavel)) {
                        $responsavel = $tipoResponsavel . ': ' . $responsavel;
                    }
                } elseif (!empty($paciente['nome_pai'])) {
                    $responsavel = $paciente['nome_pai'];
                    if (!empty($tipoResponsavel)) {
                        $responsavel = $tipoResponsavel . ': ' . $responsavel;
                    }
                } elseif (!empty($tipoResponsavel)) {
                    $responsavel = $tipoResponsavel;
                }
                
                $pacientesCarregados[] = [
                    'id' => $pacienteId,
                    'nome' => $paciente['nome_completo'] ?? 'Nome não informado',
                    'idade' => $idade,
                    'responsavel' => $responsavel,
                    'telefone' => $paciente['telefone'] ?? 'Telefone não informado',
                    'pei_anexado' => !is_null($peiInfo),
                    'pei_arquivo' => $peiInfo['arquivo'] ?? '',
                    'pei_nome_original' => $peiInfo['nome_original'] ?? '',
                    'pei_data_upload' => $peiInfo['data_upload'] ?? '',
                    'data_nascimento' => $paciente['nascimento'] ?? ''
                ];
            }
        }
    }
    
    return $pacientesCarregados;
}

// ===== FUNÇÃO PARA VERIFICAR NOVOS PACIENTES =====
function verificarNovosPacientes() {
    $caminhoPacientes = __DIR__ . '/../dados/ativo-cad.json';
    $novosPacientes = 0;
    
    if (file_exists($caminhoPacientes) && isset($_SESSION['pacientes_pei'])) {
        $pacientesJson = file_get_contents($caminhoPacientes);
        $pacientes = json_decode($pacientesJson, true);
        
        if (is_array($pacientes)) {
            $idsSessao = array_column($_SESSION['pacientes_pei'], 'id');
            
            foreach ($pacientes as $paciente) {
                // Filtrar apenas pacientes com status "Ativo"
                if (isset($paciente['status']) && $paciente['status'] !== 'Ativo') {
                    continue;
                }
                
                $pacienteId = md5(($paciente['nome_completo'] ?? '') . ($paciente['telefone'] ?? ''));
                
                // Se o ID não existe na sessão, é um novo paciente
                if (!in_array($pacienteId, $idsSessao)) {
                    $novosPacientes++;
                }
            }
        }
    }
    
    return $novosPacientes;
}

// ===== DADOS DOS PACIENTES =====
// Verificar se deve recarregar os dados
if (isset($_GET['importar']) && $_GET['importar'] == 'true') {
    // Recarregar dados do JSON e salvar na sessão
    $_SESSION['pacientes_pei'] = carregarPacientes();
    
    // Construir URL de redirecionamento corretamente
    $params = $_GET;
    unset($params['importar']);
    
    if (empty($params)) {
        $redirectUrl = 'painel_planoterapeutico.php';
    } else {
        $redirectUrl = 'painel_planoterapeutico.php?' . http_build_query($params);
    }
    
    header("Location: " . $redirectUrl);
    exit();
}

// Verificar se há refresh (após upload de PEI)
$refresh = isset($_GET['refresh']) && $_GET['refresh'] == 'true';
if ($refresh) {
    // Atualizar os dados da sessão com as informações mais recentes do JSON
    $caminhoPEISalvo = __DIR__ . '/../dados/pei_salvo/pei_salvo.json';
    if (file_exists($caminhoPEISalvo)) {
        $peisSalvos = json_decode(file_get_contents($caminhoPEISalvo), true) ?: [];
        
        // Atualizar cada paciente na sessão com as informações mais recentes do PEI
        if (isset($_SESSION['pacientes_pei']) && is_array($_SESSION['pacientes_pei'])) {
            foreach ($_SESSION['pacientes_pei'] as &$paciente) {
                $pacienteId = $paciente['id'];
                if (isset($peisSalvos[$pacienteId])) {
                    $paciente['pei_anexado'] = true;
                    $paciente['pei_arquivo'] = $peisSalvos[$pacienteId]['arquivo'] ?? '';
                    $paciente['pei_nome_original'] = $peisSalvos[$pacienteId]['nome_original'] ?? '';
                    $paciente['pei_data_upload'] = $peisSalvos[$pacienteId]['data_upload'] ?? '';
                } else {
                    $paciente['pei_anexado'] = false;
                    $paciente['pei_arquivo'] = '';
                    $paciente['pei_nome_original'] = '';
                    $paciente['pei_data_upload'] = '';
                }
            }
        }
    }
}

// Carregar pacientes da sessão ou do JSON se não existir
if (isset($_SESSION['pacientes_pei']) && !empty($_SESSION['pacientes_pei'])) {
    $todosPacientes = $_SESSION['pacientes_pei'];
} else {
    // Primeiro acesso - carregar automaticamente
    $todosPacientes = carregarPacientes();
    $_SESSION['pacientes_pei'] = $todosPacientes;
}

// Verificar quantos novos pacientes existem
$novosPacientesCount = verificarNovosPacientes();

// ===== APLICAR FILTROS DE ANO E MÊS =====
$pacientesFiltrados = $todosPacientes;

if (!empty($todosPacientes)) {
    // Filtrar por ano e mês (baseado na data de upload do PEI)
    $pacientesFiltrados = array_filter($todosPacientes, function($paciente) use ($anoSelecionado, $mesSelecionado) {
        // Se o paciente tem PEI anexado, usar a data de upload para filtrar
        if ($paciente['pei_anexado'] && !empty($paciente['pei_data_upload'])) {
            $dataUpload = new DateTime($paciente['pei_data_upload']);
            $anoUpload = $dataUpload->format('Y');
            $mesUpload = $dataUpload->format('m');
            
            // Se o ano e mês do upload corresponderem aos selecionados, incluir
            if ($anoUpload == $anoSelecionado && $mesUpload == $mesSelecionado) {
                return true;
            }
            return false;
        }
        
        // Se não tem PEI anexado, mostrar apenas se o filtro for mês/ano atual
        // (pacientes sem PEI aparecem apenas no mês atual)
        if (!$paciente['pei_anexado']) {
            $mesAtual = date('m');
            $anoAtual = date('Y');
            return ($anoSelecionado == $anoAtual && $mesSelecionado == $mesAtual);
        }
        
        return false;
    });
}

// Reindexar array após filtros
if (!empty($pacientesFiltrados)) {
    $pacientesFiltrados = array_values($pacientesFiltrados);
}

// APLICAR BUSCA - VERSÃO CORRIGIDA
if (!empty($busca) && !empty($pacientesFiltrados)) {
    $pacientesFiltrados = array_filter($pacientesFiltrados, function($paciente) use ($busca) {
        $buscaLower = strtolower($busca);
        $nomeLower = strtolower($paciente['nome'] ?? '');
        $responsavelLower = strtolower($paciente['responsavel'] ?? '');
        
        return strpos($nomeLower, $buscaLower) !== false || 
               strpos($responsavelLower, $buscaLower) !== false;
    });
}

// Reindexar array após busca
if (!empty($pacientesFiltrados)) {
    $pacientesFiltrados = array_values($pacientesFiltrados);
}

// Paginação
$totalPacientesFiltrados = count($pacientesFiltrados);
$pacientesPorPagina = 10;
$totalPaginas = $totalPacientesFiltrados > 0 ? ceil($totalPacientesFiltrados / $pacientesPorPagina) : 1;
$paginaAtual = min($paginaAtual, $totalPaginas);

// Pegar pacientes da página atual
$indiceInicio = ($paginaAtual - 1) * $pacientesPorPagina;
$pacientesPagina = !empty($pacientesFiltrados) ? array_slice($pacientesFiltrados, $indiceInicio, $pacientesPorPagina) : [];

// Contar PEIs anexados no mês selecionado
$count_pei_mes = 0;
foreach ($pacientesFiltrados as $paciente) {
    if (!empty($paciente['pei_anexado'])) $count_pei_mes++;
}

// Contar total de PEIs (todos os meses)
$count_pei_total = 0;
foreach ($todosPacientes as $paciente) {
    if (!empty($paciente['pei_anexado'])) $count_pei_total++;
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

        /* Estilos para a coluna PEI */
        .pei-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pei-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .pei-badge.anexado {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .pei-badge.sem-pei {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .pei-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-pei {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            background-color: transparent;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-pei:hover {
            background-color: #f1f5f9;
            color: #3b82f6;
        }

        .btn-pei.attach {
            width: auto;
            padding: 0 12px;
            gap: 6px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-pei.attach:hover {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .btn-pei.edit:hover {
            color: #f59e0b;
        }

        /* Toast de notificação */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        .toast-notification.error {
            background-color: #ef4444;
        }

        .toast-notification i {
            font-size: 20px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Cards de estatísticas */
        .stats-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
        }

        .stat-card .stat-title {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-card .stat-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Estilo para o botão Importar com notificação - CORRIGIDO */
        .btn-import-container {
            position: relative;
            display: inline-block;
            margin-left: 10px;
        }

        .btn-import {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: visible;
        }

        .btn-import:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4392 100%);
        }

        .btn-import:active {
            transform: translateY(0);
        }

        .btn-import i {
            font-size: 16px;
        }

        .btn-import.notification {
            animation: pulse 2s infinite;
        }

        .import-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ef4444;
            color: white;
            font-size: 12px;
            font-weight: 700;
            min-width: 24px;
            height: 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            box-shadow: 0 3px 8px rgba(239, 68, 68, 0.4);
            animation: bounce 1s ease infinite;
            border: 2px solid white;
            z-index: 15;
            line-height: 1;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.15);
            }
        }

        /* Tooltip personalizado */
        .import-tooltip {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 20;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .import-tooltip::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 0 5px 5px 5px;
            border-style: solid;
            border-color: transparent transparent #1e293b transparent;
        }

        .btn-import-container:hover .import-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: -45px;
        }

        /* Melhorias na busca */
        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .search-box input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .clear-search {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .clear-search:hover {
            color: #ef4444;
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

            <!-- Cards de Estatísticas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-title">PEIs no mês</div>
                    <div class="stat-value"><?php echo $count_pei_mes; ?></div>
                    <div class="stat-subtitle"><?php echo $mesFormatado; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total de PEIs</div>
                    <div class="stat-value"><?php echo $count_pei_total; ?></div>
                    <div class="stat-subtitle">Todos os meses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pacientes</div>
                    <div class="stat-value"><?php echo count($todosPacientes); ?></div>
                    <div class="stat-subtitle">Total ativos</div>
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

                    <!-- Botão Importar Dados - CORRIGIDO -->
                    <div class="btn-import-container">
                        <button class="btn-import <?php echo $novosPacientesCount > 0 ? 'notification' : ''; ?>" onclick="importarDados()">
                            <i class="fas fa-database"></i>
                            <span>Importar Dados</span>
                            <?php if ($novosPacientesCount > 0): ?>
                                <span class="import-badge">+<?php echo $novosPacientesCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <?php if ($novosPacientesCount > 0): ?>
                            <div class="import-tooltip">
                                <?php echo $novosPacientesCount; ?> novo(s) paciente(s) disponível(is)
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="filters-right">
                    <form method="GET" class="search-form" id="searchForm">
                        <input type="hidden" name="ano" value="<?php echo $anoSelecionado; ?>">
                        <input type="hidden" name="mes" value="<?php echo $mesSelecionado; ?>">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text"
                                   name="busca"
                                   placeholder="Buscar paciente por nome ou responsável..."
                                   value="<?php echo htmlspecialchars($busca); ?>"
                                   id="searchPatient"
                                   autocomplete="off">
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

            <!-- Informação do filtro atual -->
            <div style="margin: 10px 0; color: #64748b; font-size: 14px;">
                <i class="fas fa-info-circle"></i> 
                Mostrando PEIs do mês de <strong><?php echo $mesFormatado; ?></strong>
                <?php if (!empty($busca)): ?> 
                    com busca por "<strong><?php echo htmlspecialchars($busca); ?></strong>"
                <?php endif; ?>
                <?php if (!empty($pacientesFiltrados)): ?>
                    (<?php echo $totalPacientesFiltrados; ?> resultado(s))
                <?php endif; ?>
            </div>

            <!-- Tabela de Pacientes -->
            <div class="patients-table-container">
                <div class="table-header">
                    <h3>Plano Terapêutico - PEI Mensal</h3>
                    <div class="table-actions">
                        <button class="btn-export" <?php echo empty($pacientesFiltrados) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
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
                                <th>PEI</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <?php if (empty($pacientesFiltrados)): ?>
                                <!-- Estado vazio com filtros -->
                                <tr>
                                    <td colspan="5" class="no-results">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <h3>Nenhum PEI encontrado</h3>
                                            <p>Não há PEIs para o mês de <strong><?php echo $mesFormatado; ?></strong>
                                            <?php if (!empty($busca)): ?> 
                                                com a busca "<strong><?php echo htmlspecialchars($busca); ?></strong>"
                                            <?php endif; ?>
                                            </p>
                                            <?php if ($anoSelecionado != date('Y') || $mesSelecionado != date('m')): ?>
                                                <button class="btn-primary" onclick="window.location.href='?ano=<?php echo date('Y'); ?>&mes=<?php echo date('m'); ?>'">
                                                    <i class="fas fa-calendar"></i>
                                                    Ver mês atual
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-primary" onclick="importarDados()">
                                                    <i class="fas fa-database"></i>
                                                    Importar Dados
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php elseif (count($pacientesPagina) > 0): ?>
                                <?php foreach ($pacientesPagina as $index => $paciente): ?>
                                    <tr class="patient-row" data-patient-id="<?php echo $paciente['id']; ?>" data-patient-index="<?php echo $index; ?>">
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
                                            <div class="pei-status">
                                                <?php if ($paciente['pei_anexado']): ?>
                                                    <span class="pei-badge anexado">
                                                        <i class="fas fa-check-circle"></i>
                                                        PEI Anexado
                                                    </span>
                                                <?php else: ?>
                                                    <span class="pei-badge sem-pei">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        Sem PEI
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="pei-actions">
                                                <?php if ($paciente['pei_anexado']): ?>
                                                    <button class="btn-pei" 
                                                            onclick="visualizarPEI('<?php echo $paciente['id']; ?>', '<?php echo htmlspecialchars(addslashes($paciente['nome'])); ?>', '<?php echo $paciente['pei_arquivo']; ?>')"
                                                            title="Visualizar PEI">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-pei edit"
                                                            onclick="editarPEI('<?php echo $paciente['id']; ?>', '<?php echo htmlspecialchars(addslashes($paciente['nome'])); ?>')"
                                                            title="Editar PEI">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn-pei attach"
                                                            onclick="anexarPEI('<?php echo $paciente['id']; ?>', '<?php echo htmlspecialchars(addslashes($paciente['nome'])); ?>')">
                                                        <i class="fas fa-paperclip"></i>
                                                        Anexar
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-results">
                                        <i class="fas fa-search"></i>
                                        <p>Nenhum paciente encontrado para "<?php echo htmlspecialchars($busca); ?>"</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if (!empty($pacientesFiltrados) && $totalPaginas > 1): ?>
                    <div class="pagination">
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

                        <div class="pagination-info">
                            <span class="current-page"><?php echo $paginaAtual; ?></span>
                            <span class="total-pages">de <?php echo $totalPaginas; ?></span>
                        </div>

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

    <!-- Modal para Anexar/Editar PEI -->
    <div id="attachPEIModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-paperclip"></i> <span id="modalTitle">Anexar PEI</span></h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-patient-info">
                    <p><strong>Paciente:</strong> <span id="modalPatientName"></span></p>
                    <p><strong>Mês referência:</strong> <span id="modalMonth"><?php echo $mesFormatado; ?></span></p>
                </div>

                <form id="peiUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="paciente_id" id="pacienteId">
                    <input type="hidden" name="acao" id="acao" value="anexar">
                    
                    <div class="file-upload-area">
                        <input type="file" id="peiFile" name="pei_file" accept=".pdf" class="file-input" required>
                        <label for="peiFile" class="upload-label">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <span class="upload-text">Clique para selecionar arquivo PDF</span>
                            <span class="upload-hint">ou arraste e solte aqui</span>
                        </label>
                        <div class="selected-file" id="selectedFile" style="display: none;">
                            <i class="fas fa-file-pdf"></i>
                            <span id="selectedFileName"></span>
                            <button type="button" class="btn-remove-file" id="removeFile">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="file-formats">
                            Formato aceito: PDF<br>
                            Tamanho máximo: 10MB
                        </p>
                    </div>
                </form>

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
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Visualizar PEI</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-patient-info">
                    <p><strong>Paciente:</strong> <span id="viewPatientName"></span></p>
                    <p><strong>Mês referência:</strong> <span id="viewMonth"><?php echo $mesFormatado; ?></span></p>
                </div>

                <div id="peiContent" class="pdf-viewer">
                    <!-- PDF será carregado aqui -->
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

    <script>
        // Variável para controlar se houve refresh recente
        let refreshRealizado = <?php echo $refresh ? 'true' : 'false'; ?>;
        let novosPacientes = <?php echo $novosPacientesCount; ?>;
        
        // Função para importar dados
        function importarDados() {
            // Manter os parâmetros atuais e adicionar importar=true
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('importar', 'true');
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }

        // Função para anexar PEI
        function anexarPEI(pacienteId, pacienteNome) {
            document.getElementById('modalTitle').textContent = 'Anexar PEI';
            document.getElementById('modalPatientName').textContent = pacienteNome;
            document.getElementById('pacienteId').value = pacienteId;
            document.getElementById('acao').value = 'anexar';
            document.getElementById('peiFile').required = true;
            document.getElementById('peiUploadForm').reset();
            document.getElementById('selectedFile').style.display = 'none';
            
            // Mostrar o label de upload novamente
            document.querySelector('.upload-label').style.display = 'flex';
            
            const modal = document.getElementById('attachPEIModal');
            modal.style.display = 'flex';
        }

        // Função para editar PEI
        function editarPEI(pacienteId, pacienteNome) {
            document.getElementById('modalTitle').textContent = 'Editar PEI';
            document.getElementById('modalPatientName').textContent = pacienteNome;
            document.getElementById('pacienteId').value = pacienteId;
            document.getElementById('acao').value = 'editar';
            document.getElementById('peiFile').required = true;
            document.getElementById('peiUploadForm').reset();
            document.getElementById('selectedFile').style.display = 'none';
            
            // Mostrar o label de upload novamente
            document.querySelector('.upload-label').style.display = 'flex';
            
            const modal = document.getElementById('attachPEIModal');
            modal.style.display = 'flex';
        }

        // Função para visualizar PEI
        function visualizarPEI(pacienteId, pacienteNome, arquivo) {
            document.getElementById('viewPatientName').textContent = pacienteNome;
            
            // Carregar PDF
            const peiContent = document.getElementById('peiContent');
            peiContent.innerHTML = `<iframe src="carregar_pei.php?id=${pacienteId}&arquivo=${encodeURIComponent(arquivo)}" width="100%" height="600px" style="border: none;"></iframe>`;
            
            const modal = document.getElementById('viewPEIModal');
            modal.style.display = 'flex';
        }

        // Função para mostrar notificação toast
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification ${tipo === 'error' ? 'error' : ''}`;
            toast.innerHTML = `
                <i class="fas ${tipo === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                <span>${mensagem}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideIn 0.3s reverse';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Função para verificar novos pacientes periodicamente
        function verificarNovosPacientesPeriodicamente() {
            // Esta função pode ser implementada com AJAX para verificar novos pacientes
            // sem recarregar a página. Por enquanto, vamos apenas mostrar uma mensagem
            // se houver novos pacientes
            if (novosPacientes > 0) {
                console.log(`${novosPacientes} novo(s) paciente(s) disponível(is) para importar`);
            }
        }

        // Configurar modais
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar notificação se houve refresh
            if (refreshRealizado) {
                mostrarToast('PEI atualizado com sucesso!');
                
                // Remover parâmetro refresh da URL
                const url = new URL(window.location.href);
                url.searchParams.delete('refresh');
                window.history.replaceState({}, document.title, url.toString());
            }

            // Mostrar notificação se houver novos pacientes
            if (novosPacientes > 0) {
                mostrarToast(`${novosPacientes} novo(s) paciente(s) disponível(is) para importar`, 'success');
            }

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

            // Fechar dropdowns quando clicar fora
            function fecharDropdowns() {
                if (anoDropdown) anoDropdown.classList.remove('show');
                if (mesDropdown) mesDropdown.classList.remove('show');
            }

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

            // Configurar modais
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.modal-close, .cancel-btn, .close-btn');

            closeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    modals.forEach(modal => modal.style.display = 'none');
                    
                    // Limpar iframe quando fechar modal de visualização
                    const peiContent = document.getElementById('peiContent');
                    if (peiContent) {
                        peiContent.innerHTML = '';
                    }
                });
            });

            // Fechar modal ao clicar fora
            window.addEventListener('click', function(event) {
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                        
                        // Limpar iframe quando fechar modal de visualização
                        const peiContent = document.getElementById('peiContent');
                        if (peiContent) {
                            peiContent.innerHTML = '';
                        }
                    }
                });
            });

            // Upload de arquivo
            const fileInput = document.getElementById('peiFile');
            const selectedFile = document.getElementById('selectedFile');
            const selectedFileName = document.getElementById('selectedFileName');
            const removeFileBtn = document.getElementById('removeFile');
            const uploadLabel = document.querySelector('.upload-label');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        
                        // Validar tamanho (10MB)
                        if (file.size > 10 * 1024 * 1024) {
                            mostrarToast('Arquivo muito grande. Tamanho máximo: 10MB', 'error');
                            this.value = '';
                            return;
                        }
                        
                        // Validar tipo
                        if (file.type !== 'application/pdf') {
                            mostrarToast('Apenas arquivos PDF são permitidos', 'error');
                            this.value = '';
                            return;
                        }
                        
                        selectedFileName.textContent = file.name;
                        selectedFile.style.display = 'flex';
                        uploadLabel.style.display = 'none';
                    }
                });
            }

            if (removeFileBtn) {
                removeFileBtn.addEventListener('click', function() {
                    fileInput.value = '';
                    selectedFile.style.display = 'none';
                    uploadLabel.style.display = 'flex';
                });
            }

            // Salvar PEI
            const btnSavePEI = document.getElementById('btnSavePEI');
            if (btnSavePEI) {
                btnSavePEI.addEventListener('click', function() {
                    const form = document.getElementById('peiUploadForm');
                    const formData = new FormData(form);
                    
                    // Validar se arquivo foi selecionado
                    if (!fileInput.files || !fileInput.files[0]) {
                        mostrarToast('Por favor, selecione um arquivo PDF', 'error');
                        return;
                    }
                    
                    // Desabilitar botão para evitar múltiplos envios
                    btnSavePEI.disabled = true;
                    btnSavePEI.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                    
                    // Enviar via AJAX
                    fetch('salvar_pei.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fechar modal
                            document.getElementById('attachPEIModal').style.display = 'none';
                            
                            // Mostrar toast de sucesso
                            mostrarToast('PEI salvo com sucesso!');
                            
                            // Recarregar a página após 1 segundo para mostrar as alterações
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            mostrarToast('Erro ao salvar PEI: ' + data.message, 'error');
                            btnSavePEI.disabled = false;
                            btnSavePEI.innerHTML = '<i class="fas fa-save"></i> Salvar PEI';
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        mostrarToast('Erro ao salvar PEI. Tente novamente.', 'error');
                        btnSavePEI.disabled = false;
                        btnSavePEI.innerHTML = '<i class="fas fa-save"></i> Salvar PEI';
                    });
                });
            }

            // Adicionar submit ao formulário de busca para manter os filtros
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const busca = document.getElementById('searchPatient').value.trim();
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('busca', busca);
                    urlParams.delete('pagina'); // Resetar página ao buscar
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                });
            }

            // Busca ao pressionar Enter
            const searchInput = document.getElementById('searchPatient');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('searchForm').requestSubmit();
                    }
                });
            }

            // Verificar novos pacientes periodicamente (a cada 30 segundos)
            setInterval(verificarNovosPacientesPeriodicamente, 30000);
        });
    </script>
</body>
</html>