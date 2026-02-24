/**
 * painel_evolucoes.js
 * JavaScript para a tela de Evoluções
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== VARIÁVEIS GLOBAIS =====
    let currentTab = 'lista'; // 'lista', 'nova', 'historico'
    let historicoData = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let filtrosHistorico = {
        data_inicio: '',
        data_fim: '',
        terapia: '',
        paciente_id: ''
    };
    
    // Elementos do DOM
    const tabs = document.querySelectorAll('.evolution-tab');
    const listaContainer = document.getElementById('lista-container');
    const novaContainer = document.getElementById('nova-container');
    const historicoContainer = document.getElementById('historico-container');
    const historicoTableBody = document.getElementById('historico-table-body');
    const historicoPagination = document.getElementById('historico-pagination');
    const btnFiltrarHistorico = document.getElementById('btn-filtrar-historico');
    const btnLimparHistorico = document.getElementById('btn-limpar-historico');
    const pacienteSelect = document.getElementById('paciente-historico');
    
    // ===== MENU MOBILE =====
    initMobileMenu();
    
    // ===== INICIALIZAÇÃO =====
    initTabs();
    loadHistoricoData();
    loadPacientesSelect();
    
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
                    
                    // Recarregar histórico se necessário
                    if (historicoData.length === 0) {
                        loadHistoricoData();
                    } else {
                        renderHistoricoTable();
                    }
                }
            });
        });
    }
    
    /**
     * Carrega dados do histórico
     */
    function loadHistoricoData() {
        // Mostrar loading
        if (historicoTableBody) {
            historicoTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Carregando histórico...</p>
                    </td>
                </tr>
            `;
        }
        
        // Simular carregamento de dados (substituir com dados reais do backend)
        setTimeout(() => {
            // Dados mockados para exemplo
            historicoData = [
                {
                    id: 1,
                    data: '2026-02-24',
                    paciente: 'João Silva',
                    terapia: 'ABA',
                    profissional: 'Dra. Maria Santos',
                    resumo: 'Paciente apresentou bom engajamento nas atividades propostas...',
                    turno: 'Manhã'
                },
                {
                    id: 2,
                    data: '2026-02-23',
                    paciente: 'Maria Souza',
                    terapia: 'Fonoaudiologia',
                    profissional: 'Dra. Ana Oliveira',
                    resumo: 'Trabalhamos sons bilabiais com uso de recursos visuais...',
                    turno: 'Tarde'
                },
                {
                    id: 3,
                    data: '2026-02-23',
                    paciente: 'Pedro Almeida',
                    terapia: 'Terapia Ocupacional',
                    profissional: 'Dr. Carlos Santos',
                    resumo: 'Atividades de integração sensorial com foco na regulação...',
                    turno: 'Manhã'
                },
                {
                    id: 4,
                    data: '2026-02-22',
                    paciente: 'Luísa Souza',
                    terapia: 'Psicologia',
                    profissional: 'Dra. Fernanda Lima',
                    resumo: 'Sessão focada em regulação emocional e expressão de sentimentos...',
                    turno: 'Tarde'
                },
                {
                    id: 5,
                    data: '2026-02-22',
                    paciente: 'Henrique Costa',
                    terapia: 'ABA',
                    profissional: 'Dra. Maria Santos',
                    resumo: 'Trabalhamos habilidades de comunicação funcional...',
                    turno: 'Manhã'
                }
            ];
            
            renderHistoricoTable();
        }, 800);
    }
    
    /**
     * Renderiza a tabela de histórico
     */
    function renderHistoricoTable() {
        if (!historicoTableBody) return;
        
        // Aplicar filtros
        let dadosFiltrados = filtrarHistorico(historicoData);
        
        // Calcular paginação
        const totalItems = dadosFiltrados.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const dadosPagina = dadosFiltrados.slice(start, end);
        
        if (dadosPagina.length === 0) {
            historicoTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-history"></i>
                        <h4>Nenhuma evolução encontrada</h4>
                        <p>Tente ajustar os filtros ou criar uma nova evolução.</p>
                    </td>
                </tr>
            `;
        } else {
            let html = '';
            
            dadosPagina.forEach(item => {
                // Formatar data
                const dataFormatada = formatarData(item.data);
                
                // Determinar classe da badge
                let badgeClass = 'badge-aba';
                if (item.terapia.includes('Fono')) badgeClass = 'badge-fono';
                else if (item.terapia.includes('Ocupacional')) badgeClass = 'badge-to';
                else if (item.terapia.includes('Psico')) badgeClass = 'badge-psicologia';
                
                html += `
                    <tr>
                        <td>${dataFormatada}</td>
                        <td>${item.paciente}</td>
                        <td><span class="badge ${badgeClass}">${item.terapia}</span></td>
                        <td>${item.profissional}</td>
                        <td>${item.resumo.substring(0, 60)}${item.resumo.length > 60 ? '...' : ''}</td>
                        <td>${item.turno}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-icon" onclick="visualizarEvolucao(${item.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon" onclick="editarEvolucao(${item.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon danger" onclick="excluirEvolucao(${item.id})" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            historicoTableBody.innerHTML = html;
        }
        
        // Renderizar paginação
        renderPagination(totalPages);
        
        // Atualizar informações de total
        const totalElement = document.getElementById('historico-total');
        if (totalElement) {
            totalElement.textContent = totalItems;
        }
    }
    
    /**
     * Filtra o histórico
     */
    function filtrarHistorico(dados) {
        return dados.filter(item => {
            // Filtrar por data
            if (filtrosHistorico.data_inicio) {
                if (item.data < filtrosHistorico.data_inicio) return false;
            }
            
            if (filtrosHistorico.data_fim) {
                if (item.data > filtrosHistorico.data_fim) return false;
            }
            
            // Filtrar por terapia
            if (filtrosHistorico.terapia && filtrosHistorico.terapia !== '') {
                if (!item.terapia.toLowerCase().includes(filtrosHistorico.terapia.toLowerCase())) return false;
            }
            
            // Filtrar por paciente
            if (filtrosHistorico.paciente_id && filtrosHistorico.paciente_id !== '') {
            }
            
            return true;
        });
    }
    
    /**
     * Renderiza a paginação
     */
    function renderPagination(totalPages) {
        if (!historicoPagination) return;
        
        if (totalPages <= 1) {
            historicoPagination.innerHTML = '';
            return;
        }
        
        let html = `
            <button class="pagination-btn" onclick="changePage('prev')" ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        html += `
            <button class="pagination-btn" onclick="changePage('next')" ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        
        historicoPagination.innerHTML = html;
    }
    
    /**
     * Carrega os pacientes no select
     */
    function loadPacientesSelect() {
        if (!pacienteSelect) return;
        
        // Aqui você carregaria os pacientes do ativo-cad.json
        // Por enquanto, vamos manter o que já está no PHP
    }
    
    /**
     * Formata data para exibição
     */
    function formatarData(dataString) {
        if (!dataString) return '';
        
        try {
            const data = new Date(dataString + 'T00:00:00');
            return data.toLocaleDateString('pt-BR');
        } catch {
            return dataString;
        }
    }
    
    // ===== EVENT LISTENERS =====
    
    // Botão filtrar histórico
    if (btnFiltrarHistorico) {
        btnFiltrarHistorico.addEventListener('click', function() {
            filtrosHistorico.data_inicio = document.getElementById('data-inicio')?.value || '';
            filtrosHistorico.data_fim = document.getElementById('data-fim')?.value || '';
            filtrosHistorico.terapia = document.getElementById('terapia-filtro')?.value || '';
            filtrosHistorico.paciente_id = document.getElementById('paciente-historico')?.value || '';
            
            currentPage = 1;
            renderHistoricoTable();
        });
    }
    
    // Botão limpar filtros
    if (btnLimparHistorico) {
        btnLimparHistorico.addEventListener('click', function() {
            // Limpar campos
            const dataInicio = document.getElementById('data-inicio');
            const dataFim = document.getElementById('data-fim');
            const terapia = document.getElementById('terapia-filtro');
            const paciente = document.getElementById('paciente-historico');
            
            if (dataInicio) dataInicio.value = '';
            if (dataFim) dataFim.value = '';
            if (terapia) terapia.value = '';
            if (paciente) paciente.value = '';
            
            // Limpar filtros
            filtrosHistorico = {
                data_inicio: '',
                data_fim: '',
                terapia: '',
                paciente_id: ''
            };
            
            currentPage = 1;
            renderHistoricoTable();
        });
    }
    
    // Anexar arquivos
    const anexarLink = document.querySelector('.anexar-link');
    const fotosUpload = document.getElementById('fotos-upload');
    const anexosLista = document.getElementById('anexos-lista');
    
    if (anexarLink && fotosUpload) {
        anexarLink.addEventListener('click', function() {
            fotosUpload.click();
        });
        
        fotosUpload.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                const anexoItem = document.createElement('div');
                anexoItem.className = 'anexo-item';
                
                // Determinar ícone baseado no tipo
                let icon = 'fa-file';
                if (file.type.includes('image')) icon = 'fa-file-image';
                else if (file.type.includes('pdf')) icon = 'fa-file-pdf';
                else if (file.type.includes('word')) icon = 'fa-file-word';
                else if (file.type.includes('excel')) icon = 'fa-file-excel';
                
                anexoItem.innerHTML = `
                    <div class="anexo-nome">
                        <i class="fas ${icon}"></i>
                        <span>${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                    </div>
                    <button type="button" class="btn-remover-anexo" onclick="this.closest('.anexo-item').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                if (anexosLista) {
                    anexosLista.appendChild(anexoItem);
                }
            });
        });
    }
});

// ===== FUNÇÕES GLOBAIS =====

/**
 * Mudar de página
 */
function changePage(direction) {
    if (direction === 'prev' && currentPage > 1) {
        currentPage--;
    } else if (direction === 'next') {
        currentPage++;
    }
    renderHistoricoTable();
}

/**
 * Ir para uma página específica
 */
function goToPage(page) {
    currentPage = page;
    renderHistoricoTable();
}

/**
 * Visualizar evolução
 */
function visualizarEvolucao(id) {
    alert(`Visualizar evolução ID: ${id}`);
    // Implementar modal de visualização
}

/**
 * Editar evolução
 */
function editarEvolucao(id) {
    alert(`Editar evolução ID: ${id}`);
    // Implementar edição
}

/**
 * Excluir evolução
 */
function excluirEvolucao(id) {
    if (confirm('Tem certeza que deseja excluir esta evolução?')) {
        alert(`Excluir evolução ID: ${id}`);
        // Implementar exclusão
    }
}