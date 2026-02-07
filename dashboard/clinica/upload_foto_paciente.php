<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit;
}

if (!isset($_FILES['foto_paciente'])) {
    echo json_encode(['status' => 'error', 'message' => 'Arquivo não enviado']);
    exit;
}

$arquivo = $_FILES['foto_paciente'];

if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no upload']);
    exit;
}

$ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
$permitidas = ['jpg','jpeg','png','webp','gif'];

if (!in_array($ext, $permitidas)) {
    echo json_encode(['status' => 'error', 'message' => 'Formato não permitido']);
    exit;
}

if ($arquivo['size'] > 2 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'Arquivo maior que 2MB']);
    exit;
}

$pasta = __DIR__ . '/../../dashboard/dados/fotos_pacientes/';

if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

$nome = uniqid('paciente_', true) . '.' . $ext;
$caminhoFisico = $pasta . $nome;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) {
    echo json_encode(['status' => 'error', 'message' => 'Falha ao salvar imagem']);
    exit;
}

// ===============================
// Apaga foto antiga (se existir)
// ===============================

if (!empty($_POST['foto_antiga'])) {

    $fotoAntiga = $_POST['foto_antiga'];

    // só apaga se for foto local do projeto
    if (
        strpos($fotoAntiga, '/clinicaestrela/dashboard/dados/fotos_pacientes/') === 0
    ) {

        $caminhoFisicoAntigo =
            $_SERVER['DOCUMENT_ROOT'] . $fotoAntiga;

        if (is_file($caminhoFisicoAntigo)) {
            @unlink($caminhoFisicoAntigo);
        }

    }
}

$caminhoRelativo = '/clinicaestrela/dashboard/dados/fotos_pacientes/' . $nome;


echo json_encode([
    'status' => 'success',
    'foto'   => $caminhoRelativo
]);
