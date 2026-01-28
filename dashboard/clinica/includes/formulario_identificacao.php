<?php
// includes/formulario_identificacao.php
?>

<h3 class="form-title">Identificação</h3>

<form id="form-identificacao-data" class="patient-form">
    <!-- Linha 1 -->
    <div class="form-row">
        <div class="form-group large">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($paciente['nome_completo']); ?>">
        </div>
    </div>

    <!-- Linha 2 -->
    <div class="form-row">
        <div class="form-group">
            <label>Sexo</label>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="sexo" value="Masculino" <?php echo $paciente['sexo'] === 'Masculino' ? 'checked' : ''; ?>>
                    <span class="radio-label">Masculino</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="sexo" value="Feminino" <?php echo $paciente['sexo'] === 'Feminino' ? 'checked' : ''; ?>>
                    <span class="radio-label">Feminino</span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="sexo" value="Outro" <?php echo $paciente['sexo'] === 'Outro' ? 'checked' : ''; ?>>
                    <span class="radio-label">Outro</span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="nascimento">Nascimento</label>
            <input type="date" id="nascimento" name="nascimento" value="<?php echo htmlspecialchars($paciente['data_nascimento']); ?>">
        </div>
    </div>

    <!-- Linha 3 -->
    <div class="form-row">
        <div class="form-group">
            <label for="nome_mae">Nome da Mãe</label>
            <input type="text" id="nome_mae" name="nome_mae" value="<?php echo htmlspecialchars($paciente['nome_mae']); ?>">
        </div>
        <div class="form-group">
            <label for="nome_pai">Nome do Pai</label>
            <input type="text" id="nome_pai" name="nome_pai" value="<?php echo htmlspecialchars($paciente['nome_pai']); ?>">
        </div>
    </div>

    <!-- Linha 4 (sem parentesco) -->
    <div class="form-row">
        <div class="form-group">
            <label for="responsavel">Responsável</label>
            <select id="responsavel" name="responsavel">
                <option value="">Selecione...</option>
                <option value="Mãe" <?php echo $paciente['responsavel'] === 'Mãe' ? 'selected' : ''; ?>>Mãe</option>
                <option value="Pai" <?php echo $paciente['responsavel'] === 'Pai' ? 'selected' : ''; ?>>Pai</option>
                <option value="Avô/Avó" <?php echo $paciente['responsavel'] === 'Avô/Avó' ? 'selected' : ''; ?>>Avô/Avó</option>
                <option value="Tio/Tia" <?php echo $paciente['responsavel'] === 'Tio/Tia' ? 'selected' : ''; ?>>Tio/Tia</option>
                <option value="Outro" <?php echo $paciente['responsavel'] === 'Outro' ? 'selected' : ''; ?>>Outro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($paciente['telefone']); ?>">
        </div>
    </div>

    <!-- Linha 5 (antiga linha 6) -->
    <div class="form-row">
        <div class="form-group">
            <label for="cpf_responsavel">CPF do Responsável</label>
            <input type="text" id="cpf_responsavel" name="cpf_responsavel" value="<?php echo htmlspecialchars($paciente['cpf_responsavel']); ?>">
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($paciente['email']); ?>">
        </div>
    </div>

    <!-- Linha 6 (antiga linha 7) -->
    <div class="form-row">
        <div class="form-group">
            <label for="convenio">Convênio</label>
            <select id="convenio" name="convenio">
                <option value="">Selecione...</option>
                <option value="Nenhum" <?php echo $paciente['convenio'] === 'Nenhum' ? 'selected' : ''; ?>>Nenhum</option>
                <option value="Unimed" <?php echo $paciente['convenio'] === 'Unimed' ? 'selected' : ''; ?>>Unimed</option>
                <option value="SUS" <?php echo $paciente['convenio'] === 'SUS' ? 'selected' : ''; ?>>SUS</option>
                <option value="Outro" <?php echo $paciente['convenio'] === 'Outro' ? 'selected' : ''; ?>>Outro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="telefone_convenio">Telefone do Convênio</label>
            <input type="text" id="telefone_convenio" name="telefone_convenio" value="<?php echo htmlspecialchars($paciente['telefone_convenio']); ?>">
        </div>
    </div>

    <!-- Linha 7 (antiga linha 8) -->
    <div class="form-row">
        <div class="form-group">
            <label for="numero_carteirinha">Nº Carteirinha</label>
            <input type="text" id="numero_carteirinha" name="numero_carteirinha" value="<?php echo htmlspecialchars($paciente['numero_carteirinha']); ?>">
        </div>
        <div class="form-group">
            <label for="escolaridade">Escolaridade</label>
            <select id="escolaridade" name="escolaridade">
                <option value="">Selecione...</option>
                <option value="Fundamental Incompleto" <?php echo $paciente['escolaridade'] === 'Fundamental Incompleto' ? 'selected' : ''; ?>>Fundamental Incompleto</option>
                <option value="Fundamental Completo" <?php echo $paciente['escolaridade'] === 'Fundamental Completo' ? 'selected' : ''; ?>>Fundamental Completo</option>
                <option value="Médio Incompleto" <?php echo $paciente['escolaridade'] === 'Médio Incompleto' ? 'selected' : ''; ?>>Médio Incompleto</option>
                <option value="Médio Completo" <?php echo $paciente['escolaridade'] === 'Médio Completo' ? 'selected' : ''; ?>>Médio Completo</option>
                <option value="Superior Incompleto" <?php echo $paciente['escolaridade'] === 'Superior Incompleto' ? 'selected' : ''; ?>>Superior Incompleto</option>
                <option value="Superior Completo" <?php echo $paciente['escolaridade'] === 'Superior Completo' ? 'selected' : ''; ?>>Superior Completo</option>
                <option value="Pós-graduação" <?php echo $paciente['escolaridade'] === 'Pós-graduação' ? 'selected' : ''; ?>>Pós-graduação</option>
                <option value="Mestrado" <?php echo $paciente['escolaridade'] === 'Mestrado' ? 'selected' : ''; ?>>Mestrado</option>
                <option value="Doutorado" <?php echo $paciente['escolaridade'] === 'Doutorado' ? 'selected' : ''; ?>>Doutorado</option>
            </select>
        </div>
    </div>

    <!-- Linha 8 (antiga linha 9) -->
    <div class="form-row">
        <div class="form-group">
            <label for="cpf_paciente">CPF do Paciente</label>
            <input type="text" id="cpf_paciente" name="cpf_paciente" value="<?php echo htmlspecialchars($paciente['cpf_paciente']); ?>">
        </div>
        <div class="form-group">
            <label for="rg_paciente">RG do Paciente</label>
            <input type="text" id="rg_paciente" name="rg_paciente" value="<?php echo htmlspecialchars($paciente['rg_paciente']); ?>">
        </div>
    </div>

    <!-- Linha 9 (antiga linha 10) -->
    <div class="form-row">
        <div class="form-group large">
            <label for="escola">Escola</label>
            <input type="text" id="escola" name="escola" value="<?php echo htmlspecialchars($paciente['escola']); ?>">
        </div>
    </div>
</form>