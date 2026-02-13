<?php
// painel_adm_grade.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3b82f6">
    <title>Painel - Grade Terapêutica</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_grade.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>    
    <div class="dashboard-container">
        <!-- SIDEBAR FIXA - Agora usando o header_preca.php -->
        <?php include 'includes_precadastro/header_preca.php'; ?>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="main-content">
            <!-- Topo do Container Desktop -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-table"></i> Grade Terapêutica</h2>
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
                        
                        <!-- Grade Mobile (simplificada) -->
                        <div class="schedule-mobile">
                            <!-- Segunda-feira -->
                            <div class="mobile-schedule-day">
                                <h3>Segunda-feira</h3>
                                <div class="mobile-sessions">
                                    <div class="mobile-session aba">
                                        <span class="time">08:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">João João</span>
                                    </div>
                                    <div class="mobile-session aba">
                                        <span class="time">09:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">Maria Maria</span>
                                    </div>
                                    <div class="mobile-session aba">
                                        <span class="time">10:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">Maria Maria</span>
                                    </div>
                                    <div class="mobile-session aba">
                                        <span class="time">11:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">Maria Maria</span>
                                    </div>
                                    <div class="mobile-session aba">
                                        <span class="time">12:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">Pedro João</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Terça-feira -->
                            <div class="mobile-schedule-day">
                                <h3>Terça-feira</h3>
                                <div class="mobile-sessions">
                                    <div class="mobile-session to">
                                        <span class="time">08:00</span>
                                        <span class="type">TO</span>
                                        <span class="name">Pedro João</span>
                                    </div>
                                    <div class="mobile-session to">
                                        <span class="time">09:00</span>
                                        <span class="type">TO</span>
                                        <span class="name">João João</span>
                                    </div>
                                    <div class="mobile-session to">
                                        <span class="time">10:00</span>
                                        <span class="type">TO</span>
                                        <span class="name">João João</span>
                                    </div>
                                    <div class="mobile-session musica">
                                        <span class="time">11:00</span>
                                        <span class="type">ABA</span>
                                        <span class="name">Maria Maria</span>
                                    </div>
                                    <div class="mobile-session to">
                                        <span class="time">12:00</span>
                                        <span class="type">TO</span>
                                        <span class="name">João João</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quarta-feira -->
                            <div class="mobile-schedule-day">
                                <h3>Quarta-feira</h3>
                                <div class="mobile-sessions">
                                    <div class="mobile-session fono">
                                        <span class="time">10:00</span>
                                        <span class="type">FONO</span>
                                        <span class="name">Ana Silva</span>
                                    </div>
                                    <div class="mobile-session nutricao">
                                        <span class="time">12:00</span>
                                        <span class="type">NUTRI</span>
                                        <span class="name">Fernanda Alves</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quinta-feira -->
                            <div class="mobile-schedule-day">
                                <h3>Quinta-feira</h3>
                                <div class="mobile-sessions">
                                    <div class="mobile-session aquatica">
                                        <span class="time">17:00</span>
                                        <span class="type">AQUÁTICA</span>
                                        <span class="name">Lucas Mendes</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sexta-feira -->
                            <div class="mobile-schedule-day">
                                <h3>Sexta-feira</h3>
                                <div class="mobile-sessions">
                                    <div class="mobile-session psicologia">
                                        <span class="time">10:00</span>
                                        <span class="type">PSICO</span>
                                        <span class="name">Carlos Santos</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sábado -->
                            <div class="mobile-schedule-day">
                                <h3>Sábado</h3>
                                <div class="mobile-sessions">
                                    <p class="no-sessions">Sem sessões agendadas</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grade Desktop -->
                        <div class="schedule-table">
                            <div class="table-wrapper">
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
                </div>
            </div>
        </main>
    </div>

<script>
    // Scripts para interatividade
    document.addEventListener('DOMContentLoaded', function() {
        // Menu Mobile - AGORA USANDO O MESMO DO HEADER_PRECA.PHP
        // O header_preca.php já tem seus próprios scripts de menu mobile,
        // então vamos remover os scripts duplicados e manter apenas os específicos da grade
        
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

        // Verificar se é mobile
        function isMobile() {
            return window.innerWidth <= 768;
        }

        // Dados das sessões para mobile
        const manhaSessions = {
            segunda: [
                { time: '08:00', type: 'aba', name: 'João João' },
                { time: '09:00', type: 'aba', name: 'Maria Maria' },
                { time: '10:00', type: 'aba', name: 'Maria Maria' },
                { time: '11:00', type: 'aba', name: 'Maria Maria' },
                { time: '12:00', type: 'aba', name: 'Pedro João' }
            ],
            terca: [
                { time: '08:00', type: 'to', name: 'Pedro João' },
                { time: '09:00', type: 'to', name: 'João João' },
                { time: '10:00', type: 'to', name: 'João João' },
                { time: '11:00', type: 'musica', name: 'Maria Maria' },
                { time: '12:00', type: 'to', name: 'João João' }
            ],
            quarta: [
                { time: '10:00', type: 'fono', name: 'Ana Silva' },
                { time: '12:00', type: 'nutricao', name: 'Fernanda Alves' }
            ],
            quinta: [],
            sexta: [
                { time: '10:00', type: 'psicologia', name: 'Carlos Santos' }
            ],
            sabado: []
        };

        const tardeSessions = {
            segunda: [
                { time: '13:00', type: 'fisioterapia', name: 'Rafael Costa' },
                { time: '14:00', type: 'aquatica', name: 'Lucas Mendes' },
                { time: '15:00', type: 'aba', name: 'Pedro João' },
                { time: '16:00', type: 'fono', name: 'Ana Silva' },
                { time: '17:00', type: 'fisioterapia', name: 'Rafael Costa' }
            ],
            terca: [
                { time: '13:00', type: 'to', name: 'Pedro João' },
                { time: '14:00', type: 'to', name: 'Pedro João' },
                { time: '15:00', type: 'to', name: 'João João' },
                { time: '16:00', type: 'musica', name: 'Beatriz Lima' },
                { time: '17:00', type: 'nutricao', name: 'Fernanda Alves' }
            ],
            quarta: [
                { time: '14:00', type: 'psicologia', name: 'Sofia Costa' },
                { time: '16:00', type: 'psicologia', name: 'Carlos Santos' }
            ],
            quinta: [
                { time: '17:00', type: 'aquatica', name: 'Lucas Mendes' }
            ],
            sexta: [
                { time: '14:00', type: 'fono', name: 'Gabriel Lima' },
                { time: '16:00', type: 'musica', name: 'Beatriz Lima' }
            ],
            sabado: []
        };

        // Filtro de turno
        const turnoFilter = document.getElementById('turnoFilter');
        const horariosManha = document.getElementById('horariosManha');
        const horariosTarde = document.getElementById('horariosTarde');
        
        // Função para atualizar grade mobile
        function updateMobileSchedule(turno) {
            const scheduleMobile = document.querySelector('.schedule-mobile');
            const sessionsData = turno === 'manha' ? manhaSessions : tardeSessions;
            
            // Limpar grade mobile atual
            scheduleMobile.innerHTML = '';
            
            // Dias da semana
            const dias = [
                { key: 'segunda', nome: 'Segunda-feira' },
                { key: 'terca', nome: 'Terça-feira' },
                { key: 'quarta', nome: 'Quarta-feira' },
                { key: 'quinta', nome: 'Quinta-feira' },
                { key: 'sexta', nome: 'Sexta-feira' },
                { key: 'sabado', nome: 'Sábado' }
            ];
            
            // Criar grade para cada dia
            dias.forEach(dia => {
                const daySessions = sessionsData[dia.key];
                
                const dayElement = document.createElement('div');
                dayElement.className = 'mobile-schedule-day';
                
                let sessionsHTML = '';
                
                if (daySessions.length > 0) {
                    sessionsHTML = '<div class="mobile-sessions">';
                    
                    daySessions.forEach(sessao => {
                        sessionsHTML += `
                            <div class="mobile-session ${sessao.type}">
                                <span class="time">${sessao.time}</span>
                                <span class="type">${sessao.type.toUpperCase()}</span>
                                <span class="name">${sessao.name}</span>
                            </div>
                        `;
                    });
                    
                    sessionsHTML += '</div>';
                } else {
                    sessionsHTML = `
                        <div class="mobile-sessions">
                            <p class="no-sessions">Sem sessões agendadas</p>
                        </div>
                    `;
                }
                
                dayElement.innerHTML = `
                    <h3>${dia.nome}</h3>
                    ${sessionsHTML}
                `;
                
                scheduleMobile.appendChild(dayElement);
            });
        }

        // Função para aplicar filtro de terapia no mobile
        function applyMobileTherapyFilter(tipoSelecionado) {
            const mobileSessions = document.querySelectorAll('.mobile-session');
            
            mobileSessions.forEach(session => {
                const sessionType = session.classList[1]; // aba, to, fono, etc.
                
                if (tipoSelecionado === 'todas' || sessionType === tipoSelecionado) {
                    session.style.display = 'flex';
                } else {
                    session.style.display = 'none';
                }
            });
            
            // Esconder dias vazios
            document.querySelectorAll('.mobile-schedule-day').forEach(day => {
                const visibleSessions = day.querySelectorAll('.mobile-session[style*="display: flex"]');
                const noSessions = day.querySelector('.no-sessions');
                
                if (visibleSessions.length === 0 && !noSessions) {
                    day.querySelector('.mobile-sessions').innerHTML = 
                        '<p class="no-sessions">Sem sessões agendadas</p>';
                }
            });
        }

        // Filtro de terapia
        const terapiaFilter = document.getElementById('terapiaFilter');
        
        function aplicarFiltroTerapia() {
            const tipoSelecionado = terapiaFilter.value;
            const turnoAtual = turnoFilter.value;
            
            if (isMobile()) {
                // Aplicar filtro na grade mobile
                applyMobileTherapyFilter(tipoSelecionado);
            } else {
                // Código existente para desktop
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
        }
        
        // Event listener para filtro de turno
        turnoFilter.addEventListener('change', function() {
            if (isMobile()) {
                // Atualizar grade mobile com o novo turno
                updateMobileSchedule(this.value);
                // Reaplicar filtro de terapia se houver
                aplicarFiltroTerapia();
            } else {
                // Código existente para desktop
                if (this.value === 'manha') {
                    horariosManha.style.display = 'table-row-group';
                    horariosTarde.style.display = 'none';
                } else {
                    horariosManha.style.display = 'none';
                    horariosTarde.style.display = 'table-row-group';
                }
                aplicarFiltroTerapia();
            }
        });
        
        // Event listener para filtro de terapia
        terapiaFilter.addEventListener('change', aplicarFiltroTerapia);

        // Verificar se é mobile e ajustar interface inicialmente
        function checkMobile() {
            if (isMobile()) {
                document.querySelector('.schedule-table').style.display = 'none';
                document.querySelector('.schedule-mobile').style.display = 'block';
                // Carregar grade inicial (manhã)
                updateMobileSchedule('manha');
            } else {
                document.querySelector('.schedule-table').style.display = 'block';
                document.querySelector('.schedule-mobile').style.display = 'none';
            }
        }
        
        // Executar ao carregar e ao redimensionar
        checkMobile();
        window.addEventListener('resize', function() {
            checkMobile();
            // Recarregar grade mobile se necessário
            if (isMobile()) {
                updateMobileSchedule(turnoFilter.value);
                aplicarFiltroTerapia();
            }
        });

        // Botão editar grade
        const editBtn = document.querySelector('.btn-edit');
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                alert('Funcionalidade de edição será implementada');
            });
        }

        // Botão Grade Terapêutica na barra de ações
        const therapyBtn = document.querySelector('.btn-therapy');
        if (therapyBtn) {
            therapyBtn.addEventListener('click', function() {
                alert('Navegando para Grade Terapêutica');
            });
        }
    });
</script>
</body>
</html>