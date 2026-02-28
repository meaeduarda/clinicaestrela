<?php
// gerar_pdf_evolucao.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado.");
    exit();
}

// Pegar ID da evolução
$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    die('ID da evolução não fornecido');
}

// Carregar evoluções
$caminhoEvolucoes = __DIR__ . '/../../dashboard/dados/evolucao_pacientes.json';
$evolucao = null;

if (file_exists($caminhoEvolucoes)) {
    $evolucoes = json_decode(file_get_contents($caminhoEvolucoes), true) ?: [];
    
    foreach ($evolucoes as $e) {
        if ($e['id'] === $id) {
            $evolucao = $e;
            break;
        }
    }
}

if (!$evolucao) {
    die('Evolução não encontrada');
}

// Redirecionar para o primeiro anexo se existir
if (!empty($evolucao['anexos'])) {
    $primeiroAnexo = $evolucao['anexos'][0];
    header("Location: /clinicaestrela/dashboard/" . $primeiroAnexo['caminho']);
    exit();
} else {
    // Se não tiver anexo, mostrar mensagem
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>PDF não disponível</title>
        <style>
            body { font-family: Arial; text-align: center; padding: 50px; }
            .mensagem { background: #f8fafc; padding: 30px; border-radius: 10px; max-width: 500px; margin: 0 auto; }
            h1 { color: #ef4444; }
            .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="mensagem">
            <h1>📄 PDF não disponível</h1>
            <p>Esta evolução não possui arquivo PDF anexado.</p>
            <a href="evolucao_historico.php?paciente_id=<?php echo urlencode($evolucao['paciente_id']); ?>&paciente_nome=<?php echo urlencode($evolucao['paciente_nome']); ?>" class="btn">← Voltar ao Histórico</a>
        </div>
    </body>
    </html>
    <?php
}
?>