<?php
// dashboard/clinica/detalhes_paciente.php

// Habilitar debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Verificar se é uma requisição GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit();
}

// Receber parâmetros
$index = isset($_GET['index']) ? (int)$_GET['index'] : null;
$origem = isset($_GET['origem']) ? $_GET['origem'] : 'ativo';

if ($index === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Índice não fornecido']);
    exit();
}

// ✅✅✅ CORREÇÃO AQUI - CAMINHO CORRETO ✅✅✅
// De: __DIR__ . '/../../dados/ativo-cad.json'  ❌ (subia 2 níveis)
// Para: __DIR__ . '/../dados/ativo-cad.json'   ✅ (sobe 1 nível apenas)
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
    echo json_encode([
        'status' => 'error', 
        'message' => 'Arquivo de dados não encontrado: ' . basename($arquivoJson),
        'caminho' => $arquivoJson
    ]);
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
    
    // Calcular idade
    function calcularIdade($dataNascimento) {
        if (empty($dataNascimento)) {
            return 'Idade n/d';
        }
        
        try {
            $dataNasc = DateTime::createFromFormat('Y-m-d', $dataNascimento);
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
    
    $paciente = $pacientes[$index];
    
    // Adicionar idade calculada
    $dataNascimento = isset($paciente['nascimento']) ? $paciente['nascimento'] : (isset($paciente['data_nascimento']) ? $paciente['data_nascimento'] : '');
    $paciente['idade_calculada'] = calcularIdade($dataNascimento);
    
    // Formatar datas para exibição
    if (isset($paciente['data_ativacao'])) {
        $dataAtivacao = new DateTime($paciente['data_ativacao']);
        $paciente['data_ativacao_formatada'] = $dataAtivacao->format('d/m/Y H:i');
    }
    
    if (isset($paciente['data_registro'])) {
        $dataRegistro = new DateTime($paciente['data_registro']);
        $paciente['data_registro_formatada'] = $dataRegistro->format('d/m/Y H:i');
    }
    
    // Retornar os dados do paciente
    echo json_encode([
        'status' => 'success',
        'paciente' => $paciente
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>