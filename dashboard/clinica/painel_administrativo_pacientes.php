<?php
// C:\wamp64\www\clinicaestrela\dashboard\clinica\painel_administrativo_pacientes.php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Pacientes</title>
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_administrativo_grade.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_administrativo_paciente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../../imagens/logo_clinica_estrela.png" alt="Logo Clínica Estrela" class="logo-img">
                </div>
                <h1>Clinica Estrela</h1>
            </div>

            <nav class="menu">
                <ul>
                    <li class="active"><a href="painel_administrativo_pacientes.php"><i class="fas fa-users"></i> <span>Pacientes</span></a></li>
                    <li><a href="#"><i class="fas fa-file-medical"></i> <span>Pré-cadastro</span></a></li>
                    <li><a href="#"><i class="fas fa-user-check"></i> <span>Ativos</span></a></li>
                    <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Altas</span></a></li>
                    <li><a href="#"><i class="fas fa-calendar-check"></i> <span>Plano Terapêutico</span></a></li>
                    <li><a href="painel_administrativo_grade.php"><i class="fas fa-table"></i> <span>Grade Terapêutica</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Evoluções</span></a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a></li>
                    <li><a href="visita_agendamento.php"><i class="fas fa-calendar-check"></i> <span>Visitas Agendadas</a></li>
                    <li><a href="#"><i class="fas fa-door-closed"></i> <span>Salas</span></a></li>
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

            <div class="page-title">
                <h2><i class="fas fa-users"></i> Pacientes</h2>
            </div>

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

            <div class="patients-search-container">
                <div class="patients-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar paciente">
                </div>
                <div class="patients-actions">
                    <button class="btn-add-patient">
                        <i class="fas fa-plus"></i> Novo Paciente
                    </button>
                    <button class="btn-export">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                </div>
            </div>

            <div class="patients-table-container">
                <div class="table-header">
                    <h3>Tabela de Pacientes</h3>
                    <div class="table-actions">
                        <button class="btn-filter">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <button class="btn-refresh">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="patients-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Diagnóstico</th>
                                <th>Próxima Sessão</th>
                                <th>Contato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="patient-cell">
                                        <div class="patient-avatar-small">
                                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="João Silva">
                                        </div>
                                        <div class="patient-name">
                                            <strong>João Silva</strong>
                                            <span class="patient-age">5 anos</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="diagnostic-tags">
                                        <span class="diagnostic-badge">TDAH</span>
                                        <span class="diagnostic-badge">TEA</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="next-session">
                                        <span class="session-date">24/Abr</span>
                                        <span class="session-time">11:00</span>
                                    </div>
                                </td>
                                <td>(11) 93765-4321</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action delete" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <button class="pagination-btn prev">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    
                    <div class="pagination-info">
                        <span class="current-page">1</span>
                        <span class="total-pages">de 25</span>
                    </div>
                    
                    <button class="pagination-btn next">
                        Próximo <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Scripts para interatividade originais mantidos
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.patients-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8fafc';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            const actionButtons = document.querySelectorAll('.btn-action');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const action = this.classList.contains('view') ? 'visualizar' : 
                                  this.classList.contains('edit') ? 'editar' : 'excluir';
                    
                    if (action === 'excluir') {
                        if (confirm('Tem certeza que deseja excluir este paciente?')) {
                            alert('Paciente excluído com sucesso!');
                        }
                    } else {
                        alert(`Ação de ${action} será implementada`);
                    }
                });
            });
        });
    </script>
</body>
</html>