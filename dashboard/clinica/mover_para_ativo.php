<?php
// mover_para_ativo.php
header('Content-Type: application/json');

// Recebe os dados do JavaScript
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['index'])) {
    echo json_encode(['status' => 'error', 'message' => 'Índice não fornecido.']);
    exit;
}

$index = $data['index'];

// Caminhos dos arquivos
$caminhoPendentes = __DIR__ . '/../../dashboard/dados/pre-cad.json';
$caminhoAtivos = __DIR__ . '/../../dashboard/dados/ativo-cad.json';

// 1. Carrega os Pendentes
if (!file_exists($caminhoPendentes)) {
    echo json_encode(['status' => 'error', 'message' => 'Arquivo de pendentes não encontrado.']);
    exit;
}

$pendentes = json_decode(file_get_contents($caminhoPendentes), true);

// Verifica se o índice existe
if (!isset($pendentes[$index])) {
    echo json_encode(['status' => 'error', 'message' => 'Paciente não encontrado.']);
    exit;
}

// 2. Pega o paciente e muda o status
$pacienteParaMover = $pendentes[$index];
$pacienteParaMover['status'] = 'Ativo'; // A MÁGICA ACONTECE AQUI
$pacienteParaMover['data_ativacao'] = date('Y-m-d H:i:s'); // Adiciona data de ativação

// 3. Carrega (ou cria) a lista de Ativos
$ativos = [];
if (file_exists($caminhoAtivos)) {
    $ativos = json_decode(file_get_contents($caminhoAtivos), true);
    if (!is_array($ativos)) $ativos = [];
}

// 4. Adiciona na lista de Ativos
$ativos[] = $pacienteParaMover;

// 5. Remove da lista de Pendentes
array_splice($pendentes, $index, 1);

// 6. Salva os dois arquivos
if (file_put_contents($caminhoAtivos, json_encode($ativos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) &&
    file_put_contents($caminhoPendentes, json_encode($pendentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    
    echo json_encode(['status' => 'success', 'message' => 'Paciente ativado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao gravar nos arquivos.']);
}
?>