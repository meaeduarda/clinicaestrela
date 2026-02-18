<?php
// painel_evolucoes.php
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

// ===== FILTROS =====
$pacienteFiltro = isset($_GET['paciente']) ? $_GET['paciente'] : '';
$terapiaFiltro = isset($_GET['terapia']) ? $_GET['terapia'] : '';
$turnoFiltro = isset($_GET['turno']) ? $_GET['turno'] : '';

// ===== CARREGAR PACIENTES DO JSON =====
$caminhoPacientes = __DIR__ . '/../dados/ativo-cad.json';
$pacientesAtivos = [];

if (file_exists($caminhoPacientes)) {
    $pacientesJson = file_get_contents($caminhoPacientes);
    $pacientes = json_decode($pacientesJson, true);
    
    if (is_array($pacientes)) {
        foreach ($pacientes as $paciente) {
            // Filtrar apenas pacientes com status "Ativo"
            if (isset($paciente['status']) && $paciente['status'] === 'Ativo') {
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
                $responsavel = 'Não informado';
                if (!empty($paciente['nome_mae'])) {
                    $responsavel = $paciente['nome_mae'];
                } elseif (!empty($paciente['nome_pai'])) {
                    $responsavel = $paciente['nome_pai'];
                }
                
                $pacientesAtivos[] = [
                    'id' => md5(($paciente['nome_completo'] ?? '') . ($paciente['telefone'] ?? '')),
                    'nome' => $paciente['nome_completo'] ?? 'Sem nome',
                    'idade' => $idade,
                    'responsavel' => $responsavel,
                    'telefone' => $paciente['telefone'] ?? '',
                    'data_cadastro' => $paciente['data_ativacao'] ?? $paciente['data_registro'] ?? '',
                    'foto' => $paciente['foto'] ?? ''
                ];
            }
        }
    }
}

// ===== APLICAR FILTROS =====
$pacientesFiltrados = $pacientesAtivos;

if (!empty($pacienteFiltro)) {
    $pacientesFiltrados = array_filter($pacientesFiltrados, function($p) use ($pacienteFiltro) {
        return stripos($p['nome'], $pacienteFiltro) !== false;
    });
}

// Reindexar array
$pacientesFiltrados = array_values($pacientesFiltrados);

// ===== DADOS PARA O FORMULÁRIO (quando um paciente é selecionado) =====
$modo_formulario = isset($_GET['paciente_id']) && !empty($_GET['paciente_id']);
$paciente_selecionado = null;

if ($modo_formulario) {
    $paciente_id = $_GET['paciente_id'];
    foreach ($pacientesAtivos as $p) {
        if ($p['id'] === $paciente_id) {
            $paciente_selecionado = $p;
            break;
        }
    }
}

// Data atual
$data_atual = date('Y-m-d');

// Lista de terapias (clínica multidisciplinar de autismo)
$terapias = [
    'ABA' => 'ABA (Análise do Comportamento Aplicada)',
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

// Turnos
$turnos = ['Manhã', 'Tarde'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title><?php echo $modo_formulario ? 'Nova Evolução' : 'Evoluções'; ?> - Clínica Estrela</title>

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
                        <a href="painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a>
                    </li>
                    
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li <?php echo ($pagina_atual == 'painel_adm_preca.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_planoterapeutico.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_planoterapeutico.php"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_adm_grade.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a>
                        </li>
                        
                        <li <?php echo ($pagina_atual == 'painel_evolucoes.php') ? 'class="active"' : ''; ?>>
                            <a href="painel_evolucoes.php"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a>
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
                        <a href="login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a>
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
                <h2><i class="fas fa-chart-line"></i> <?php echo $modo_formulario ? 'Nova Evolução' : 'Evoluções'; ?></h2>
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

            <?php if (!$modo_formulario): ?>
                <!-- ===== MODO LISTA ===== -->
                
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <span>Evoluções</span>
                </div>

                <!-- Cards de estatísticas -->
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count($pacientesAtivos); ?></h3>
                            <p>Total de Pacientes</p>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="stat-content">
                            <h3>0</h3>
                            <p>Evoluções hoje</p>
                        </div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo date('d/m/Y'); ?></h3>
                            <p>Data atual</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="plan-filters">
                    <div class="filters-left">
                        <!-- Filtro por Nome -->
                        <div class="filter-group">
                            <div class="filter-label">
                                <i class="fas fa-user"></i>
                                <span>Paciente:</span>
                            </div>
                            <form method="GET" style="display: flex; gap: 8px;">
                                <input type="text" 
                                       name="paciente" 
                                       value="<?php echo htmlspecialchars($pacienteFiltro); ?>" 
                                       placeholder="Buscar por nome..."
                                       style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; width: 200px;">
                                <button type="submit" class="btn-action view" style="padding: 8px 16px;">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <?php if ($pacienteFiltro): ?>
                                    <a href="painel_evolucoes.php" class="btn-cancel" style="padding: 8px 16px;">
                                        <i class="fas fa-times"></i> Limpar
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Pacientes -->
                <div class="patients-table-container">
                    <div class="table-header">
                        <h3>Selecione um paciente para fazer a evolução</h3>
                    </div>

                    <?php if (empty($pacientesFiltrados)): ?>
                        <div class="no-results">
                            <i class="fas fa-users"></i>
                            <p>Nenhum paciente ativo encontrado</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="patients-table">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Idade</th>
                                        <th>Responsável</th>
                                        <th>Contato</th>
                                        <th>Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pacientesFiltrados as $paciente): ?>
                                    <tr>
                                        <td>
                                            <div class="patient-cell">
                                                <div class="patient-avatar-small">
                                                    <?php if (!empty($paciente['foto'])): ?>
                                                        <img src="<?php echo $paciente['foto']; ?>" alt="<?php echo htmlspecialchars($paciente['nome']); ?>">
                                                    <?php else: ?>
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($paciente['nome']); ?>&background=random" alt="">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="patient-name">
                                                    <strong><?php echo htmlspecialchars($paciente['nome']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $paciente['idade']; ?></td>
                                        <td><?php echo htmlspecialchars($paciente['responsavel']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['telefone']); ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($paciente['data_cadastro'])) {
                                                echo date('d/m/Y', strtotime($paciente['data_cadastro']));
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="?paciente_id=<?php echo $paciente['id']; ?>" 
                                               class="btn-action view" 
                                               style="text-decoration: none; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-plus"></i> Nova Evolução
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <!-- ===== MODO FORMULÁRIO ===== -->
                
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="painel_evolucoes.php">Evoluções</a> > 
                    <span>Nova Evolução</span>
                </div>

                <!-- Título da Página -->
                <h1 class="page-title">
                    <i class="fas fa-chart-line"></i>
                    Nova Evolução - <?php echo htmlspecialchars($paciente_selecionado['nome'] ?? ''); ?>
                </h1>

                <!-- Card de Identificação do Paciente -->
                <div class="card-identificacao">
                    <h2 class="section-title">
                        <i class="fas fa-id-card"></i>
                        Identificação do Paciente
                    </h2>
                    
                    <div class="identificacao-content">
                        <p><span class="label">Nome:</span> <?php echo htmlspecialchars($paciente_selecionado['nome'] ?? ''); ?></p>
                        <p><span class="label">Idade:</span> <?php echo $paciente_selecionado['idade'] ?? ''; ?></p>
                        <p><span class="label">Responsável:</span> <?php echo htmlspecialchars($paciente_selecionado['responsavel'] ?? ''); ?></p>
                        <p><span class="label">Contato:</span> <?php echo htmlspecialchars($paciente_selecionado['telefone'] ?? ''); ?></p>
                    </div>
                </div>

                <!-- Formulário de Evolução -->
                <form class="evolution-form" method="POST" action="salvar_evolucao.php" enctype="multipart/form-data">
                    <input type="hidden" name="paciente_id" value="<?php echo $_GET['paciente_id']; ?>">
                    <input type="hidden" name="paciente_nome" value="<?php echo htmlspecialchars($paciente_selecionado['nome'] ?? ''); ?>">
                    
                    <!-- Dados da Sessão -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Dados da Sessão
                        </h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="data_sessao">Data da Sessão:</label>
                                <input type="date" id="data_sessao" name="data_sessao" class="form-input" value="<?php echo $data_atual; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="turno">Turno:</label>
                                <select id="turno" name="turno" class="form-input" required>
                                    <option value="">Selecione o turno</option>
                                    <?php foreach ($turnos as $turno): ?>
                                        <option value="<?php echo $turno; ?>"><?php echo $turno; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="horario_inicio">Horário Início:</label>
                                <input type="time" id="horario_inicio" name="horario_inicio" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="horario_fim">Horário Fim:</label>
                                <input type="time" id="horario_fim" name="horario_fim" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="terapia">Terapia:</label>
                                <select id="terapia" name="terapia" class="form-input" required>
                                    <option value="">Selecione a terapia</option>
                                    <?php foreach ($terapias as $valor => $nome): ?>
                                        <option value="<?php echo $valor; ?>"><?php echo $nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Condição Apresentada -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-heartbeat"></i>
                            Condição Apresentada no Atendimento
                        </h2>
                        <textarea class="form-textarea" name="condicao" placeholder="Descrever brevemente o estado geral, comportamento, queixas, respostas etiológicas ou físicas observadas durante a sessão..."></textarea>
                    </div>

                    <!-- Materiais e Recursos -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-tools"></i>
                            Materiais e Recursos Aplicados
                        </h2>
                        <textarea class="form-textarea" name="materiais" placeholder="Listar instrumentos, atividades, materiais lúdicos, jogos, textos ou recursos utilizados..."></textarea>
                    </div>

                    <!-- Estratégias e Métodos -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-brain"></i>
                            Estratégias, Métodos e Abordagens Utilizadas
                        </h2>
                        <textarea class="form-textarea" name="estrategias" placeholder="Descrever técnicas, métodos terapêuticos, intervenções ou estratégias aplicadas durante a sessão..."></textarea>
                    </div>

                    <!-- Descrição da Evolução -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-chart-line"></i>
                            Descrição da Evolução e Intervenções Realizadas
                        </h2>
                        <textarea class="form-textarea" name="descricao" placeholder="Relatar desfechos relevantes e que foi realizado, respectando os requisitos, progresso observados, dificuldades correntes..."></textarea>
                    </div>

                    <!-- Observações Complementares -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Observações Complementares
                        </h2>
                        <textarea class="form-textarea" name="observacoes" placeholder="..."></textarea>
                    </div>

                    <!-- Anexos -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-paperclip"></i>
                            Anexos
                        </h2>
                        
                        <div class="anexos-container">
                            <div class="anexar-link">
                                <i class="fas fa-paperclip"></i>
                                <a href="#" onclick="document.getElementById('fotos-upload').click(); return false;">Anexar fotos e documentos...</a>
                                <input type="file" id="fotos-upload" name="fotos[]" multiple accept="image/*,application/pdf" style="display: none;">
                            </div>

                            <div id="anexos-lista" class="anexos-lista">
                                <!-- Anexos serão adicionados aqui via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé do Formulário -->
                    <div class="form-footer">
                        <span class="clinic-label">CLÍNICA</span>
                        <div class="action-buttons">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Salvar Evolução
                            </button>
                            <button type="button" class="btn-cancel" onclick="window.location.href='painel_evolucoes.php'">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <script src="../../js/dashboard/clinica/painel_evolucoes.js"></script>
</body>
</html>