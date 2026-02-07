<?php
// dashboard/clinica/salvar_dados.php

// Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

function gravarLog($mensagem) {
    $log = date('Y-m-d H:i:s') . " - " . $mensagem . PHP_EOL;
    file_put_contents('log_erros.txt', $log, FILE_APPEND);
}

try {
    // 1. Recebe JSON
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception("Método inválido");
    $inputJSON = file_get_contents('php://input');
    if (!$inputJSON) throw new Exception("Nenhum dado recebido");
    
    $novoDado = json_decode($inputJSON, true);
    if (json_last_error() !== JSON_ERROR_NONE) throw new Exception("JSON inválido");

    // 2. Caminhos
    $pastaDados = __DIR__ . '/../dados'; 
    
    // Verifica se é para salvar no arquivo de ativos
    $origem = isset($novoDado['origem']) ? $novoDado['origem'] : 'pendente';
    if ($origem === 'ativo') {
        $caminhoArquivo = $pastaDados . '/ativo-cad.json';
    } else {
        $caminhoArquivo = $pastaDados . '/pre-cad.json';
    }
    
    // Remove o campo origem para não salvar no JSON
    if (isset($novoDado['origem'])) {
        unset($novoDado['origem']);
    }

    // Mantém compatibilidade com campo nascimento
    if (isset($novoDado['data_nascimento']) && !empty($novoDado['data_nascimento'])) {
        $novoDado['nascimento'] = $novoDado['data_nascimento'];
    }

    if (!is_dir($pastaDados)) mkdir($pastaDados, 0777, true);

    // 3. Lê dados atuais
    $dadosAtuais = [];
    if (file_exists($caminhoArquivo)) {
        $conteudo = file_get_contents($caminhoArquivo);
        $dadosAtuais = json_decode($conteudo, true);
        if (!is_array($dadosAtuais)) $dadosAtuais = [];
    }

    // --- LÓGICA DE ATUALIZAÇÃO VS CRIAÇÃO ---
    $mensagem = "";

    // Verifica se veio um índice válido para edição
    if (isset($novoDado['index']) && is_numeric($novoDado['index'])) {
        $index = (int)$novoDado['index'];
        
        if (isset($dadosAtuais[$index])) {
            // Remove o campo 'index' para não salvar lixo no JSON
            unset($novoDado['index']);
            
            // Mescla os dados antigos com os novos (preserva o que não veio no form)
            $dadosAtuais[$index] = array_merge($dadosAtuais[$index], $novoDado);
            
            // Atualiza data de modificação
            $dadosAtuais[$index]['data_atualizacao'] = date('Y-m-d H:i:s');
            
            if ($origem === 'ativo') {
                $mensagem = 'Paciente ativo atualizado com sucesso!';
                gravarLog("Paciente ativo index $index atualizado.");
            } else {
                $mensagem = 'Paciente atualizado com sucesso!';
                gravarLog("Paciente index $index atualizado.");
            }
        } else {
            // Se índice não existe, adiciona como novo (no arquivo correspondente)
            $novoDado['status_paciente'] = 'Pendente';
            $novoDado['data_registro'] = date('Y-m-d H:i:s');
            unset($novoDado['index']);
            $dadosAtuais[] = $novoDado;
            $mensagem = 'Índice não encontrado. Salvo como novo.';
        }
    } else {
        // Novo Cadastro (Comportamento padrão)
        $novoDado['status_paciente'] = 'Pendente';
        $novoDado['data_registro'] = date('Y-m-d H:i:s');
        $dadosAtuais[] = $novoDado;
        $mensagem = 'Novo paciente cadastrado com sucesso!';
        gravarLog("Novo paciente cadastrado.");
    }

    // 4. Salva no arquivo
    $resultado = file_put_contents($caminhoArquivo, json_encode($dadosAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if ($resultado === false) throw new Exception("Falha ao escrever no arquivo.");

    echo json_encode(['status' => 'success', 'message' => $mensagem]);

} catch (Exception $e) {
    gravarLog("ERRO: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>