<?php
// painel_pacientes_pendentes.php
session_start();

// 1. Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// 2. Dados da Sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Função para calcular idade a partir da data de nascimento
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) {
        return 'Idade n/d';
    }
    
    try {
        // Tenta converter a data para objeto DateTime
        // Primeiro tenta o formato Y-m-d (que está no JSON)
        $dataNasc = DateTime::createFromFormat('Y-m-d', $dataNascimento);
        
        // Se não funcionar, tenta outros formatos comuns
        if (!$dataNasc) {
            $dataNasc = DateTime::createFromFormat('d/m/Y', $dataNascimento);
        }
        
        if (!$dataNasc) {
            $dataNasc = new DateTime($dataNascimento);
        }
        
        $hoje = new DateTime();
        $diferenca = $hoje->diff($dataNasc);
        
        return $diferenca->y . ' anos';
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

// 3. Leitura do Arquivo JSON (Pendentes)
$arquivoJson = __DIR__ . '/../../dashboard/dados/pre-cad.json';
$pacientesPendentes = [];

if (file_exists($arquivoJson)) {
    $conteudo = file_get_contents($arquivoJson);
    $dados = json_decode($conteudo, true);
    
    // Verifica se é uma lista válida
    if (is_array($dados)) {
        $pacientesPendentes = $dados;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes Pendentes - Clínica Estrela</title>
    
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos específicos para a Tabela */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 20px;
            margin-top: 20px;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Inter', sans-serif;
        }

        .custom-table th {
            text-align: left;
            padding: 15px;
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .custom-table tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background-color: #fef3c7;
            color: #d97706; /* Amarelo escuro para Pendente */
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-right: 5px; /* Espaço entre botões */
        }

        .btn-active {
            background-color: #10b981;
            color: white;
        }
        .btn-active:hover { background-color: #059669; }

        /* Estilo para o botão Editar (Azul) */
        .btn-edit {
            background-color: #3b82f6;
            color: white;
        }
        .btn-edit:hover { background-color: #2563eb; }
        
        /* Estilo para o botão Excluir (Vermelho) */
        .btn-delete {
            background-color: #ef4444;
            color: white;
        }
        .btn-delete:hover { background-color: #dc2626; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #cbd5e1;
        }

        /* Avatar pequeno na tabela */
        .table-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        /* Modal de confirmação para exclusão */
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
        .patient-info-confirm {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .patient-info-confirm strong {
            color: #1e293b;
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        .patient-info-confirm span {
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
        
        /* Notificações */
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes_precadastro/header_preca.php'; ?>
        
        <main class="main-content">
            <div class="main-top desktop-only">
                <h2><i class="fas fa-clipboard-list"></i> Lista de Pacientes Pendentes</h2>
                <div class="top-icons">
                    <div class="icon-btn with-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge">2</span>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>

            <div class="patient-card" style="margin-bottom: 20px;">
                <div class="patient-info">
                    <div class="info-text">
                        <h3>Gestão de Pendências</h3>
                        <p>Aqui estão os pré-cadastros aguardando validação para se tornarem ativos.</p>
                    </div>
                </div>
                <a href="painel_adm_preca.php" class="btn-archive" style="text-decoration: none; background: #64748b;">
                    <i class="fas fa-plus"></i> Novo Pré-Cadastro
                </a>
            </div>

            <div class="table-container">
                <?php if (empty($pacientesPendentes)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3>Nenhum paciente pendente encontrado</h3>
                        <p>Realize um pré-cadastro para vê-lo aqui.</p>
                    </div>
                <?php else: ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Responsável</th>
                                <th>Contato</th>
                                <th>Data Cadastro</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="pacientes-tbody">
                            <?php foreach ($pacientesPendentes as $index => $p): ?>
                                <?php if(empty($p['nome_completo'])) continue; ?>
                                
                                <?php 
                                    // Calcular idade a partir da data de nascimento
                                    $dataNascimento = isset($p['nascimento']) ? $p['nascimento'] : (isset($p['data_nascimento']) ? $p['data_nascimento'] : '');
                                    $idade = calcularIdade($dataNascimento);
                                    
                                    // Gerar foto do paciente
                                    $fotoUrl = !empty($p['foto']) ? $p['foto'] : 'https://ui-avatars.com/api/?name='.urlencode($p['nome_completo']).'&background=random';
                                ?>
                                
                                <tr id="paciente-row-<?php echo $index; ?>">
                                    <td>
                                        <img src="<?php echo htmlspecialchars($fotoUrl); ?>" class="table-avatar" alt="Foto">
                                        <strong><?php echo htmlspecialchars($p['nome_completo']); ?></strong>
                                        <br>
                                        <small style="color: #94a3b8; margin-left: 54px;">
                                            <?php echo htmlspecialchars($idade); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($p['responsavel'] ?? 'Resp. n/d'); ?>
                                        <br>
                                        <small style="color: #94a3b8;">
                                            <?php echo htmlspecialchars($p['parentesco'] ?? ''); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone" style="font-size: 0.8rem; color: #94a3b8;"></i> 
                                        <?php echo htmlspecialchars($p['telefone'] ?? 'Sem telefone'); ?>
                                        <br>
                                        <i class="fas fa-envelope" style="font-size: 0.8rem; color: #94a3b8;"></i> 
                                        <?php echo htmlspecialchars($p['email'] ?? 'Sem email'); ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $dataReg = isset($p['data_registro']) ? date('d/m/Y', strtotime($p['data_registro'])) : date('d/m/Y');
                                            echo $dataReg;
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <i class="fas fa-clock"></i> Pendente
                                        </span>
                                    </td>
                                    <td>
                                        <a href="painel_adm_preca.php?index=<?php echo $index; ?>&origem=pendente" class="btn-action btn-edit">
                                            <i class="fas fa-pen"></i> Editar
                                        </a>

                                        <button class="btn-action btn-active" onclick="ativarPaciente(<?php echo $index; ?>)">
                                            <i class="fas fa-check"></i> Ativar
                                        </button>
                                        
                                        <button class="btn-action btn-delete" 
                                                onclick="excluirPacientePendente(<?php echo $index; ?>, '<?php echo htmlspecialchars(addslashes($p['nome_completo'])); ?>')">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <footer class="main-footer">
                <div class="footer-logo">
                    <i class="fas fa-star"></i>
                    <span>CLÍNICA ESTRELA</span>
                </div>
            </footer>
        </main>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h3>
            </div>
            <div class="confirmation-modal-body">
                <p>Tem certeza que deseja excluir este paciente pendente? Esta ação não pode ser desfeita.</p>
                <div class="patient-info-confirm">
                    <strong id="patient-name-confirm"></strong>
                    <span>ID: <span id="patient-id-confirm"></span></span>
                </div>
                <p><small><i class="fas fa-info-circle"></i> Todos os dados do paciente serão removidos permanentemente.</small></p>
            </div>
            <div class="confirmation-modal-footer">
                <button class="btn-cancel" id="cancelDelete">Cancelar</button>
                <button class="btn-confirm" id="confirmDelete">Excluir Permanentemente</button>
            </div>
        </div>
    </div>

    <script>
        // Dados globais para exclusão
        let pacienteParaExcluir = {
            index: null,
            nome: '',
            origem: 'pendente'
        };

        function ativarPaciente(index) {
            if(confirm("Deseja realmente ATIVAR este paciente? \nIsso moverá os dados para a lista de ativos.")) {
                // Chama o PHP via Fetch para mover o dado
                fetch('mover_para_ativo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ index: index })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        mostrarNotificacao('sucesso', data.message || 'Paciente ativado com sucesso!');
                        setTimeout(() => {
                            location.reload(); // Recarrega a página para atualizar a lista
                        }, 1500);
                    } else {
                        mostrarNotificacao('erro', data.message || 'Erro ao ativar paciente.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('erro', 'Erro ao processar solicitação.');
                });
            }
        }
        
        function excluirPacientePendente(index, nome) {
            // Preencher modal de confirmação
            pacienteParaExcluir = { index, nome, origem: 'pendente' };
            document.getElementById('patient-name-confirm').textContent = nome;
            document.getElementById('patient-id-confirm').textContent = `#${index}`;
            
            // Mostrar modal
            document.getElementById('confirmationModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('active');
            document.body.style.overflow = '';
            pacienteParaExcluir = { index: null, nome: '', origem: '' };
        }
        
        function excluirPaciente() {
            const { index, nome, origem } = pacienteParaExcluir;
            
            if (!index) return;
            
            // Desabilitar botões durante a exclusão
            const confirmBtn = document.getElementById('confirmDelete');
            const cancelBtn = document.getElementById('cancelDelete');
            const originalConfirmText = confirmBtn.textContent;
            
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
            confirmBtn.disabled = true;
            cancelBtn.disabled = true;
            
            // Enviar requisição para excluir (usando o mesmo arquivo de exclusão)
            fetch('excluir_paciente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    index: parseInt(index),
                    origem: origem
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Remover linha da tabela
                    const row = document.getElementById(`paciente-row-${index}`);
                    if (row) {
                        row.style.backgroundColor = '#fee2e2';
                        setTimeout(() => {
                            row.remove();
                            
                            // Mostrar notificação de sucesso
                            mostrarNotificacao('sucesso', data.message || 'Paciente excluído com sucesso!');
                        }, 300);
                    }
                    
                    // Verificar se não há mais pacientes
                    const tbody = document.getElementById('pacientes-tbody');
                    if (tbody && tbody.children.length === 0) {
                        const noPatientsRow = document.createElement('tr');
                        noPatientsRow.innerHTML = `
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                                Nenhum paciente pendente encontrado. Realize um pré-cadastro para vê-lo aqui.
                            </td>
                        `;
                        tbody.appendChild(noPatientsRow);
                    }
                } else {
                    mostrarNotificacao('erro', data.message || 'Erro ao excluir paciente.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('erro', 'Erro de comunicação com o servidor.');
            })
            .finally(() => {
                closeConfirmationModal();
                confirmBtn.innerHTML = originalConfirmText;
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
            });
        }
        
        function mostrarNotificacao(tipo, mensagem) {
            const div = document.createElement('div');
            let classeTipo = 'notification-success';
            let icone = 'fa-check-circle';
            
            if (tipo === 'erro') { 
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

        // Configurar eventos do modal
        document.addEventListener('DOMContentLoaded', function() {
            // Botão cancelar exclusão
            document.getElementById('cancelDelete').addEventListener('click', function() {
                closeConfirmationModal();
            });

            // Botão confirmar exclusão
            document.getElementById('confirmDelete').addEventListener('click', function() {
                excluirPaciente();
            });

            // Fechar modal ao clicar fora
            document.getElementById('confirmationModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeConfirmationModal();
                }
            });
        });
    </script>
</body>
</html>