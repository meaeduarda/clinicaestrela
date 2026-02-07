// dashboard/clinica/js/atualizar_contador_visitas.js

/**
 * Atualiza o contador de visitas agendadas não confirmadas
 */
function atualizarContadorVisitas() {
    fetch('atualizar_contador_visitas.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Atualizar o badge do sino
                const badgeElement = document.querySelector('.visitas-badge');
                const kpiCardCount = document.querySelector('.kpi-card.pink h3');
                
                if (data.count > 0) {
                    if (!badgeElement) {
                        // Criar badge se não existir
                        const iconBtn = document.querySelector('.icon-btn.with-badge');
                        if (iconBtn) {
                            const badge = document.createElement('span');
                            badge.className = 'visitas-badge';
                            badge.textContent = data.count;
                            iconBtn.appendChild(badge);
                        }
                    } else {
                        badgeElement.textContent = data.count;
                    }
                    
                    // Adicionar animação de pulso
                    if (badgeElement) {
                        badgeElement.style.animation = 'pulse 2s infinite';
                    }
                } else {
                    // Remover badge se count for 0
                    if (badgeElement) {
                        badgeElement.remove();
                    }
                }
                
                // Atualizar KPI card
                if (kpiCardCount) {
                    kpiCardCount.textContent = data.count;
                }
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar contador de visitas:', error);
        });
}

// Atualizar a cada 30 segundos
setInterval(atualizarContadorVisitas, 30000);

// Atualizar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    atualizarContadorVisitas();
});