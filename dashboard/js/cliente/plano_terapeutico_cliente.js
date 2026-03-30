document.addEventListener('DOMContentLoaded', function() {
    // Menu Mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileClose = document.querySelector('.mobile-close');
    
    if (mobileMenuToggle && sidebar && mobileClose) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        mobileClose.addEventListener('click', function() {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Função de notificação
    function mostrarNotificacao(mensagem, tipo = 'success') {
        const notification = document.getElementById('notification');
        const messageSpan = document.getElementById('notificationMessage');
        const icon = notification.querySelector('i');
        
        notification.style.display = 'flex';
        notification.className = 'notification show';
        
        if (tipo === 'success') {
            notification.classList.add('success');
            icon.className = 'fas fa-check-circle';
        } else {
            notification.classList.add('error');
            icon.className = 'fas fa-exclamation-circle';
        }
        
        messageSpan.textContent = mensagem;
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 3000);
    }

    // Animação ao clicar nos botões de download/visualização
    const btns = document.querySelectorAll('.btn-view, .btn-download');
    btns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const action = this.classList.contains('btn-view') ? 'visualizar' : 'baixar';
            const fileName = this.closest('.pei-item')?.querySelector('.pei-name')?.textContent || 'documento';
            
            // Mostrar notificação apenas para feedback (opcional)
            
        });
    });

    // Verificar se há PEIs e mostrar contagem
    const peiCount = document.querySelectorAll('.pei-item').length;
    if (peiCount > 0) {
        console.log(`Total de ${peiCount} PEI(s) encontrado(s) para o paciente`);
    }

    // Carregar background salvo no localStorage
    const contentArea = document.getElementById('contentArea');
    const savedBgGradient = localStorage.getItem('clienteBgGradient');
    if (savedBgGradient && contentArea && !contentArea.style.backgroundImage) {
        contentArea.style.background = savedBgGradient;
        contentArea.style.backgroundSize = 'cover';
        contentArea.style.backgroundPosition = 'center';
    }
});