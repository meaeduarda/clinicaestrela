<?php
// painel_salas_teste.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];
$pagina_atual = basename($_SERVER['PHP_SELF']);

// --- LEITURA DAS SALAS ---
$arquivoSalas = __DIR__ . '/../../dashboard/dados/salas.json';
$salas = [];

// Verificar se o arquivo existe
if (file_exists($arquivoSalas)) {
    $conteudoSalas = file_get_contents($arquivoSalas);
    
    // Decodificar JSON
    $salas = json_decode($conteudoSalas, true);
    
    // Garantir que é um array
    if (!is_array($salas)) {
        $salas = [];
    }
    
    // Se for um array associativo (objeto) em vez de lista, converter para lista
    $salas = array_values($salas);
}

// Se não houver salas, criar algumas padrão
if (empty($salas)) {
    $salas = [
        ['id' => 1, 'nome' => 'Sala 01 - ABA', 'capacidade' => 3, 'tipo' => 'ABA'],
        ['id' => 2, 'nome' => 'Sala 02 - Fono', 'capacidade' => 3, 'tipo' => 'Fono'],
        ['id' => 3, 'nome' => 'Sala 03 - Fono', 'capacidade' => 2, 'tipo' => 'Fono']
    ];
    file_put_contents($arquivoSalas, json_encode($salas, JSON_PRETTY_PRINT));
}

function getCorPorTipo($tipo) {
    $cores = [
        'ABA' => '#8b5cf6',
        'Fono' => '#ec4899',
        'TO' => '#f59e0b',
        'Funcional' => '#ef4444'
    ];
    return $cores[$tipo] ?? '#94a3b8';
}

// Debug (opcional - remover em produção)
echo "<!-- DEBUG: " . htmlspecialchars(json_encode($salas)) . " -->";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teste Salas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .salas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        .sala-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .sala-color-bar { height: 8px; }
        .sala-content { padding: 20px; }
        .sala-nome { margin: 0 0 10px 0; font-size: 1.2rem; }
        .sala-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .sala-tipo { padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; }
        .sala-actions { display: flex; gap: 10px; }
        .btn-action { border: none; background: none; cursor: pointer; font-size: 1.2rem; padding: 5px; }
        .btn-action.edit { color: #3b82f6; }
        .btn-action.delete { color: #ef4444; }
        .add-sala { cursor: pointer; background: #f8fafc; }
        .btn-add-sala { width: 40px; height: 40px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 10px; width: 90%; max-width: 500px; }
        .modal-header { padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .modal-header button { background: none; border: none; font-size: 24px; cursor: pointer; }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 5px; box-sizing: border-box; }
        .btn-save { background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #2563eb; }
        .btn-cancel { background: #6b7280; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #4b5563; }
        
        .loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); align-items: center; justify-content: center; z-index: 2000; }
        .loading-overlay.active { display: flex; }
        .loading-spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .notification { position: fixed; top: 20px; right: 20px; padding: 15px; border-radius: 5px; color: white; z-index: 3000; }
        .notification.success { background: #10b981; }
        .notification.error { background: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-door-closed"></i> Painel de Salas (Versão Teste)</h1>
        
        <div class="salas-grid">
            <?php if (is_array($salas) && !empty($salas)): ?>
                <?php foreach ($salas as $sala): 
                    // Garantir que $sala é um array
                    if (!is_array($sala)) continue;
                    
                    $cor = getCorPorTipo($sala['tipo'] ?? '');
                ?>
                    <div class="sala-card" id="sala-<?php echo $sala['id'] ?? 0; ?>">
                        <div class="sala-color-bar" style="background-color: <?php echo $cor; ?>;"></div>
                        <div class="sala-content">
                            <h3 class="sala-nome"><?php echo htmlspecialchars($sala['nome'] ?? 'Sem nome'); ?></h3>
                            <div class="sala-info">
                                <span><i class="fas fa-user-friends"></i> Até <?php echo $sala['capacidade'] ?? 0; ?> pessoas</span>
                                <span class="sala-tipo" style="background-color: <?php echo $cor; ?>20; color: <?php echo $cor; ?>;">
                                    <?php echo htmlspecialchars($sala['tipo'] ?? ''); ?>
                                </span>
                            </div>
                            <div class="sala-actions">
                                <button class="btn-action edit" onclick="editarSala(<?php echo $sala['id'] ?? 0; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action delete" onclick="excluirSala(<?php echo $sala['id'] ?? 0; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="sala-card add-sala" onclick="abrirModal()">
                <div class="sala-color-bar" style="background-color: #9ca3af;"></div>
                <div class="sala-content">
                    <h3 class="sala-nome">Adicionar Sala</h3>
                    <div class="sala-info">
                        <span>Clique para adicionar</span>
                        <div class="btn-add-sala">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal" id="modalSala">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitulo">Adicionar Sala</h3>
                <button onclick="fecharModal()">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="salaId">
                <div class="form-group">
                    <label>Nome da Sala:</label>
                    <input type="text" id="salaNome" class="form-control" placeholder="Ex: Sala 01 - ABA">
                </div>
                <div class="form-group">
                    <label>Capacidade (pessoas):</label>
                    <input type="number" id="salaCapacidade" class="form-control" min="1" value="4">
                </div>
                <div class="form-group">
                    <label>Tipo de Terapia:</label>
                    <select id="salaTipo" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="ABA">ABA</option>
                        <option value="Fono">Fono</option>
                        <option value="TO">TO</option>
                        <option value="Funcional">Funcional</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button class="btn-save" onclick="salvarSala()">Salvar</button>
            </div>
        </div>
    </div>
    
    <!-- Loading -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <script>
    let salas = <?php echo json_encode($salas); ?>;
    
    function mostrarNotificacao(tipo, mensagem) {
        const div = document.createElement('div');
        div.className = `notification ${tipo}`;
        div.textContent = mensagem;
        document.body.appendChild(div);
        
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
    
    function abrirModal() {
        document.getElementById('modalTitulo').textContent = 'Adicionar Sala';
        document.getElementById('salaId').value = '';
        document.getElementById('salaNome').value = '';
        document.getElementById('salaCapacidade').value = '4';
        document.getElementById('salaTipo').value = '';
        document.getElementById('modalSala').classList.add('active');
    }
    
    function editarSala(id) {
        const sala = salas.find(s => s.id === id);
        if (!sala) {
            mostrarNotificacao('error', 'Sala não encontrada');
            return;
        }
        
        document.getElementById('modalTitulo').textContent = 'Editar Sala';
        document.getElementById('salaId').value = sala.id;
        document.getElementById('salaNome').value = sala.nome;
        document.getElementById('salaCapacidade').value = sala.capacidade;
        document.getElementById('salaTipo').value = sala.tipo;
        document.getElementById('modalSala').classList.add('active');
    }
    
    function fecharModal() {
        document.getElementById('modalSala').classList.remove('active');
    }
    
    async function salvarSala() {
        const id = document.getElementById('salaId').value;
        const nome = document.getElementById('salaNome').value.trim();
        const capacidade = document.getElementById('salaCapacidade').value;
        const tipo = document.getElementById('salaTipo').value;
        
        if (!nome || !capacidade || !tipo) {
            mostrarNotificacao('error', 'Preencha todos os campos');
            return;
        }
        
        document.getElementById('loadingOverlay').classList.add('active');
        
        try {
            const formData = new FormData();
            formData.append('acao', id ? 'editar' : 'adicionar');
            if (id) formData.append('id', id);
            formData.append('nome', nome);
            formData.append('capacidade', capacidade);
            formData.append('tipo', tipo);
            
            console.log('Enviando:', Object.fromEntries(formData));
            
            const response = await fetch('salvar_sala.php', {
                method: 'POST',
                body: formData
            });
            
            const text = await response.text();
            console.log('Resposta bruta:', text);
            
            try {
                const result = JSON.parse(text);
                if (result.status === 'success') {
                    mostrarNotificacao('success', result.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    mostrarNotificacao('error', 'Erro: ' + result.message);
                }
            } catch (e) {
                console.error('Erro ao parsear JSON:', e);
                mostrarNotificacao('error', 'Resposta inválida do servidor: ' + text.substring(0, 100));
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            mostrarNotificacao('error', 'Erro na requisição: ' + error.message);
        } finally {
            document.getElementById('loadingOverlay').classList.remove('active');
            fecharModal();
        }
    }
    
    async function excluirSala(id) {
        if (!confirm('Tem certeza que deseja excluir esta sala?')) return;
        
        document.getElementById('loadingOverlay').classList.add('active');
        
        try {
            const formData = new FormData();
            formData.append('acao', 'excluir');
            formData.append('id', id);
            
            console.log('Excluindo sala:', id);
            
            const response = await fetch('salvar_sala.php', {
                method: 'POST',
                body: formData
            });
            
            const text = await response.text();
            console.log('Resposta bruta:', text);
            
            try {
                const result = JSON.parse(text);
                if (result.status === 'success') {
                    mostrarNotificacao('success', result.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    mostrarNotificacao('error', 'Erro: ' + result.message);
                }
            } catch (e) {
                console.error('Erro ao parsear JSON:', e);
                mostrarNotificacao('error', 'Resposta inválida do servidor: ' + text.substring(0, 100));
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            mostrarNotificacao('error', 'Erro na requisição: ' + error.message);
        } finally {
            document.getElementById('loadingOverlay').classList.remove('active');
        }
    }
    </script>
</body>
</html>