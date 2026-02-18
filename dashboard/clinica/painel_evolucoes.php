<?php
// painel_evolucoes.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'] ?? 'Usuário';
$perfilLogado = $_SESSION['usuario_perfil'] ?? 'profissional';

// Pega o nome do arquivo atual para o menu ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Dados do paciente (recebidos via GET ou da sessão)
$paciente_id = isset($_GET['paciente_id']) ? $_GET['paciente_id'] : (isset($_SESSION['paciente_id']) ? $_SESSION['paciente_id'] : '');
$paciente_nome = isset($_GET['paciente_nome']) ? $_GET['paciente_nome'] : (isset($_SESSION['paciente_nome']) ? $_SESSION['paciente_nome'] : '');
$terapia = isset($_GET['terapia']) ? $_GET['terapia'] : (isset($_SESSION['terapia']) ? $_SESSION['terapia'] : 'Não informada');
$horario = isset($_GET['horario']) ? $_GET['horario'] : (isset($_SESSION['horario']) ? $_SESSION['horario'] : '--:-- - --:--');

// Se não tiver paciente, redirecionar para lista de pacientes
if (empty($paciente_id)) {
    header("Location: painel_adm_pacientes.php?error=Selecione um paciente para fazer a evolução");
    exit();
}

// Data atual formatada
$data_atual = date('d/m/Y');

// Buscar dados completos do paciente do JSON (opcional - se quiser mais informações)
$dados_paciente = [];
$caminhoPacientes = __DIR__ . '/../dados/ativo-cad.json';
if (file_exists($caminhoPacientes)) {
    $pacientesJson = file_get_contents($caminhoPacientes);
    $pacientes = json_decode($pacientesJson, true);
    
    if (is_array($pacientes)) {
        foreach ($pacientes as $paciente) {
            $pacienteIdTemp = md5(($paciente['nome_completo'] ?? '') . ($paciente['telefone'] ?? ''));
            if ($pacienteIdTemp == $paciente_id) {
                $dados_paciente = $paciente;
                break;
            }
        }
    }
}

// Se encontrou dados do paciente no JSON, usar nome oficial
if (!empty($dados_paciente)) {
    $paciente_nome = $dados_paciente['nome_completo'] ?? $paciente_nome;
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
    <title>Nova Evolução - Clínica Estrela</title>

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
    
    <!-- Font Awesome e Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Ajustes específicos para o painel de evoluções */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-left: 4px solid;
            min-height: 100px;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-card.blue { border-left-color: #3b82f6; }
        .stat-card.green { border-left-color: #10b981; }
        .stat-card.purple { border-left-color: #8b5cf6; }
        .stat-card.orange { border-left-color: #f97316; }

        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .stat-card.blue .stat-icon {
            background-color: #eff6ff;
            color: #3b82f6;
        }

        .stat-card.green .stat-icon {
            background-color: #f0fdf4;
            color: #10b981;
        }

        .stat-card.purple .stat-icon {
            background-color: #f5f3ff;
            color: #8b5cf6;
        }

        .stat-card.orange .stat-icon {
            background-color: #fff7ed;
            color: #f97316;
        }

        .stat-card .stat-icon i {
            font-size: 18px;
        }

        .stat-card .stat-content h3 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            line-height: 1;
            font-size: clamp(20px, 5vw, 28px);
        }

        .stat-card .stat-content p {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .stat-card {
                padding: 14px;
            }
        }

        @media (max-width: 359px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
                padding: 12px;
            }
            
            .stat-card .stat-icon {
                margin-right: 0;
                margin-bottom: 8px;
            }
        }

        @media (max-height: 500px) and (orientation: landscape) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .stat-card {
                padding: 12px;
            }
        }

        @media (max-width: 768px) {
            .stat-card:active {
                transform: scale(0.98);
            }
        }

        /* Melhorias para o breadcrumb */
        .breadcrumb-modern {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .breadcrumb-modern a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .breadcrumb-modern a:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .breadcrumb-modern i {
            font-size: 14px;
            color: #94a3b8;
        }

        .breadcrumb-modern span {
            color: #64748b;
            font-weight: 500;
        }

        .breadcrumb-modern .separator {
            color: #cbd5e1;
            font-size: 14px;
        }

        .breadcrumb-modern .current {
            color: #1e293b;
            font-weight: 600;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* Cabeçalho da página */
        .page-header-modern {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title-modern {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title-modern i {
            color: #3b82f6;
            background: #eff6ff;
            padding: 10px;
            border-radius: 12px;
            font-size: 24px;
        }

        .patient-tag {
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #475569;
        }

        .patient-tag i {
            color: #3b82f6;
        }

        /* Info cards do paciente */
        .patient-info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .patient-info-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
        }

        .patient-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .info-card-icon {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-card-icon i {
            font-size: 24px;
            color: #3b82f6;
        }

        .info-card-content {
            flex: 1;
        }

        .info-card-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .info-card-value {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Ajustes para o formulário */
        .evolution-form {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .form-section {
            padding: 28px 32px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 3px solid #3b82f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #3b82f6;
            font-size: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .form-textarea {
            width: 100%;
            min-height: 120px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1e293b;
            background: #f8fafc;
            resize: vertical;
            transition: all 0.2s;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .form-textarea::placeholder {
            color: #94a3b8;
            font-style: italic;
        }

        /* Upload area */
        .upload-area-modern {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
        }

        .upload-box-modern {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-box-modern:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: translateY(-2px);
        }

        .upload-box-modern i {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .upload-box-modern p {
            color: #64748b;
            margin: 0;
            font-size: 16px;
        }

        .upload-box-modern small {
            display: block;
            color: #94a3b8;
            margin-top: 8px;
            font-size: 13px;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .image-preview-item:hover {
            transform: scale(1.05);
        }

        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(239,68,68,0.9);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s;
        }

        .remove-image-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* Rodapé do formulário */
        .form-footer-modern {
            padding: 24px 32px;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .clinic-label-modern {
            font-weight: 700;
            color: #3b82f6;
            font-size: 18px;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .clinic-label-modern i {
            font-size: 20px;
        }

        .action-buttons-modern {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-save-modern {
            padding: 14px 36px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(16,185,129,0.2);
        }

        .btn-save-modern:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
        }

        .btn-save-modern:active {
            transform: translateY(0);
        }

        .btn-cancel-modern {
            padding: 14px 36px;
            background: transparent;
            color: #64748b;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-cancel-modern:hover {
            background: #f1f5f9;
            color: #ef4444;
            border-color: #ef4444;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .patient-info-cards {
                grid-template-columns: 1fr;
            }
            
            .form-footer-modern {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            
            .action-buttons-modern {
                width: 100%;
                flex-direction: column;
            }
            
            .btn-save-modern,
            .btn-cancel-modern {
                width: 100%;
                justify-content: center;
            }
            
            .breadcrumb-modern {
                flex-wrap: wrap;
            }
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
                        
                        <li class="active">
                            <a href="painel_evolucoes.php?paciente_id=<?php echo $paciente_id; ?>&paciente_nome=<?php echo urlencode($paciente_nome); ?>"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a>
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
                <h2><i class="fas fa-chart-line"></i> Nova Evolução</h2>
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

            <!-- Cards de Estatísticas - Indicadores Rápidos -->
            <div class="stats-cards">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data_atual; ?></h3>
                        <p>Data atual</p>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $horario; ?></h3>
                        <p>Horário da sessão</p>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo htmlspecialchars($terapia); ?></h3>
                        <p>Terapia</p>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo htmlspecialchars($nomeLogado); ?></h3>
                        <p>Profissional</p>
                    </div>
                </div>
            </div>

            <!-- Breadcrumb Moderno -->
            <div class="breadcrumb-modern">
                <a href="painel_adm_pacientes.php"><i class="fas fa-home"></i> Pacientes</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <a href="paciente_detalhe.php?id=<?php echo $paciente_id; ?>">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($paciente_nome); ?>
                </a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current"><i class="fas fa-chart-line"></i> Nova Evolução</span>
            </div>

            <!-- Cabeçalho da Página -->
            <div class="page-header-modern">
                <h1 class="page-title-modern">
                    <i class="fas fa-file-medical"></i>
                    Nova Evolução Terapêutica
                </h1>
                <div class="patient-tag">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($paciente_nome); ?>
                </div>
            </div>

            <!-- Informações do Paciente em Cards -->
            <div class="patient-info-cards">
                <div class="patient-info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Paciente</div>
                        <div class="info-card-value"><?php echo htmlspecialchars($paciente_nome); ?></div>
                    </div>
                </div>

                <div class="patient-info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Data da Sessão</div>
                        <div class="info-card-value"><?php echo $data_atual; ?></div>
                    </div>
                </div>

                <div class="patient-info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Horário</div>
                        <div class="info-card-value"><?php echo $horario; ?></div>
                    </div>
                </div>

                <div class="patient-info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-briefcase-medical"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Terapia</div>
                        <div class="info-card-value"><?php echo htmlspecialchars($terapia); ?></div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Evolução -->
            <form class="evolution-form" method="POST" action="salvar_evolucao.php" enctype="multipart/form-data">
                <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                <input type="hidden" name="data_sessao" value="<?php echo $data_atual; ?>">
                <input type="hidden" name="terapia" value="<?php echo htmlspecialchars($terapia); ?>">
                <input type="hidden" name="horario" value="<?php echo htmlspecialchars($horario); ?>">
                
                <!-- Condição Apresentada -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-heartbeat"></i>
                        Condição Apresentada no Atendimento
                    </h2>
                    <textarea class="form-textarea" name="condicao" placeholder="Descreva brevemente o estado geral do paciente no início da sessão, seu humor, disposição, comportamentos observados..." required></textarea>
                </div>

                <!-- Materiais e Recursos -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-tools"></i>
                        Materiais e Recursos Aplicados
                    </h2>
                    <textarea class="form-textarea" name="materiais" placeholder="Liste os materiais, brinquedos, instrumentos, atividades estruturadas, recursos visuais utilizados durante a sessão..."></textarea>
                </div>

                <!-- Estratégias e Métodos -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-brain"></i>
                        Estratégias, Métodos e Abordagens Utilizadas
                    </h2>
                    <textarea class="form-textarea" name="estrategias" placeholder="Descreva as técnicas terapêuticas empregadas (ex: modelação, reforçamento, dicas visuais, PECS, ensino naturalístico, etc.)..."></textarea>
                </div>

                <!-- Descrição da Evolução -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-line"></i>
                        Descrição da Evolução e Intervenções Realizadas
                    </h2>
                    <textarea class="form-textarea" name="descricao" placeholder="Relate detalhadamente o que foi trabalhado, as atividades realizadas, a resposta do paciente, os avanços observados, dificuldades encontradas, e como as intervenções foram conduzidas..." required></textarea>
                </div>

                <!-- Observações Complementares -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Observações Complementares
                    </h2>
                    <textarea class="form-textarea" name="observacoes" placeholder="Informações adicionais relevantes: orientações aos pais, intercorrências, encaminhamentos, sugestões para próxima sessão..."></textarea>
                </div>

                <!-- Anexos (Fotos/Documentos) -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-camera"></i>
                        Anexos
                    </h2>
                    
                    <div class="upload-area-modern">
                        <p style="margin-bottom: 16px; color: #475569;">
                            <i class="fas fa-info-circle" style="color: #3b82f6;"></i> 
                            Anexe fotos, documentos ou arquivos relevantes para esta evolução (máx. 5 imagens)
                        </p>
                        
                        <div class="upload-box-modern" onclick="document.getElementById('fotos-upload').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Arraste e solte aqui ou clique para fazer upload</p>
                            <small>Formatos aceitos: JPG, PNG, GIF, PDF (até 10MB cada)</small>
                            <input type="file" id="fotos-upload" name="fotos[]" multiple accept="image/*,application/pdf" style="display: none;">
                        </div>
                        
                        <div class="image-preview-container" id="imagePreviewContainer"></div>
                    </div>
                </div>

                <!-- Rodapé do Formulário -->
                <div class="form-footer-modern">
                    <span class="clinic-label-modern">
                        <i class="fas fa-star"></i>
                        CLÍNICA ESTRELA
                    </span>
                    <div class="action-buttons-modern">
                        <button type="submit" class="btn-save-modern">
                            <i class="fas fa-save"></i> Salvar Evolução
                        </button>
                        <button type="button" class="btn-cancel-modern" onclick="window.location.href='paciente_detalhe.php?id=<?php echo $paciente_id; ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Preview de imagens
        document.getElementById('fotos-upload')?.addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('imagePreviewContainer');
            if (!previewContainer) return;
            
            previewContainer.innerHTML = '';
            
            for (let i = 0; i < Math.min(files.length, 5); i++) {
                const file = files[i];
                
                // Se for PDF, mostrar ícone diferente
                if (file.type === 'application/pdf') {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'image-preview-item';
                    previewDiv.innerHTML = `
                        <div style="width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                            <i class="fas fa-file-pdf" style="font-size: 32px; color: #ef4444;"></i>
                            <span style="font-size: 10px; text-align: center; padding: 4px;">${file.name.substring(0, 10)}...</span>
                        </div>
                        <button type="button" class="remove-image-btn" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(previewDiv);
                    continue;
                }
                
                // Para imagens
                if (!file.type.startsWith('image/')) continue;
                
                const reader = new FileReader();
                reader.onload = function(readerEvent) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'image-preview-item';
                    previewDiv.innerHTML = `
                        <img src="${readerEvent.target.result}" class="image-preview" alt="Preview">
                        <button type="button" class="remove-image-btn" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            }
        });

        // Menu Mobile
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

            // Validação do formulário antes de enviar
            const form = document.querySelector('.evolution-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const condicao = document.querySelector('textarea[name="condicao"]').value.trim();
                    const descricao = document.querySelector('textarea[name="descricao"]').value.trim();
                    
                    if (!condicao || !descricao) {
                        e.preventDefault();
                        alert('Por favor, preencha os campos obrigatórios: Condição Apresentada e Descrição da Evolução.');
                    }
                });
            }
        });

        // Função para mostrar notificação toast (pode ser útil depois)
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
    </script>
</body>
</html>