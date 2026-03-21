// ===== FUNCIONALIDADE DE EDIÇÃO DA GRADE =====
let editMode = false;
const editBtn = document.querySelector('.btn-edit');

// ===== DADOS REAIS CARREGADOS DOS ARQUIVOS JSON =====
let pacientesAtivos = [];
let terapeutas = [];

// Função para carregar pacientes do arquivo ativo-cad.json
async function carregarPacientes() {
    try {
        const response = await fetch('/clinicaestrela/dashboard/dados/ativo-cad.json');
        const dados = await response.json();
        
        // Filtrar apenas pacientes com status "Ativo"
        pacientesAtivos = dados.filter(paciente => paciente.status === "Ativo");
        
        console.log(`✅ ${pacientesAtivos.length} pacientes ativos carregados`);
        return pacientesAtivos;
    } catch (error) {
        console.error('Erro ao carregar pacientes:', error);
        return [];
    }
}

// Função para carregar terapeutas do arquivo users.json
async function carregarTerapeutas() {
    try {
        const response = await fetch('/clinicaestrela/dashboard/dados/users.json');
        const dados = await response.json();
        
        // Perfis que são considerados terapeutas (que aparecem na grade)
        const perfisTerapeutas = [
            'ABA',
            'Fonoterapeuta',
            'Fisioterapeuta',
            'Nutricionista',
            'Musicoterapeuta',
            'Psicopedagogia',
            'Psicologia',
            'Terapeuta Ocupacional',
            'TO'
        ];
        
        // Filtrar apenas usuários ativos com perfil de terapeuta
        terapeutas = dados.filter(user => 
            user.ativo === true && 
            perfisTerapeutas.includes(user.perfil)
        );
        
        console.log(`✅ ${terapeutas.length} terapeutas carregados:`, terapeutas.map(t => `${t.nome} (${t.perfil})`));
        return terapeutas;
    } catch (error) {
        console.error('Erro ao carregar terapeutas:', error);
        return [];
    }
}

// Função auxiliar para obter o nome do paciente (suporta ambas as classes)
function getPatientName(sessao) {
    const patientNameNew = sessao.querySelector('.patient-name');
    const patientNameOld = sessao.querySelector('.name');
    return patientNameNew ? patientNameNew.textContent : (patientNameOld ? patientNameOld.textContent : '');
}

// Função auxiliar para obter o nome do profissional (suporta ambas as classes)
function getProfessionalName(sessao) {
    const professionalNameNew = sessao.querySelector('.professional-name');
    const professionalNameOld = sessao.querySelector('.professional');
    return professionalNameNew ? professionalNameNew.textContent : (professionalNameOld ? professionalNameOld.textContent : '');
}

// Função auxiliar para obter o tipo da sessão (suporta ambas as classes)
function getSessionType(sessao) {
    // Verifica classes novas (session-aba, session-fono, etc)
    const classList = Array.from(sessao.classList);
    const newType = classList.find(c => c.startsWith('session-') && c !== 'session');
    if (newType) {
        return newType.replace('session-', '');
    }
    
    // Verifica classes antigas (aba, fono, to, etc)
    const oldTypes = ['aba', 'fono', 'psicologia', 'to', 'musica', 'fisioterapia', 
                      'casa_habilidades', 'sala_kids', 'nutricao', 'aquatica','lanche'];
    const oldType = classList.find(c => oldTypes.includes(c));
    if (oldType) {
        return oldType;
    }
    
    return 'aba'; // default
}

// Função auxiliar para atualizar o nome do paciente (suporta ambas as classes)
function updatePatientName(sessao, newName) {
    const patientNameNew = sessao.querySelector('.patient-name');
    const patientNameOld = sessao.querySelector('.name');
    
    if (patientNameNew) {
        patientNameNew.textContent = newName;
    } else if (patientNameOld) {
        patientNameOld.textContent = newName;
    } else {
        // Se não encontrar, cria o novo padrão
        const newSpan = document.createElement('span');
        newSpan.className = 'patient-name';
        newSpan.textContent = newName;
        sessao.insertBefore(newSpan, sessao.firstChild);
    }
}

// Função auxiliar para atualizar o nome do profissional (suporta ambas as classes)
function updateProfessionalName(sessao, newName) {
    const professionalNameNew = sessao.querySelector('.professional-name');
    const professionalNameOld = sessao.querySelector('.professional');
    
    if (professionalNameNew) {
        professionalNameNew.textContent = newName;
    } else if (professionalNameOld) {
        professionalNameOld.textContent = newName;
    } else {
        // Se não encontrar, cria o novo padrão
        const newSpan = document.createElement('span');
        newSpan.className = 'professional-name';
        newSpan.textContent = newName;
        sessao.appendChild(newSpan);
    }
}

// Função auxiliar para atualizar o tipo da sessão (suporta ambas as classes)
function updateSessionType(sessao, newType) {
    // Lista de todos os tipos possíveis (novos e antigos)
    const oldTypes = ['aba', 'fono', 'psicologia', 'to', 'musica', 'fisioterapia', 
                      'casa_habilidades', 'sala_kids', 'nutricao', 'aquatica','lanche'];
    const newTypes = oldTypes.map(t => `session-${t}`);
    const allTypes = [...oldTypes, ...newTypes];
    
    // Remove todas as classes de tipo existentes
    allTypes.forEach(type => {
        sessao.classList.remove(type);
    });
    
    // Adiciona a nova classe (padrão novo)
    sessao.classList.add(`session-${newType}`);
}

// Função para criar o modal de edição
function criarModalEdicao(celula, sessaoAtual, linha, coluna, tipoSala, horario) {
    // Garantir que os dados foram carregados
    if (pacientesAtivos.length === 0 || terapeutas.length === 0) {
        alert('Aguarde o carregamento dos dados. Tente novamente em alguns segundos.');
        return;
    }
    
    // Remover modal existente se houver
    const modalExistente = document.querySelector('.edit-modal');
    if (modalExistente) modalExistente.remove();

    const modal = document.createElement('div');
    modal.className = 'edit-modal';
    modal.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        z-index: 10000;
        width: 90%;
        max-width: 400px;
        font-family: 'Inter', sans-serif;
    `;

    // Overlay escuro
    const overlay = document.createElement('div');
    overlay.className = 'edit-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
    `;

    // Obter valores atuais (usando funções auxiliares)
    const nomeAtual = sessaoAtual ? getPatientName(sessaoAtual) : '';
    const terapeutaAtual = sessaoAtual ? getProfessionalName(sessaoAtual) : '';
    const tipoAtual = sessaoAtual ? getSessionType(sessaoAtual) : 'aba';

    // Mapeamento de tipos para exibição
    const tipoDisplay = {
        'aba': 'ABA',
        'fono': 'Fonoaudiologia',
        'to': 'Terapia Ocupacional',
        'psicologia': 'Psicologia',
        'musica': 'Musicoterapia',
        'fisioterapia': 'Fisioterapia',
        'nutricao': 'Nutrição',
        'aquatica': 'Terapia Aquática',
        'casa_habilidades': 'Casa de Habilidades',
        'sala_kids': 'Sala Kids'
    };

    // Gerar HTML das opções de pacientes
    const pacientesOptions = pacientesAtivos.map(p => {
        const selected = p.nome_completo === nomeAtual ? 'selected' : '';
        return `<option value="${p.nome_completo}" ${selected}>${p.nome_completo}</option>`;
    }).join('');

    // Gerar HTML das opções de terapeutas
    const terapeutasOptions = terapeutas.map(t => {
        const selected = t.nome === terapeutaAtual ? 'selected' : '';
        return `<option value="${t.nome}" ${selected}>${t.nome} (${t.perfil})</option>`;
    }).join('');

    modal.innerHTML = `
        <h3 style="margin:0 0 20px 0; color:#1e293b; font-size:18px; font-weight:700;">
            <i class="fas fa-edit" style="color:#3b82f6; margin-right:10px;"></i>
            Editar Sessão
        </h3>
        
        <div style="margin-bottom:16px; padding:12px; background:#f8fafc; border-radius:8px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="color:#64748b; font-size:12px;">Sala:</span>
                <span style="color:#1e293b; font-weight:600; font-size:13px;">${tipoSala}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:#64748b; font-size:12px;">Horário:</span>
                <span style="color:#1e293b; font-weight:600; font-size:13px;">${horario}</span>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; color:#475569; font-weight:500; font-size:13px;">
                <i class="fas fa-user" style="margin-right:6px; color:#3b82f6; font-size:12px;"></i>
                Paciente:
            </label>
            <select id="editPaciente" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                <option value="">Selecione um paciente</option>
                ${pacientesOptions}
            </select>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; color:#475569; font-weight:500; font-size:13px;">
                <i class="fas fa-stethoscope" style="margin-right:6px; color:#3b82f6; font-size:12px;"></i>
                Terapeuta:
            </label>
            <select id="editTerapeuta" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                <option value="">Selecione um terapeuta</option>
                ${terapeutasOptions}
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:6px; color:#475569; font-weight:500; font-size:13px;">
                <i class="fas fa-tag" style="margin-right:6px; color:#3b82f6; font-size:12px;"></i>
                Tipo de Terapia:
            </label>
            <select id="editTipo" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                <option value="aba" ${tipoAtual === 'aba' ? 'selected' : ''}>ABA</option>
                <option value="fono" ${tipoAtual === 'fono' ? 'selected' : ''}>Fonoaudiologia</option>
                <option value="to" ${tipoAtual === 'to' ? 'selected' : ''}>Terapia Ocupacional</option>
                <option value="psicologia" ${tipoAtual === 'psicologia' ? 'selected' : ''}>Psicologia</option>
                <option value="musica" ${tipoAtual === 'musica' ? 'selected' : ''}>Musicoterapia</option>
                <option value="fisioterapia" ${tipoAtual === 'fisioterapia' ? 'selected' : ''}>Fisioterapia</option>
                <option value="nutricao" ${tipoAtual === 'nutricao' ? 'selected' : ''}>Nutrição</option>
                <option value="aquatica" ${tipoAtual === 'aquatica' ? 'selected' : ''}>Terapia Aquática</option>
                <option value="casa_habilidades" ${tipoAtual === 'casa_habilidades' ? 'selected' : ''}>Casa de Habilidades</option>
                <option value="sala_kids" ${tipoAtual === 'sala_kids' ? 'selected' : ''}>Sala Kids</option>
                <option value="lanche" ${tipoAtual === 'lanche' ? 'selected' : ''}>Lanche</option>
            </select>
        </div>

        <div style="display:flex; gap:12px;">
            <button id="cancelEdit" style="flex:1; padding:12px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                Cancelar
            </button>
            <button id="saveEdit" style="flex:1; padding:12px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                Salvar
            </button>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(modal);

    // Event listeners
    document.getElementById('cancelEdit').addEventListener('click', () => {
        overlay.remove();
        modal.remove();
    });

    document.getElementById('saveEdit').addEventListener('click', () => {
        const paciente = document.getElementById('editPaciente').value;
        const terapeuta = document.getElementById('editTerapeuta').value;
        const tipo = document.getElementById('editTipo').value;

        if (!paciente || !terapeuta) {
            alert('Por favor, selecione paciente e terapeuta');
            return;
        }

        // Atualizar a sessão
        if (sessaoAtual) {
            // Atualizar sessão existente usando funções auxiliares
            updatePatientName(sessaoAtual, paciente);
            updateProfessionalName(sessaoAtual, terapeuta);
            updateSessionType(sessaoAtual, tipo);
        } else {
            // Criar nova sessão com o novo padrão
            const novaSessao = document.createElement('div');
            novaSessao.className = `session session-${tipo}`;
            novaSessao.innerHTML = `
                <span class="patient-name">${paciente}</span>
                <span class="professional-name">${terapeuta}</span>
            `;
            
            // Adicionar evento de clique para edição
            novaSessao.style.cursor = 'pointer';
            novaSessao.addEventListener('click', (e) => {
                e.stopPropagation();
                if (editMode) {
                    const th = celula.closest('table').querySelectorAll('th')[Array.from(celula.parentNode.children).indexOf(celula)];
                    const horario = celula.parentNode.querySelector('.time-col').textContent;
                    criarModalEdicao(celula, novaSessao, null, null, th?.textContent || 'Sala', horario);
                }
            });
            
            celula.innerHTML = '';
            celula.appendChild(novaSessao);
        }

        overlay.remove();
        modal.remove();
        
        // Mostrar mensagem de sucesso
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 10001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        `;
        toast.textContent = 'Sessão atualizada com sucesso!';
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 3000);
    });

    overlay.addEventListener('click', () => {
        overlay.remove();
        modal.remove();
    });
}

// Função para habilitar/desabilitar modo de edição
function toggleEditMode() {
    editMode = !editMode;
    
    const sessoes = document.querySelectorAll('.grade-table .session');
    
    if (editMode) {
        editBtn.innerHTML = '<i class="fas fa-check" style="margin-right:8px;"></i> Sair da Edição';
        editBtn.style.backgroundColor = '#10b981';
        editBtn.style.color = 'white';
        
        sessoes.forEach(sessao => {
            sessao.style.cursor = 'pointer';
            sessao.style.outline = '2px dashed #3b82f6';
            sessao.style.outlineOffset = '2px';
            
            // Adicionar evento de clique para edição
            sessao.addEventListener('click', function(e) {
                e.stopPropagation();
                if (editMode) {
                    const celula = this.closest('td');
                    const linha = this.closest('tr');
                    const tabela = this.closest('table');
                    const th = tabela.querySelectorAll('th')[Array.from(celula.parentNode.children).indexOf(celula)];
                    const horario = linha.querySelector('.time-col').textContent;
                    
                    criarModalEdicao(celula, this, linha, celula, th?.textContent || 'Sala', horario);
                }
            });
        });
        
        // Adicionar evento para células vazias
        const celulasVazias = document.querySelectorAll('.grade-table td:not(.time-col)');
        celulasVazias.forEach(celula => {
            if (!celula.querySelector('.session')) {
                celula.style.cursor = 'pointer';
                celula.style.backgroundColor = '#f0f9ff';
                celula.style.border = '2px dashed #3b82f6';
                
                celula.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (editMode) {
                        const linha = this.closest('tr');
                        const tabela = this.closest('table');
                        const th = tabela.querySelectorAll('th')[Array.from(linha.children).indexOf(this)];
                        const horario = linha.querySelector('.time-col').textContent;
                        
                        criarModalEdicao(this, null, linha, this, th?.textContent || 'Sala', horario);
                    }
                });
            }
        });
    } else {
        editBtn.innerHTML = 'Editar Grade';
        editBtn.style.backgroundColor = '#eff6ff';
        editBtn.style.color = '#3b82f6';
        
        sessoes.forEach(sessao => {
            sessao.style.cursor = 'default';
            sessao.style.outline = 'none';
            sessao.style.outlineOffset = '0';
            
            // Remover eventos antigos (clone para remover listeners)
            const novoSessao = sessao.cloneNode(true);
            sessao.parentNode.replaceChild(novoSessao, sessao);
        });
        
        const celulasVazias = document.querySelectorAll('.grade-table td:not(.time-col)');
        celulasVazias.forEach(celula => {
            celula.style.cursor = 'default';
            celula.style.backgroundColor = '';
            celula.style.border = '';
            
            // Remover eventos
            const novaCelula = celula.cloneNode(true);
            celula.parentNode.replaceChild(novaCelula, celula);
        });
    }
}

// Inicializar carregamento dos dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🔄 Carregando dados para edição da grade...');
    await Promise.all([carregarPacientes(), carregarTerapeutas()]);
    console.log('✅ Dados carregados com sucesso! Pronto para edição.');
    
    // Exibir resumo no console
    console.log(`📋 Pacientes ativos: ${pacientesAtivos.length}`);
    console.log(`👨‍⚕️ Terapeutas: ${terapeutas.length}`);
});

// Adicionar evento ao botão de editar
if (editBtn) {
    editBtn.addEventListener('click', toggleEditMode);
}

// Adicionar estilos CSS para o modal e overlay
const style = document.createElement('style');
style.textContent = `
    .edit-modal select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .edit-modal button:hover {
        transform: translateY(-1px);
        transition: all 0.2s;
    }
    
    #cancelEdit:hover {
        background-color: #e2e8f0;
    }
    
    #saveEdit:hover {
        background-color: #2563eb;
    }
`;
document.head.appendChild(style);