<?php
// includes/formulario_antecedentes.php
?>

<h3 class="form-title">Antecedentes</h3>

<form id="form-antecedente-data" class="patient-form">
    <!-- Seção 1: Gestação e Nascimento -->
    <div class="form-section">
        <h4 class="section-title">Gestação e Nascimento</h4>
        
        <!-- Duração da gestação -->
        <div class="form-row">
            <div class="form-group">
                <label>Duração da gestação</label>
                <div class="checkbox-group-grid">
                    <div class="checkbox-item">
                        <input type="radio" name="duracao_gestacao" id="gestacao_normal" value="normal">
                        <label for="gestacao_normal">Normal (37-41 sem)</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="duracao_gestacao" id="gestacao_prematura" value="prematura">
                        <label for="gestacao_prematura">Prematura (&lt;37 sem)</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="duracao_gestacao" id="gestacao_prolongada" value="prolongada">
                        <label for="gestacao_prolongada">Prolongada (42+ sem)</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipo de parto -->
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de parto</label>
                <div class="checkbox-group-grid">
                    <div class="checkbox-item">
                        <input type="radio" name="tipo_parto" id="parto_normal" value="normal" checked>
                        <label for="parto_normal">Normal</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="tipo_parto" id="parto_cesarea" value="cesarea">
                        <label for="parto_cesarea">Cesárea</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="tipo_parto" id="parto_complicado" value="complicado">
                        <label for="parto_complicado">Complicado</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problemas durante a gestação ou parto -->
        <div class="form-row">
            <div class="form-group">
                <label>Houve problemas durante a gestação ou parto?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="problemas_gestacao" value="sim" id="problemas_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="problemas_gestacao" value="nao" id="problemas_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar problemas -->
        <div id="detalhes-problemas-gestacao" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="quais_problemas_gestacao">Quais problemas houve?</label>
                        <textarea id="quais_problemas_gestacao" name="quais_problemas_gestacao" rows="3" placeholder="Descreva os problemas ocorridos durante a gestação ou parto..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problemas após o nascimento -->
        <div class="form-row">
            <div class="form-group">
                <label>Teve algum problema após o nascimento?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="problemas_pos_nascimento" value="sim" id="pos_nascimento_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="problemas_pos_nascimento" value="nao" id="pos_nascimento_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar problemas pós-nascimento -->
        <div id="detalhes-problemas-pos-nascimento" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="quais_problemas_pos_nascimento">Quais problemas houve?</label>
                        <textarea id="quais_problemas_pos_nascimento" name="quais_problemas_pos_nascimento" rows="3" placeholder="Descreva os problemas ocorridos após o nascimento..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 2: História Médica Pessoal -->
    <div class="form-section medical-history-section">
        <h4 class="section-title">História Médica Pessoal</h4>
        
        <!-- Complicações graves de saúde -->
        <div class="form-row">
            <div class="form-group">
                <label>Teve alguma complicação grave de saúde?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="complicacoes_graves" value="sim" id="complicacoes_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="complicacoes_graves" value="nao" id="complicacoes_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar complicações -->
        <div id="detalhes-complicacoes" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="quais_complicacoes">Qual(is)?</label>
                        <textarea id="quais_complicacoes" name="quais_complicacoes" rows="3" placeholder="Descreva as complicações de saúde..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hospitalizações -->
        <div class="form-row">
            <div class="form-group">
                <label>Hospitalizações</label>
                <div class="checkbox-group-grid">
                    <div class="checkbox-item">
                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_nunca" value="nunca">
                        <label for="hospitalizacao_nunca">Nunca</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_1vez" value="1vez">
                        <label for="hospitalizacao_1vez">1 vez</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="hospitalizacoes" id="hospitalizacao_2mais" value="2mais">
                        <label for="hospitalizacao_2mais">2 ou mais</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalhes hospitalizações -->
        <div class="form-row two-col">
            <div class="form-group">
                <div class="details-field">
                    <label for="motivo_hospitalizacao">Motivo:</label>
                    <textarea id="motivo_hospitalizacao" name="motivo_hospitalizacao" rows="2" placeholder="Motivo da hospitalização..."></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="details-field">
                    <label for="idade_hospitalizacao">Idade:</label>
                    <textarea id="idade_hospitalizacao" name="idade_hospitalizacao" rows="2" placeholder="Idade na hospitalização..."></textarea>
                </div>
            </div>
        </div>

        <!-- Histórico de convulsões -->
        <div class="form-row">
            <div class="form-group">
                <label>Histórico de convulsões?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="convulsoes" value="sim" id="convulsoes_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="convulsoes" value="nao" id="convulsoes_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar convulsões -->
        <div id="detalhes-convulsoes" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="detalhes_convulsoes">Detalhes das crises</label>
                        <textarea id="detalhes_convulsoes" name="detalhes_convulsoes" rows="3" placeholder="Descreva o tipo, frequência e características das crises..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de alergias -->
        <div class="form-row">
            <div class="form-group">
                <label>Histórico de alergias ou restrições alimentares</label>
                <div class="checkbox-group-grid">
                    <div class="checkbox-item">
                        <input type="radio" name="alergias" id="alergias_nenhuma" value="nenhuma" checked>
                        <label for="alergias_nenhuma">Sem alergias</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="alergias" id="alergias_restricoes" value="restricoes">
                        <label for="alergias_restricoes">Tem algumas restrições</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar alergias -->
        <div id="detalhes-alergias" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="quais_alergias">Qual(is) alergia / restrição?</label>
                        <textarea id="quais_alergias" name="quais_alergias" rows="3" placeholder="Descreva as alergias ou restrições alimentares..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 3: História Médica Familiar -->
    <div class="form-section">
        <h4 class="section-title">História Médica Familiar</h4>
        
        <!-- Histórico familiar -->
        <div class="form-row">
            <div class="form-group">
                <label>Alguém na família tem histórico de:</label>
                <div class="checkbox-group-grid">
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_autismo" name="historico_familiar[]" value="autismo">
                        <label for="familia_autismo">Autismo</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_tdah" name="historico_familiar[]" value="tdah">
                        <label for="familia_tdah">Transtorno de Déficit de Atenção (TDAH)</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_atraso" name="historico_familiar[]" value="atraso">
                        <label for="familia_atraso">Atraso no desenvolvimento</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_epilepsia" name="historico_familiar[]" value="epilepsia">
                        <label for="familia_epilepsia">Epilepsia / Convulsões</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_esquizofrenia" name="historico_familiar[]" value="esquizofrenia">
                        <label for="familia_esquizofrenia">Esquizofrenia</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_aprendizagem" name="historico_familiar[]" value="aprendizagem">
                        <label for="familia_aprendizagem">Transtorno de Aprendizagem</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="familia_outros" name="historico_familiar[]" value="outros">
                        <label for="familia_outros">Outros:</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar "outros" -->
        <div id="detalhes-outros-familia" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <input type="text" id="familia_outros_descricao" name="familia_outros_descricao" placeholder="Especificar outros históricos familiares...">
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 4: Sobre o crescimento da criança -->
    <div class="form-section">
        <h4 class="section-title">Sobre o crescimento da criança</h4>
        
        <!-- Crescimento similar aos irmãos -->
        <div class="form-row">
            <div class="form-group">
                <label>O crescimento da criança é similar ao de seus irmãos?</label>
                <div class="radio-group horizontal">
                    <label class="radio-option">
                        <input type="radio" name="crescimento_similar" value="sim" id="crescimento_sim">
                        <span class="radio-label">Sim</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="crescimento_similar" value="nao" id="crescimento_nao">
                        <span class="radio-label">Não</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Campo para detalhar diferenças -->
        <div id="detalhes-diferenca-crescimento" style="display: none;">
            <div class="form-row">
                <div class="form-group large">
                    <div class="details-field">
                        <label for="diferenca_crescimento">Descreva a diferença:</label>
                        <textarea id="diferenca_crescimento" name="diferenca_crescimento" rows="3" placeholder="Descreva as diferenças no crescimento em relação aos irmãos..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>