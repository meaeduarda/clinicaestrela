<?php
// limpar_salas.php
$arquivoSalas = __DIR__ . '/../../dashboard/dados/salas.json';

// Criar array vazio
$salas = [];

// Salvar como array vazio
if (file_put_contents($arquivoSalas, json_encode($salas, JSON_PRETTY_PRINT))) {
    echo "Arquivo limpo com sucesso!";
} else {
    echo "Erro ao limpar arquivo.";
}

echo "<br><br><a href='painel_salas_teste.php'>Ir para o painel de teste</a>";
?>