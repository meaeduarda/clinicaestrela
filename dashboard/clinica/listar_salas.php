<?php
// listar_salas.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$arquivoSalas = __DIR__ . '/../../dashboard/dados/salas.json';

if (!file_exists($arquivoSalas)) {
    // Criar salas padrão se o arquivo não existir
    $salasPadrao = [
        ['id' => 1, 'nome' => 'Sala 01 - ABA', 'capacidade' => 3, 'tipo' => 'ABA', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 2, 'nome' => 'Sala 02 - Fono', 'capacidade' => 3, 'tipo' => 'Fono', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 3, 'nome' => 'Sala 03 - Fono', 'capacidade' => 2, 'tipo' => 'Fono', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 4, 'nome' => 'Sala 04 - ABA', 'capacidade' => 4, 'tipo' => 'ABA', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 5, 'nome' => 'Sala 05 - ABA', 'capacidade' => 4, 'tipo' => 'ABA', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 6, 'nome' => 'Sala 06 - TO', 'capacidade' => 2, 'tipo' => 'TO', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 7, 'nome' => 'Sala 07 - TO', 'capacidade' => 2, 'tipo' => 'TO', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 8, 'nome' => 'Sala 08 - Funcional', 'capacidade' => 2, 'tipo' => 'Funcional', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 9, 'nome' => 'Sala 09 - Jogos', 'capacidade' => 4, 'tipo' => 'ABA', 'data_criacao' => date('Y-m-d H:i:s')],
        ['id' => 10, 'nome' => 'Sala 10 - ABA', 'capacidade' => 2, 'tipo' => 'ABA', 'data_criacao' => date('Y-m-d H:i:s')]
    ];
    file_put_contents($arquivoSalas, json_encode($salasPadrao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$conteudo = file_get_contents($arquivoSalas);
$salas = json_decode($conteudo, true);

if (!is_array($salas)) {
    $salas = [];
}

echo json_encode($salas);
?>