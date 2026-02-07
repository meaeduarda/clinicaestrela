<?php
// dashboard/clinica/gerar_pdf_paciente.php

session_start();

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    die('Acesso negado.');
}

// Incluir manualmente a biblioteca Dompdf
$dompdfPath = __DIR__ . '/libs/dompdf/';

// Verificar se a biblioteca existe
if (!file_exists($dompdfPath . 'autoload.inc.php')) {
    // Tentar caminho alternativo
    $dompdfPath = __DIR__ . '/../../libs/dompdf/';
    
    if (!file_exists($dompdfPath . 'autoload.inc.php')) {
        die('Erro: Biblioteca Dompdf não encontrada. Caminho procurado: ' . $dompdfPath . 'autoload.inc.php');
    }
}

// Incluir o autoload do Dompdf
require_once $dompdfPath . 'autoload.inc.php';

// Verificar se as classes necessárias existem
if (!class_exists('Dompdf\Dompdf')) {
    die('Erro: Classe Dompdf\Dompdf não encontrada. Verifique a instalação da biblioteca.');
}

use Dompdf\Dompdf;
use Dompdf\Options;

// Receber dados
$index = isset($_POST['index']) ? (int)$_POST['index'] : null;
$origem = isset($_POST['origem']) ? $_POST['origem'] : 'ativo';

if ($index === null) {
    die('Paciente não especificado.');
}

// Caminho do arquivo JSON
$arquivoJson = ($origem === 'ativo') 
    ? __DIR__ . '/../dados/ativo-cad.json'
    : __DIR__ . '/../dados/pre-cad.json';

if (!file_exists($arquivoJson)) {
    die('Arquivo de dados não encontrado: ' . $arquivoJson);
}

// Ler dados
$conteudo = file_get_contents($arquivoJson);
$pacientes = json_decode($conteudo, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Erro ao decodificar JSON: ' . json_last_error_msg());
}

if (!isset($pacientes[$index])) {
    die('Paciente não encontrado no índice: ' . $index);
}

$paciente = $pacientes[$index];

// Calcular idade
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) return 'N/A';
    try {
        $dataNasc = new DateTime($dataNascimento);
        $hoje = new DateTime();
        return $hoje->diff($dataNasc)->y . ' anos';
    } catch (Exception $e) {
        return 'N/A';
    }
}

$idade = calcularIdade($paciente['nascimento'] ?? $paciente['data_nascimento'] ?? '');

// Função para formatar dados para exibição
function formatarCampo($valor, $tipo = 'texto') {
    if ($valor === null || $valor === '') {
        return '-';
    }
    
    if (is_array($valor) && empty($valor)) {
        return '-';
    }
    
    switch($tipo) {
        case 'data':
            try {
                $data = new DateTime($valor);
                return $data->format('d/m/Y');
            } catch (Exception $e) {
                return $valor;
            }
        case 'booleano':
            if ($valor === true || $valor === 'true' || $valor === 'sim' || $valor === 'Sim' || $valor === '1') {
                return 'Sim';
            } elseif ($valor === false || $valor === 'false' || $valor === 'nao' || $valor === 'Não' || $valor === '0' || $valor === '') {
                return 'Não';
            }
            return $valor;
        case 'lista':
            if (is_array($valor)) {
                // Filtrar valores vazios
                $valor = array_filter($valor, function($item) {
                    return !empty($item) && $item !== '';
                });
                
                if (empty($valor)) return '-';
                
                // Se for array de arrays (como anexos)
                if (isset($valor[0]) && is_array($valor[0])) {
                    return count($valor) . ' itens';
                }
                
                return implode(', ', $valor);
            }
            return $valor;
        default:
            return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
}

// Função para verificar se um campo tem valor
function temValor($valor) {
    return !empty($valor) && $valor !== '' && $valor !== null;
}

// Preparar dados para o PDF
$dadosParaExibir = [];

// Dados Pessoais
$dadosParaExibir['Dados Pessoais'] = [
    'Nome Completo' => formatarCampo($paciente['nome_completo'] ?? ''),
    'Sexo' => formatarCampo($paciente['sexo'] ?? ''),
    'Data de Nascimento' => formatarCampo($paciente['nascimento'] ?? $paciente['data_nascimento'] ?? '', 'data') . ' (' . $idade . ')',
    'Nome da Mãe' => formatarCampo($paciente['nome_mae'] ?? ''),
    'Nome do Pai' => formatarCampo($paciente['nome_pai'] ?? ''),
    'Responsável' => formatarCampo($paciente['responsavel'] ?? '')
];

// Contato
$dadosParaExibir['Contato'] = [
    'Telefone' => formatarCampo($paciente['telefone'] ?? ''),
    'E-mail' => formatarCampo($paciente['email'] ?? ''),
    'CPF do Responsável' => formatarCampo($paciente['cpf_responsavel'] ?? '')
];

// Convênio
$dadosParaExibir['Convênio'] = [
    'Convênio' => formatarCampo($paciente['convenio'] ?? ''),
    'Telefone do Convênio' => formatarCampo($paciente['telefone_convenio'] ?? ''),
    'Número da Carteirinha' => formatarCampo($paciente['numero_carteirinha'] ?? '')
];

// Escolaridade
$dadosParaExibir['Escolaridade'] = [
    'Escolaridade' => formatarCampo($paciente['escolaridade'] ?? ''),
    'Escola' => formatarCampo($paciente['escola'] ?? ''),
    'CPF do Paciente' => formatarCampo($paciente['cpf_paciente'] ?? ''),
    'RG do Paciente' => formatarCampo($paciente['rg_paciente'] ?? '')
];

// Identificação da Necessidade
$dadosParaExibir['Identificação da Necessidade'] = [
    'Motivo Principal' => formatarCampo($paciente['motivo_principal'] ?? ''),
    'Quem Identificou' => formatarCampo($paciente['quem_identificou'] ?? '', 'lista'),
    'Encaminhado' => formatarCampo($paciente['encaminhado'] ?? '', 'booleano'),
    'Nome do Profissional' => formatarCampo($paciente['nome_profissional'] ?? ''),
    'Especialidade' => formatarCampo($paciente['especialidade_profissional'] ?? ''),
    'Possui Relatório' => formatarCampo($paciente['possui_relatorio'] ?? '', 'booleano'),
    'Sinais Observados' => formatarCampo($paciente['sinais_observados'] ?? '', 'lista'),
    'Descrição de Outros Sinais' => formatarCampo($paciente['sinal_outro_descricao'] ?? ''),
    'Descrição dos Sinais' => formatarCampo($paciente['descricao_sinais'] ?? ''),
    'Expectativas da Família' => formatarCampo($paciente['expectativas_familia'] ?? '')
];

// Histórico de Tratamento
$dadosParaExibir['Histórico de Tratamento'] = [
    'Tratamento Anterior' => formatarCampo($paciente['tratamento_anterior'] ?? '', 'booleano'),
    'Tipo de Tratamento' => formatarCampo($paciente['tipo_tratamento'] ?? ''),
    'Local do Tratamento' => formatarCampo($paciente['local_tratamento'] ?? ''),
    'Período do Tratamento' => formatarCampo($paciente['periodo_tratamento'] ?? '')
];

// Gestação e Nascimento
$dadosParaExibir['Gestação e Nascimento'] = [
    'Duração da Gestação' => formatarCampo($paciente['duracao_gestacao'] ?? ''),
    'Tipo de Parto' => formatarCampo($paciente['tipo_parto'] ?? ''),
    'Problemas na Gestação' => formatarCampo($paciente['problemas_gestacao'] ?? '', 'booleano'),
    'Quais Problemas na Gestação' => formatarCampo($paciente['quais_problemas_gestacao'] ?? ''),
    'Problemas Pós-Nascimento' => formatarCampo($paciente['problemas_pos_nascimento'] ?? '', 'booleano'),
    'Quais Problemas Pós-Nascimento' => formatarCampo($paciente['quais_problemas_pos_nascimento'] ?? '')
];

// Histórico Médico
$dadosParaExibir['Histórico Médico'] = [
    'Complicações Graves' => formatarCampo($paciente['complicacoes_graves'] ?? '', 'booleano'),
    'Quais Complicações' => formatarCampo($paciente['quais_complicacoes'] ?? ''),
    'Hospitalizações' => formatarCampo($paciente['hospitalizacoes'] ?? ''),
    'Motivo da Hospitalização' => formatarCampo($paciente['motivo_hospitalizacao'] ?? ''),
    'Idade da Hospitalização' => formatarCampo($paciente['idade_hospitalizacao'] ?? ''),
    'Convulsões' => formatarCampo($paciente['convulsoes'] ?? '', 'booleano'),
    'Detalhes das Convulsões' => formatarCampo($paciente['detalhes_convulsoes'] ?? ''),
    'Alergias' => formatarCampo($paciente['alergias'] ?? '', 'booleano'),
    'Quais Alergias' => formatarCampo($paciente['quais_alergias'] ?? ''),
    'Histórico Familiar' => formatarCampo($paciente['historico_familiar'] ?? '', 'lista'),
    'Outros Históricos Familiares' => formatarCampo($paciente['familia_outros_descricao'] ?? '')
];

// Desenvolvimento
$dadosParaExibir['Desenvolvimento'] = [
    'Crescimento Similar' => formatarCampo($paciente['crescimento_similar'] ?? '', 'booleano'),
    'Diferença no Crescimento' => formatarCampo($paciente['diferenca_crescimento'] ?? ''),
    'Sentou sem Apoio' => formatarCampo($paciente['sentou_sem_apoio'] ?? '', 'booleano'),
    'Idade que Sentou' => formatarCampo($paciente['idade_sentou'] ?? ''),
    'Engatinhou' => formatarCampo($paciente['engatinhou'] ?? '', 'booleano'),
    'Idade que Engatinhou' => formatarCampo($paciente['idade_engatinhou'] ?? ''),
    'Começou a Andar' => formatarCampo($paciente['comecou_andar'] ?? '', 'booleano'),
    'Idade que Andou' => formatarCampo($paciente['idade_andou'] ?? ''),
    'Controle de Esfíncteres' => formatarCampo($paciente['controle_esfincteres'] ?? '', 'booleano'),
    'Idade do Controle' => formatarCampo($paciente['idade_controle'] ?? ''),
    'Balbuciou' => formatarCampo($paciente['balbuciou'] ?? '', 'booleano'),
    'Idade do Balbucio' => formatarCampo($paciente['idade_balbucio'] ?? ''),
    'Primeiras Palavras' => formatarCampo($paciente['primeiras_palavras'] ?? '', 'booleano'),
    'Idade das Primeiras Palavras' => formatarCampo($paciente['idade_primeiras_palavras'] ?? ''),
    'Montou Frases' => formatarCampo($paciente['montou_frases'] ?? '', 'booleano'),
    'Idade das Frases' => formatarCampo($paciente['idade_frases'] ?? ''),
    'Frases Completas' => formatarCampo($paciente['frases_completas'] ?? '', 'booleano'),
    'Sorriu em Interações' => formatarCampo($paciente['sorriu_interacoes'] ?? '', 'booleano'),
    'Interage com Crianças' => formatarCampo($paciente['interage_criancas'] ?? '', 'booleano')
];

// Alimentação
$dadosParaExibir['Alimentação'] = [
    'Introdução Alimentar' => formatarCampo($paciente['introducao_alimentar'] ?? '', 'booleano'),
    'Alimenta Sozinho' => formatarCampo($paciente['alimenta_sozinho'] ?? '', 'booleano'),
    'Hábitos Alimentares' => formatarCampo($paciente['habitos_alimentares'] ?? '')
];

// Observações
$observacoes = [];
if (temValor($paciente['observacoes_clinicas'] ?? '')) {
    $observacoes['Observações Clínicas'] = formatarCampo($paciente['observacoes_clinicas'] ?? '');
}

// Verificar anexos
if (isset($paciente['anexos']) && !empty($paciente['anexos'])) {
    $anexosCount = 0;
    foreach ($paciente['anexos'] as $anexo) {
        if (!empty($anexo)) {
            $anexosCount++;
        }
    }
    if ($anexosCount > 0) {
        $observacoes['Anexos'] = 'Sim (' . $anexosCount . ' arquivos)';
    } else {
        $observacoes['Anexos'] = 'Nenhum';
    }
} else {
    $observacoes['Anexos'] = 'Nenhum';
}

if (!empty($observacoes)) {
    $dadosParaExibir['Observações'] = $observacoes;
}

// Informações do Sistema
$dadosParaExibir['Informações do Sistema'] = [
    'Status do Paciente' => formatarCampo($paciente['status_paciente'] ?? ''),
    'Data de Registro' => formatarCampo($paciente['data_registro'] ?? '', 'data'),
    'Status' => formatarCampo($paciente['status'] ?? ''),
    'Data de Ativação' => formatarCampo($paciente['data_ativacao'] ?? '', 'data'),
    'Última Atualização' => formatarCampo($paciente['data_atualizacao'] ?? '', 'data')
];

// HTML para PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ficha Completa do Paciente - ' . htmlspecialchars($paciente['nome_completo'] ?? 'Paciente') . '</title>
    <style>
        @page { 
            margin: 15mm;
            margin-top: 25mm;
        }
        
        * { 
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif; 
            box-sizing: border-box;
        }
        
        body { 
            font-size: 10px; 
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Cabeçalho */
        .header { 
            position: fixed;
            top: -20mm;
            left: 0;
            right: 0;
            height: 25mm;
            text-align: center; 
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 5mm;
            background: white;
        }
        
        .header h1 { 
            color: #1e40af; 
            margin: 2mm 0 1mm 0; 
            font-size: 18px;
        }
        
        .header h2 { 
            color: #3b82f6; 
            margin: 1mm 0; 
            font-size: 14px;
            font-weight: normal;
        }
        
        .header p { 
            margin: 1mm 0; 
            color: #666; 
            font-size: 9px;
        }
        
        /* Conteúdo principal */
        .content {
            margin-top: 25mm;
        }
        
        /* Seções */
        .section { 
            margin-bottom: 12px; 
            page-break-inside: avoid;
        }
        
        .section-header { 
            color: #1e40af; 
            background-color: #f1f5f9; 
            padding: 5px 10px; 
            margin: 0 0 8px 0;
            border-left: 4px solid #3b82f6;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        /* Tabela de dados */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        
        .data-table th { 
            background-color: #f8fafc; 
            text-align: left; 
            padding: 5px 8px; 
            border: 1px solid #e2e8f0;
            font-weight: 600;
            color: #4b5563;
            width: 35%;
        }
        
        .data-table td { 
            padding: 5px 8px; 
            border: 1px solid #e2e8f0;
            color: #1f2937;
        }
        
        /* Informações do paciente */
        .patient-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .patient-info {
            flex: 1;
        }
        
        .patient-name {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 3px;
        }
        
        .patient-meta {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 5px;
            font-size: 9px;
        }
        
        .patient-meta-item {
            padding: 1px 0;
        }
        
        .patient-meta-label {
            font-weight: 600;
            color: #4b5563;
        }
        
        /* Badge de status */
        .status-badge { 
            display: inline-block; 
            padding: 2px 8px; 
            border-radius: 12px; 
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-ativo { background: #d1fae5; color: #065f46; }
        .status-pendente { background: #fef3c7; color: #92400e; }
        .status-inativo { background: #f3f4f6; color: #4b5563; }
        
        /* Rodapé */
        .footer { 
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 15mm;
            text-align: center; 
            font-size: 8px; 
            color: #666; 
            border-top: 1px solid #ddd;
            padding-top: 3mm;
            background: white;
        }
        
        /* Quebra de página */
        .page-break {
            page-break-before: always;
            padding-top: 25mm;
        }
        
        /* Estilo para valores vazios */
        .empty-value {
            color: #9ca3af;
            font-style: italic;
        }
        
        /* Grid layout para múltiplas colunas */
        .grid-2-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        
        .grid-item {
            margin-bottom: 5px;
        }
        
        .grid-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 9px;
            margin-bottom: 1px;
        }
        
        .grid-value {
            color: #1f2937;
            font-size: 9px;
        }
        
        /* Para telas muito pequenas no PDF */
        @media print {
            body { 
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .section {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>Clínica Estrela</h1>
        <h2>Ficha Completa do Paciente</h2>
        <p>Emitido em: ' . date('d/m/Y H:i') . ' por ' . htmlspecialchars($_SESSION['usuario_nome'] ?? 'Sistema') . '</p>
    </div>
    
    <!-- Conteúdo -->
    <div class="content">
        <!-- Resumo do paciente -->
        <div class="patient-summary">
            <div class="patient-avatar">
                ' . strtoupper(substr($paciente['nome_completo'] ?? 'P', 0, 1)) . '
            </div>
            <div class="patient-info">
                <div class="patient-name">' . htmlspecialchars($paciente['nome_completo'] ?? 'Paciente sem nome') . '</div>
                <div class="patient-meta">
                    <div class="patient-meta-item">
                        <span class="patient-meta-label">ID:</span> #' . $index . '
                    </div>
                    <div class="patient-meta-item">
                        <span class="patient-meta-label">Nascimento:</span> ' . formatarCampo($paciente['nascimento'] ?? $paciente['data_nascimento'] ?? '', 'data') . ' (' . $idade . ')
                    </div>
                    <div class="patient-meta-item">
                        <span class="patient-meta-label">Sexo:</span> ' . formatarCampo($paciente['sexo'] ?? '', 'texto') . '
                    </div>
                    <div class="patient-meta-item">
                        <span class="patient-meta-label">Status:</span> <span class="status-badge status-' . (strtolower($paciente['status'] ?? 'ativo')) . '">' . ($paciente['status'] ?? 'Ativo') . '</span>
                    </div>
                </div>
            </div>
        </div>
';

// Gerar seções dinamicamente
foreach ($dadosParaExibir as $tituloSecao => $campos) {
    // Filtrar campos que têm valor
    $camposComValor = array_filter($campos, function($valor) {
        return $valor !== '-' && !empty($valor);
    });
    
    // Se a seção tiver campos com valor, exibir
    if (!empty($camposComValor)) {
        $html .= '
        <div class="section">
            <div class="section-header">' . $tituloSecao . '</div>
            <table class="data-table">
        ';
        
        foreach ($camposComValor as $label => $value) {
            $html .= '
                <tr>
                    <th>' . $label . '</th>
                    <td>' . $value . '</td>
                </tr>
            ';
        }
        
        $html .= '
            </table>
        </div>
        ';
    }
}

// Campos adicionais não mapeados
$camposNaoMapeados = [];
$camposConhecidos = [
    'nome_completo', 'sexo', 'nascimento', 'data_nascimento', 'nome_mae', 'nome_pai', 'responsavel',
    'telefone', 'email', 'cpf_responsavel', 'convenio', 'telefone_convenio', 'numero_carteirinha',
    'escolaridade', 'escola', 'cpf_paciente', 'rg_paciente', 'motivo_principal', 'quem_identificou',
    'encaminhado', 'nome_profissional', 'especialidade_profissional', 'possui_relatorio', 'sinais_observados',
    'sinal_outro_descricao', 'descricao_sinais', 'expectativas_familia', 'tratamento_anterior',
    'tipo_tratamento', 'local_tratamento', 'periodo_tratamento', 'duracao_gestacao', 'tipo_parto',
    'problemas_gestacao', 'quais_problemas_gestacao', 'problemas_pos_nascimento', 'quais_problemas_pos_nascimento',
    'complicacoes_graves', 'quais_complicacoes', 'hospitalizacoes', 'motivo_hospitalizacao',
    'idade_hospitalizacao', 'convulsoes', 'detalhes_convulsoes', 'alergias', 'quais_alergias',
    'historico_familiar', 'familia_outros_descricao', 'crescimento_similar', 'diferenca_crescimento',
    'sentou_sem_apoio', 'idade_sentou', 'engatinhou', 'idade_engatinhou', 'comecou_andar', 'idade_andou',
    'controle_esfincteres', 'idade_controle', 'balbuciou', 'idade_balbucio', 'primeiras_palavras',
    'idade_primeiras_palavras', 'montou_frases', 'idade_frases', 'frases_completas', 'sorriu_interacoes',
    'interage_criancas', 'introducao_alimentar', 'alimenta_sozinho', 'habitos_alimentares',
    'observacoes_clinicas', 'anexos', 'status_paciente', 'data_registro', 'status', 'data_ativacao',
    'data_atualizacao', 'foto', 'idade_calculada', 'data_ativacao_formatada', 'data_registro_formatada'
];

foreach ($paciente as $campo => $valor) {
    if (!in_array($campo, $camposConhecidos) && temValor($valor)) {
        $camposNaoMapeados[$campo] = $valor;
    }
}

if (!empty($camposNaoMapeados)) {
    $html .= '
        <div class="section">
            <div class="section-header">Outras Informações</div>
            <table class="data-table">
    ';
    
    foreach ($camposNaoMapeados as $campo => $valor) {
        $labelFormatado = ucwords(str_replace('_', ' ', $campo));
        $valorFormatado = formatarCampo($valor, is_array($valor) ? 'lista' : 'texto');
        
        $html .= '
            <tr>
                <th>' . $labelFormatado . '</th>
                <td>' . $valorFormatado . '</td>
            </tr>
        ';
    }
    
    $html .= '
            </table>
        </div>
    ';
}

// Rodapé
$html .= '
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema da Clínica Estrela.</p>
        <p>Confidencial - Uso interno | ID do Documento: PDF-' . $index . '-' . date('YmdHis') . '</p>
        <p>Página <span class="page-number"></span> de <span class="page-count"></span></p>
    </div>
</body>
</html>';

try {
    // Configurar Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultPaperSize', 'A4');
    $options->set('defaultPaperOrientation', 'portrait');
    
    // Habilitar paginação
    $options->set('isFontSubsettingEnabled', true);
    $options->set('isJavascriptEnabled', true);

    $dompdf = new Dompdf($options);
    
    // Carregar HTML
    $dompdf->loadHtml($html, 'UTF-8');
    
    // Renderizar PDF
    $dompdf->render();
    
    // Adicionar números de página
    $canvas = $dompdf->getCanvas();
    $font = $dompdf->getFontMetrics()->getFont("helvetica", "normal");
    $canvas->page_text(500, 800, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
    
    // Nome do arquivo
    $nomeArquivo = 'paciente_completo_' . preg_replace('/[^a-z0-9]/i', '_', strtolower($paciente['nome_completo'] ?? 'paciente')) . '_' . date('Ymd_His') . '.pdf';
    
    // Forçar o download
    $dompdf->stream($nomeArquivo, [
        'Attachment' => true,
        'compress' => true
    ]);
    
} catch (Exception $e) {
    die('Erro ao gerar PDF: ' . $e->getMessage() . 
        '<br><br>Verifique se a biblioteca Dompdf está instalada corretamente.');
}