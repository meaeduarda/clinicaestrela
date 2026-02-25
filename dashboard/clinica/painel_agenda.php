<?php
// dashboard/clinica/painel_agenda.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title>Agenda - Clínica Estrela</title>

    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_agenda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos específicos para a agenda - SEM KPI CARDS */
        .main-content {
            padding-top: 20px;
        }
        
        .main-top {
            margin-bottom: 20px;
        }
        
        /* Container principal da agenda - ocupando toda a largura */
        .agenda-full-container {
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        /* Cabeçalho da agenda */
        .agenda-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        
        .agenda-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .agenda-title h2 {
            font-size: 1.5rem;
            color: #0f172a;
            margin: 0;
            font-weight: 600;
        }
        
        .agenda-title i {
            color: #3b82f6;
            font-size: 2rem;
        }
        
        .agenda-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-agenda {
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.9rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .btn-agenda-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-agenda-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .btn-agenda-secondary {
            background: white;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        
        .btn-agenda-secondary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Container do Google Calendar - TELA CHEIA */
        .google-calendar-full {
            width: 100%;
            height: 80vh; /* 80% da altura da viewport */
            min-height: 700px;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }
        
        .google-calendar-full iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        /* Badge de visitas */
        .visitas-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
            animation: pulse 2s infinite;
            border: 2px solid white;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        
        .icon-btn.with-badge {
            position: relative;
            text-decoration: none;
            color: inherit;
        }
        
        /* Seletor de visualização */
        .view-selector {
            display: flex;
            gap: 4px;
            background: white;
            padding: 4px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .view-option {
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            color: #64748b;
        }
        
        .view-option.active {
            background: #3b82f6;
            color: white;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }
        
        .view-option:hover:not(.active) {
            background: #f1f5f9;
            color: #334155;
        }
        
        /* Controles de navegação do mês */
        .month-navigation {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
        }
        
        .month-nav-btn {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #475569;
        } 
        
        .current-month {
            font-weight: 600;
            color: #0f172a;
            min-width: 150px;
            text-align: center;
        }
        
        /* Responsividade */
        @media (max-width: 1024px) {
            .agenda-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .agenda-actions {
                width: 100%;
                flex-wrap: wrap;
            }
            
            .view-selector {
                flex: 1;
            }
            
            .month-navigation {
                margin-left: 0;
                width: 100%;
                justify-content: space-between;
            }
        }
        
        @media (max-width: 768px) {
            .agenda-header {
                padding: 16px;
            }
            
            .agenda-title h2 {
                font-size: 1.2rem;
            }
            
            .agenda-actions {
                flex-direction: column;
            }
            
            .view-selector {
                width: 100%;
                justify-content: center;
            }
            
            .view-option {
                flex: 1;
                text-align: center;
                padding: 8px 4px;
                font-size: 0.8rem;
            }
            
            .btn-agenda {
                width: 100%;
                justify-content: center;
            }
            
            .month-navigation {
                flex-wrap: wrap;
            }
            
            .google-calendar-full {
                height: 70vh;
                min-height: 500px;
            }
        }
        
        @media (max-width: 480px) {
            .google-calendar-full {
                height: 60vh;
                min-height: 400px;
            }
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
                    <li><a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                    <?php if ($perfilLogado !== 'recepcionista'): ?>
                        <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_planoterapeutico.php"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                        <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                        <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_evolucoes.php"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <?php endif; ?>
                    <li class="active"><a href="painel_agenda.php"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_salas.php"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a></li>
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
                <h2><i class="fas fa-calendar-alt"></i> Agenda</h2>
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

            <!-- Container FULL da Agenda - SEM KPI CARDS -->
            <div class="agenda-full-container">
                <div class="agenda-header">

                    
                    <div class="agenda-actions">
                        <!-- Navegação do mês -->
                        
                        <a href="http://localhost/clinicaestrela/dashboard/clinica/painel_agenda.php" class="btn-agenda btn-agenda-primary" onclick="reloadCalendar(); return false;">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </a>
                        
                        <a href="https://calendar.google.com" target="_blank" class="btn-agenda btn-agenda-secondary">
                            <i class="fas fa-external-link-alt"></i> Abrir no Google
                        </a>
                    </div>
                </div>
                
                <!-- Google Calendar Iframe  -->
                <div class="google-calendar-full">
                    <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=America%2FRecife&showPrint=0&src=bWVhZWR1YXJkYWRldmdyYXVAZ21haWwuY29t&src=Y2xhc3Nyb29tMTEzMzg5ODUyMDIzMDM5NzIxMzE3QGdyb3VwLmNhbGVuZGFyLmdvb2dsZS5jb20&src=cHQtYnIuYnJhemlsaWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&src=Y2xhc3Nyb29tMTA1ODU2MTAzMjg2NzA4MzIyOTYyQGdyb3VwLmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23039be5&color=%23c26401&color=%230b8043&color=%23b80672" style="border-width:0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>

            <!-- Instruções rápidas (opcional - pode remover se quiser) -->
            <div style="background: #f8fafc; border-radius: 12px; padding: 15px 20px; border: 1px solid #e2e8f0; margin-top: 10px;">
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                        <span style="color: #475569; font-size: 0.9rem;">Todos os eventos aparecem com suas cores originais</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-paint-brush" style="color: #8b5cf6;"></i>
                        <span style="color: #475569; font-size: 0.9rem;">Calendários coloridos por categoria</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-bell" style="color: #f59e0b;"></i>
                        <span style="color: #475569; font-size: 0.9rem;">Notificações e lembretes ativos</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
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

        // Atualizar display do mês atual
        updateMonthDisplay();
    });

    // Função para recarregar o calendário
    function reloadCalendar() {
        const iframe = document.getElementById('googleCalendar');
        iframe.src = iframe.src; // Isso recarrega o iframe
    }

    // Função para mudar a visualização
    function changeView(view) {
        const iframe = document.getElementById('googleCalendar');
        let currentSrc = iframe.src;
        
        // Atualizar o parâmetro mode
        currentSrc = currentSrc.replace(/mode=\w+/, 'mode=' + view);
        iframe.src = currentSrc;
        
        // Atualizar classe ativa nos botões
        document.querySelectorAll('.view-option').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    // Função para navegar entre meses
    function navigateMonth(direction) {
        const iframe = document.getElementById('googleCalendar');
        let currentSrc = iframe.src;
        
        // O Google Calendar usa parâmetros para navegação
        // Como não temos acesso direto, recarregamos e deixamos o Google lidar
        iframe.src = currentSrc;
        
        // Pequena dica: podemos tentar extrair a data atual do iframe
        // Mas o Google já gerencia isso internamente
    }

    // Função para ir para o dia atual
    function goToToday() {
        const iframe = document.getElementById('googleCalendar');
        let currentSrc = iframe.src;
        
        // Remover parâmetros de data se existirem e recarregar
        // O Google mostra o dia atual por padrão
        iframe.src = currentSrc.split('?')[0] + '?src=meaeduardadevgrau%40gmail.com&ctz=America%2FRecife&mode=month';
    }

    // Função para atualizar o display do mês (apenas visual)
    function updateMonthDisplay() {
        const months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];
        const now = new Date();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();
        
        const display = document.getElementById('currentMonthDisplay');
        if (display) {
            display.textContent = `${monthName} de ${year}`;
        }
    }

    // Função para alternar entre contas (se necessário)
    function switchCalendar(calendarId) {
        const iframe = document.getElementById('googleCalendar');
        let currentSrc = iframe.src;
        
        // Substituir o src do calendário
        currentSrc = currentSrc.replace(/src=[^&]+/, 'src=' + encodeURIComponent(calendarId));
        iframe.src = currentSrc;
    }
    </script>
</body>
</html>