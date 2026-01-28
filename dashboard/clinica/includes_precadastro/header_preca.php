<?php
// includes/header_preca.php
?>

<!-- Menu Mobile Toggle -->
<div class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class="fas fa-bars"></i>
</div>

<!-- Header Mobile -->
<div class="mobile-header">
    <h1>Pré-Cadastro Clínico</h1>
    <div class="mobile-close" id="mobileClose">
        <i class="fas fa-times"></i>
    </div>
</div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="logo">
        <div class="logo-icon">
            <img src="../../imagens/logo_clinica_estrela.png" alt="Logo" class="logo-img">
        </div>
        <h1>Clinica Estrela</h1>
        <div class="mobile-close">
            <i class="fas fa-times"></i>
        </div>
    </div>

    <!-- Menu de Navegação -->
    <nav class="menu">
        <ul>
            <li><a href="painel_adm_pacientes.php"><i class="fas fa-user-check"></i> <span>Pacientes Ativos</span></a></li>
            <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_adm_preca.php"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
            <li><a href="http://localhost/clinicaestrela/dashboard/clinica/painel_pacientes_pendentes.php"><i class="fas fa-users"></i> <span>Pacientes Pendentes</span></a></li>
            
            <?php if ($perfilLogado !== 'recepcionista'): ?>
                <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                <li><a href="painel_adm_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
            <?php endif; ?>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
            <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</span></a></li>
            <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
            <li><a href="http://localhost/clinicaestrela/dashboard/clinica/login_cadastro_clinica.php"><i class="fas fa-user-plus"></i> <span>Adicionar Colaborador</span></a></li>
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
    </div>
</aside>