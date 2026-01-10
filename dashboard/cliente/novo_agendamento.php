<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Visita</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }
        
        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
        .main-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
        }
        
        .form-container {
            width: 100%;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        
        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .form-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 400;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        label {
            display: block;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        select, input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            color: #333;
            transition: all 0.3s ease;
        }
        
        select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23555555"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 20px;
            padding-right: 45px;
        }
        
        input::placeholder {
            color: #999;
        }
        
        select:focus, input:focus {
            outline: none;
            border-color: #4a6ee0;
            box-shadow: 0 0 0 3px rgba(74, 110, 224, 0.1);
        }
        
        .buttons-container {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            flex: 1;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 52px;
        }
        
        .btn-confirmar {
            background: linear-gradient(to right, #4a6ee0, #3a5ed0);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 110, 224, 0.3);
        }
        
        .btn-confirmar:hover {
            background: linear-gradient(to right, #3a5ed0, #2a4ec0);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 110, 224, 0.4);
        }
        
        .btn-limpar {
            background-color: #f8f9fa;
            color: #495057;
            border: 2px solid #dee2e6;
        }
        
        .btn-limpar:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }
        
        .aviso {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-top: 25px;
            font-style: italic;
            padding: 10px;
            background-color: rgba(248, 249, 250, 0.8);
            border-radius: 6px;
            border-left: 4px solid #4a6ee0;
        }
        
        .mensagem {
            text-align: center;
            color: #4a6ee0;
            font-weight: 600;
            font-size: 15px;
            margin-top: 15px;
            padding: 12px;
            background-color: rgba(74, 110, 224, 0.1);
            border-radius: 6px;
            animation: fadeIn 0.5s ease;
        }
        
        .mensagem-whatsapp {
            text-align: center;
            color: #25D366;
            font-weight: 600;
            font-size: 15px;
            margin-top: 15px;
            padding: 15px;
            background-color: rgba(37, 211, 102, 0.1);
            border-radius: 6px;
            border-left: 4px solid #25D366;
            animation: fadeIn 0.5s ease;
        }
        
        .whatsapp-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #25D366;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .whatsapp-link:hover {
            background-color: #1da851;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        
        .form-group.error input,
        .form-group.error select {
            border-color: #dc3545;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Modal de confirmação */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .modal-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .modal-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4a6ee0;
        }
        
        .modal-detail-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .modal-detail-label {
            font-weight: 600;
            color: #2c3e50;
            display: inline-block;
            width: 150px;
        }
        
        .modal-detail-value {
            color: #495057;
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .modal-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .modal-btn-whatsapp {
            background-color: #25D366;
            color: white;
        }
        
        .modal-btn-whatsapp:hover {
            background-color: #1da851;
        }
        
        .modal-btn-close {
            background-color: #f8f9fa;
            color: #495057;
            border: 2px solid #dee2e6;
        }
        
        .modal-btn-close:hover {
            background-color: #e9ecef;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .main-container {
                max-width: 95%;
                min-height: auto;
            }
            
            .form-container {
                padding: 30px 25px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .buttons-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-title {
                font-size: 24px;
            }
            
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            
            .modal-detail-label {
                width: 120px;
            }
            
            .modal-buttons {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 20px 15px;
            }
            
            .form-title {
                font-size: 22px;
            }
            
            select, input {
                padding: 12px 14px;
                font-size: 15px;
            }
            
            .btn {
                padding: 14px 20px;
                font-size: 15px;
                min-height: 48px;
            }
            
            .modal-detail-label {
                width: 100%;
                display: block;
                margin-bottom: 5px;
            }
            
            .modal-detail-value {
                display: block;
            }
        }
        
        /* Estilo para quando a imagem de fundo não carrega */
        .background-container.fallback {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
    <?php
    // Configurações iniciais
    $backgroundImage = '../../imagens/telaagendamento.png';
    
    // Horários disponíveis
    $horarios = [
        '-- Escolha --',
        '08:00',
        '08:30',
        '09:00',
        '09:30',
        '10:00',
        '10:30',
        '11:00',
        '11:30',
        '13:00',
        '13:30',
        '14:00',
        '14:30',
        '15:00',
        '15:30',
        '16:00',
        '16:30',
        '17:00'
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
    
    // Processamento do formulário (se enviado)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Coletar dados do formulário
        $data_val = isset($_POST['data']) ? $_POST['data'] : '';
        $horario_val = isset($_POST['horario']) ? $_POST['horario'] : '';
        $nome_responsavel_val = isset($_POST['nome_responsavel']) ? $_POST['nome_responsavel'] : '';
        $telefone_val = isset($_POST['telefone']) ? $_POST['telefone'] : '';
        $nome_aluno_val = isset($_POST['nome_aluno']) ? $_POST['nome_aluno'] : '';
        $cpf_responsavel_val = isset($_POST['cpf_responsavel']) ? $_POST['cpf_responsavel'] : '';
        
        // Validações básicas
        if (empty($data_val)) {
            $mensagem = 'Por favor, informe a data.';
        } elseif ($horario_val === '-- Escolha --' || $horario_val === '') {
            $mensagem = 'Por favor, selecione um horário.';
        } elseif (empty($nome_responsavel_val) || empty($telefone_val) || empty($nome_aluno_val) || empty($cpf_responsavel_val)) {
            $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        } else {
            // Validar formato do telefone
            $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone_val);
            if (strlen($telefone_limpo) < 10) {
                $mensagem = 'Telefone inválido. Digite um número com DDD + número.';
            } 
            // Validar CPF
            elseif (!validarCPF($cpf_responsavel_val)) {
                $mensagem = 'CPF inválido. Por favor, verifique o número digitado.';
            } else {
                // Formatando a data para exibição
                $data_formatada = date('d/m/Y', strtotime($data_val));
                
                // Gerar número de protocolo
                $protocolo = 'AG' . date('YmdHis') . rand(100, 999);
                
                // Mensagem de sucesso
                $mensagem = '✓ Agendamento realizado com sucesso! Protocolo: ' . $protocolo;
                $agendamento_sucesso = true;
                
                // Preparar mensagem para WhatsApp
                $mensagem_whatsapp = "📋 *CONFIRMAÇÃO DE AGENDAMENTO*\n\n";
                $mensagem_whatsapp .= "✅ *Agendamento Confirmado!*\n";
                $mensagem_whatsapp .= "📋 *Protocolo:* " . $protocolo . "\n\n";
                $mensagem_whatsapp .= "👤 *Dados do Agendamento:*\n";
                $mensagem_whatsapp .= "• *Responsável:* " . $nome_responsavel_val . "\n";
                $mensagem_whatsapp .= "• *Aluno:* " . $nome_aluno_val . "\n";
                $mensagem_whatsapp .= "• *CPF:* " . $cpf_responsavel_val . "\n";
                $mensagem_whatsapp .= "• *Telefone:* " . $telefone_val . "\n\n";
                $mensagem_whatsapp .= "📅 *Data da Visita:* " . $data_formatada . "\n";
                $mensagem_whatsapp .= "⏰ *Horário:* " . $horario_val . "\n\n";
                $mensagem_whatsapp .= "📍 *Local:* Clínica Estrela\n";
                $mensagem_whatsapp .= "📞 *Nosso Contato:* (11) 99999-9999\n\n";
                $mensagem_whatsapp .= "📌 *Instruções:*\n";
                $mensagem_whatsapp .= "• Chegar 15 minutos antes do horário\n";
                $mensagem_whatsapp .= "• Trazer documento de identidade\n";
                $mensagem_whatsapp .= "• Trazer CPF do responsável\n\n";
                $mensagem_whatsapp .= "⚠️ *Importante:*\n";
                $mensagem_whatsapp .= "Para cancelar ou remarcar, entre em contato com 48h de antecedência.";
                
                // Codificar mensagem para URL
                $mensagem_whatsapp_url = urlencode($mensagem_whatsapp);
                
                // Número da recepção clínica (exemplo: +5511999999999)
                $telefone_recepcao = "+5511999999999";
                $telefone_cliente_limpo = preg_replace('/[^0-9]/', '', $telefone_val);
                
                // Se for Brasil, adicionar código do país
                if (strlen($telefone_cliente_limpo) == 11) {
                    $telefone_cliente = "+55" . $telefone_cliente_limpo;
                } elseif (strlen($telefone_cliente_limpo) == 10) {
                    $telefone_cliente = "+55" . $telefone_cliente_limpo;
                } else {
                    $telefone_cliente = $telefone_cliente_limpo;
                }
                
                // Links para WhatsApp
                $whatsapp_cliente = "https://wa.me/" . $telefone_cliente . "?text=" . $mensagem_whatsapp_url;
                $whatsapp_recepcao = "https://wa.me/" . $telefone_recepcao . "?text=" . $mensagem_whatsapp_url . "\n\n📞 *Cliente para contato:* " . $telefone_val;
            }
        }
    }
    
    // Função para validar CPF no PHP
    function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    ?>
    
    <!-- Modal de Confirmação -->
    <div class="modal" id="modalConfirmacao" style="<?php echo $agendamento_sucesso ? 'display: flex;' : 'display: none;'; ?>">
        <div class="modal-content">
            <h2 class="modal-title">✓ Agendamento Confirmado!</h2>
            <p class="modal-subtitle">Detalhes do seu agendamento:</p>
            
            <div class="modal-details">
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Protocolo:</span>
                    <span class="modal-detail-value"><?php echo isset($protocolo) ? $protocolo : ''; ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Responsável:</span>
                    <span class="modal-detail-value"><?php echo htmlspecialchars($nome_responsavel_val); ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Aluno:</span>
                    <span class="modal-detail-value"><?php echo htmlspecialchars($nome_aluno_val); ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Data:</span>
                    <span class="modal-detail-value"><?php echo isset($data_formatada) ? $data_formatada : ''; ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Horário:</span>
                    <span class="modal-detail-value"><?php echo htmlspecialchars($horario_val); ?></span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Telefone:</span>
                    <span class="modal-detail-value"><?php echo htmlspecialchars($telefone_val); ?></span>
                </div>
            </div>
            
            <div class="mensagem-whatsapp">
                <p>📱 As informações do Agendamento serão enviadas para seu WhatsApp!</p>
                <p>Clique em Enviar para concluir o processo.</p>
                
            </div>           
            <div class="modal-buttons">
                <button onclick="fecharModal()" class="modal-btn modal-btn-close">Fechar</button>
                <?php if (isset($whatsapp_cliente)): ?>
                    <a href="<?php echo $whatsapp_cliente; ?>" target="_blank" class="modal-btn modal-btn-whatsapp">
                        📱 Enviar
                    </a>
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
            
            <form method="POST" action="" id="form-agendamento" novalidate>
                <div class="form-grid">
                    <!-- Data -->
                    <div class="form-group">
                        <label for="data">Data</label>
                        <input type="date" name="data" id="data" 
                               value="<?php echo htmlspecialchars($data_val); ?>" 
                               required>
                        <div class="error-message" id="data-error"></div>
                    </div>
                    
                    <!-- Horário -->
                    <div class="form-group">
                        <label for="horario">Horário</label>
                        <select name="horario" id="horario" required>
                            <?php foreach ($horarios as $horario): ?>
                                <option value="<?php echo htmlspecialchars($horario); ?>" 
                                    <?php echo ($horario_val === $horario) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($horario); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="horario-error"></div>
                    </div>
                    
                    <!-- Nome Responsável -->
                    <div class="form-group">
                        <label for="nome_responsavel">Nome Responsável</label>
                        <input type="text" name="nome_responsavel" id="nome_responsavel" 
                               placeholder="Digite o nome do responsável" 
                               value="<?php echo htmlspecialchars($nome_responsavel_val); ?>" 
                               required>
                        <div class="error-message" id="nome_responsavel-error"></div>
                    </div>
                    
                    <!-- Nome do Aluno -->
                    <div class="form-group">
                        <label for="nome_aluno">Nome do Aluno</label>
                        <input type="text" name="nome_aluno" id="nome_aluno" 
                               placeholder="Digite o nome do aluno" 
                               value="<?php echo htmlspecialchars($nome_aluno_val); ?>" 
                               required>
                        <div class="error-message" id="nome_aluno-error"></div>
                    </div>
                    
                    <!-- Telefone -->
                    <div class="form-group">
                        <label for="telefone">Telefone (WhatsApp)</label>
                        <input type="text" name="telefone" id="telefone" 
                               placeholder="(00) 00000-0000" 
                               value="<?php echo htmlspecialchars($telefone_val); ?>" 
                               required>
                        <div class="error-message" id="telefone-error"></div>
                    </div>
                    
                    <!-- CPF -->
                    <div class="form-group">
                        <label for="cpf_responsavel">CPF do Responsável</label>
                        <input type="text" name="cpf_responsavel" id="cpf_responsavel" 
                               placeholder="000.000.000-00" 
                               value="<?php echo htmlspecialchars($cpf_responsavel_val); ?>" 
                               required>
                        <div class="error-message" id="cpf_responsavel-error"></div>
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="buttons-container">
                    <button type="submit" class="btn btn-confirmar">
                        <span>✓ Confirmar Agendamento</span>
                    </button>
                    <button type="button" class="btn btn-limpar" id="btn-limpar">
                        <span>Limpar</span>
                    </button>
                </div>
                
                <!-- Aviso -->
                <div class="aviso">
                    *Você receberá uma mensagem no seu Whatsapp após confirmar o agendamento.
                </div>
                
                <?php if (!empty($mensagem) && !$agendamento_sucesso): ?>
                    <div class="mensagem">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <script>
        // Formatar data atual para o campo de data
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar data mínima como hoje
            const dataInput = document.getElementById('data');
            const today = new Date();
            const todayFormatted = today.toISOString().split('T')[0];
            dataInput.min = todayFormatted;
            
            // Se não houver valor definido, colocar a data de amanhã
            if (!dataInput.value) {
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
                dataInput.value = tomorrowFormatted;
            }
            
            // Limpar formulário
            document.getElementById('btn-limpar').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja limpar todos os campos do formulário?')) {
                    document.getElementById('form-agendamento').reset();
                    // Resetar a data para amanhã
                    const newDate = new Date();
                    newDate.setDate(newDate.getDate() + 1);
                    dataInput.value = newDate.toISOString().split('T')[0];
                    
                    // Limpar mensagens de erro
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.style.display = 'none';
                        el.textContent = '';
                    });
                    document.querySelectorAll('.form-group').forEach(el => {
                        el.classList.remove('error');
                    });
                    
                    // Limpar mensagens de sucesso
                    const mensagemDiv = document.querySelector('.mensagem');
                    if (mensagemDiv) {
                        mensagemDiv.remove();
                    }
                }
            });
            
            // Máscara para telefone
            const telefoneInput = document.getElementById('telefone');
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limitar a 11 dígitos
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                // Aplicar máscara
                if (value.length <= 10) {
                    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                } else {
                    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }
                
                e.target.value = value;
                
                // Limpar erro enquanto digita
                clearFieldError(e.target);
            });
            
            // Máscara para CPF
            const cpfInput = document.getElementById('cpf_responsavel');
            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limitar a 11 dígitos
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                // Aplicar máscara
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                
                e.target.value = value;
                
                // Limpar erro enquanto digita
                clearFieldError(e.target);
            });
            
            // Validar telefone apenas quando o campo tiver conteúdo
            telefoneInput.addEventListener('blur', function(e) {
                const value = e.target.value.trim();
                if (value === '') return; // Não validar se estiver vazio
                
                const telefoneLimpo = value.replace(/\D/g, '');
                if (telefoneLimpo.length < 10) {
                    showFieldError(e.target, 'Telefone inválido. Digite um número com DDD + número.');
                } else {
                    clearFieldError(e.target);
                }
            });
            
            // Validar CPF apenas quando o campo tiver conteúdo
            cpfInput.addEventListener('blur', function(e) {
                const value = e.target.value.trim();
                if (value === '') return; // Não validar se estiver vazio
                
                const cpfLimpo = value.replace(/\D/g, '');
                if (cpfLimpo.length === 11 && !validarCPF(cpfLimpo)) {
                    showFieldError(e.target, 'CPF inválido. Por favor, verifique o número digitado.');
                } else {
                    clearFieldError(e.target);
                }
            });
            
            // Função para mostrar erro em um campo
            function showFieldError(input, message) {
                const formGroup = input.closest('.form-group');
                const errorElement = formGroup.querySelector('.error-message');
                
                formGroup.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            // Função para limpar erro de um campo
            function clearFieldError(input) {
                const formGroup = input.closest('.form-group');
                const errorElement = formGroup.querySelector('.error-message');
                
                formGroup.classList.remove('error');
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
            
            // Função para validar CPF
            function validarCPF(cpf) {
                cpf = cpf.replace(/\D/g, '');
                
                // Verifica se tem 11 dígitos e não é uma sequência repetida
                if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
                    return false;
                }
                
                // Calcula primeiro dígito verificador
                let soma = 0;
                for (let i = 0; i < 9; i++) {
                    soma += parseInt(cpf.charAt(i)) * (10 - i);
                }
                let resto = soma % 11;
                let digito1 = resto < 2 ? 0 : 11 - resto;
                
                if (digito1 !== parseInt(cpf.charAt(9))) {
                    return false;
                }
                
                // Calcula segundo dígito verificador
                soma = 0;
                for (let i = 0; i < 10; i++) {
                    soma += parseInt(cpf.charAt(i)) * (11 - i);
                }
                resto = soma % 11;
                let digito2 = resto < 2 ? 0 : 11 - resto;
                
                return digito2 === parseInt(cpf.charAt(10));
            }
            
            // Validação do formulário antes do envio
            document.getElementById('form-agendamento').addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validar data
                const dataValue = document.getElementById('data').value;
                if (!dataValue) {
                    showFieldError(document.getElementById('data'), 'Por favor, informe a data.');
                    isValid = false;
                }
                
                // Validar horário
                const horarioValue = document.getElementById('horario').value;
                if (horarioValue === '-- Escolha --') {
                    showFieldError(document.getElementById('horario'), 'Por favor, selecione um horário.');
                    isValid = false;
                }
                
                // Validar nome responsável
                const nomeResponsavelValue = document.getElementById('nome_responsavel').value.trim();
                if (!nomeResponsavelValue) {
                    showFieldError(document.getElementById('nome_responsavel'), 'Por favor, informe o nome do responsável.');
                    isValid = false;
                }
                
                // Validar nome do aluno
                const nomeAlunoValue = document.getElementById('nome_aluno').value.trim();
                if (!nomeAlunoValue) {
                    showFieldError(document.getElementById('nome_aluno'), 'Por favor, informe o nome do aluno.');
                    isValid = false;
                }
                
                // Validar telefone
                const telefoneValue = document.getElementById('telefone').value.trim();
                if (!telefoneValue) {
                    showFieldError(document.getElementById('telefone'), 'Por favor, informe o telefone.');
                    isValid = false;
                } else {
                    const telefoneLimpo = telefoneValue.replace(/\D/g, '');
                    if (telefoneLimpo.length < 10) {
                        showFieldError(document.getElementById('telefone'), 'Telefone inválido. Digite um número com DDD + número.');
                        isValid = false;
                    }
                }
                
                // Validar CPF
                const cpfValue = document.getElementById('cpf_responsavel').value.trim();
                if (!cpfValue) {
                    showFieldError(document.getElementById('cpf_responsavel'), 'Por favor, informe o CPF.');
                    isValid = false;
                } else {
                    const cpfLimpo = cpfValue.replace(/\D/g, '');
                    if (cpfLimpo.length === 11 && !validarCPF(cpfLimpo)) {
                        showFieldError(document.getElementById('cpf_responsavel'), 'CPF inválido. Por favor, verifique o número digitado.');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    e.preventDefault(); // Impedir envio do formulário
                }
            });
            
            // Adicionar efeito de foco nos campos
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Função para fechar modal
            window.fecharModal = function() {
                document.getElementById('modalConfirmacao').style.display = 'none';
            }
            
            // Fechar modal clicando fora
            document.getElementById('modalConfirmacao').addEventListener('click', function(e) {
                if (e.target === this) {
                    fecharModal();
                }
            });
        });
    </script>
</body>
</html>