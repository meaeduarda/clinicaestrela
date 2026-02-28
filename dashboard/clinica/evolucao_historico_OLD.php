<?php
// evolucao_historico.php - VERSÃO NOVA
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado.");
    exit();
}

// Pegar dados da URL
$paciente_id = $_GET['paciente_id'] ?? '';
$paciente_nome = $_GET['paciente_nome'] ?? '';

// Carregar JSON
$caminho = __DIR__ . '/../../dashboard/dados/evolucao_pacientes.json';
$evolucoes = [];

if (file_exists($caminho)) {
    $evolucoes = json_decode(file_get_contents($caminho), true) ?: [];
}

// Filtrar
$evolucoes_paciente = array_filter($evolucoes, function($e) use ($paciente_id) {
    return ($e['paciente_id'] ?? '') === $paciente_id;
});

// ===== DEBUG =====
echo '<div style="background: #000; color: #0f0; padding: 20px; margin: 10px;">';
echo "<h2>DEBUG:</h2>";
echo "<p>ID: $paciente_id</p>";
echo "<p>Total no JSON: " . count($evolucoes) . "</p>";
echo "<p>Encontradas: " . count($evolucoes_paciente) . "</p>";
echo "</div>";
// ===== FIM DEBUG =====

?>
<!DOCTYPE html>
<html>
<head>
    <title>Histórico de <?php echo htmlspecialchars($paciente_nome); ?></title>
</head>
<body>
    <h1>Histórico de <?php echo htmlspecialchars($paciente_nome); ?></h1>
    
    <?php if (empty($evolucoes_paciente)): ?>
        <p style="color: red;">Nenhuma evolução encontrada</p>
    <?php else: ?>
        <?php foreach ($evolucoes_paciente as $e): ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                <p><strong>Data:</strong> <?php echo $e['data_sessao'] ?? ''; ?></p>
                <p><strong>Terapia:</strong> <?php echo $e['terapia'] ?? ''; ?></p>
                <p><strong>Terapeuta:</strong> <?php echo $e['terapeuta'] ?? ''; ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>