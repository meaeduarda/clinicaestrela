<?php
// salvar_pei.php
session_start();

header('Content-Type: application/json');

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

// Verificar se arquivo foi enviado
if (!isset($_FILES['pei_file']) || $_FILES['pei_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erro no upload do arquivo']);
    exit();
}

$pacienteId = $_POST['paciente_id'] ?? '';
$acao = $_POST['acao'] ?? 'anexar';

if (empty($pacienteId)) {
    echo json_encode(['success' => false, 'message' => 'ID do paciente não fornecido']);
    exit();
}

// Configurar diretórios
$uploadDir = __DIR__ . '/../uploads/pei/';
$jsonDir = __DIR__ . '/../dados/pei_salvo/';

// Criar diretórios se não existirem
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

// Processar arquivo
$arquivo = $_FILES['pei_file'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

// Validar extensão
if ($extensao !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Apenas arquivos PDF são permitidos']);
    exit();
}

// Validar tamanho (10MB)
if ($arquivo['size'] > 10 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Tamanho máximo: 10MB']);
    exit();
}

// Se for edição, remover arquivo antigo
if ($acao === 'editar') {
    $jsonFile = $jsonDir . 'pei_salvo.json';
    if (file_exists($jsonFile)) {
        $peisSalvos = json_decode(file_get_contents($jsonFile), true) ?: [];
        if (isset($peisSalvos[$pacienteId]) && !empty($peisSalvos[$pacienteId]['arquivo'])) {
            $arquivoAntigo = $uploadDir . $peisSalvos[$pacienteId]['arquivo'];
            if (file_exists($arquivoAntigo)) {
                unlink($arquivoAntigo); // Remove arquivo antigo
            }
        }
    }
}

// Gerar nome único para o arquivo
$nomeArquivo = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $arquivo['name']);
$caminhoCompleto = $uploadDir . $nomeArquivo;

// Mover arquivo
if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar arquivo']);
    exit();
}

// Atualizar JSON de PEIs salvos
$jsonFile = $jsonDir . 'pei_salvo.json';
$peisSalvos = [];

if (file_exists($jsonFile)) {
    $peisSalvos = json_decode(file_get_contents($jsonFile), true) ?: [];
}

// Salvar informações do PEI
$peisSalvos[$pacienteId] = [
    'arquivo' => $nomeArquivo,
    'nome_original' => $arquivo['name'],
    'data_upload' => date('Y-m-d H:i:s'),
    'usuario_id' => $_SESSION['usuario_id'],
    'mes_referencia' => date('m'),
    'ano_referencia' => date('Y')
];

// Salvar JSON com formatação adequada para UTF-8
file_put_contents($jsonFile, json_encode($peisSalvos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// ===== ATUALIZAR A SESSÃO DO USUÁRIO =====
if (isset($_SESSION['pacientes_pei'])) {
    $sessaoAtualizada = false;
    foreach ($_SESSION['pacientes_pei'] as &$paciente) {
        if ($paciente['id'] === $pacienteId) {
            $paciente['pei_anexado'] = true;
            $paciente['pei_arquivo'] = $nomeArquivo;
            $paciente['pei_nome_original'] = $arquivo['name'];
            $paciente['pei_data_upload'] = date('Y-m-d H:i:s');
            $sessaoAtualizada = true;
            break;
        }
    }
    
    // Se não encontrou o paciente na sessão, pode ser necessário recarregar
    if (!$sessaoAtualizada) {
        // Opcional: recarregar pacientes da sessão ou do JSON
        error_log("Paciente ID $pacienteId não encontrado na sessão durante atualização do PEI");
    }
}

echo json_encode(['success' => true, 'message' => 'PEI salvo com sucesso']);