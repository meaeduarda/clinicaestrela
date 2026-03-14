<?php
// painel_adm_grade.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'] ?? 'Administrador';
$perfilLogado = $_SESSION['usuario_perfil'] ?? 'admin';
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
        <!-- SIDEBAR FIXA  -->
        <?php include 'includes_precadastro/header_preca.php'; ?>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="main-content">
            <!-- Topo do Container Desktop -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-table"></i> Grade Terapêutica</h2>
                <div class="top-icons">
                    <button class="icon-btn" id="fullscreenBtn" title="Tela Cheia">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
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
                    
                    <!-- CONTAINER DE SCROLL ÚNICO PARA A GRADE UNIFICADA -->
                    <div class="grade-scroll-container" id="gradeScrollContainer">
                        <div class="grade-unified">
                            <!-- PRIMEIRA SEÇÃO: SALAS 1 A 10 (SEM TÍTULO) -->
                            <table class="grade-table section1" id="gradeTableSection1">
                                <thead>
                                    <tr>
                                        <th class="time-col">Horário</th>
                                        <th>FONO 1</th>
                                        <th>FONO 2</th>
                                        <th>FONO 3</th>
                                        <th>FONO 4</th>
                                        <th>FONO 5</th>
                                        <th>ABA 1</th>
                                        <th>ABA 2</th>
                                        <th>PSICOTERAPIA</th>
                                        <th>MUSICOTERAPIA</th>
                                        <th>PSICOLOGIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Horário 07:50 - 08:20 -->
                                    <tr>
                                        <td class="time-col">07:50 - 08:20</td>
                                        <td><div class="session fono"><span class="name">João Pedro</span><span class="professional">Dra. Maria</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Ana Clara</span><span class="professional">Dr. Carlos</span></div></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Pedro Lucas</span><span class="professional">Dra. Sofia</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session musica"><span class="name">Laura Beatriz</span><span class="professional">Dr. João</span></div></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 08:20 - 08:50 -->
                                    <tr>
                                        <td class="time-col">08:20 - 08:50</td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Mariana Souza</span><span class="professional">Dra. Ana</span></div></td>
                                        <td><div class="session fono"><span class="name">Gabriel Lima</span><span class="professional">Dr. Pedro</span></div></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Rafaela Costa</span><span class="professional">Dra. Carla</span></div></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Enzo Gabriel</span><span class="professional">Dr. Marcos</span></div></td>
                                        <td><div class="session psicologia"><span class="name">Sofia Costa</span><span class="professional">Dra. Juliana</span></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 08:50 - 09:20 -->
                                    <tr>
                                        <td class="time-col">08:50 - 09:20</td>
                                        <td><div class="session fono"><span class="name">Lucas Mendes</span><span class="professional">Dra. Patricia</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Beatriz Lima</span><span class="professional">Dr. Roberto</span></div></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Maria Eduarda</span><span class="professional">Dra. Camila</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session musica"><span class="name">Arthur Silva</span><span class="professional">Dr. Ricardo</span></div></td>
                                        <td><div class="session psicologia"><span class="name">Isabela Santos</span><span class="professional">Dra. Renata</span></div></td>
                                    </tr>
                                    <!-- Horário 09:20 - 09:50 -->
                                    <tr>
                                        <td class="time-col">09:20 - 09:50</td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Davi Lucca</span><span class="professional">Dra. Fernanda</span></div></td>
                                        <td><div class="session fono"><span class="name">Helena Alves</span><span class="professional">Dr. Gustavo</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Valentina Rocha</span><span class="professional">Dra. Beatriz</span></div></td>
                                        <td><div class="session psicologia"><span class="name">Theo Mendes</span><span class="professional">Dr. André</span></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 09:50 - 10:20 -->
                                    <tr>
                                        <td class="time-col">09:50 - 10:20</td>
                                        <td><div class="session fono"><span class="name">Cecília Dias</span><span class="professional">Dra. Mariana</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Heitor Cardoso</span><span class="professional">Dr. Fábio</span></div></td>
                                        <td><div class="session fono"><span class="name">Lara Oliveira</span><span class="professional">Dra. Paula</span></div></td>
                                        <td><div class="session aba"><span class="name">Bernardo Costa</span><span class="professional">Dr. Lucas</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Manuela Rios</span><span class="professional">Dra. Carla</span></div></td>
                                    </tr>
                                    <!-- Horário 10:20 - 10:50 -->
                                    <tr>
                                        <td class="time-col">10:20 - 10:50</td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Nicolas Ferreira</span><span class="professional">Dr. Rafael</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Lívia Castro</span><span class="professional">Dra. Talita</span></div></td>
                                        <td><div class="session psicologia"><span class="name">Gael Souza</span><span class="professional">Dr. Marcelo</span></div></td>
                                        <td><div class="session musica"><span class="name">Alice Gomes</span><span class="professional">Dra. Luciana</span></div></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 10:50 - 11:20 -->
                                    <tr>
                                        <td class="time-col">10:50 - 11:20</td>
                                        <td><div class="session fono"><span class="name">Miguel Araújo</span><span class="professional">Dra. Vanessa</span></div></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Laura Martins</span><span class="professional">Dr. Eduardo</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Heloísa Ribeiro</span><span class="professional">Dra. Daniela</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Samuel Correia</span><span class="professional">Dr. Ricardo</span></div></td>
                                    </tr>
                                    <!-- Horário 11:20 - 11:50 -->
                                    <tr>
                                        <td class="time-col">11:20 - 11:50</td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Sophia Nunes</span><span class="professional">Dra. Adriana</span></div></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Matheus Pinto</span><span class="professional">Dr. Bruno</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Giovanna Melo</span><span class="professional">Dra. Patrícia</span></div></td>
                                        <td><div class="session psicologia"><span class="name">João Vitor</span><span class="professional">Dr. Alexandre</span></div></td>
                                        <td><div class="session musica"><span class="name">Clara Fernandes</span><span class="professional">Dra. Roberta</span></div></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- SEGUNDA SEÇÃO: SALAS 11 A 20 (SEM TÍTULO) -->
                            <table class="grade-table section2" id="gradeTableSection2">
                                <thead>
                                    <tr>
                                        <th class="time-col">Horário</th>
                                        <th>FISIOTERAPIA</th>
                                        <th>CASA HABILIDADES</th>
                                        <th>SALA KIDS</th>
                                        <th>TERAPIA OCUP</th>
                                        <th>FONO 6</th>
                                        <th>FONO 7</th>
                                        <th>ABA 3</th>
                                        <th>PSICOPEDAGOGIA</th>
                                        <th>ARTETERAPIA</th>
                                        <th>EQUOTERAPIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Horário 07:50 - 08:20 -->
                                    <tr>
                                        <td class="time-col">07:50 - 08:20</td>
                                        <td><div class="session fisioterapia"><span class="name">Rafael Costa</span><span class="professional">Dr. André</span></div></td>
                                        <td><div class="session casa_habilidades"><span class="name">Bianca Rocha</span><span class="professional">Dra. Paula</span></div></td>
                                        <td></td>
                                        <td><div class="session to"><span class="name">Yuri Alves</span><span class="professional">Dra. Sandra</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session aba"><span class="name">Tânia Lima</span><span class="professional">Dr. Marcos</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 08:20 - 08:50 -->
                                    <tr>
                                        <td class="time-col">08:20 - 08:50</td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session sala_kids"><span class="name">Enzo Gabriel</span><span class="professional">Dra. Juliana</span></div></td>
                                        <td><div class="session to"><span class="name">Isabela Cristina</span><span class="professional">Dr. Ricardo</span></div></td>
                                        <td><div class="session fono"><span class="name">Otávio Mendes</span><span class="professional">Dra. Renata</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Larissa Farias</span><span class="professional">Dr. Roberto</span></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 08:50 - 09:20 -->
                                    <tr>
                                        <td class="time-col">08:50 - 09:20</td>
                                        <td><div class="session fisioterapia"><span class="name">Carlos Eduardo</span><span class="professional">Dra. Márcia</span></div></td>
                                        <td><div class="session casa_habilidades"><span class="name">Amanda Nunes</span><span class="professional">Dr. Sérgio</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Vinicius Oliveira</span><span class="professional">Dra. Tânia</span></div></td>
                                        <td><div class="session aba"><span class="name">Marina Silva</span><span class="professional">Dr. Gustavo</span></div></td>
                                        <td></td>
                                        <td><div class="session musica"><span class="name">Fernanda Lima</span><span class="professional">Dra. Cíntia</span></div></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 09:20 - 09:50 -->
                                    <tr>
                                        <td class="time-col">09:20 - 09:50</td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session sala_kids"><span class="name">João Miguel</span><span class="professional">Dra. Vanessa</span></div></td>
                                        <td><div class="session to"><span class="name">Ana Luísa</span><span class="professional">Dr. Márcio</span></div></td>
                                        <td><div class="session fono"><span class="name">Eduarda Castro</span><span class="professional">Dra. Sabrina</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Felipe Santos</span><span class="professional">Dr. Jorge</span></div></td>
                                        <td></td>
                                        <td><div class="session fisioterapia"><span class="name">Lívia Andrade</span><span class="professional">Dra. Kátia</span></div></td>
                                    </tr>
                                    <!-- Horário 09:50 - 10:20 -->
                                    <tr>
                                        <td class="time-col">09:50 - 10:20</td>
                                        <td><div class="session fisioterapia"><span class="name">Roberta Martins</span><span class="professional">Dra. Lúcia</span></div></td>
                                        <td><div class="session casa_habilidades"><span class="name">Thiago Rodrigues</span><span class="professional">Dr. Marcelo</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Camila Ferreira</span><span class="professional">Dra. Eliane</span></div></td>
                                        <td><div class="session aba"><span class="name">Raquel Pires</span><span class="professional">Dr. César</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 10:20 - 10:50 -->
                                    <tr>
                                        <td class="time-col">10:20 - 10:50</td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session sala_kids"><span class="name">Breno Lopes</span><span class="professional">Dra. Denise</span></div></td>
                                        <td><div class="session to"><span class="name">Clarice Almeida</span><span class="professional">Dr. Antônio</span></div></td>
                                        <td><div class="session fono"><span class="name">Danilo Ribeiro</span><span class="professional">Dra. Olga</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Natália Correia</span><span class="professional">Dr. Sérgio</span></div></td>
                                        <td><div class="session musica"><span class="name">Igor Teixeira</span><span class="professional">Dra. Rita</span></div></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 10:50 - 11:20 -->
                                    <tr>
                                        <td class="time-col">10:50 - 11:20</td>
                                        <td><div class="session fisioterapia"><span class="name">Simone Araújo</span><span class="professional">Dra. Mônica</span></div></td>
                                        <td><div class="session casa_habilidades"><span class="name">Júlio César</span><span class="professional">Dr. Rogério</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session fono"><span class="name">Tatiana Neves</span><span class="professional">Dra. Gláucia</span></div></td>
                                        <td><div class="session aba"><span class="name">André Luiz</span><span class="professional">Dr. Hélio</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Horário 11:20 - 11:50 -->
                                    <tr>
                                        <td class="time-col">11:20 - 11:50</td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session sala_kids"><span class="name">Carolina Dias</span><span class="professional">Dra. Silvia</span></div></td>
                                        <td><div class="session to"><span class="name">Marcos Vinícius</span><span class="professional">Dr. Eduardo</span></div></td>
                                        <td><div class="session fono"><span class="name">Patrícia Gomes</span><span class="professional">Dra. Regina</span></div></td>
                                        <td></td>
                                        <td></td>
                                        <td><div class="session psicologia"><span class="name">Ricardo Lemos</span><span class="professional">Dr. Wagner</span></div></td>
                                        <td></td>
                                        <td><div class="session fisioterapia"><span class="name">Vera Lúcia</span><span class="professional">Dra. Elisa</span></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Grade Mobile (mantida exatamente como estava) -->
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
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TELA CHEIA -->
    <div class="fullscreen-modal" id="fullscreenModal">
        <div class="modal-content fullscreen-content">
            <div class="modal-header">
                <h2><i class="fas fa-table"></i> Grade Terapêutica - Tela Cheia</h2>
                <button class="modal-close" id="closeFullscreen"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="fullscreenBody"></div>
        </div>
    </div>

    <script>
    // Scripts para interatividade
    document.addEventListener('DOMContentLoaded', function() {
        // ===== MENU MOBILE - EXATAMENTE COMO ESTAVA =====
        const menuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const closeMenu = document.querySelector('.logo .mobile-close');
        
        console.log('Menu toggle:', menuToggle);
        console.log('Sidebar:', sidebar);
        console.log('Close menu:', closeMenu);
        
        if (menuToggle && sidebar && closeMenu) {
            menuToggle.addEventListener('click', function() {
                console.log('Abrindo menu');
                sidebar.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            closeMenu.addEventListener('click', function() {
                console.log('Fechando menu');
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Fechar ao clicar fora
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(event.target) && 
                    !menuToggle.contains(event.target) && 
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        } else {
            console.log('Elementos do menu não encontrados!');
        }

        // ===== FUNÇÃO PARA MAXIMIZAR GRADE (FORMATO EXATO DA IMAGEM) =====
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const fullscreenModal = document.getElementById('fullscreenModal');
        const closeFullscreen = document.getElementById('closeFullscreen');
        const fullscreenBody = document.getElementById('fullscreenBody');

    function maximizarGrade() {
        fullscreenBody.innerHTML = '';
        
        // Criar container principal SEM scroll
        const container = document.createElement('div');
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.padding = '5px';
        container.style.backgroundColor = '#f8fafc';
        container.style.overflow = 'hidden'; // SEM SCROLL!
        
        // ===== PRIMEIRA TABELA (SALAS 1-10) =====
        const titulo1 = document.createElement('h3');
        titulo1.textContent = 'SALAS 1 A 10';
        titulo1.style.margin = '2px 0 2px 0';
        titulo1.style.color = '#2563eb';
        titulo1.style.fontSize = '14px';
        titulo1.style.fontWeight = '600';
        titulo1.style.flexShrink = '0';
        container.appendChild(titulo1);
        
        // Container para a primeira tabela com altura flexível
        const tableContainer1 = document.createElement('div');
        tableContainer1.style.flex = '1';
        tableContainer1.style.minHeight = '0';
        tableContainer1.style.overflow = 'hidden';
        container.appendChild(tableContainer1);
        
        const table1 = document.createElement('table');
        table1.style.width = '100%';
        table1.style.height = '100%';
        table1.style.borderCollapse = 'collapse';
        table1.style.backgroundColor = 'white';
        table1.style.border = '1px solid #e2e8f0';
        table1.style.fontSize = '10px';
        table1.style.tableLayout = 'fixed';
        
        // Cabeçalho da primeira tabela
        const thead1 = document.createElement('thead');
        const headerRow1 = document.createElement('tr');
        const headers1 = document.querySelectorAll('#gradeTableSection1 thead th');
        
        headers1.forEach(th => {
            const newTh = document.createElement('th');
            newTh.textContent = th.textContent;
            newTh.style.backgroundColor = '#3b82f6';
            newTh.style.color = 'white';
            newTh.style.padding = '4px 2px';
            newTh.style.textAlign = 'center';
            newTh.style.border = '1px solid #e2e8f0';
            newTh.style.fontWeight = '600';
            newTh.style.fontSize = '9px';
            newTh.style.whiteSpace = 'nowrap';
            if (th.classList.contains('time-col')) {
                newTh.style.backgroundColor = '#2563eb';
                newTh.style.width = '70px';
            }
            headerRow1.appendChild(newTh);
        });
        thead1.appendChild(headerRow1);
        table1.appendChild(thead1);
        
        // Corpo da primeira tabela
        const tbody1 = document.createElement('tbody');
        const rows1 = document.querySelectorAll('#gradeTableSection1 tbody tr');
        
        rows1.forEach(row => {
            const newRow = document.createElement('tr');
            newRow.style.height = '30px'; // Altura fixa reduzida
            
            const cells = row.querySelectorAll('td');
            
            cells.forEach(cell => {
                const newCell = document.createElement('td');
                newCell.style.padding = '1px';
                newCell.style.border = '1px solid #e2e8f0';
                newCell.style.height = '30px';
                newCell.style.verticalAlign = 'top';
                
                if (cell.classList.contains('time-col')) {
                    newCell.style.backgroundColor = '#f1f5f9';
                    newCell.style.fontWeight = '600';
                    newCell.style.textAlign = 'center';
                    newCell.style.padding = '2px 1px';
                    newCell.style.fontSize = '9px';
                    newCell.style.width = '70px';
                    newCell.textContent = cell.textContent;
                } else {
                    // Processar sessões
                    const sessoes = cell.querySelectorAll('.session');
                    if (sessoes.length > 0) {
                        sessoes.forEach(sessao => {
                            const nome = sessao.querySelector('.name')?.textContent || '';
                            const profissional = sessao.querySelector('.professional')?.textContent || '';
                            const tipo = sessao.classList[1] || '';
                            
                            const sessionDiv = document.createElement('div');
                            sessionDiv.style.margin = '0';
                            sessionDiv.style.padding = '1px 2px';
                            sessionDiv.style.borderRadius = '2px';
                            sessionDiv.style.fontSize = '7px';
                            sessionDiv.style.lineHeight = '1.1';
                            sessionDiv.style.height = '28px';
                            sessionDiv.style.display = 'flex';
                            sessionDiv.style.flexDirection = 'column';
                            sessionDiv.style.justifyContent = 'center';
                            
                            // Aplicar cor conforme o tipo
                            if (tipo === 'aba') {
                                sessionDiv.style.backgroundColor = '#eef2ff';
                                sessionDiv.style.color = '#4f46e5';
                                sessionDiv.style.borderLeft = '3px solid #4f46e5';
                            } else if (tipo === 'fono') {
                                sessionDiv.style.backgroundColor = '#f0fdf4';
                                sessionDiv.style.color = '#16a34a';
                                sessionDiv.style.borderLeft = '3px solid #16a34a';
                            } else if (tipo === 'psicologia') {
                                sessionDiv.style.backgroundColor = '#fdf2f8';
                                sessionDiv.style.color = '#db2777';
                                sessionDiv.style.borderLeft = '3px solid #db2777';
                            } else if (tipo === 'to') {
                                sessionDiv.style.backgroundColor = '#f0f9ff';
                                sessionDiv.style.color = '#0891b2';
                                sessionDiv.style.borderLeft = '3px solid #0891b2';
                            } else if (tipo === 'musica') {
                                sessionDiv.style.backgroundColor = '#faf5ff';
                                sessionDiv.style.color = '#9333ea';
                                sessionDiv.style.borderLeft = '3px solid #9333ea';
                            } else if (tipo === 'fisioterapia') {
                                sessionDiv.style.backgroundColor = '#fffbeb';
                                sessionDiv.style.color = '#d97706';
                                sessionDiv.style.borderLeft = '3px solid #d97706';
                            } else if (tipo === 'casa_habilidades') {
                                sessionDiv.style.backgroundColor = '#ecfdf5';
                                sessionDiv.style.color = '#059669';
                                sessionDiv.style.borderLeft = '3px solid #059669';
                            } else if (tipo === 'sala_kids') {
                                sessionDiv.style.backgroundColor = '#fff1f2';
                                sessionDiv.style.color = '#e11d48';
                                sessionDiv.style.borderLeft = '3px solid #e11d48';
                            }
                            
                            sessionDiv.innerHTML = `
                                <span style="font-weight:600; font-size:7px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${nome}</span>
                                <span style="font-size:6px; opacity:0.8; font-style:italic; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${profissional}</span>
                            `;
                            
                            newCell.appendChild(sessionDiv);
                        });
                    }
                }
                newRow.appendChild(newCell);
            });
            tbody1.appendChild(newRow);
        });
        table1.appendChild(tbody1);
        tableContainer1.appendChild(table1);
        
        // ===== LINHA DIVISÓRIA =====
        const divider = document.createElement('hr');
        divider.style.margin = '5px 0 5px 0';
        divider.style.border = 'none';
        divider.style.borderTop = '2px solid #94a3b8';
        divider.style.flexShrink = '0';
        container.appendChild(divider);
        
        // ===== SEGUNDA TABELA (SALAS 11-20) =====
        const titulo2 = document.createElement('h3');
        titulo2.textContent = 'SALAS 11 A 20';
        titulo2.style.margin = '2px 0 2px 0';
        titulo2.style.color = '#2563eb';
        titulo2.style.fontSize = '14px';
        titulo2.style.fontWeight = '600';
        titulo2.style.flexShrink = '0';
        container.appendChild(titulo2);
        
        // Container para a segunda tabela com altura flexível
        const tableContainer2 = document.createElement('div');
        tableContainer2.style.flex = '1';
        tableContainer2.style.minHeight = '0';
        tableContainer2.style.overflow = 'hidden';
        container.appendChild(tableContainer2);
        
        const table2 = document.createElement('table');
        table2.style.width = '100%';
        table2.style.height = '100%';
        table2.style.borderCollapse = 'collapse';
        table2.style.backgroundColor = 'white';
        table2.style.border = '1px solid #e2e8f0';
        table2.style.fontSize = '10px';
        table2.style.tableLayout = 'fixed';
        
        // Cabeçalho da segunda tabela
        const thead2 = document.createElement('thead');
        const headerRow2 = document.createElement('tr');
        const headers2 = document.querySelectorAll('#gradeTableSection2 thead th');
        
        headers2.forEach(th => {
            const newTh = document.createElement('th');
            newTh.textContent = th.textContent;
            newTh.style.backgroundColor = '#3b82f6';
            newTh.style.color = 'white';
            newTh.style.padding = '4px 2px';
            newTh.style.textAlign = 'center';
            newTh.style.border = '1px solid #e2e8f0';
            newTh.style.fontWeight = '600';
            newTh.style.fontSize = '9px';
            newTh.style.whiteSpace = 'nowrap';
            if (th.classList.contains('time-col')) {
                newTh.style.backgroundColor = '#2563eb';
                newTh.style.width = '70px';
            }
            headerRow2.appendChild(newTh);
        });
        thead2.appendChild(headerRow2);
        table2.appendChild(thead2);
        
        // Corpo da segunda tabela
        const tbody2 = document.createElement('tbody');
        const rows2 = document.querySelectorAll('#gradeTableSection2 tbody tr');
        
        rows2.forEach(row => {
            const newRow = document.createElement('tr');
            newRow.style.height = '30px'; // Altura fixa reduzida
            
            const cells = row.querySelectorAll('td');
            
            cells.forEach(cell => {
                const newCell = document.createElement('td');
                newCell.style.padding = '1px';
                newCell.style.border = '1px solid #e2e8f0';
                newCell.style.height = '30px';
                newCell.style.verticalAlign = 'top';
                
                if (cell.classList.contains('time-col')) {
                    newCell.style.backgroundColor = '#f1f5f9';
                    newCell.style.fontWeight = '600';
                    newCell.style.textAlign = 'center';
                    newCell.style.padding = '2px 1px';
                    newCell.style.fontSize = '9px';
                    newCell.style.width = '70px';
                    newCell.textContent = cell.textContent;
                } else {
                    // Processar sessões
                    const sessoes = cell.querySelectorAll('.session');
                    if (sessoes.length > 0) {
                        sessoes.forEach(sessao => {
                            const nome = sessao.querySelector('.name')?.textContent || '';
                            const profissional = sessao.querySelector('.professional')?.textContent || '';
                            const tipo = sessao.classList[1] || '';
                            
                            const sessionDiv = document.createElement('div');
                            sessionDiv.style.margin = '0';
                            sessionDiv.style.padding = '1px 2px';
                            sessionDiv.style.borderRadius = '2px';
                            sessionDiv.style.fontSize = '7px';
                            sessionDiv.style.lineHeight = '1.1';
                            sessionDiv.style.height = '28px';
                            sessionDiv.style.display = 'flex';
                            sessionDiv.style.flexDirection = 'column';
                            sessionDiv.style.justifyContent = 'center';
                            
                            // Aplicar cor conforme o tipo
                            if (tipo === 'aba') {
                                sessionDiv.style.backgroundColor = '#eef2ff';
                                sessionDiv.style.color = '#4f46e5';
                                sessionDiv.style.borderLeft = '3px solid #4f46e5';
                            } else if (tipo === 'fono') {
                                sessionDiv.style.backgroundColor = '#f0fdf4';
                                sessionDiv.style.color = '#16a34a';
                                sessionDiv.style.borderLeft = '3px solid #16a34a';
                            } else if (tipo === 'psicologia') {
                                sessionDiv.style.backgroundColor = '#fdf2f8';
                                sessionDiv.style.color = '#db2777';
                                sessionDiv.style.borderLeft = '3px solid #db2777';
                            } else if (tipo === 'to') {
                                sessionDiv.style.backgroundColor = '#f0f9ff';
                                sessionDiv.style.color = '#0891b2';
                                sessionDiv.style.borderLeft = '3px solid #0891b2';
                            } else if (tipo === 'musica') {
                                sessionDiv.style.backgroundColor = '#faf5ff';
                                sessionDiv.style.color = '#9333ea';
                                sessionDiv.style.borderLeft = '3px solid #9333ea';
                            } else if (tipo === 'fisioterapia') {
                                sessionDiv.style.backgroundColor = '#fffbeb';
                                sessionDiv.style.color = '#d97706';
                                sessionDiv.style.borderLeft = '3px solid #d97706';
                            } else if (tipo === 'casa_habilidades') {
                                sessionDiv.style.backgroundColor = '#ecfdf5';
                                sessionDiv.style.color = '#059669';
                                sessionDiv.style.borderLeft = '3px solid #059669';
                            } else if (tipo === 'sala_kids') {
                                sessionDiv.style.backgroundColor = '#fff1f2';
                                sessionDiv.style.color = '#e11d48';
                                sessionDiv.style.borderLeft = '3px solid #e11d48';
                            }
                            
                            sessionDiv.innerHTML = `
                                <span style="font-weight:600; font-size:7px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${nome}</span>
                                <span style="font-size:6px; opacity:0.8; font-style:italic; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${profissional}</span>
                            `;
                            
                            newCell.appendChild(sessionDiv);
                        });
                    }
                }
                newRow.appendChild(newCell);
            });
            tbody2.appendChild(newRow);
        });
        table2.appendChild(tbody2);
        tableContainer2.appendChild(table2);
        
        fullscreenBody.appendChild(container);
        fullscreenModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', maximizarGrade);
        }

        if (closeFullscreen) {
            closeFullscreen.addEventListener('click', function() {
                fullscreenModal.style.display = 'none';
                document.body.style.overflow = '';
            });
        }

        if (fullscreenModal) {
            fullscreenModal.addEventListener('click', function(e) {
                if (e.target === fullscreenModal) {
                    fullscreenModal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
        }

        // ===== RESTO DO SEU CÓDIGO EXISTENTE =====
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