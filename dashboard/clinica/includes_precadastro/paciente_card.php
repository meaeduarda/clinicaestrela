<?php
// includes/paciente_card.php
?>

<div class="patient-card">
    <div class="patient-photo-container">
        <div class="patient-photo">
            <img src="<?php echo $paciente['foto']; ?>" alt="Foto do paciente <?php echo htmlspecialchars($paciente['nome_social']); ?>">
            <div class="photo-upload-overlay">
                <i class="fas fa-camera"></i>
                <span>Alterar foto</span>
            </div>
        </div>
    </div>
    <div class="patient-info">
        <div class="patient-header">
            <h2 class="patient-name"><?php echo htmlspecialchars($paciente['nome_social']); ?></h2>
            <span class="patient-age"><?php echo htmlspecialchars($paciente['idade']); ?></span>
        </div>
        <div class="patient-details">
            <div class="patient-contact">
                <span class="contact-label">Mãe:</span>
                <span class="contact-value"><?php echo htmlspecialchars($paciente['nome_mae']); ?></span>
            </div>
            <div class="patient-phone">
                <i class="fas fa-phone"></i>
                <span><?php echo htmlspecialchars($paciente['telefone']); ?></span>
                <i class="fas fa-comment message-icon"></i>
            </div>
        </div>
    </div>
    <div class="patient-status">
        <span class="status-badge">Status: <?php echo htmlspecialchars($paciente['status']); ?></span>
    </div>
</div>