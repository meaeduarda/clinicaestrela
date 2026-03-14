// ===== FUNCIONALIDADE DE EDIÇÃO DA GRADE =====
let editMode = false;
const editBtn = document.querySelector('.btn-edit');

// Dados mockados para seleção (em produção, viriam do banco de dados)
const pacientes = [
    'João Pedro', 'Ana Clara', 'Mariana Souza', 'Gabriel Lima', 'Rafaela Costa'
];

const terapeutas = [
    'Dra. Maria', 'Dr. Carlos', 'Dra. Sofia', 'Dr. João', 'Dra. Ana'
];

// Função para criar o modal de edição
function criarModalEdicao(celula, sessaoAtual, linha, coluna, tipoSala, horario) {
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

    // Obter valores atuais
    const nomeAtual = sessaoAtual ? sessaoAtual.querySelector('.name')?.textContent || '' : '';
    const terapeutaAtual = sessaoAtual ? sessaoAtual.querySelector('.professional')?.textContent || '' : '';
    const tipoAtual = sessaoAtual ? Array.from(sessaoAtual.classList).find(c => 
        ['aba', 'fono', 'psicologia', 'to', 'musica', 'fisioterapia', 
         'casa_habilidades', 'sala_kids', 'nutricao', 'aquatica'].includes(c)
    ) : 'aba';

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
                ${pacientes.map(p => `<option value="${p}" ${p === nomeAtual ? 'selected' : ''}>${p}</option>`).join('')}
            </select>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; color:#475569; font-weight:500; font-size:13px;">
                <i class="fas fa-stethoscope" style="margin-right:6px; color:#3b82f6; font-size:12px;"></i>
                Terapeuta:
            </label>
            <select id="editTerapeuta" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                <option value="">Selecione um terapeuta</option>
                ${terapeutas.map(t => `<option value="${t}" ${t === terapeutaAtual ? 'selected' : ''}>${t}</option>`).join('')}
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
            // Atualizar sessão existente
            const nameSpan = sessaoAtual.querySelector('.name');
            const profSpan = sessaoAtual.querySelector('.professional');
            
            if (nameSpan) nameSpan.textContent = paciente;
            if (profSpan) profSpan.textContent = terapeuta;
            
            // Atualizar classe do tipo
            const tipos = ['aba', 'fono', 'psicologia', 'to', 'musica', 'fisioterapia', 
                          'casa_habilidades', 'sala_kids', 'nutricao', 'aquatica'];
            
            tipos.forEach(t => sessaoAtual.classList.remove(t));
            sessaoAtual.classList.add(tipo);
        } else {
            // Criar nova sessão
            const novaSessao = document.createElement('div');
            novaSessao.className = `session ${tipo}`;
            novaSessao.innerHTML = `
                <span class="name">${paciente}</span>
                <span class="professional">${terapeuta}</span>
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