/**
 * painel_evolucoes.js
 * JavaScript para a tela de Evoluções
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== VARIÁVEIS GLOBAIS =====
    let currentTab = 'lista'; // 'lista', 'nova', 'historico'
    
    // Elementos do DOM
    const tabs = document.querySelectorAll('.evolution-tab');
    const listaContainer = document.getElementById('lista-container');
    const novaContainer = document.getElementById('nova-container');
    const historicoContainer = document.getElementById('historico-container');
    
    // ===== MENU MOBILE =====
    initMobileMenu();
    
    // ===== INICIALIZAÇÃO =====
    initTabs();
    
    // ===== FUNÇÕES =====
    
    /**
     * Inicializa o menu mobile
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
     * Inicializa as abas de navegação
     */
    function initTabs() {
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remover classe active de todas as abas
                tabs.forEach(t => t.classList.remove('active'));
                
                // Adicionar classe active na aba clicada
                this.classList.add('active');
                
                // Esconder todos os containers
                if (listaContainer) listaContainer.style.display = 'none';
                if (novaContainer) novaContainer.style.display = 'none';
                if (historicoContainer) historicoContainer.style.display = 'none';
                
                // Mostrar o container correspondente
                if (tabId === 'lista' && listaContainer) {
                    listaContainer.style.display = 'block';
                    currentTab = 'lista';
                } else if (tabId === 'nova' && novaContainer) {
                    novaContainer.style.display = 'block';
                    currentTab = 'nova';
                } else if (tabId === 'historico' && historicoContainer) {
                    historicoContainer.style.display = 'block';
                    currentTab = 'historico';
                    // Não carregar via AJAX, usar o HTML já renderizado pelo PHP
                }
            });
        });
    }
    
    // Anexar arquivos
    const anexarLink = document.querySelector('.anexar-link');
    const fotosUpload = document.getElementById('fotos-upload');
    const anexosLista = document.getElementById('anexos-lista');
    
    if (anexarLink && fotosUpload) {
        anexarLink.addEventListener('click', function(e) {
            e.preventDefault();
            fotosUpload.click();
        });
        
        fotosUpload.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                // Verificar tamanho (máximo 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`Arquivo "${file.name}" excede 10MB. Não será anexado.`);
                    return;
                }
                
                // Verificar tipo
                const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert(`Arquivo "${file.name}" não é um tipo permitido. Apenas imagens e PDF.`);
                    return;
                }
                
                const anexoItem = document.createElement('div');
                anexoItem.className = 'anexo-item';
                
                // Determinar ícone baseado no tipo
                let icon = 'fa-file-image';
                if (file.type.includes('pdf')) icon = 'fa-file-pdf';
                
                // Tamanho formatado
                const tamanho = (file.size / 1024).toFixed(1) + ' KB';
                
                anexoItem.innerHTML = `
                    <div class="anexo-icon">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="anexo-info">
                        <div class="anexo-nome">${escapeHtml(file.name)}</div>
                        <div class="anexo-tamanho">${tamanho}</div>
                    </div>
                    <div class="anexo-remove" onclick="this.closest('.anexo-item').remove()">
                        <i class="fas fa-times-circle"></i>
                    </div>
                `;
                
                if (anexosLista) {
                    anexosLista.appendChild(anexoItem);
                }
            });
            
            // Limpar o input para permitir selecionar os mesmos arquivos novamente
            fotosUpload.value = '';
        });
    }
    
    /**
     * Escapa HTML para evitar XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

// ===== FUNÇÕES GLOBAIS =====

/**
 * Visualizar evolução
 */
function visualizarEvolucao(id) {
    // Usar a função do evolucao_historico.php se disponível
    if (typeof window.visualizarEvolucao === 'function') {
        window.visualizarEvolucao(id);
    } else {
        alert('Função visualizarEvolucao não disponível');
    }
}

/**
 * Gerar PDF da evolução
 */
function gerarPDF(id) {
    window.location.href = 'gerar_pdf_evolucao.php?id=' + id;
}