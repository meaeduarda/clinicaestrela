<?php
// includes/formulario_queixa.php
?>

<h3 class="form-title">Queixa Principal e Demanda Atual</h3>

<form id="form-queixa-data" class="patient-form">
    <!-- Seção 1: Motivo da Procura -->
    <div class="form-section">
        <h4 class="section-title">Motivo da Procura</h4>
        
        <!-- Motivo principal da procura -->
        <div class="form-row">
            <div class="form-group large">
                <label for="motivo_principal">Motivo principal da procura</label>
                <textarea id="motivo_principal" name="motivo_principal" rows="3" placeholder="Descreva com suas próprias palavras, por exemplo: 'Dificuldade na fala, comportamento agitado, atraso no desenvolvimento...'"></textarea>
            </div>
        </div>

        <!-- Quem identificou a necessidade? (CHECKBOXES) -->
        <div class="form-row">
            <div class="form-group large">
                <label>Quem identificou a necessidade?</label>
                <div class="checkbox-grid">
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_pais" name="quem_identificou[]" value="pais">
                        <label for="identificou_pais">Pais / Responsáveis</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_escola" name="quem_identificou[]" value="escola">
                        <label for="identificou_escola">Escola</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_pediatra" name="quem_identificou[]" value="pediatra">
                        <label for="identificou_pediatra">Pediatra</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_neurologista" name="quem_identificou[]" value="neurologista">
                        <label for="identificou_neurologista">Neurologista</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_outro_prof" name="quem_identificou[]" value="outro_profissional">
                        <label for="identificou_outro_prof">Outro profissional</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="identificou_outro" name="quem_identificou[]" value="outro">
                        <label for="identificou_outro">Outro</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encaminhado por algum profissional? (RADIO) -->
        <div class="form-row">
            <div class="form-group">
                <label>Encaminhado por algum profissional?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="encaminhado" value="sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="encaminhado" value="nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Dados do profissional (aparece se SIM for selecionado) -->
        <div id="dados-profissional" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome_profissional">Nome do profissional</label>
                    <input type="text" id="nome_profissional" name="nome_profissional" placeholder="Digite o nome">
                </div>
                <div class="form-group">
                    <label for="especialidade_profissional">Especialidade</label>
                    <input type="text" id="especialidade_profissional" name="especialidade_profissional" placeholder="Digite a especialidade">
                </div>
            </div>
        </div>

        <!-- Possui relatório? (RADIO) -->
        <div class="form-row">
            <div class="form-group">
                <label>Possui relatório?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="possui_relatorio" value="sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="possui_relatorio" value="nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 2: Sinais Observados -->
    <div class="form-section">
        <h4 class="section-title">Sinais Observados</h4>
        
        <!-- Checkboxes de sinais -->
        <div class="form-row">
            <div class="form-group large">
                <div class="checkbox-grid two-columns">
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_atraso_fala" name="sinais_observados[]" value="atraso_fala">
                        <label for="sinal_atraso_fala">Atraso na fala</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_dificuldade_comunicacao" name="sinais_observados[]" value="dificuldade_comunicacao">
                        <label for="sinal_dificuldade_comunicacao">Dificuldade de comunicação</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_pouco_contato_visual" name="sinais_observados[]" value="pouco_contato_visual">
                        <label for="sinal_pouco_contato_visual">Pouco contato visual</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_nao_responde_chamado" name="sinais_observados[]" value="nao_responde_chamado">
                        <label for="sinal_nao_responde_chamado">Não responde quando chamado</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_comportamentos_repetitivos" name="sinais_observados[]" value="comportamentos_repetitivos">
                        <label for="sinal_comportamentos_repetitivos">Comportamentos repetitivos</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_sensibilidade_sons" name="sinais_observados[]" value="sensibilidade_sons">
                        <label for="sinal_sensibilidade_sons">Sensibilidade a sons / texturas</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_agitacao" name="sinais_observados[]" value="agitacao">
                        <label for="sinal_agitacao">Agitação / hiperatividade</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_dificuldade_interacao" name="sinais_observados[]" value="dificuldade_interacao">
                        <label for="sinal_dificuldade_interacao">Dificuldade de interação social</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_agressividade" name="sinais_observados[]" value="agressividade">
                        <label for="sinal_agressividade">Agressividade</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_dificuldade_aprendizagem" name="sinais_observados[]" value="dificuldade_aprendizagem">
                        <label for="sinal_dificuldade_aprendizagem">Dificuldade de aprendizagem</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="sinal_outro" name="sinais_observados[]" value="outro">
                        <label for="sinal_outro">Outro:</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campo "Outro" detalhado (aparece se "Outro" for selecionado) -->
        <div id="sinal-outro-detalhe" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <label for="sinal_outro_descricao">Especificar outro sinal</label>
                    <input type="text" id="sinal_outro_descricao" name="sinal_outro_descricao" placeholder="Descreva o sinal observado">
                </div>
            </div>
        </div>

        <!-- Descrição dos sinais observados -->
        <div class="form-row">
            <div class="form-group large">
                <label for="descricao_sinais">Descrever os sinais observados</label>
                <textarea id="descricao_sinais" name="descricao_sinais" rows="3" placeholder="A criança fala poucas palavras e se irrita quando não é compreendida."></textarea>
            </div>
        </div>
    </div>

    <!-- Seção 3: Expectativas da Família -->
    <div class="form-section">
        <h4 class="section-title">Expectativas da Família</h4>
        
        <!-- Expectativas -->
        <div class="form-row">
            <div class="form-group large">
                <label for="expectativas_familia">O que a família espera do atendimento?</label>
                <textarea id="expectativas_familia" name="expectativas_familia" rows="3" placeholder="Esperamos que ela consiga se comunicar melhor e interagir com outras crianças..."></textarea>
            </div>
        </div>

        <!-- Tratamento anterior (RADIO) -->
        <div class="form-row">
            <div class="form-group">
                <label>Já realizou algum tratamento anteriormente?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="tratamento_anterior" value="sim" id="tratamento_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tratamento_anterior" value="nao" id="tratamento_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Dados do tratamento anterior (aparece se SIM for selecionado) -->
        <div id="dados-tratamento" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_tratamento">Tipo de tratamento:</label>
                    <input type="text" id="tipo_tratamento" name="tipo_tratamento" placeholder="Ex: Fonoaudiologia, Psicologia, TO...">
                </div>
                <div class="form-group">
                    <label for="local_tratamento">Local:</label>
                    <input type="text" id="local_tratamento" name="local_tratamento" placeholder="Nome da clínica/hospital">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="periodo_tratamento">Período aproximado:</label>
                    <input type="text" id="periodo_tratamento" name="periodo_tratamento" placeholder="Ex: 6 meses em 2023">
                </div>
            </div>
        </div>
    </div>
</form>