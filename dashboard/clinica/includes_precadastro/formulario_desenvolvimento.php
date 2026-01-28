<?php
// includes/formulario_desenvolvimento.php
?>

<h3 class="form-title">Desenvolvimento</h3>

<form id="form-desenvolvimento-data" class="patient-form">
    <!-- Seção 1: Motor -->
    <div class="form-section">
        <h4 class="section-title">Motor</h4>
        
        <!-- Sentou-se sem apoio? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Sentou-se sem apoio?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="sentou_sem_apoio" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="sentou_sem_apoio" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_sentou" name="idade_sentou" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Engatinhou? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Engatinhou?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="engatinhou" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="engatinhou" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_engatinhou" name="idade_engatinhou" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Começou a andar? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Começou a andar?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="comecou_andar" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="comecou_andar" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_andou" name="idade_andou" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Controle dos esfíncteres -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Controle dos esfíncteres</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="controle_esfincteres" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="controle_esfincteres" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_controle" name="idade_controle" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 2: Fala e Linguagem -->
    <div class="form-section">
        <h4 class="section-title">Fala e Linguagem</h4>
        
        <!-- Balbuciou? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Balbuciou?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="balbuciou" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="balbuciou" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_balbucio" name="idade_balbucio" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Falou as primeiras palavras? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Falou as primeiras palavras?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="primeiras_palavras" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="primeiras_palavras" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_primeiras_palavras" name="idade_primeiras_palavras" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Montou frases? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Montou frases?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="montou_frases" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="montou_frases" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                    <div class="age-input-container">
                        <input type="text" class="age-input" id="idade_frases" name="idade_frases" placeholder="Idade (meses)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Atualmente conversa com frases completas? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Atualmente conversa com frases completas?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="frases_completas" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="frases_completas" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 3: Social -->
    <div class="form-section">
        <h4 class="section-title">Social</h4>
        
        <!-- Sorriu em resposta a interações? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Sorriu em resposta a interações?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="sorriu_interacoes" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="sorriu_interacoes" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interage com outras crianças? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Interage com outras crianças?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="interage_criancas" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="interage_criancas" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 4: Alimentação -->
    <div class="form-section">
        <h4 class="section-title">Alimentação</h4>
        
        <!-- Aceitou bem a introdução alimentar? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Aceitou bem a introdução alimentar?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="introducao_alimentar" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="introducao_alimentar" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alimenta-se sozinho? -->
        <div class="form-row question-row">
            <div class="question-item">
                <div class="question-text">
                    <span class="question-label">Alimenta-se sozinho?</span>
                </div>
                <div class="question-controls">
                    <div class="radio-group compact">
                        <label class="radio-option">
                            <input type="radio" name="alimenta_sozinho" value="sim">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Sim</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="alimenta_sozinho" value="nao">
                            <span class="radio-dot"></span>
                            <span class="radio-label">Não</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hábitos alimentares / Observações -->
        <div class="form-row">
            <div class="form-group large">
                <div class="textarea-container">
                    <label for="habitos_alimentares">Hábitos alimentares / Observações</label>
                    <div class="textarea-wrapper">
                        <textarea id="habitos_alimentares" name="habitos_alimentares" rows="4" placeholder="Descreva os hábitos alimentares, preferências, dificuldades..."></textarea>
                        <div class="char-counter">
                            <span class="char-count">0</span>/400
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>