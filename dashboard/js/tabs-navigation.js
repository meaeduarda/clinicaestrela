// js/tabs-navigation.js - Gerenciador de navegação por abas

class TabsManager {
    constructor() {
        this.currentTab = 'identificacao';
        this.init();
    }
    
    init() {
        this.setupTabs();
        this.showTab('identificacao');
    }
    
    setupTabs() {
        const tabs = document.querySelectorAll('.tab');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const tabName = tab.dataset.tab;
                this.switchTab(tabName);
            });
        });
    }
    
    switchTab(tabName) {
        // Salvar dados da aba atual (se necessário)
        if (window.painelPreCadastro) {
            window.painelPreCadastro.saveCurrentTabData();
        }
        
        // Atualizar navegação
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        const clickedTab = document.querySelector(`.tab[data-tab="${tabName}"]`);
        if (clickedTab) clickedTab.classList.add('active');
        
        // Esconder todos os conteúdos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
            content.classList.remove('active');
        });
        
        // Mostrar conteúdo da aba selecionada
        const targetContent = document.getElementById(`form-${tabName}`);
        if (targetContent) {
            targetContent.style.display = 'block';
            setTimeout(() => {
                targetContent.classList.add('active');
            }, 10);
        }
        
        this.currentTab = tabName;
        
        // Atualizar título da página (opcional)
        this.updatePageTitle(tabName);
        
        console.log(`📋 Aba alterada para: ${this.getTabDisplayName(tabName)}`);
    }
    
    showTab(tabName) {
        this.switchTab(tabName);
    }
    
    updatePageTitle(tabName) {
        const tabDisplayName = this.getTabDisplayName(tabName);
        document.title = `Pré-Cadastro - ${tabDisplayName} | Clínica Estrela`;
    }
    
    getTabDisplayName(tabId) {
        const tabNames = {
            'identificacao': 'Identificação',
            'queixa': 'Queixa',
            'antecedente': 'Antecedentes',
            'desenvolvimento': 'Desenvolvimento',
            'observacao': 'Observação Clínica'
        };
        
        return tabNames[tabId] || tabId;
    }
    
    getCurrentTab() {
        return this.currentTab;
    }
    
    nextTab() {
        const tabs = ['identificacao', 'queixa', 'antecedente', 'desenvolvimento', 'observacao'];
        const currentIndex = tabs.indexOf(this.currentTab);
        
        if (currentIndex < tabs.length - 1) {
            this.switchTab(tabs[currentIndex + 1]);
        }
    }
    
    previousTab() {
        const tabs = ['identificacao', 'queixa', 'antecedente', 'desenvolvimento', 'observacao'];
        const currentIndex = tabs.indexOf(this.currentTab);
        
        if (currentIndex > 0) {
            this.switchTab(tabs[currentIndex - 1]);
        }
    }
}

// Exportar para uso global
window.TabsManager = TabsManager;