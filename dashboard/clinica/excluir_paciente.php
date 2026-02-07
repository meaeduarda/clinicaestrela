<?php
// dashboard/clinica/excluir_paciente.php

// Habilitar debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit();
}

// Verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit();
}

// Verificar permissões (opcional)
$perfilLogado = $_SESSION['usuario_perfil'];
$perfisPermitidos = [
        'admin', 
        'medico', 
        'coordenadorequipe'
        // Adicione aqui se quiser permitir exclusão para outros perfis
        ];        

if (!in_array($perfilLogado, $perfisPermitidos)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para excluir pacientes']);
    exit();
}

// Função para registrar log
function gravarLog($mensagem) {
    $log = date('Y-m-d H:i:s') . " - " . $mensagem . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/exclusoes.log', $log, FILE_APPEND);
}

// Receber dados JSON
$inputJSON = file_get_contents('php://input');
$dados = json_decode($inputJSON, true);

if (!$dados || !isset($dados['index']) || !isset($dados['origem'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
    exit();
}

$index = (int)$dados['index'];
$origem = $dados['origem'];

// Definir caminho do arquivo baseado na origem
if ($origem === 'ativo') {
    $arquivoJson = __DIR__ . '/../dados/ativo-cad.json';
} elseif ($origem === 'pendente') {
    $arquivoJson = __DIR__ . '/../dados/pre-cad.json';
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Origem inválida']);
    exit();
}

// Verificar se o arquivo existe
if (!file_exists($arquivoJson)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Arquivo de dados não encontrado']);
    exit();
}

// Ler e processar o arquivo JSON
try {
    $conteudo = file_get_contents($arquivoJson);
    $pacientes = json_decode($conteudo, true);
    
    if (!is_array($pacientes)) {
        throw new Exception('Formato de arquivo inválido');
    }
    
    // Verificar se o índice existe
    if (!isset($pacientes[$index])) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Paciente não encontrado']);
        exit();
    }
    
    // Salvar dados do paciente para log (opcional)
    $pacienteExcluido = $pacientes[$index];
    $nomePaciente = $pacienteExcluido['nome_completo'] ?? 'Paciente sem nome';
    
    // Remover o paciente do array
    array_splice($pacientes, $index, 1);
    
    // Reindexar o array (opcional, mas recomendado)
    $pacientes = array_values($pacientes);
    
    // Salvar o arquivo atualizado
    $jsonAtualizado = json_encode($pacientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($arquivoJson, $jsonAtualizado) === false) {
        throw new Exception('Falha ao salvar arquivo');
    }
    
    // Registrar log da exclusão
    gravarLog("Paciente excluído: $nomePaciente (Índice: $index, Origem: $origem) por usuário: {$_SESSION['usuario_nome']}");
    
    // Registrar em um arquivo de backup (opcional)
    $backupFile = __DIR__ . '/../dados/backup_exclusoes.json';
    $backupData = [];
    
    if (file_exists($backupFile)) {
        $backupContent = file_get_contents($backupFile);
        $backupData = json_decode($backupContent, true) ?: [];
    }
    
    $backupData[] = [
        'data_exclusao' => date('Y-m-d H:i:s'),
        'usuario' => $_SESSION['usuario_nome'],
        'paciente' => $pacienteExcluido,
        'origem' => $origem
    ];
    
    file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Retornar sucesso
    echo json_encode([
        'status' => 'success', 
        'message' => 'Paciente excluído com sucesso',
        'nome_paciente' => $nomePaciente,
        'novo_total' => count($pacientes)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno: ' . $e->getMessage()]);
    gravarLog("ERRO ao excluir paciente: " . $e->getMessage());
}
?>