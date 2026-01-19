<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    <!-- Fontes e ícones -->
    <link rel="stylesheet" href="../../css/dashboard/cliente/novo_agendamento.css">
    <title>Agendamento Visita</title>
</head>
<body>
    <?php
    // Configurações iniciais
    $backgroundImage = '../../imagens/telaagendamento.png';
    
    // Horários disponíveis
    $horarios = [
        '-- Escolha --', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', 
        '11:00', '11:30', '13:00', '13:30', '14:00', '14:30', '15:00', 
        '15:30', '16:00', '16:30', '17:00'
    ];
    
    // Valores padrão dos campos
    $data_val = '';
    $horario_val = '';
    $nome_responsavel_val = '';
    $telefone_val = '';
    $nome_aluno_val = '';
    $cpf_responsavel_val = '';
    $mensagem = '';
    $agendamento_sucesso = false;
    
    // Processamento do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data_val = isset($_POST['data']) ? $_POST['data'] : '';
        $horario_val = isset($_POST['horario']) ? $_POST['horario'] : '';
        $nome_responsavel_val = isset($_POST['nome_responsavel']) ? $_POST['nome_responsavel'] : '';
        $telefone_val = isset($_POST['telefone']) ? $_POST['telefone'] : '';
        $nome_aluno_val = isset($_POST['nome_aluno']) ? $_POST['nome_aluno'] : '';
        $cpf_responsavel_val = isset($_POST['cpf_responsavel']) ? $_POST['cpf_responsavel'] : '';
        
        if (empty($data_val)) {
            $mensagem = 'Por favor, informe a data.';
        } elseif ($horario_val === '-- Escolha --' || $horario_val === '') {
            $mensagem = 'Por favor, selecione um horário.';
        } elseif (empty($nome_responsavel_val) || empty($telefone_val) || empty($nome_aluno_val) || empty($cpf_responsavel_val)) {
            $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        } else {
            $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone_val);
            if (strlen($telefone_limpo) < 10) {
                $mensagem = 'Telefone inválido.';
            } elseif (!validarCPF($cpf_responsavel_val)) {
                $mensagem = 'CPF inválido.';
            } else {
                // Sucesso: Gerar Protocolo
                $data_formatada = date('d/m/Y', strtotime($data_val));
                $protocolo = 'AG' . date('YmdHis') . rand(100, 999);
                
                // --- SALVAR NO JSON ---
                $caminho_dados = '../dados/';
                $arquivo_json = $caminho_dados . 'dados.json';

                if (!is_dir($caminho_dados)) { mkdir($caminho_dados, 0777, true); }

                $novo_registro = [
                    'protocolo'         => $protocolo,
                    'data_visita'       => $data_val,
                    'horario'           => $horario_val,
                    'nome_responsavel'  => $nome_responsavel_val,
                    'nome_aluno'        => $nome_aluno_val,
                    'telefone'          => $telefone_val,
                    'cpf'               => $cpf_responsavel_val,
                    'data_registro'     => date('Y-m-d H:i:s'),
                    'confirmado'        => false // Status inicial para gerência
                ];

                $registros_atuais = [];
                if (file_exists($arquivo_json)) {
                    $registros_atuais = json_decode(file_get_contents($arquivo_json), true) ?? [];
                }
                $registros_atuais[] = $novo_registro;
                file_put_contents($arquivo_json, json_encode($registros_atuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $agendamento_sucesso = true;
                
                // --- MENSAGEM PARA O GESTOR (WHATSAPP) ---
                $numero_gestor = "5581994527528";
                $url_gerencia = "http://localhost/clinicaestrela/dashboard/clinica/visita_agendamento.php";
                
                $msg_gestor = "🚨 *NOVO AGENDAMENTO RECEBIDO*\n\n";
                $msg_gestor .= "👤 *Responsável:* " . $nome_responsavel_val . "\n";
                $msg_gestor .= "🎓 *Aluno:* " . $nome_aluno_val . "\n";
                $msg_gestor .= "📅 *Data:* " . $data_formatada . "\n";
                $msg_gestor .= "⏰ *Horário:* " . $horario_val . "\n";
                $msg_gestor .= "📞 *Fone Cliente:* " . $telefone_val . "\n";
                $msg_gestor .= "📋 *Protocolo:* " . $protocolo . "\n\n";
                $msg_gestor .= "🔗 *Acesse para confirmar:* \n" . $url_gerencia;
                
                $whatsapp_url = "https://wa.me/" . $numero_gestor . "?text=" . urlencode($msg_gestor);
            }
        }
    }
    
    function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) { $d += $cpf[$c] * (($t + 1) - $c); }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }
    ?>
    
    <div class="modal" id="modalConfirmacao" style="<?php echo $agendamento_sucesso ? 'display: flex;' : 'display: none;'; ?>">
        <div class="modal-content">
            <h2 class="modal-title">✓ Quase lá!</h2>
            <p class="modal-subtitle">Seus dados foram pré-cadastrados com sucesso.</p>
            <div class="modal-details">
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Protocolo:</span>
                    <span class="modal-detail-value"><?php echo $protocolo ?? ''; ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Data/Hora:</span>
                    <span class="modal-detail-value"><?php echo ($data_formatada ?? '') . ' às ' . ($horario_val ?? ''); ?></span>
                </div>
            </div>
            
            <div class="mensagem-whatsapp">
                <p>⚠️ **Atenção:** Clique no botão **ENVIAR** abaixo para notificar a clínica e aguardar a confirmação oficial.</p>
            </div>           
            <div class="modal-buttons">
                <button onclick="fecharModal()" class="modal-btn modal-btn-close">Fechar</button>
                <?php if (isset($whatsapp_url)): ?>
                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="modal-btn modal-btn-whatsapp">📱 ENVIAR</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="background-container" id="backgroundContainer">
        <img src="<?php echo $backgroundImage; ?>" alt="Background Agendamento" class="background-image" 
             onerror="this.onerror=null; document.getElementById('backgroundContainer').classList.add('fallback');">
    </div>
    
    <div class="main-container">
        <div class="form-container">
            <h1 class="form-title">Agendamento Visita</h1>
            <p class="form-subtitle">Preencha os dados abaixo para agendar sua visita</p>
            
            <form method="POST" action="" id="form-agendamento">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="data">Data</label>
                        <input type="date" name="data" id="data" value="<?php echo htmlspecialchars($data_val); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="horario">Horário</label>
                        <select name="horario" id="horario" required>
                            <?php foreach ($horarios as $horario): ?>
                                <option value="<?php echo htmlspecialchars($horario); ?>" <?php echo ($horario_val === $horario) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($horario); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome_responsavel">Nome Responsável</label>
                        <input type="text" name="nome_responsavel" id="nome_responsavel" value="<?php echo htmlspecialchars($nome_responsavel_val); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome_aluno">Nome do Aluno</label>
                        <input type="text" name="nome_aluno" id="nome_aluno" value="<?php echo htmlspecialchars($nome_aluno_val); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone (WhatsApp)</label>
                        <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" value="<?php echo htmlspecialchars($telefone_val); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf_responsavel">CPF do Responsável</label>
                        <input type="text" name="cpf_responsavel" id="cpf_responsavel" placeholder="000.000.000-00" value="<?php echo htmlspecialchars($cpf_responsavel_val); ?>" required>
                    </div>
                </div>
                
                <div class="buttons-container">
                    <button type="submit" class="btn btn-confirmar"><span>✓ Confirmar Agendamento</span></button>
                    <button type="button" class="btn btn-limpar" id="btn-limpar"><span>Limpar</span></button>
                </div>
                
                <?php if (!empty($mensagem) && !$agendamento_sucesso): ?>
                    <div class="mensagem" style="color:red; text-align:center; margin-top:10px;"><?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <script src="../js/script.js"></script>
</body>
</html>