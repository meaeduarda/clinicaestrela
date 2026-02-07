<?php
// painel_adm_preca.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// --- DEFINIÇÃO DO PACIENTE PADRÃO (BLUEPRINT) ---
// Isso garante que TODAS as chaves existam, mesmo que vazias.
$pacientePadrao = [
    'nome_completo' => '',
    'nome_social' => '',
    'idade' => '',
    'data_nascimento' => '', 
    'nascimento' => '',
    'sexo' => '',
    'nome_mae' => '',
    'nome_pai' => '',
    'responsavel' => '',
    'parentesco' => '',
    'telefone' => '',
    'email' => '',
    'cpf_responsavel' => '',
    'rg_responsavel' => '',
    'cpf_paciente' => '',
    'rg_paciente' => '',
    'convenio' => '',
    'telefone_convenio' => '',
    'numero_carteirinha' => '',
    'escolaridade' => '',
    'escola' => '',
    'status' => 'Novo',
    'foto' => '',
    // Adicionando campos que podem causar erro se faltarem
    'motivo_principal' => '',
    'quem_identificou' => [],
    'encaminhado' => 'nao',
    'duracao_gestacao' => '',
    'tipo_parto' => '',
    'sentou_sem_apoio' => '',
    'engatinhou' => '',
    'comecou_andar' => '',
    'fala' => '',
    'observacoes_clinicas' => ''
];

// --- LÓGICA DE EDIÇÃO ---
$indexEditar = isset($_GET['index']) ? $_GET['index'] : null;
$origem = isset($_GET['origem']) ? $_GET['origem'] : 'pendente'; // 'ativo' ou 'pendente'
$paciente = $pacientePadrao; // Começa com o padrão
$modoEdicao = false;

// Se tem índice, tenta carregar do JSON correto
if ($indexEditar !== null) {
    // Define qual arquivo carregar baseado na origem
    if ($origem === 'ativo') {
        $arquivoJson = __DIR__ . '/../../dashboard/dados/ativo-cad.json';
    } else {
        $arquivoJson = __DIR__ . '/../../dashboard/dados/pre-cad.json';
    }
    
    if (file_exists($arquivoJson)) {
        $conteudo = file_get_contents($arquivoJson);
        $lista = json_decode($conteudo, true);
        
        if (isset($lista[$indexEditar])) {
            // A MÁGICA ACONTECE AQUI:
            // Pegamos o padrão e sobrescrevemos com o que existe no JSON.
            // O que não existir no JSON, mantém o valor vazio do padrão.
            $paciente = array_merge($pacientePadrao, $lista[$indexEditar]);
            $modoEdicao = true;
        }
    }
}

// DATA: Garante formatação correta para o HTML (yyyy-mm-dd)
$dataNascimentoInput = '';
if (!empty($paciente['data_nascimento'])) {
    if (strpos($paciente['data_nascimento'], '-') !== false) {
        $dataNascimentoInput = $paciente['data_nascimento'];
    } else {
        $dateObj = DateTime::createFromFormat('d/m/Y', $paciente['data_nascimento']);
        if ($dateObj) {
            $dataNascimentoInput = $dateObj->format('Y-m-d');
        }
    }
} elseif (!empty($paciente['nascimento'])) {
    // Usa o campo 'nascimento' se 'data_nascimento' estiver vazio
    $dataNascimentoInput = $paciente['nascimento'];
    
    // Converte para o formato correto se necessário
    if (strpos($dataNascimentoInput, '/') !== false) {
        $dateObj = DateTime::createFromFormat('d/m/Y', $dataNascimentoInput);
        if ($dateObj) {
            $dataNascimentoInput = $dateObj->format('Y-m-d');
        }
    }
}

// FOTO: Define avatar padrão se vazio
if (empty($paciente['foto'])) {
    $nomeParaAvatar = !empty($paciente['nome_social']) ? $paciente['nome_social'] : 
                      (!empty($paciente['nome_completo']) ? $paciente['nome_completo'] : 'Novo Paciente');
    
    $paciente['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($nomeParaAvatar) . '&size=200&background=e2e8f0&color=64748b&bold=true';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Cadastro Clínico - Clinica Estrela</title>
    
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaqueixa.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precaantecedentes.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_precadesenv.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca_observacao.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .modo-edicao-alert {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-voltar {
            text-decoration: none;
            color: #3b82f6;
            border: 1px solid #3b82f6;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .notification {
            position: fixed; top: 20px; right: 20px; padding: 15px 20px;
            border-radius: 8px; color: white; display: flex; align-items: center; gap: 10px;
            z-index: 10000; transform: translateX(150%); transition: transform 0.3s ease;
            max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .notification.show { transform: translateX(0); }
        .notification-success { background-color: #10b981; border-left: 4px solid #059669; }
        .notification-error { background-color: #ef4444; border-left: 4px solid #dc2626; }
        .notification-info { background-color: #3b82f6; border-left: 4px solid #2563eb; }
        .origem-indicator {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 20px;
        }
        .origem-ativo {
            background-color: #d1fae5;
            color: #065f46;
        }
        .origem-pendente {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes_precadastro/header_preca.php'; ?>
        
        <main class="main-content">
            <?php if($modoEdicao): ?>
                <div class="modo-edicao-alert desktop-only">
                    <span>
                        <i class="fas fa-pen"></i> 
                        Editando paciente: <strong><?php echo htmlspecialchars($paciente['nome_completo']); ?></strong>
                        <span class="origem-indicator <?php echo $origem === 'ativo' ? 'origem-ativo' : 'origem-pendente'; ?>">
                            <i class="fas <?php echo $origem === 'ativo' ? 'fa-user-check' : 'fa-user-clock'; ?>"></i>
                            <?php echo $origem === 'ativo' ? 'Paciente Ativo' : 'Paciente Pendente'; ?>
                        </span>
                    </span>
                    <a href="<?php echo $origem === 'ativo' ? 'painel_adm_pacientes.php' : 'painel_pacientes_pendentes.php'; ?>" 
                       class="btn-voltar">
                        Voltar para Lista
                    </a>
                </div>
            <?php endif; ?>

            <div class="main-top desktop-only">
                <h2><i class="fas fa-file-medical"></i> <?php echo $modoEdicao ? 'Editar Paciente' : 'Pré-Cadastro Clínico'; ?></h2>
                <div class="top-icons">
                    <div class="icon-btn with-badge"><i class="fas fa-bell"></i><span class="badge">2</span></div>
                    <div class="icon-btn"><i class="fas fa-user-circle"></i></div>
                    <div class="icon-btn"><i class="fas fa-cog"></i></div>
                </div>
            </div>

            <?php include 'includes_precadastro/paciente_card.php'; ?>

            <div class="navigation-tabs">
                <a href="#" class="tab active" data-tab="identificacao"><i class="fas fa-id-card"></i> <span>Identificação</span></a>
                <a href="#" class="tab" data-tab="queixa"><i class="fas fa-comment-medical"></i> <span>Queixa</span></a>
                <a href="#" class="tab" data-tab="antecedente"><i class="fas fa-history"></i> <span>Antecedente</span></a>
                <a href="#" class="tab" data-tab="desenvolvimento"><i class="fas fa-baby"></i> <span>Desenvolvimento</span></a>
                <a href="#" class="tab" data-tab="observacao"><i class="fas fa-clipboard-check"></i> <span>Observação Clínica</span></a>
            </div>

            <div id="form-identificacao" class="form-card tab-content active"><?php include 'includes_precadastro/formulario_identificacao.php'; ?></div>
            <div id="form-queixa" class="form-card tab-content"><?php include 'includes_precadastro/formulario_queixa.php'; ?></div>
            <div id="form-antecedente" class="form-card tab-content"><?php include 'includes_precadastro/formulario_antecedentes.php'; ?></div>
            <div id="form-desenvolvimento" class="form-card tab-content"><?php include 'includes_precadastro/formulario_desenvolvimento.php'; ?></div>
            <div id="form-observacao" class="form-card tab-content"><?php include 'includes_precadastro/formulario_observacao.php'; ?></div>

            <footer class="main-footer">
                <div class="footer-logo"><i class="fas fa-star"></i> <span>CLÍNICA ESTRELA</span></div>
            </footer>
        </main>

        <div class="modal" id="photoModal">
            <div class="modal-content">
                <div class="modal-header"><h3>Alterar Foto do Paciente</h3><button class="modal-close">&times;</button></div>
                <div class="modal-body">
                    <div class="photo-preview"><img id="photoPreview" src="<?php echo $paciente['foto']; ?>" alt="Preview da foto"></div>
                    <form id="photoUploadForm" class="upload-form">
                        <div class="form-group">
                            <label for="foto_paciente">Selecionar Foto</label>
                            <input type="file" id="foto_paciente" name="foto_paciente" accept="image/*">
                            <small>Formatos aceitos: JPG, PNG, GIF, WebP (máx. 2MB)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-cancel">Cancelar</button><button type="button" class="btn btn-upload">Enviar Foto</button></div>
            </div>
        </div>
    </div>
     
    <script>
        const pacienteData = <?php echo json_encode($paciente); ?>;
        const dataNascimentoFormatada = "<?php echo $dataNascimentoInput; ?>";
        const indiceEdicao = "<?php echo $indexEditar !== null ? $indexEditar : ''; ?>";
        const origemEdicao = "<?php echo $origem; ?>";
        
        const usuarioLogado = {
            nome: "<?php echo htmlspecialchars($nomeLogado); ?>",
            perfil: "<?php echo htmlspecialchars($perfilLogado); ?>"
        };
        const config = {
            maxAnexos: 10, maxTamanhoAnexo: 5 * 1024 * 1024, maxTamanhoFoto: 2 * 1024 * 1024,
            tiposAnexoPermitidos: ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            tiposFotoPermitidos: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            storageKey: 'preCadastroDados'
        };
</script>

<script src="/clinicaestrela/dashboard/js/painel_adm_preca.js" defer></script>
<script src="/clinicaestrela/dashboard/js/tabs-navigation.js" defer></script>
<script src="/clinicaestrela/dashboard/js/form-handlers.js" defer></script>
<script src="/clinicaestrela/dashboard/js/file-upload.js" defer></script>
<script src="/clinicaestrela/dashboard/js/modal-handler.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // =========================
    // FOTO
    // =========================

    let fotoPacienteAtual = pacienteData.foto || '';

    const btnUpload = document.querySelector('.btn-upload');
    const inputFoto = document.getElementById('foto_paciente');
    const preview   = document.getElementById('photoPreview');
    const fotoCard  = document.querySelector('.patient-photo img');

    if (btnUpload && inputFoto) {

        btnUpload.addEventListener('click', function () {

            if (!inputFoto.files.length) {
                alert('Selecione uma imagem.');
                return;
            }

            const arquivo = inputFoto.files[0];

            if (arquivo.size > config.maxTamanhoFoto) {
                alert('A imagem deve ter no máximo 2MB.');
                return;
            }

            if (!config.tiposFotoPermitidos.includes(arquivo.type)) {
                alert('Formato de imagem não permitido.');
                return;
            }
            
            const formData = new FormData();
            formData.append('foto_paciente', arquivo);

            if (fotoPacienteAtual) {
                formData.append('foto_antiga', fotoPacienteAtual);
            }

            btnUpload.disabled = true;
            btnUpload.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            fetch('upload_foto_paciente.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {

                if (resp.status !== 'success') {
                    alert(resp.message || 'Erro ao enviar imagem');
                    return;
                }

                fotoPacienteAtual = resp.foto;

                if (preview)  preview.src  = resp.foto;
                if (fotoCard) fotoCard.src = resp.foto;

                if (typeof mostrarNotificacao === 'function') {
                    mostrarNotificacao('sucesso', 'Foto atualizada com sucesso.');
                }

                const modal = document.getElementById('photoModal');
                if (modal) modal.style.display = 'none';

            })
            .catch(() => {
                alert('Erro ao enviar imagem.');
            })
            .finally(() => {
                btnUpload.disabled = false;
                btnUpload.innerHTML = 'Enviar Foto';
            });

        });

    }

    // =========================
    // DATA
    // =========================

    const inputNasc = document.querySelector('input[type="date"]');
    if(inputNasc && dataNascimentoFormatada) {
        inputNasc.value = dataNascimentoFormatada;
    }

    // =========================
    // BOTÃO SALVAR
    // =========================

    const botaoSalvar = document.querySelector('.btn-archive');
    if (botaoSalvar) {

        if(indiceEdicao !== "") {
            botaoSalvar.innerHTML = '<i class="fas fa-save"></i> Atualizar Dados';
        }

        botaoSalvar.removeAttribute('onclick'); 
        const novoBotao = botaoSalvar.cloneNode(true);
        botaoSalvar.parentNode.replaceChild(novoBotao, botaoSalvar);
        
        novoBotao.addEventListener('click', function(e) {
            e.preventDefault();
            salvarPendenteAgoraMesmo(novoBotao);
        });
    }

    // disponibiliza a foto para a função de salvar
    window.__fotoPacienteAtual = () => fotoPacienteAtual;

});

function salvarPendenteAgoraMesmo(btnElement) {

    const urlBase = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
    const urlDestino = urlBase + '/salvar_dados.php';
    
    const formIds = [
        'form-identificacao-data',
        'form-queixa-data',
        'form-antecedente-data',
        'form-desenvolvimento-data',
        'form-observacao-data'
    ];

    let dadosCompletos = {};

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // FOTO ENTRA JUNTO NO JSON
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


    if(indiceEdicao !== "") {
        dadosCompletos.index = indiceEdicao;
        dadosCompletos.origem = origemEdicao;
    } else {
        dadosCompletos.origem = 'pendente';
    }

    formIds.forEach(id => {
        const form = document.getElementById(id);
        if (form) {

            const formData = new FormData(form);

            for (let [key, value] of formData.entries()) {

                if (key.endsWith('[]')) {

                    const chaveLimpa = key.slice(0, -2);

                    if (!dadosCompletos[chaveLimpa])
                        dadosCompletos[chaveLimpa] = [];

                    dadosCompletos[chaveLimpa].push(value);

                } else {

                    if (dadosCompletos.hasOwnProperty(key)) {

                        if (!Array.isArray(dadosCompletos[key]))
                            dadosCompletos[key] = [dadosCompletos[key]];

                        dadosCompletos[key].push(value);

                    } else {

                        dadosCompletos[key] = value;

                    }

                }
            }
        }
    });

    const textoOriginal = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    btnElement.disabled = true;

    
    if (typeof window.__fotoPacienteAtual === 'function') {
        const f = window.__fotoPacienteAtual();
        if (f) {
            dadosCompletos.foto = f;
        }
    }


    fetch(urlDestino, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dadosCompletos)
    })
    .then(async response => {
        const texto = await response.text();
        if (!response.ok) throw new Error("Erro HTTP " + response.status);
        try { return JSON.parse(texto); } 
        catch (e) { throw new Error("Resposta inválida do servidor."); }
    })
    .then(data => {

        if (data.status === 'success') {

            if (typeof mostrarNotificacao === 'function') {
                mostrarNotificacao('sucesso', data.message || 'Dados salvos com sucesso!');
            } else {
                alert("Dados salvos com sucesso!");
            }

            setTimeout(() => {

                if(origemEdicao === 'ativo') {
                    window.location.href = 'painel_adm_pacientes.php';
                } else if(indiceEdicao !== "") {
                    window.location.href = 'painel_pacientes_pendentes.php';
                } else {
                    location.reload();
                }

            }, 1000);

        } else {

            alert("Erro ao salvar: " + data.message);

        }

    })
    .catch(error => {
        console.error(error);
        alert("Erro de comunicação: " + error.message);
    })
    .finally(() => {
        btnElement.innerHTML = textoOriginal;
        btnElement.disabled = false;
    });
};

function mostrarNotificacao(tipo, mensagem) {

    const div = document.createElement('div');

    let classeTipo = 'notification-info';
    let icone = 'fa-info-circle';

    if (tipo === 'sucesso') {
        classeTipo = 'notification-success';
        icone = 'fa-check-circle';
    } else if (tipo === 'erro') {
        classeTipo = 'notification-error';
        icone = 'fa-exclamation-circle';
    }

    div.className = `notification show ${classeTipo}`;
    div.innerHTML = `<i class="fas ${icone}"></i><span>${mensagem}</span>`;
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.classList.remove('show');
        setTimeout(() => div.remove(), 300);
    }, 4000);

}

window.salvarComoPacienteAtivo = function() {
    alert("Esta funcionalidade será implementada em breve.");
};
</script>
</body>
</html>