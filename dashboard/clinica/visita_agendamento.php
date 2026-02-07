<?php

// C:\wamp64\www\clinicaestrela\dashboard\clinica\painel_administrativo_pacientes.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];


$caminho_json = '../dados/dados_visita_agendamento.json'; // Corrigido: caminho relativo
$mensagem = '';
$tipo_mensagem = '';

// --- LÓGICA AJAX PARA MARCAR COMO CONFIRMADO NO JSON ---
if (isset($_GET['ajax_confirmar'])) {
    $prot = $_GET['ajax_confirmar'];
    if (file_exists($caminho_json)) {
        $dados_json = file_get_contents($caminho_json);
        $agendamentos = json_decode($dados_json, true) ?? [];

        $encontrado = false;
        foreach ($agendamentos as &$ag) {
            if ($ag['protocolo'] === $prot) {
                $ag['confirmado'] = true;
                $encontrado = true;
                break;
            }
        }
        
        if ($encontrado) {
            if (file_put_contents($caminho_json, json_encode($agendamentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                echo 'OK';
            } else {
                echo 'ERRO';
            }
        } else {
            echo 'NAO_ENCONTRADO';
        }
    } else {
        echo 'ARQUIVO_NAO_EXISTE';
    }
    exit;
}

// Lógica para Excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_protocolo'])) {
    $protocolo_para_excluir = $_POST['excluir_protocolo'];
    if (file_exists($caminho_json)) {
        $dados_json = file_get_contents($caminho_json);
        $agendamentos = json_decode($dados_json, true) ?? [];
        $novos_agendamentos = array_filter($agendamentos, function ($item) use ($protocolo_para_excluir) {
            return $item['protocolo'] !== $protocolo_para_excluir;
        });
        if (file_put_contents($caminho_json, json_encode(array_values($novos_agendamentos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $mensagem = "✓ Agendamento $protocolo_para_excluir removido com sucesso.";
            $tipo_mensagem = "success";
        }
    }
}

// Leitura dos dados
$agendamentos = [];
if (file_exists($caminho_json)) {
    $dados_json = file_get_contents($caminho_json);
    if ($dados_json !== false && trim($dados_json) !== '') {
        $agendamentos = json_decode($dados_json, true) ?? [];
        usort($agendamentos, function ($a, $b) {
            return strtotime($b['data_registro']) - strtotime($a['data_registro']);
        });
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerência de Visitas - Clínica Estrela</title>   
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Clinica Estrela" />
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest" />
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/visita_agendamento.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="background-overlay"></div>

    <div class="container-listagem">
        <header class="header-listagem">
            <div class="logo-container">
                <img src="../../imagens/logo_clinica_estrela.png" alt="Logo Clínica Estrela">
            </div>

            <div class="logo-area"><span>Clínica<strong>Estrela</strong></span></div>
            <h1>Painel de Gerência</h1>
            <p>Controle e Confirmação de Visitas Agendadas</p>
        </header>

        <div class="content-area">
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <i class="fa-solid fa-check-circle"></i> <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table-agendamentos">
                    <thead>
                        <tr>
                            <th>Protocolo</th>
                            <th>Data/Hora</th>
                            <th>Responsável / Aluno</th>
                            <th>Contato</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agendamentos)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">Nenhum agendamento pendente.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($agendamentos as $ag):
                                $is_confirmado = isset($ag['confirmado']) && $ag['confirmado'] === true;
                                $tel_limpo = preg_replace('/\D/', '', $ag['telefone']);
                                $msg_confirmacao = "Olá *" . $ag['nome_responsavel'] . "*, a Clínica Estrela confirma a visita do aluno *" . $ag['nome_aluno'] . "* agendada para " . date('d/m/Y', strtotime($ag['data_visita'])) . " às " . $ag['horario'] . ". Estamos aguardando você!";
                                $link_wa = "https://wa.me/55" . $tel_limpo . "?text=" . urlencode($msg_confirmacao);
                            ?>
                                <tr>
                                    <td class="col-protocolo"><strong><?php echo $ag['protocolo']; ?></strong></td>
                                    <td>
                                        <div class="info-data">
                                            <span><i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ag['data_visita'])); ?></span>
                                            <span class="badge-hora"><?php echo $ag['horario']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-nomes">
                                            <span class="nome-resp"><?php echo htmlspecialchars($ag['nome_responsavel']); ?></span>
                                            <span class="nome-aluno">Aluno: <?php echo htmlspecialchars($ag['nome_aluno']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/55<?php echo $tel_limpo; ?>" target="_blank" class="btn-whatsapp-table">
                                            <i class="fa-brands fa-whatsapp"></i> <?php echo htmlspecialchars($ag['telefone']); ?>
                                        </a>
                                    </td>
                                    <td class="col-acoes">
                                        <div class="action-buttons">
                                            <a href="<?php echo $link_wa; ?>"
                                                target="_blank"
                                                class="btn-confirmar-msg <?php echo $is_confirmado ? 'btn-confirmado-disabled' : ''; ?>"
                                                onclick="confirmarClick(this, '<?php echo $ag['protocolo']; ?>')"
                                                title="Confirmar via WhatsApp">
                                                <i class="fa-solid <?php echo $is_confirmado ? 'fa-check-double' : 'fa-check'; ?>"></i>
                                                <?php echo $is_confirmado ? 'Enviado' : 'Confirmar'; ?>
                                            </a>

                                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este agendamento?');" style="display:inline;">
                                                <input type="hidden" name="excluir_protocolo" value="<?php echo $ag['protocolo']; ?>">
                                                <button type="submit" class="btn-excluir" title="Excluir agendamento"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="footer-actions">
                <a href="painel_administrativo_pacientes.php" class="btn-voltar"><i class="fa-solid fa-arrow-left"></i> Painel</a>
                <a href="../cliente/novo_agendamento.php" class="btn-novo"><i class="fa-solid fa-plus"></i> Novo</a>
            </div>
        </div>
    </div>

    <script src="../js/script_visita_agendamento.js"></script>
</body>

</html>