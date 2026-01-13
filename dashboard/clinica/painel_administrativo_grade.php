<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Grade Terapeutica</title>
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_administrativo_grade.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR FIXA -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-icon">
                    <img src="../../imagens/logo_clinica_estrela.png" alt="Logo Clínica Estrela" class="logo-img">
                </div>
                <h1>Clinica Estrela</h1>
            </div>

            <!-- Menu de Navegação -->
            <nav class="menu">
                <ul>
                    <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_administrativo_pacientes.php"><i class="fas fa-users"></i> <span>Pacientes</span></a></li>
                    <li><a href="#"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Ativos</span></a></li>
                    <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Altas</span></a></li>
                    <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                    <li class="active"><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_administrativo_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
                </ul>
            </nav>

            <!-- Usuário Logado -->
            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Maria">
                </div>
                <div class="user-details">
                    <h3>Maria</h3>
                    <p>Coordenadora</p>
                </div>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="main-content">
            <!-- Topo do Container -->
            <div class="main-top">
                <h1>Painel Administrativo</h1>
                <div class="top-icons">
                    <div class="icon-btn">
                        <i class="fas fa-search"></i>
                    </div>
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

            <!-- Barra de Ações -->
            <div class="action-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar paciente.">
                </div>
                <button class="btn-therapy">Grade Terapêutica</button>
            </div>

            <!-- Cards de Indicadores -->
            <div class="kpi-cards">
                <div class="kpi-card blue">
                    <div class="kpi-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="kpi-content">
                        <h3>254</h3>
                        <p>Pacientes Ativos</p>
                    </div>
                </div>
                
                <div class="kpi-card green">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="kpi-content">
                        <h3>12</h3>
                        <p>Sessões Hoje</p>
                    </div>
                </div>
                
                <div class="kpi-card yellow">
                    <div class="kpi-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="kpi-content">
                        <h3>5</h3>
                        <p>Pacientes Pendentes</p>
                    </div>
                </div>
                
                <div class="kpi-card pink">
                    <div class="kpi-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="kpi-content">
                        <h3>3</h3>
                        <p>Lembretes</p>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Central em Duas Colunas -->
            <div class="content-grid">
                <!-- Coluna Esquerda - Grade Terapêutica ÚNICA -->
                <div class="left-column">
                    <!-- Grade Terapêutica Única e Integrada -->
                    <div class="therapy-card">
                        <div class="card-header">
                            <h2>Grade Terapêutica</h2>
                            <div class="header-controls">
                                <div class="filters">
                                    <select id="turnoFilter">
                                        <option value="manha">Manhã</option>
                                        <option value="tarde">Tarde</option>
                                    </select>
                                    <select id="terapiaFilter">
                                        <option value="todas">Todas as terapias</option>
                                        <option value="aba">ABA</option>
                                        <option value="to">TO</option>
                                        <option value="fono">Fonoaudiologia</option>
                                        <option value="psicologia">Psicologia</option>
                                        <option value="musica">Musicoterapia</option>
                                        <option value="nutricao">Nutrição</option>
                                        <option value="aquatica">Estimulação Aquática</option>
                                        <option value="fisioterapia">Fisioterapia</option>
                                    </select>
                                </div>
                                <button class="btn-edit">Editar Grade</button>
                            </div>
                        </div>
                        
                        <div class="schedule-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="time-col">Horário</th>
                                        <th>Segunda</th>
                                        <th>Terça</th>
                                        <th>Quarta</th>
                                        <th>Quinta</th>
                                        <th>Sexta</th>
                                        <th>Sábado</th>
                                    </tr>
                                </thead>
                                <tbody id="horariosManha">
                                    <!-- Horários da Manhã (8:00 - 12:00) -->
                                    <tr>
                                        <td class="time-col">08:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">João João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">09:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">Maria Maria</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">João João</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">10:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">Maria Maria</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">João João</span>
                                            </div>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session fono">
                                                <span class="type">Fono</span>
                                                <span class="name">Ana Silva</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div class="session psicologia">
                                                <span class="type">Psico</span>
                                                <span class="name">Carlos Santos</span>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">11:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">Maria Maria</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session musica">
                                                <span class="type">ABA</span>
                                                <span class="name">Maria Maria</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">12:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">João João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session nutricao">
                                                <span class="type">Nutri</span>
                                                <span class="name">Fernanda Alves</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                
                                <tbody id="horariosTarde" style="display: none;">
                                    <!-- Horários da Tarde (13:00 - 17:00) -->
                                    <tr>
                                        <td class="time-col">13:00</td>
                                        <td>
                                            <div class="session fisioterapia">
                                                <span class="type">Fisio</span>
                                                <span class="name">Rafael Costa</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">14:00</td>
                                        <td>
                                            <div class="session aquatica">
                                                <span class="type">Aquática</span>
                                                <span class="name">Lucas Mendes</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session psicologia">
                                                <span class="type">Psico</span>
                                                <span class="name">Sofia Costa</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div class="session fono">
                                                <span class="type">Fono</span>
                                                <span class="name">Gabriel Lima</span>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">15:00</td>
                                        <td>
                                            <div class="session aba">
                                                <span class="type">ABA</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">João João</span>
                                            </div>
                                            <div class="session to">
                                                <span class="type">TO</span>
                                                <span class="name">Pedro João</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">16:00</td>
                                        <td>
                                            <div class="session fono">
                                                <span class="type">Fono</span>
                                                <span class="name">Ana Silva</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session musica">
                                                <span class="type">Música</span>
                                                <span class="name">Beatriz Lima</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session psicologia">
                                                <span class="type">Psico</span>
                                                <span class="name">Carlos Santos</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div class="session musica">
                                                <span class="type">Música</span>
                                                <span class="name">Beatriz Lima</span>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="time-col">17:00</td>
                                        <td>
                                            <div class="session fisioterapia">
                                                <span class="type">Fisio</span>
                                                <span class="name">Rafael Costa</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session nutricao">
                                                <span class="type">Nutri</span>
                                                <span class="name">Fernanda Alves</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div class="session aquatica">
                                                <span class="type">Aquática</span>
                                                <span class="name">Lucas Mendes</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita - Pacientes e Lembretes -->
                <div class="right-column">
                    <!-- Card de Pacientes -->
                    <div class="patients-card">
                        <div class="card-header">
                            <h2>Pacientes Pendentes</h2>
                            <a href="#" class="see-all">Ver todos</a>
                        </div>
                        
                        <div class="patients-list">
                            <div class="patient-item">
                                <div class="patient-avatar">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="João Silva">
                                </div>
                                <div class="patient-info">
                                    <h4>João Silva</h4>
                                    <p class="age">5 anos</p>
                                    <p class="responsible">Ana de Souza</p>
                                </div>
                                <button class="btn-analyze">Analisar</button>
                            </div>
                            
                            <div class="patient-item">
                                <div class="patient-avatar">
                                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sofia Santos">
                                </div>
                                <div class="patient-info">
                                    <h4>Sofia Santos</h4>
                                    <p class="age">7 anos</p>
                                    <p class="responsible">Marcos Santos</p>
                                </div>
                                <button class="btn-analyze">Analisar</button>
                            </div>
                            
                            <div class="patient-item">
                                <div class="patient-avatar">
                                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Pedro Almeida">
                                </div>
                                <div class="patient-info">
                                    <h4>Pedro Almeida</h4>
                                    <p class="age">6 anos</p>
                                    <p class="responsible">Fernanda Almeida</p>
                                </div>
                                <button class="btn-analyze">Analisar</button>
                            </div>
                            
                            <!-- Pacientes adicionais para scroll -->
                            <div class="patient-item">
                                <div class="patient-avatar">
                                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Ana Costa">
                                </div>
                                <div class="patient-info">
                                    <h4>Ana Costa</h4>
                                    <p class="age">8 anos</p>
                                    <p class="responsible">Roberto Costa</p>
                                </div>
                                <button class="btn-analyze">Analisar</button>
                            </div>
                            
                            <div class="patient-item">
                                <div class="patient-avatar">
                                    <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Lucas Oliveira">
                                </div>
                                <div class="patient-info">
                                    <h4>Lucas Oliveira</h4>
                                    <p class="age">9 anos</p>
                                    <p class="responsible">Patrícia Oliveira</p>
                                </div>
                                <button class="btn-analyze">Analisar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Lembretes -->
                    <div class="reminders-card">
                        <div class="card-header">
                            <h2>Lembretes</h2>
                            <a href="#" class="see-all">Ver todos</a>
                        </div>
                        
                        <div class="reminders-list">
                            <div class="reminder-item">
                                <div class="reminder-icon" style="background-color: #FF6B8B;">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="reminder-content">
                                    <p class="reminder-text">Solicitar material de escritório</p>
                                    <p class="reminder-subtext">Almoxarifado</p>
                                </div>
                                <button class="btn-small">Analisar</button>
                            </div>
                            
                            <div class="reminder-item">
                                <div class="reminder-icon" style="background-color: #6BD4FF;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="reminder-content">
                                    <p class="reminder-text">Gerar relatório mensal</p>
                                    <p class="reminder-subtext">Fechamento</p>
                                </div>
                                <span class="badge-tag">10/Maio</span>
                            </div>
                            
                            <div class="reminder-item">
                                <div class="reminder-icon" style="background-color: #FFD36B;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="reminder-content">
                                    <p class="reminder-text">Reunião de equipe</p>
                                    <p class="reminder-subtext">Sala 3, 15:00</p>
                                </div>
                                <span class="badge-tag today">Hoje</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Scripts para interatividade
        document.addEventListener('DOMContentLoaded', function() {
            // Hover nos cards de KPI
            const kpiCards = document.querySelectorAll('.kpi-card');
            kpiCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Hover nas sessões
            const sessions = document.querySelectorAll('.session');
            sessions.forEach(session => {
                session.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.opacity = '0.9';
                });
                session.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.opacity = '1';
                });
            });

            // Efeito nos botões
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Botão editar grade
            const editBtn = document.querySelector('.btn-edit');
            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    alert('Funcionalidade de edição será implementada');
                });
            }

            // Filtro de turno
            const turnoFilter = document.getElementById('turnoFilter');
            const horariosManha = document.getElementById('horariosManha');
            const horariosTarde = document.getElementById('horariosTarde');
            
            turnoFilter.addEventListener('change', function() {
                if (this.value === 'manha') {
                    horariosManha.style.display = 'table-row-group';
                    horariosTarde.style.display = 'none';
                } else {
                    horariosManha.style.display = 'none';
                    horariosTarde.style.display = 'table-row-group';
                }
                aplicarFiltroTerapia();
            });

            // Filtro de terapia
            const terapiaFilter = document.getElementById('terapiaFilter');
            
            function aplicarFiltroTerapia() {
                const tipoSelecionado = terapiaFilter.value;
                const turnoAtual = turnoFilter.value;
                const tbodyAtual = turnoAtual === 'manha' ? horariosManha : horariosTarde;
                
                // Mostrar todas as sessões primeiro
                const todasSessoes = tbodyAtual.querySelectorAll('.session');
                todasSessoes.forEach(sessao => {
                    sessao.style.display = 'flex';
                });
                
                // Se não for "todas", filtrar
                if (tipoSelecionado !== 'todas') {
                    todasSessoes.forEach(sessao => {
                        const tipoSessao = sessao.classList[1]; // Pega a segunda classe (aba, to, fono, etc.)
                        if (tipoSessao !== tipoSelecionado) {
                            sessao.style.display = 'none';
                        }
                    });
                    
                    // Também esconder células vazias ou com todas sessões escondidas
                    const celulas = tbodyAtual.querySelectorAll('td');
                    celulas.forEach(celula => {
                        if (celula.classList.contains('time-col')) return;
                        
                        const sessoesNaCelula = celula.querySelectorAll('.session');
                        const sessoesVisiveis = Array.from(sessoesNaCelula).filter(s => s.style.display !== 'none');
                        
                        if (sessoesVisiveis.length === 0) {
                            celula.innerHTML = '';
                        }
                    });
                }
            }
            
            terapiaFilter.addEventListener('change', aplicarFiltroTerapia);

            // Botão Grade Terapêutica na barra de ações
            const therapyBtn = document.querySelector('.btn-therapy');
            if (therapyBtn) {
                therapyBtn.addEventListener('click', function() {
                    alert('Navegando para Grade Terapêutica');
                });
            }

            // Botões Analisar nos pacientes
            const analyzeBtns = document.querySelectorAll('.btn-analyze');
            analyzeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const patientName = this.closest('.patient-item').querySelector('h4').textContent;
                    alert('Analisando paciente: ' + patientName);
                });
            });

            // Botões Analisar nos lembretes
            const smallBtns = document.querySelectorAll('.btn-small');
            smallBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const reminderText = this.closest('.reminder-item').querySelector('.reminder-text').textContent;
                    alert('Analisando lembrete: ' + reminderText);
                });
            });
        });
    </script>
</body>
</html>