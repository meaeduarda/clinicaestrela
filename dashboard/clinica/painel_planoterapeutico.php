<?php
// painel_planoterapeutico.php
session_start();

// Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login_clinica.php?error=Acesso negado. Por favor, faça login.");
    exit();
}

// Dados dinâmicos da sessão
$nomeLogado = $_SESSION['usuario_nome'];
$perfilLogado = $_SESSION['usuario_perfil'];

// Parâmetros da URL
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$pagina = max(1, $pagina);
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('Y-m');
$mesFormatado = date('F Y', strtotime($mes));

// Configurações
$pacientesPorPagina = 10;
$totalPacientes = 0;
$pacientes = [];

// Conexão com o banco de dados (substitua com sua configuração)
try {
    // Remova os comentários e configure sua conexão
    /*
    require_once '../../config/database.php';
    $conn = new PDO("mysql:host=localhost;dbname=sua_database", "usuario", "senha");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Construir WHERE
    $whereConditions = [];
    $params = [];
    
    if (!empty($busca)) {
        $whereConditions[] = "(p.nome_completo LIKE ? OR p.nome_mae LIKE ?)";
        $params[] = "%$busca%";
        $params[] = "%$busca%";
    }
    
    $where = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Contar total
    $sqlCount = "SELECT COUNT(*) as total FROM pacientes p $where";
    $stmtCount = $conn->prepare($sqlCount);
    if ($params) {
        $stmtCount->execute($params);
    } else {
        $stmtCount->execute();
    }
    $resultCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $totalPacientes = $resultCount['total'];
    
    // Buscar pacientes
    $offset = ($pagina - 1) * $pacientesPorPagina;
    $sql = "SELECT p.* FROM pacientes p $where ORDER BY p.nome_completo LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $limitParam = $pacientesPorPagina;
    $offsetParam = $offset;
    
    if ($params) {
        $stmt->bindParam(1, $limitParam, PDO::PARAM_INT);
        $stmt->bindParam(2, $offsetParam, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt->bindParam(1, $limitParam, PDO::PARAM_INT);
        $stmt->bindParam(2, $offsetParam, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    */
    
    // Para demonstração (remova quando configurar o banco)
    $totalPacientes = 45;
    
} catch (PDOException $e) {
    // Log do erro
    error_log("Erro na conexão: " . $e->getMessage());
    $erroBanco = "Erro ao conectar com o banco de dados";
}

// Calcular total de páginas
$totalPaginas = ceil($totalPacientes / $pacientesPorPagina);

// Gerar lista de meses (últimos 12 meses)
$meses = [];
for ($i = 0; $i < 12; $i++) {
    $data = strtotime("-$i months");
    $valor = date('Y-m', $data);
    $texto = date('F Y', $data);
    $meses[$valor] = $texto;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano Terapêutico - Clínica Estrela</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/clinicaestrela/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/clinicaestrela/favicon/favicon.svg">
    <link rel="shortcut icon" href="/clinicaestrela/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/clinicaestrela/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/clinicaestrela/favicon/site.webmanifest">
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_adm_preca.css">
    <link rel="stylesheet" href="../../css/dashboard/clinica/painel_planoterapeutico.css">
    
    <!-- Fontes e ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php 
        // Inclui o header do painel com menu ativo
        $menuAtivo = 'evolucao';
        include 'includes_precadastro/header_preca.php'; 
        ?>
        
        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Topo Desktop -->
            <div class="main-top desktop-only">
                <h2><i class="fas fa-clipboard-list"></i> Plano Terapêutico</h2>
                <div class="top-icons">
                    <div class="icon-btn with-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge">2</span>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="icon-btn">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>

            <!-- Área de Filtros -->
            <div class="filtros-container">
                <div class="filtro-mes">
                    <label for="selectMes">
                        <i class="fas fa-calendar-alt"></i>
                        Filtrar por: Mês
                    </label>
                    <select id="selectMes" class="select-mes" onchange="filtrarPorMes(this.value)">
                        <?php foreach ($meses as $valor => $texto): ?>
                            <option value="<?php echo $valor; ?>" 
                                <?php echo $mes == $valor ? 'selected' : ''; ?>>
                                <?php echo $texto; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="campo-busca">
                    <form method="GET" action="" class="form-busca">
                        <div class="input-busca-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   name="busca" 
                                   placeholder="Buscar paciente…" 
                                   value="<?php echo htmlspecialchars($busca); ?>"
                                   class="input-busca">
                        </div>
                        <input type="hidden" name="mes" value="<?php echo $mes; ?>">
                        <button type="submit" class="btn-busca">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de Pacientes -->
            <div class="pacientes-container">
                <?php if (isset($erroBanco)): ?>
                    <div class="mensagem-erro">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p><?php echo $erroBanco; ?></p>
                    </div>
                <?php elseif (empty($pacientes) && !empty($busca)): ?>
                    <div class="sem-resultados">
                        <i class="fas fa-search"></i>
                        <h3>Nenhum paciente encontrado</h3>
                        <p>Não encontramos pacientes para "<?php echo htmlspecialchars($busca); ?>"</p>
                    </div>
                <?php elseif (empty($pacientes)): ?>
                    <div class="sem-resultados">
                        <i class="fas fa-user-injured"></i>
                        <h3>Nenhum paciente cadastrado</h3>
                        <p>Cadastre pacientes para começar a criar planos terapêuticos</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pacientes as $paciente): ?>
                        <div class="card-paciente">
                            <!-- Informações do Paciente -->
                            <div class="info-paciente">
                                <div class="foto-paciente">
                                    <?php if (!empty($paciente['foto'])): ?>
                                        <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" 
                                             alt="<?php echo htmlspecialchars($paciente['nome_completo']); ?>">
                                    <?php else: ?>
                                        <div class="avatar-paciente">
                                            <?php 
                                            $iniciais = '';
                                            $nomes = explode(' ', $paciente['nome_completo']);
                                            if (count($nomes) > 0) {
                                                $iniciais = strtoupper(substr($nomes[0], 0, 1));
                                                if (count($nomes) > 1) {
                                                    $iniciais .= strtoupper(substr($nomes[1], 0, 1));
                                                }
                                            }
                                            echo $iniciais;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="detalhes-paciente">
                                    <h3 class="nome-paciente"><?php echo htmlspecialchars($paciente['nome_completo']); ?></h3>
                                    <p class="idade-paciente"><?php echo !empty($paciente['idade']) ? htmlspecialchars($paciente['idade']) : 'Idade não informada'; ?></p>
                                    <p class="mae-paciente">
                                        <i class="fas fa-user-female"></i>
                                        <?php echo !empty($paciente['nome_mae']) ? htmlspecialchars($paciente['nome_mae']) : 'Responsável não informado'; ?>
                                    </p>
                                    <p class="telefone-paciente">
                                        <i class="fas fa-phone"></i>
                                        <?php echo !empty($paciente['telefone']) ? htmlspecialchars($paciente['telefone']) : 'Telefone não informado'; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Ações do Plano Terapêutico -->
                            <div class="acoes-paciente">
                                <button class="btn-anexar-pei" onclick="abrirModalPei(<?php echo $paciente['id']; ?>)">
                                    <i class="fas fa-folder"></i>
                                    Anexar PEI
                                </button>
                                <small class="info-pei">
                                    Plano mensal - <?php echo $mesFormatado; ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginação -->
            <?php if ($totalPacientes > 0): ?>
            <div class="paginacao-container">
                <!-- Info de paginação -->
                <div class="paginacao-info">
                    <?php
                    $inicio = (($pagina - 1) * $pacientesPorPagina) + 1;
                    $fim = min($pagina * $pacientesPorPagina, $totalPacientes);
                    ?>
                    <span><?php echo $inicio; ?> – <?php echo $fim; ?> de <?php echo $totalPacientes; ?> pacientes</span>
                </div>
                
                <!-- Controles de página -->
                <div class="paginacao-controles">
                    <!-- Botão anterior -->
                    <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo $pagina - 1; ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo $mes; ?>" 
                           class="paginacao-btn paginacao-anterior">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="paginacao-btn paginacao-anterior disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    <?php endif; ?>
                    
                    <!-- Números das páginas -->
                    <?php
                    $paginaInicial = max(1, $pagina - 2);
                    $paginaFinal = min($totalPaginas, $pagina + 2);
                    
                    for ($i = $paginaInicial; $i <= $paginaFinal; $i++):
                    ?>
                        <?php if ($i == $pagina): ?>
                            <span class="paginacao-numero ativa"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo $mes; ?>" 
                               class="paginacao-numero"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <!-- Botão próximo -->
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="?pagina=<?php echo $pagina + 1; ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo $mes; ?>" 
                           class="paginacao-btn paginacao-proximo">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="paginacao-btn paginacao-proximo disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Rodapé -->
            <footer class="main-footer">
                <div class="footer-logo">
                    <i class="fas fa-star"></i>
                    <span>CLÍNICA ESTRELA</span>
                </div>
            </footer>
        </main>
    </div>

    <!-- Modal para Upload do PEI -->
    <div class="modal-pei" id="modalPei">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h3><i class="fas fa-folder"></i> Anexar Plano de Ensino Individualizado</h3>
                <button class="modal-fechar" onclick="fecharModalPei()">&times;</button>
            </div>
            <div class="modal-corpo">
                <div class="info-paciente-modal">
                    <div id="fotoPacienteModal"></div>
                    <div>
                        <h4 id="nomePacienteModal"></h4>
                        <p id="infoPacienteModal"></p>
                    </div>
                </div>
                
                <form id="formUploadPei" class="form-upload">
                    <input type="hidden" id="idPaciente" name="id_paciente">
                    <input type="hidden" id="mesPei" name="mes_pei" value="<?php echo $mes; ?>">
                    
                    <div class="form-grupo">
                        <label for="arquivoPei">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Selecionar arquivo do PEI
                        </label>
                        <input type="file" 
                               id="arquivoPei" 
                               name="arquivo_pei" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               required>
                        <small class="texto-ajuda">
                            Formatos aceitos: PDF, DOC, DOCX, JPG, PNG (Máximo: 10MB)
                        </small>
                    </div>
                    
                    <div class="preview-arquivo" id="previewArquivo">
                        <p>Nenhum arquivo selecionado</p>
                    </div>
                </form>
            </div>
            <div class="modal-rodape">
                <button type="button" class="btn btn-secundario" onclick="fecharModalPei()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primario" onclick="enviarPei()">
                    <i class="fas fa-upload"></i>
                    Enviar PEI
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Funções para o modal
        function abrirModalPei(idPaciente) {
            // Aqui você buscaria os dados do paciente via AJAX ou PHP
            // Para demonstração, vou usar valores estáticos
            const modal = document.getElementById('modalPei');
            document.getElementById('idPaciente').value = idPaciente;
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('mostrar');
            }, 10);
        }
        
        function fecharModalPei() {
            const modal = document.getElementById('modalPei');
            modal.classList.remove('mostrar');
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('formUploadPei').reset();
                document.getElementById('previewArquivo').innerHTML = '<p>Nenhum arquivo selecionado</p>';
            }, 300);
        }
        
        function filtrarPorMes(mes) {
            window.location.href = `?mes=${mes}&busca=<?php echo urlencode($busca); ?>`;
        }
        
        // Preview do arquivo
        document.getElementById('arquivoPei')?.addEventListener('change', function(e) {
            const arquivo = e.target.files[0];
            const preview = document.getElementById('previewArquivo');
            
            if (arquivo) {
                const tamanhoMB = (arquivo.size / 1024 / 1024).toFixed(2);
                
                // Validar tamanho
                if (arquivo.size > 10 * 1024 * 1024) {
                    alert('Arquivo muito grande. Tamanho máximo: 10MB');
                    this.value = '';
                    preview.innerHTML = '<p>Nenhum arquivo selecionado</p>';
                    return;
                }
                
                // Validar tipo
                const tiposPermitidos = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/jpg',
                    'image/png'
                ];
                
                if (!tiposPermitidos.includes(arquivo.type)) {
                    alert('Tipo de arquivo não permitido');
                    this.value = '';
                    preview.innerHTML = '<p>Nenhum arquivo selecionado</p>';
                    return;
                }
                
                // Mostrar preview
                preview.innerHTML = `
                    <div class="info-arquivo">
                        <i class="fas fa-file-${arquivo.type.includes('image') ? 'image' : 'pdf'}"></i>
                        <div>
                            <strong>${arquivo.name}</strong>
                            <span>${tamanhoMB} MB</span>
                        </div>
                    </div>
                `;
            }
        });
        
        // Enviar PEI
        function enviarPei() {
            const form = document.getElementById('formUploadPei');
            const arquivoInput = document.getElementById('arquivoPei');
            
            if (!arquivoInput.files.length) {
                alert('Por favor, selecione um arquivo');
                return;
            }
            
            // Aqui você faria o upload via AJAX
            // Exemplo:
            /*
            const formData = new FormData(form);
            
            fetch('upload_pei.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('PEI enviado com sucesso!');
                    fecharModalPei();
                    // Atualizar interface se necessário
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro ao enviar arquivo');
            });
            */
            
            // Para demonstração
            alert('PEI enviado com sucesso! (Simulação)');
            fecharModalPei();
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('modalPei')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalPei();
            }
        });
    </script>
</body>
</html>