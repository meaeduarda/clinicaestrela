<?php
// salvar_sala.php
header('Content-Type: application/json');

session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit();
}

// Caminho do arquivo JSON
$arquivoSalas = __DIR__ . '/../../dashboard/dados/salas.json';

// Função para ler salas
function lerSalas($arquivo) {
    if (!file_exists($arquivo)) {
        return [];
    }
    $conteudo = file_get_contents($arquivo);
    $salas = json_decode($conteudo, true);
    return is_array($salas) ? $salas : [];
}

// Função para salvar salas
function salvarSalas($arquivo, $salas) {
    return file_put_contents($arquivo, json_encode($salas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Log para debug
error_log("Requisição recebida: " . print_r($_POST, true));

// Processar a ação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    switch ($acao) {
        case 'adicionar':
            $salas = lerSalas($arquivoSalas);
            
            $nome = trim($_POST['nome'] ?? '');
            $capacidade = intval($_POST['capacidade'] ?? 0);
            $tipo = $_POST['tipo'] ?? '';
            
            if (empty($nome) || $capacidade < 1 || empty($tipo)) {
                echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
                exit();
            }
            
            // Gerar novo ID
            $novoId = 1;
            if (!empty($salas)) {
                $ids = array_column($salas, 'id');
                $novoId = max($ids) + 1;
            }
            
            $novaSala = [
                'id' => $novoId,
                'nome' => $nome,
                'capacidade' => $capacidade,
                'tipo' => $tipo,
                'data_criacao' => date('Y-m-d H:i:s')
            ];
            
            $salas[] = $novaSala;
            
            if (salvarSalas($arquivoSalas, $salas)) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Sala adicionada com sucesso!', 
                    'sala' => $novaSala
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a sala.']);
            }
            break;
            
        case 'editar':
            $salas = lerSalas($arquivoSalas);
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $capacidade = intval($_POST['capacidade'] ?? 0);
            $tipo = $_POST['tipo'] ?? '';
            
            if (empty($nome) || $capacidade < 1 || empty($tipo)) {
                echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
                exit();
            }
            
            $encontrada = false;
            foreach ($salas as &$sala) {
                if ($sala['id'] === $id) {
                    $sala['nome'] = $nome;
                    $sala['capacidade'] = $capacidade;
                    $sala['tipo'] = $tipo;
                    $encontrada = true;
                    break;
                }
            }
            
            if ($encontrada && salvarSalas($arquivoSalas, $salas)) {
                echo json_encode(['status' => 'success', 'message' => 'Sala editada com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao editar a sala.']);
            }
            break;
            
        case 'excluir':
            $salas = lerSalas($arquivoSalas);
            $id = intval($_POST['id'] ?? 0);
            
            $novaLista = array_filter($salas, function($sala) use ($id) {
                return $sala['id'] !== $id;
            });
            
            $novaLista = array_values($novaLista);
            
            if (count($novaLista) < count($salas) && salvarSalas($arquivoSalas, $novaLista)) {
                echo json_encode(['status' => 'success', 'message' => 'Sala excluída com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir a sala.']);
            }
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
}
?>