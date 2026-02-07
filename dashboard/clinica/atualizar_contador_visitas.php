<?php
// atualizar_contador_visitas.php
session_start();

// Verificar se o usuário está logado (opcional, mas recomendado)
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit();
}

// Caminho para o arquivo JSON
$arquivoVisitas = __DIR__ . '/../dados/dados_visita_agendamento.json';
$count = 0;

if (file_exists($arquivoVisitas)) {
    $conteudo = file_get_contents($arquivoVisitas);
    if (!empty($conteudo)) {
        $agendamentos = json_decode($conteudo, true);
        if (is_array($agendamentos)) {
            foreach ($agendamentos as $agendamento) {
                if (isset($agendamento['confirmado']) && $agendamento['confirmado'] === false) {
                    $count++;
                }
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'count' => $count,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>