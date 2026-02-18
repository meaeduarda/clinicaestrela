/**
 * painel_evolucoes.js
 */
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initFileUpload();
});

/**
 * Menu Mobile
 */
function initMobileMenu() {
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

        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !mobileMenuToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
}

/**
 * Upload de arquivos com preview
 */
function initFileUpload() {
    const fileInput = document.getElementById('fotos-upload');
    if (!fileInput) return;

    fileInput.addEventListener('change', function(e) {
        const files = e.target.files;
        const anexosLista = document.getElementById('anexos-lista');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Calcular tamanho do arquivo
            let tamanho = '';
            if (file.size < 1024) {
                tamanho = file.size + ' B';
            } else if (file.size < 1024 * 1024) {
                tamanho = (file.size / 1024).toFixed(1) + ' KB';
            } else {
                tamanho = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            }
            
            // Determinar ícone baseado no tipo
            let icon = 'fa-file';
            if (file.type.startsWith('image/')) {
                icon = 'fa-file-image';
            } else if (file.type === 'application/pdf') {
                icon = 'fa-file-pdf';
            }
            
            // Criar elemento do anexo
            const anexoDiv = document.createElement('div');
            anexoDiv.className = 'anexo-item';
            anexoDiv.innerHTML = `
                <div class="anexo-nome">
                    <i class="fas ${icon}"></i>
                    <span>${file.name} (${tamanho})</span>
                </div>
                <button type="button" class="btn-remover-anexo" onclick="this.closest('.anexo-item').remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            anexosLista.appendChild(anexoDiv);
        }
        
        // Limpar input para permitir novos uploads
        fileInput.value = '';
    });
}

/**
 * Função para mostrar notificação toast
 */
function mostrarToast(mensagem, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${tipo === 'error' ? 'error' : ''}`;
    toast.innerHTML = `
        <i class="fas ${tipo === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
        <span>${mensagem}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s reverse';
        setTimeout(() => {
            if (toast.parentNode) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Exportar função global
window.mostrarToast = mostrarToast;