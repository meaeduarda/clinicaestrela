<?php
// painel_salas.php
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

// --- LEITURA DE PACIENTES PENDENTES PARA O CONTADOR ---
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

// Função para obter a cor baseada no tipo de terapia
function getCorPorTipo($tipo) {
    $cores = [
        'ABA' => '#8b5cf6', // Roxo
        'Fono' => '#ec4899', // Rosa
        'TO' => '#f59e0b', // Amarelo
        'Funcional' => '#ef4444', // Vermelho
        'Psicomotricidade' => '#3b82f6', // Azul
        'Nutrição' => '#10b981', // Verde
        'Fisioterapia' => '#ef4444', // Vermelho
        'Fonoterapia' => '#ec4899', // Rosa
        'Psicoterapia' => '#f97316', // Laranja
        'Psicopedagogia' => '#6b7280' // Cinza
    ];
    
    return $cores[$tipo] ?? '#94a3b8';
}

// Dados das salas
$salas = [
    ['id' => 1, 'nome' => 'Sala 01 - ABA', 'capacidade' => 3, 'tipo' => 'ABA'],
    ['id' => 2, 'nome' => 'Sala 02 - Fono', 'capacidade' => 3, 'tipo' => 'Fono'],
    ['id' => 3, 'nome' => 'Sala 03 - Fono', 'capacidade' => 2, 'tipo' => 'Fono'],
    ['id' => 4, 'nome' => 'Sala 04 - ABA', 'capacidade' => 4, 'tipo' => 'ABA'],
    ['id' => 5, 'nome' => 'Sala 05 - ABA', 'capacidade' => 4, 'tipo' => 'ABA'],
    ['id' => 6, 'nome' => 'Sala 06 - TO', 'capacidade' => 2, 'tipo' => 'TO'],
    ['id' => 7, 'nome' => 'Sala 07 - TO', 'capacidade' => 2, 'tipo' => 'TO'],
    ['id' => 8, 'nome' => 'Sala 08 - Funcional', 'capacidade' => 2, 'tipo' => 'Funcional'],
    ['id' => 9, 'nome' => 'Sala 09 - Jogos', 'capacidade' => 4, 'tipo' => 'ABA'],
    ['id' => 10, 'nome' => 'Sala 10 - ABA', 'capacidade' => 2, 'tipo' => 'ABA']
];

$capacidadeTotal = array_sum(array_column($salas, 'capacidade'));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title>Painel de Salas - Clínica Estrela</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">

    <!-- Estilos CSS (mesmos do sistema) -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_paciente.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_planoterapeutico.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_evolucoes.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/evolucao_historico.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_salas.css">
    
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

        /* Estilos para notificações */
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
        
        .sala-info-confirm {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .sala-info-confirm strong {
            color: #1e293b;
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        .sala-info-confirm span {
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
        
        .btn-confirm, .btn-cancel {
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
    </style>
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
                        <a href="painel_agenda.php"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'visita_agendamento.php') ? 'class="active"' : ''; ?>>
                        <a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a>
                    </li>
                    
                    <li class="active">
                        <a href="painel_salas.php"><i class="fas fa-door-closed"></i> <span>Salas</span></a>
                    </li>
                    
                    <li <?php echo ($pagina_atual == 'login_cadastro_clinica.php') ? 'class="active"' : ''; ?>>
                        <a href="login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a>
                    </li>
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
                <h2><i class="fas fa-door-closed"></i> Salas</h2>
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

            <!-- Salas Grid -->
            <div class="salas-grid">
                <?php foreach ($salas as $sala): 
                    $cor = getCorPorTipo($sala['tipo']);
                ?>
                    <div class="sala-card" id="sala-<?php echo $sala['id']; ?>">
                        <div class="sala-color-bar" style="background-color: <?php echo $cor; ?>;"></div>
                        <div class="sala-content">
                            <h3 class="sala-nome"><?php echo $sala['nome']; ?></h3>
                            <div class="sala-info">
                                <span><i class="fas fa-user-friends"></i> Até <?php echo $sala['capacidade']; ?> pessoas</span>
                                <span class="sala-tipo" style="background-color: <?php echo $cor; ?>20; color: <?php echo $cor; ?>; border: 1px solid <?php echo $cor; ?>40;">
                                    <?php echo $sala['tipo']; ?>
                                </span>
                            </div>
                            
                            <!-- Botões de ação -->
                            <div class="sala-actions">
                                <button class="btn-action edit" onclick="editarSala(<?php echo $sala['id']; ?>)" title="Editar sala">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action delete" onclick="confirmarExclusao(<?php echo $sala['id']; ?>, '<?php echo $sala['nome']; ?>')" title="Excluir sala">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Card para Adicionar Nova Sala -->
                <div class="sala-card add-sala" onclick="abrirModalAdicionarSala()">
                    <div class="sala-color-bar" style="background-color: #9ca3af;"></div>
                    <div class="sala-content">
                        <h3 class="sala-nome">Sala 11 - Adicionar Sala</h3>
                        <div class="sala-info">
                            <span>Adicionar Sala</span>
                            <div class="btn-add-sala">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para Adicionar/Editar Sala -->
            <div class="modal" id="modalSala">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modal-titulo"><i class="fas fa-plus-circle"></i> Adicionar Nova Sala</h3>
                        <button class="modal-close" onclick="fecharModalSala()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formSala" onsubmit="event.preventDefault(); salvarSala();">
                            <input type="hidden" id="salaId" name="salaId" value="">
                            
                            <div class="form-group">
                                <label for="salaNumero"><i class="fas fa-hashtag"></i> Número da Sala</label>
                                <input type="number" id="salaNumero" class="form-control" placeholder="Ex: 11" required>
                            </div>
                            <div class="form-group">
                                <label for="salaNome"><i class="fas fa-tag"></i> Nome da Sala</label>
                                <input type="text" id="salaNome" class="form-control" placeholder="Ex: Sala 11 - ABA" required>
                            </div>
                            <div class="form-group">
                                <label for="salaCapacidade"><i class="fas fa-user-friends"></i> Capacidade (pessoas)</label>
                                <input type="number" id="salaCapacidade" class="form-control" placeholder="Ex: 4" min="1" max="20" required>
                            </div>
                            <div class="form-group">
                                <label for="salaTipo"><i class="fas fa-stethoscope"></i> Tipo de Terapia</label>
                                <select id="salaTipo" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option value="ABA">ABA (Roxo)</option>
                                    <option value="Fono">Fono (Rosa)</option>
                                    <option value="TO">TO (Amarelo)</option>
                                    <option value="Funcional">Funcional (Vermelho)</option>
                                    <option value="Psicomotricidade">Psicomotricidade (Azul)</option>
                                    <option value="Nutrição">Nutrição (Verde)</option>
                                    <option value="Fisioterapia">Fisioterapia (Vermelho)</option>
                                    <option value="Fonoterapia">Fonoterapia (Rosa)</option>
                                    <option value="Psicoterapia">Psicoterapia (Laranja)</option>
                                    <option value="Psicopedagogia">Psicopedagogia (Cinza)</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" onclick="fecharModalSala()">Cancelar</button>
                        <button class="btn-save" onclick="salvarSala()" id="btnSalvarSala">Salvar Sala</button>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmação de Exclusão -->
            <div class="confirmation-modal" id="confirmacaoModal">
                <div class="confirmation-modal-content">
                    <div class="confirmation-modal-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h3>
                    </div>
                    <div class="confirmation-modal-body">
                        <p>Tem certeza que deseja excluir esta sala? Esta ação não pode ser desfeita.</p>
                        <div class="sala-info-confirm">
                            <strong id="sala-nome-confirm"></strong>
                            <span>ID: <span id="sala-id-confirm"></span></span>
                        </div>
                        <p><small><i class="fas fa-info-circle"></i> Todos os dados da sala serão removidos permanentemente.</small></p>
                    </div>
                    <div class="confirmation-modal-footer">
                        <button class="btn-cancel" id="cancelarExclusao">Cancelar</button>
                        <button class="btn-confirm" id="confirmarExclusao">Excluir Permanentemente</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Variáveis globais
        let salaParaExcluir = null;
        let salas = <?php echo json_encode($salas); ?>;
        let proximoId = <?php echo count($salas) + 1; ?>;

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

            // Efeitos nos cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Fechar modal com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    fecharModalSala();
                    fecharConfirmacaoModal();
                }
            });

            // Fechar modal clicando fora
            const modalSala = document.getElementById('modalSala');
            modalSala.addEventListener('click', function(e) {
                if (e.target === this) {
                    fecharModalSala();
                }
            });

            // Fechar modal de confirmação clicando fora
            const confirmacaoModal = document.getElementById('confirmacaoModal');
            confirmacaoModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    fecharConfirmacaoModal();
                }
            });

            // Botões do modal de confirmação
            document.getElementById('cancelarExclusao').addEventListener('click', fecharConfirmacaoModal);
            document.getElementById('confirmarExclusao').addEventListener('click', function() {
                if (salaParaExcluir) {
                    excluirSala(salaParaExcluir.id);
                }
            });
        });

        // Funções do Modal de Sala
        function abrirModalAdicionarSala() {
            document.getElementById('modal-titulo').innerHTML = '<i class="fas fa-plus-circle"></i> Adicionar Nova Sala';
            document.getElementById('salaId').value = '';
            document.getElementById('salaNumero').value = proximoId;
            document.getElementById('salaNome').value = '';
            document.getElementById('salaCapacidade').value = '';
            document.getElementById('salaTipo').value = '';
            document.getElementById('modalSala').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function editarSala(id) {
            const sala = salas.find(s => s.id === id);
            if (!sala) return;
            
            document.getElementById('modal-titulo').innerHTML = '<i class="fas fa-edit"></i> Editar Sala';
            document.getElementById('salaId').value = sala.id;
            document.getElementById('salaNumero').value = sala.id;
            document.getElementById('salaNome').value = sala.nome;
            document.getElementById('salaCapacidade').value = sala.capacidade;
            document.getElementById('salaTipo').value = sala.tipo;
            document.getElementById('modalSala').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function fecharModalSala() {
            document.getElementById('modalSala').classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('formSala').reset();
        }

        function salvarSala() {
            const id = document.getElementById('salaId').value;
            const numero = document.getElementById('salaNumero').value;
            const nome = document.getElementById('salaNome').value;
            const capacidade = document.getElementById('salaCapacidade').value;
            const tipo = document.getElementById('salaTipo').value;

            if (!numero || !nome || !capacidade || !tipo) {
                mostrarNotificacao('erro', 'Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            const btnSalvar = document.getElementById('btnSalvarSala');
            const textoOriginal = btnSalvar.innerHTML;
            btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            btnSalvar.disabled = true;

            setTimeout(() => {
                if (id) {
                    // Editar sala existente
                    const index = salas.findIndex(s => s.id == id);
                    if (index !== -1) {
                        salas[index] = {
                            ...salas[index],
                            nome: nome,
                            capacidade: parseInt(capacidade),
                            tipo: tipo
                        };
                    }
                    mostrarNotificacao('sucesso', 'Sala editada com sucesso!');
                } else {
                    // Adicionar nova sala
                    const novaSala = {
                        id: proximoId,
                        nome: nome,
                        capacidade: parseInt(capacidade),
                        tipo: tipo
                    };
                    salas.push(novaSala);
                    proximoId++;
                    mostrarNotificacao('sucesso', 'Sala adicionada com sucesso!');
                }
                
                fecharModalSala();
                btnSalvar.innerHTML = textoOriginal;
                btnSalvar.disabled = false;
                location.reload(); // Recarregar para mostrar as alterações
            }, 1000);
        }

        // Funções de exclusão
        function confirmarExclusao(id, nome) {
            salaParaExcluir = { id, nome };
            document.getElementById('sala-nome-confirm').textContent = nome;
            document.getElementById('sala-id-confirm').textContent = `#${id}`;
            document.getElementById('confirmacaoModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function fecharConfirmacaoModal() {
            document.getElementById('confirmacaoModal').classList.remove('active');
            document.body.style.overflow = '';
            salaParaExcluir = null;
        }

        function excluirSala(id) {
            const btnConfirmar = document.getElementById('confirmarExclusao');
            const textoOriginal = btnConfirmar.innerHTML;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
            btnConfirmar.disabled = true;

            setTimeout(() => {
                const index = salas.findIndex(s => s.id === id);
                if (index !== -1) {
                    salas.splice(index, 1);
                    
                    // Remover o card da sala
                    const salaCard = document.getElementById(`sala-${id}`);
                    if (salaCard) {
                        salaCard.style.backgroundColor = '#fee2e2';
                        salaCard.style.transition = 'all 0.3s';
                        setTimeout(() => {
                            salaCard.remove();
                            mostrarNotificacao('sucesso', 'Sala excluída com sucesso!');
                            
                            // Atualizar contadores
                            atualizarContadores();
                        }, 300);
                    }
                }
                
                fecharConfirmacaoModal();
                btnConfirmar.innerHTML = textoOriginal;
                btnConfirmar.disabled = false;
            }, 1000);
        }

        function atualizarContadores() {
            const totalSalas = salas.length;
            const capacidadeTotal = salas.reduce((acc, sala) => acc + sala.capacidade, 0);
            
            // Atualizar os cards de estatísticas
            const statCards = document.querySelectorAll('.stat-card h3');
            if (statCards.length >= 4) {
                statCards[0].textContent = totalSalas; // Total de Salas
                statCards[1].textContent = capacidadeTotal; // Capacidade Total
                statCards[3].textContent = totalSalas; // Salas Disponíveis
            }
        }

        // Função para mostrar notificações
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

        // Função para atualizar contador de visitas
        function atualizarContadorVisitas() {
            fetch('atualizar_contador_visitas.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const badgeElement = document.querySelector('.visitas-badge');
                        
                        if (data.count > 0) {
                            if (!badgeElement) {
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
                        } else {
                            if (badgeElement) {
                                badgeElement.remove();
                            }
                        }
                    }
                })
                .catch(error => console.error('Erro ao atualizar contador:', error));
        }

        setInterval(atualizarContadorVisitas, 30000);
        atualizarContadorVisitas();
    </script>
</body>
</html>