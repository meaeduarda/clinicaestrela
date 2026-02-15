<?php
// carregar_pei.php
session_start();

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Acesso negado';
    exit();
}

$pacienteId = $_GET['id'] ?? '';
$arquivo = $_GET['arquivo'] ?? '';

if (empty($pacienteId) || empty($arquivo)) {
    header('HTTP/1.0 404 Not Found');
    echo 'Arquivo não encontrado';
    exit();
}

// Validar se o paciente realmente tem permissão para acessar este PEI
// (opcional - pode ser implementado conforme necessidade)
$temPermissao = false;

// Verificar na sessão se o paciente existe e tem este PEI
if (isset($_SESSION['pacientes_pei'])) {
    foreach ($_SESSION['pacientes_pei'] as $paciente) {
        if ($paciente['id'] === $pacienteId && $paciente['pei_arquivo'] === $arquivo) {
            $temPermissao = true;
            break;
        }
    }
}

// Se não encontrou na sessão, verificar no JSON de PEIs salvos
if (!$temPermissao) {
    $jsonFile = __DIR__ . '/../dados/pei_salvo/pei_salvo.json';
    if (file_exists($jsonFile)) {
        $peisSalvos = json_decode(file_get_contents($jsonFile), true) ?: [];
        if (isset($peisSalvos[$pacienteId]) && $peisSalvos[$pacienteId]['arquivo'] === $arquivo) {
            $temPermissao = true;
        }
    }
}

if (!$temPermissao) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Acesso negado a este arquivo';
    exit();
}

// Caminho do arquivo - usar basename para segurança
$nomeArquivoSeguro = basename($arquivo);
$caminhoArquivo = __DIR__ . '/../uploads/pei/' . $nomeArquivoSeguro;

// Verificar se o arquivo existe e é um PDF
if (!file_exists($caminhoArquivo)) {
    header('HTTP/1.0 404 Not Found');
    echo 'Arquivo não encontrado';
    exit();
}

// Verificar se é realmente um PDF (segurança adicional)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $caminhoArquivo);
finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    header('HTTP/1.0 403 Forbidden');
    echo 'Tipo de arquivo inválido';
    exit();
}

// Servir o arquivo PDF com headers adequados
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $peisSalvos[$pacienteId]['nome_original'] . '"');
header('Content-Length: ' . filesize($caminhoArquivo));
header('Cache-Control: public, max-age=86400'); // Cache de 24 horas
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

// Limpar buffer de saída
if (ob_get_level()) {
    ob_end_clean();
}

// Ler e enviar o arquivo
readfile($caminhoArquivo);
exit();