<?php
// includes/formulario_observacao.php
?>

<h3 class="form-title">Observação Clínica</h3>

<form id="form-observacao-data" class="patient-form">
    <!-- Seção 1: Observações Clínicas -->
    <div class="form-section">
        <h4 class="section-title">
            <i class="fas fa-clipboard-check"></i>
            <span>Observações Clínicas</span>
        </h4>
        
        <!-- Campo principal de observações -->
        <div class="form-row">
            <div class="form-group large">
                <div class="textarea-container">
                    <label for="observacoes_clinicas">Observações Clínicas</label>
                    <div class="textarea-wrapper">
                        <textarea id="observacoes_clinicas" name="observacoes_clinicas" rows="6" 
                                  placeholder="Neste espaço, registre observações relevantes sobre a criança, interações durante a avaliação inicial ou outros pontos importantes."></textarea>
                        <div class="char-counter">
                            <span class="char-count" id="observacoes_counter">0</span>/500
                        </div>
                    </div>
                    <small class="field-info">Este campo é visível ao prontuário clínico</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção 2: Anexos -->
    <div class="form-section collapsible-section">
        <div class="section-header collapsible-header">
            <h4 class="section-title">
                <i class="fas fa-folder"></i>
                <span>Anexos</span>
            </h4>
        </div>
        
        <div class="collapsible-content">
            <!-- Botão para adicionar anexo -->
            <div class="form-row">
                <div class="form-group large">
                    <button type="button" class="btn-add-attachment" id="btn-add-attachment">
                        <i class="fas fa-plus"></i>
                        <span>Adicionar Anexo</span>
                    </button>
                    <input type="file" id="file-upload" name="anexos[]" multiple accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                </div>
            </div>

            <!-- Lista de anexos (inicialmente vazia) -->
            <div class="attachments-list" id="attachments-list">
                <div class="no-attachments">
                    <i class="fas fa-paperclip"></i>
                    <p>Nenhum anexo adicionado</p>
                </div>
            </div>

            <!-- Regras de upload -->
            <div class="upload-rules">
                <p class="rules-title">Regras de upload:</p>
                <ul class="rules-list">
                    <li><i class="fas fa-check-circle"></i> Máx.: <strong>10 arquivos</strong></li>
                    <li><i class="fas fa-check-circle"></i> Tipos aceitos: <strong>PDF, JPG, PNG</strong></li>
                    <li><i class="fas fa-check-circle"></i> Tamanho máximo: <strong>até 5MB por arquivo</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Botões de ação -->
    <div class="action-buttons observacao-buttons">
        <div class="button-group">
            <button type="button" class="btn btn-convert" onclick="salvarComoPacienteAtivo()">
                <i class="fas fa-user-check"></i>
                <span>Salvar Como Paciente Ativo</span>
            </button>
            <button type="button" class="btn btn-archive" onclick="salvarComoPacientePendente()">
                <i class="fas fa-user-clock"></i>
                <span>Salvar Como Paciente Pendente</span>
            </button>
        </div>
    </div>
</form>