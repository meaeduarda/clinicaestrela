<?php
// Este arquivo é incluído pelo painel_cliente.php
// Função para formatar data
function formatarData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

// Função para obter ícone do tipo de arquivo
function getFileIcon($nomeArquivo) {
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
    
    $icones = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image',
        'txt' => 'fa-file-alt',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive'
    ];
    
    return $icones[$extensao] ?? 'fa-file';
}

// Carregar PEIs salvos
$peiJsonFile = __DIR__ . '/../dados/pei_salvo/pei_salvo.json';
$peis = [];

if (file_exists($peiJsonFile)) {
    $peiContent = file_get_contents($peiJsonFile);
    $todosPeis = json_decode($peiContent, true) ?: [];
    
    // Filtrar PEIs que contêm o nome do paciente
    $nomePaciente = $paciente['nome_completo'];
    
    foreach ($todosPeis as $hash => $pei) {
        $nomeOriginal = $pei['nome_original'] ?? '';
        
        if (stripos($nomeOriginal, $nomePaciente) !== false) {
            $peis[$hash] = $pei;
        }
    }
    
    uasort($peis, function($a, $b) {
        return strtotime($b['data_upload']) - strtotime($a['data_upload']);
    });
}


$debugInfo = [];
if (!empty($peis)) {
    foreach ($peis as $hash => $pei) {
       
        $nomeArquivo = $pei['arquivo'];
        
        $caminhoCompleto = $_SERVER['DOCUMENT_ROOT'] . '/clinicaestrela/dashboard/uploads/pei/' . $nomeArquivo;
        $debugInfo[$pei['nome_original']] = [
            'nome_arquivo' => $nomeArquivo,
            'caminho' => $caminhoCompleto,
            'existe' => file_exists($caminhoCompleto) ? 'SIM' : 'NÃO'
        ];
    }
}
?>

<div class="page-header">
    <h1>
        <i class="fas fa-file-medical"></i>
        Plano Terapêutico
    </h1>
    <div class="patient-info-mini">
        <img src="<?php echo $fotoPerfil; ?>" alt="<?php echo htmlspecialchars($paciente['nome_completo']); ?>" class="mini-avatar">
        <span class="patient-name"><?php echo htmlspecialchars($paciente['nome_completo']); ?></span>
    </div>
</div>

<div class="content-area" id="contentArea" <?php echo !empty($backgroundAtual) ? 'style="background-image: url(\'' . $backgroundAtual . '\'); background-size: cover; background-position: center;"' : ''; ?>>
    
    <div class="pei-container">
        <div class="pei-header">
            <h2>
                <i class="fas fa-document"></i>
                Planos de Ensino Individualizados (PEI)
            </h2>
            <p class="pei-subtitle">Documentos anexados pela equipe terapêutica</p>
        </div>

        <?php if (empty($peis)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Nenhum PEI encontrado</h3>
                <p>Ainda não há planos terapêuticos anexados para <?php echo htmlspecialchars($paciente['nome_completo']); ?>.</p>
                <p class="empty-hint">Os documentos serão disponibilizados pela equipe quando estiverem prontos.</p>
            </div>
        <?php else: ?>
            <div class="pei-list">
                <?php foreach ($peis as $hash => $pei): ?>
                    <?php 
                   
                    $nomeArquivo = $pei['arquivo'];
                    // Caminho CORRETO: dashboard/uploads/pei/
                    $caminhoArquivo = '../../dashboard/uploads/pei/' . $nomeArquivo;
                    ?>
                    <div class="pei-item">
                        <div class="pei-icon">
                            <i class="fas <?php echo getFileIcon($pei['nome_original']); ?>"></i>
                        </div>
                        <div class="pei-info">
                            <div class="pei-name">
                                <?php 
                                $nomeSemExtensao = pathinfo($pei['nome_original'], PATHINFO_FILENAME);
                                echo htmlspecialchars($nomeSemExtensao);
                                ?>
                            </div>
                            <div class="pei-meta">
                                <span class="pei-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo formatarData($pei['data_upload']); ?>
                                </span>
                                <span class="pei-extension">
                                    <i class="fas fa-file"></i>
                                    <?php echo strtoupper(pathinfo($pei['nome_original'], PATHINFO_EXTENSION)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="pei-actions">
                            <a href="<?php echo $caminhoArquivo; ?>" 
                               class="btn-view" 
                               target="_blank"
                               title="Visualizar documento">
                                <i class="fas fa-eye"></i>
                                <span>Visualizar</span>
                            </a>
                            <a href="<?php echo $caminhoArquivo; ?>" 
                               class="btn-download" 
                               download="<?php echo $pei['nome_original']; ?>"
                               title="Baixar documento">
                                <i class="fas fa-download"></i>
                                <span>Baixar</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="pei-footer-info">
                <i class="fas fa-info-circle"></i>
                <span>Total de <?php echo count($peis); ?> documento(s) encontrado(s)</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="../../css/dashboard/cliente/plano_terapeutico_cliente.css">
<script src="../../dashboard/js/cliente/plano_terapeutico_cliente.js"></script>