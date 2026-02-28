<?php
// salvar_evolucao.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Configurações
//$uploadDir = __DIR__ . '/../../dashboard/uploads/arq_evolucoes/';
$uploadDir = __DIR__ . '/../uploads/arq_evolucoes/';
$dadosFile = __DIR__ . '/../../dashboard/dados/evolucao_pacientes.json';

// Criar diretório de upload se não existir
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Função para gerar ID único
function gerarIdUnico() {
    return uniqid() . '_' . bin2hex(random_bytes(8));
}

// Função para sanitizar nome de arquivo
function sanitizarNomeArquivo($nome) {
    // Remove caracteres especiais e acentos
    $nome = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $nome);
    // Limita o tamanho
    return substr($nome, 0, 100);
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar campos obrigatórios
    $camposObrigatorios = ['paciente_id', 'paciente_nome', 'data_sessao', 'turno', 'horario_inicio', 'horario_fim', 'terapia'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            header("Location: painel_evolucoes.php?paciente_id=" . urlencode($_POST['paciente_id']) . "&error=Preencha todos os campos obrigatórios.");
            exit();
        }
    }

    // Dados do terapeuta logado
    $terapeuta_nome = $_SESSION['usuario_nome'] ?? 'Profissional não identificado';
    $terapeuta_perfil = $_SESSION['usuario_perfil'] ?? '';

    // Processar anexos
    $anexos = [];
    if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
        $totalArquivos = count($_FILES['fotos']['name']);
        
        for ($i = 0; $i < $totalArquivos; $i++) {
            if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                $arquivoTmp = $_FILES['fotos']['tmp_name'][$i];
                $nomeOriginal = $_FILES['fotos']['name'][$i];
                $tipoArquivo = $_FILES['fotos']['type'][$i];
                $tamanhoArquivo = $_FILES['fotos']['size'][$i];
                
                // Validar tipo de arquivo
                $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                if (!in_array($tipoArquivo, $tiposPermitidos)) {
                    continue;
                }
                
                // Validar tamanho (máximo 10MB)
                if ($tamanhoArquivo > 10 * 1024 * 1024) {
                    continue;
                }
                
                // Gerar nome único para o arquivo
                $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                $nomeUnico = gerarIdUnico() . '_' . sanitizarNomeArquivo(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '.' . $extensao;
                $caminhoDestino = $uploadDir . $nomeUnico;
                
                // Mover arquivo
                if (move_uploaded_file($arquivoTmp, $caminhoDestino)) {
                    $anexos[] = [
                        'nome_original' => $nomeOriginal,
                        'nome_arquivo' => $nomeUnico,
                        'caminho' => 'uploads/arq_evolucoes/' . $nomeUnico,
                        'tipo' => $tipoArquivo,
                        'tamanho' => $tamanhoArquivo,
                        'data_upload' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }
    }

    // Montar array da evolução
    $evolucao = [
        'id' => uniqid('evol_'),
        'paciente_id' => $_POST['paciente_id'],
        'paciente_nome' => $_POST['paciente_nome'],
        'data_sessao' => $_POST['data_sessao'],
        'turno' => $_POST['turno'],
        'horario_inicio' => $_POST['horario_inicio'],
        'horario_fim' => $_POST['horario_fim'],
        'terapia' => $_POST['terapia'],
        'condicao' => $_POST['condicao'] ?? '',
        'materiais' => $_POST['materiais'] ?? '',
        'estrategias' => $_POST['estrategias'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'observacoes' => $_POST['observacoes'] ?? '',
        'terapeuta' => $terapeuta_nome,
        'terapeuta_perfil' => $terapeuta_perfil,
        'anexos' => $anexos,
        'data_registro' => date('Y-m-d H:i:s'),
        'data_modificacao' => date('Y-m-d H:i:s'),
        'status' => 'ativo'
    ];

    // Carregar evoluções existentes
    $evolucoes = [];
    if (file_exists($dadosFile)) {
        $conteudo = file_get_contents($dadosFile);
        if (!empty($conteudo)) {
            $evolucoes = json_decode($conteudo, true) ?: [];
        }
    }

    // Adicionar nova evolução
    $evolucoes[] = $evolucao;

    // Salvar no arquivo
    if (file_put_contents($dadosFile, json_encode($evolucoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        // Redirecionar com sucesso
        $url = "evolucao_historico.php?paciente_id=" . urlencode($_POST['paciente_id']) . 
               "&paciente_nome=" . urlencode($_POST['paciente_nome']) . 
               "&responsavel=" . urlencode($_POST['responsavel'] ?? '') . 
               "&telefone=" . urlencode($_POST['telefone'] ?? '') . 
               "&success=Evolução salva com sucesso!";
        header("Location: $url");
        exit();
    } else {
        // Erro ao salvar
        header("Location: painel_evolucoes.php?paciente_id=" . urlencode($_POST['paciente_id']) . "&error=Erro ao salvar evolução.");
        exit();
    }
    
} else {
    // Se não for POST, redirecionar
    header("Location: painel_evolucoes.php");
    exit();
}
?>