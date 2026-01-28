// js/painel_adm_preca.js - Classe principal do painel

class PainelPreCadastro {
    constructor() {
        this.formData = {
            identificacao: {},
            queixa: {},
            antecedentes: {},
            desenvolvimento: {},
            observacao: {}
        };
        
        this.currentTab = 'identificacao';
        this.init();
    }
    
    init() {
        console.log('🔧 Inicializando Painel de Pré-Cadastro...');
        
        // Inicializar componentes
        this.initTabs();
        this.initMobileMenu();
        this.initFormHandlers();
        this.initFileUpload();
        this.initModal();
        this.initAutoSave();
        
        // Carregar dados salvos
        this.loadSavedData();
        
        console.log('✅ Painel de Pré-Cadastro inicializado');
    }
    
    initTabs() {
        const tabs = document.querySelectorAll('.tab');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchTab(tab.dataset.tab);
            });
        });
    }
    
    switchTab(tabName) {
        // Salvar dados da aba atual
        this.saveCurrentTabData();
        
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
            
            // Carregar dados salvos para esta aba
            this.loadTabData(tabName);
        }
        
        this.currentTab = tabName;
        console.log(`📋 Aba ativa: ${this.getTabDisplayName(tabName)}`);
    }
    
    initMobileMenu() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileClose = document.getElementById('mobileClose');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.querySelector('.sidebar .mobile-close');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                sidebar.classList.add('active');
            });
        }
        
        if (mobileClose) {
            mobileClose.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }
        
        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }
        
        // Fechar menu ao clicar fora (mobile)
        document.addEventListener('click', (event) => {
            const isClickInsideSidebar = sidebar?.contains(event.target);
            const isClickOnMobileToggle = mobileMenuToggle?.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnMobileToggle && window.innerWidth <= 768) {
                sidebar?.classList.remove('active');
            }
        });
    }
    
    initFormHandlers() {
        // Configurar campos condicionais
        this.setupConditionalFields();
        
        // Configurar contadores de caracteres
        this.setupCharacterCounters();
        
        // Configurar seções colapsáveis
        this.setupCollapsibleSections();
    }
    
    setupConditionalFields() {
        // Queixa - Encaminhado por profissional
        const radiosEncaminhado = document.querySelectorAll('input[name="encaminhado"]');
        radiosEncaminhado.forEach(radio => {
            radio.addEventListener('change', () => this.toggleQueixaConditionalFields());
        });
        
        // Queixa - Checkbox "Outro" em sinais
        const sinalOutro = document.getElementById('sinal_outro');
        if (sinalOutro) {
            sinalOutro.addEventListener('change', () => this.toggleQueixaConditionalFields());
        }
        
        // Queixa - Tratamento anterior
        const radiosTratamento = document.querySelectorAll('input[name="tratamento_anterior"]');
        radiosTratamento.forEach(radio => {
            radio.addEventListener('change', () => this.toggleQueixaConditionalFields());
        });
        
        // Antecedentes - Todos os campos condicionais
        const antecedentesFields = [
            'problemas_gestacao', 'problemas_pos_nascimento', 'complicacoes_graves',
            'convulsoes', 'alergias', 'crescimento_similar'
        ];
        
        antecedentesFields.forEach(fieldName => {
            const radios = document.querySelectorAll(`input[name="${fieldName}"]`);
            radios.forEach(radio => {
                radio.addEventListener('change', () => this.toggleAntecedentesConditionalFields());
            });
        });
        
        // Antecedentes - Checkbox "outros" em histórico familiar
        const familiaOutros = document.getElementById('familia_outros');
        if (familiaOutros) {
            familiaOutros.addEventListener('change', () => this.toggleAntecedentesConditionalFields());
        }
        
        // Desenvolvimento - Radio buttons
        const desenvolvimentoRadios = document.querySelectorAll('#form-desenvolvimento input[type="radio"]');
        desenvolvimentoRadios.forEach(radio => {
            radio.addEventListener('change', () => this.toggleDesenvolvimentoAgeFields());
        });
        
        // Inicializar campos condicionais
        setTimeout(() => {
            this.toggleQueixaConditionalFields();
            this.toggleAntecedentesConditionalFields();
            this.toggleDesenvolvimentoAgeFields();
        }, 100);
    }
    
    toggleQueixaConditionalFields() {
        // Encaminhado por profissional
        const encaminhadoSim = document.querySelector('input[name="encaminhado"][value="sim"]');
        const dadosProfissional = document.getElementById('dados-profissional');
        if (encaminhadoSim && dadosProfissional) {
            dadosProfissional.style.display = encaminhadoSim.checked ? 'block' : 'none';
        }
        
        // Outro sinal
        const sinalOutro = document.getElementById('sinal_outro');
        const sinalOutroDetalhe = document.getElementById('sinal-outro-detalhe');
        if (sinalOutro && sinalOutroDetalhe) {
            sinalOutroDetalhe.style.display = sinalOutro.checked ? 'block' : 'none';
        }
        
        // Tratamento anterior
        const tratamentoSim = document.getElementById('tratamento_sim');
        const dadosTratamento = document.getElementById('dados-tratamento');
        if (tratamentoSim && dadosTratamento) {
            dadosTratamento.style.display = tratamentoSim.checked ? 'block' : 'none';
        }
    }
    
    toggleAntecedentesConditionalFields() {
        const elementos = [
            { radioId: 'problemas_sim', campoId: 'detalhes-problemas-gestacao' },
            { radioId: 'pos_nascimento_sim', campoId: 'detalhes-problemas-pos-nascimento' },
            { radioId: 'complicacoes_sim', campoId: 'detalhes-complicacoes' },
            { radioId: 'convulsoes_sim', campoId: 'detalhes-convulsoes' },
            { radioId: 'alergias_restricoes', campoId: 'detalhes-alergias' },
            { radioId: 'crescimento_nao', campoId: 'detalhes-diferenca-crescimento' }
        ];
        
        elementos.forEach(item => {
            const radio = document.getElementById(item.radioId);
            const campo = document.getElementById(item.campoId);
            if (radio && campo) {
                campo.style.display = radio.checked ? 'block' : 'none';
            }
        });
        
        // Checkbox "outros" em histórico familiar
        const familiaOutros = document.getElementById('familia_outros');
        const detalhesOutrosFamilia = document.getElementById('detalhes-outros-familia');
        if (familiaOutros && detalhesOutrosFamilia) {
            detalhesOutrosFamilia.style.display = familiaOutros.checked ? 'block' : 'none';
        }
    }
    
    toggleDesenvolvimentoAgeFields() {
        const ageInputs = document.querySelectorAll('.age-input-container');
        
        ageInputs.forEach(container => {
            const questionItem = container.closest('.question-item');
            const radioYes = questionItem.querySelector('input[type="radio"][value="sim"]');
            
            if (radioYes && radioYes.checked) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    }
    
    setupCharacterCounters() {
        // Observações clínicas
        const observacoesTextarea = document.getElementById('observacoes_clinicas');
        const observacoesCounter = document.getElementById('observacoes_counter');
        
        if (observacoesTextarea && observacoesCounter) {
            observacoesCounter.textContent = observacoesTextarea.value.length;
            
            observacoesTextarea.addEventListener('input', function() {
                observacoesCounter.textContent = this.value.length;
                observacoesCounter.style.color = this.value.length > 500 ? '#ef4444' : '#3b82f6';
            });
        }
        
        // Hábitos alimentares
        const habitosTextarea = document.getElementById('habitos_alimentares');
        const charCount = document.querySelector('.char-count');
        
        if (habitosTextarea && charCount) {
            charCount.textContent = habitosTextarea.value.length;
            
            habitosTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                charCount.style.color = length > 400 ? '#ef4444' : '#3b82f6';
            });
        }
    }
    
    setupCollapsibleSections() {
        const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
        
        collapsibleHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const section = header.closest('.collapsible-section');
                if (section) {
                    section.classList.toggle('active');
                }
            });
        });
    }
    
    initFileUpload() {
        const btnAddAttachment = document.getElementById('btn-add-attachment');
        const fileUpload = document.getElementById('file-upload');
        const attachmentsList = document.getElementById('attachments-list');
        
        if (btnAddAttachment && fileUpload) {
            btnAddAttachment.addEventListener('click', () => {
                fileUpload.click();
            });
            
            fileUpload.addEventListener('change', (e) => {
                this.handleFileUpload(e.target.files);
            });
        }
        
        // Drag and drop
        if (attachmentsList) {
            attachmentsList.addEventListener('dragover', (e) => {
                e.preventDefault();
                attachmentsList.style.backgroundColor = '#f0f9ff';
                attachmentsList.style.borderColor = '#3b82f6';
            });
            
            attachmentsList.addEventListener('dragleave', (e) => {
                e.preventDefault();
                attachmentsList.style.backgroundColor = '';
                attachmentsList.style.borderColor = '';
            });
            
            attachmentsList.addEventListener('drop', (e) => {
                e.preventDefault();
                attachmentsList.style.backgroundColor = '';
                attachmentsList.style.borderColor = '';
                
                if (e.dataTransfer.files.length) {
                    this.handleFileUpload(e.dataTransfer.files);
                }
            });
        }
    }
    
    handleFileUpload(files) {
        const attachmentsList = document.getElementById('attachments-list');
        const noAttachments = attachmentsList?.querySelector('.no-attachments');
        
        if (!attachmentsList) return;
        
        // Remover mensagem "nenhum anexo"
        if (noAttachments) {
            noAttachments.style.display = 'none';
        }
        
        // Limitar a 10 arquivos
        const existingFiles = attachmentsList.querySelectorAll('.attachment-item').length;
        const remainingSlots = config.maxAnexos - existingFiles;
        
        if (files.length > remainingSlots) {
            this.showNotification(`Você só pode adicionar mais ${remainingSlots} arquivo(s). Limite máximo: ${config.maxAnexos} arquivos.`, 'error');
            files = Array.from(files).slice(0, remainingSlots);
        }
        
        // Processar cada arquivo
        Array.from(files).forEach(file => {
            this.processFile(file, attachmentsList);
        });
        
        // Resetar input
        const fileUpload = document.getElementById('file-upload');
        if (fileUpload) {
            fileUpload.value = '';
        }
    }
    
    processFile(file, attachmentsList) {
        // Validar tipo de arquivo
        const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            this.showNotification(`Arquivo "${file.name}" não suportado. Use PDF, JPG ou PNG.`, 'error');
            return;
        }
        
        // Validar tamanho (5MB)
        if (file.size > config.maxTamanhoAnexo) {
            this.showNotification(`Arquivo "${file.name}" muito grande. Tamanho máximo: ${this.formatFileSize(config.maxTamanhoAnexo)}.`, 'error');
            return;
        }
        
        // Criar elemento de anexo
        const attachmentElement = this.createAttachmentElement(file);
        attachmentsList.appendChild(attachmentElement);
        
        console.log(`📎 Arquivo adicionado: ${file.name}`);
    }
    
    createAttachmentElement(file) {
        const div = document.createElement('div');
        div.className = 'attachment-item';
        
        // Formatar tamanho do arquivo
        const fileSize = this.formatFileSize(file.size);
        
        // Determinar ícone baseado no tipo
        let iconClass = 'fa-file';
        if (file.type === 'application/pdf') {
            iconClass = 'fa-file-pdf';
        } else if (file.type.startsWith('image/')) {
            iconClass = 'fa-file-image';
        }
        
        div.innerHTML = `
            <div class="attachment-info">
                <i class="fas ${iconClass} attachment-icon"></i>
                <div>
                    <span class="attachment-name">${file.name}</span>
                    <span class="attachment-size">(${fileSize})</span>
                </div>
            </div>
            <div class="attachment-actions">
                <button type="button" class="btn-remove-attachment" title="Remover arquivo">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Adicionar evento para remover anexo
        const removeBtn = div.querySelector('.btn-remove-attachment');
        removeBtn.addEventListener('click', () => {
            div.remove();
            
            // Mostrar mensagem "nenhum anexo" se lista estiver vazia
            const attachmentsList = document.getElementById('attachments-list');
            if (attachmentsList && attachmentsList.querySelectorAll('.attachment-item').length === 0) {
                const noAttachments = attachmentsList.querySelector('.no-attachments');
                if (noAttachments) {
                    noAttachments.style.display = 'block';
                }
            }
        });
        
        return div;
    }
    
    initModal() {
        const photoModal = document.getElementById('photoModal');
        const photoUploadOverlay = document.querySelector('.photo-upload-overlay');
        const modalClose = document.querySelector('.modal-close');
        const cancelBtn = document.querySelector('.btn-cancel');
        const uploadBtn = document.querySelector('.btn-upload');
        const fileInput = document.getElementById('foto_paciente');
        const photoPreview = document.getElementById('photoPreview');
        const patientPhoto = document.querySelector('.patient-photo img');
        
        if (!photoModal || !photoUploadOverlay) return;
        
        // Abrir modal
        photoUploadOverlay.addEventListener('click', () => {
            photoModal.style.display = 'block';
        });
        
        // Fechar modal
        const closeModal = () => {
            photoModal.style.display = 'none';
            if (fileInput) fileInput.value = '';
        };
        
        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        
        // Preview da foto
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) this.handlePhotoSelection(file);
            });
        }
        
        // Upload da foto
        if (uploadBtn) {
            uploadBtn.addEventListener('click', () => {
                this.handlePhotoUpload();
            });
        }
        
        // Fechar modal ao clicar fora
        window.addEventListener('click', (e) => {
            if (e.target === photoModal) {
                closeModal();
            }
        });
    }
    
    handlePhotoSelection(file) {
        // Validar tamanho
        if (file.size > config.maxTamanhoFoto) {
            alert('A foto deve ter no máximo 2MB.');
            return;
        }
        
        // Validar tipo
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Formato de imagem não suportado. Use JPG, PNG, GIF ou WebP.');
            return;
        }
        
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const photoPreview = document.getElementById('photoPreview');
            if (photoPreview) {
                photoPreview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
    
    handlePhotoUpload() {
        const fileInput = document.getElementById('foto_paciente');
        const uploadBtn = document.querySelector('.btn-upload');
        
        if (!fileInput || fileInput.files.length === 0) {
            alert('Por favor, selecione uma foto.');
            return;
        }
        
        const file = fileInput.files[0];
        
        // Simular upload
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = (e) => {
                // Atualizar foto do paciente
                const patientPhoto = document.querySelector('.patient-photo img');
                if (patientPhoto) patientPhoto.src = e.target.result;
                
                const photoPreview = document.getElementById('photoPreview');
                if (photoPreview) photoPreview.src = e.target.result;
                
                this.showNotification('Foto atualizada com sucesso!', 'success');
                
                // Restaurar botão
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Enviar Foto';
                
                // Fechar modal
                document.getElementById('photoModal').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }, 1500);
    }
    
    initAutoSave() {
        // Salvar ao sair da página
        window.addEventListener('beforeunload', () => {
            this.saveAllData();
            this.saveToLocalStorage();
        });
        
        // Salvar periodicamente (a cada 30 segundos)
        setInterval(() => {
            this.saveAllData();
            this.saveToLocalStorage();
        }, 30000);
    }
    
    saveCurrentTabData() {
        switch(this.currentTab) {
            case 'queixa':
                this.saveQueixaForm();
                break;
            case 'antecedente':
                this.saveAntecedentesForm();
                break;
            case 'desenvolvimento':
                this.saveDesenvolvimentoForm();
                break;
            case 'observacao':
                this.saveObservacaoForm();
                break;
        }
    }
    
    saveQueixaForm() {
        const form = document.getElementById('form-queixa-data');
        if (!form) return;
        
        this.formData.queixa = {
            motivo_principal: form.querySelector('#motivo_principal')?.value || '',
            quem_identificou: Array.from(form.querySelectorAll('input[name="quem_identificou[]"]:checked'))
                .map(cb => cb.value),
            encaminhado: form.querySelector('input[name="encaminhado"]:checked')?.value || '',
            nome_profissional: form.querySelector('#nome_profissional')?.value || '',
            especialidade_profissional: form.querySelector('#especialidade_profissional')?.value || '',
            possui_relatorio: form.querySelector('input[name="possui_relatorio"]:checked')?.value || '',
            sinais_observados: Array.from(form.querySelectorAll('input[name="sinais_observados[]"]:checked'))
                .map(cb => cb.value),
            sinal_outro_descricao: form.querySelector('#sinal_outro_descricao')?.value || '',
            descricao_sinais: form.querySelector('#descricao_sinais')?.value || '',
            expectativas_familia: form.querySelector('#expectativas_familia')?.value || '',
            tratamento_anterior: form.querySelector('input[name="tratamento_anterior"]:checked')?.value || '',
            tipo_tratamento: form.querySelector('#tipo_tratamento')?.value || '',
            local_tratamento: form.querySelector('#local_tratamento')?.value || '',
            periodo_tratamento: form.querySelector('#periodo_tratamento')?.value || ''
        };
    }
    
    saveAntecedentesForm() {
        const form = document.getElementById('form-antecedente-data');
        if (!form) return;
        
        this.formData.antecedentes = {
            duracao_gestacao: form.querySelector('input[name="duracao_gestacao"]:checked')?.value || '',
            tipo_parto: form.querySelector('input[name="tipo_parto"]:checked')?.value || '',
            problemas_gestacao: form.querySelector('input[name="problemas_gestacao"]:checked')?.value || '',
            quais_problemas_gestacao: form.querySelector('#quais_problemas_gestacao')?.value || '',
            problemas_pos_nascimento: form.querySelector('input[name="problemas_pos_nascimento"]:checked')?.value || '',
            quais_problemas_pos_nascimento: form.querySelector('#quais_problemas_pos_nascimento')?.value || '',
            complicacoes_graves: form.querySelector('input[name="complicacoes_graves"]:checked')?.value || '',
            quais_complicacoes: form.querySelector('#quais_complicacoes')?.value || '',
            hospitalizacoes: form.querySelector('input[name="hospitalizacoes"]:checked')?.value || '',
            motivo_hospitalizacao: form.querySelector('#motivo_hospitalizacao')?.value || '',
            idade_hospitalizacao: form.querySelector('#idade_hospitalizacao')?.value || '',
            convulsoes: form.querySelector('input[name="convulsoes"]:checked')?.value || '',
            detalhes_convulsoes: form.querySelector('#detalhes_convulsoes')?.value || '',
            alergias: form.querySelector('input[name="alergias"]:checked')?.value || '',
            quais_alergias: form.querySelector('#quais_alergias')?.value || '',
            historico_familiar: Array.from(form.querySelectorAll('input[name="historico_familiar[]"]:checked'))
                .map(cb => cb.value),
            familia_outros_descricao: form.querySelector('#familia_outros_descricao')?.value || '',
            crescimento_similar: form.querySelector('input[name="crescimento_similar"]:checked')?.value || '',
            diferenca_crescimento: form.querySelector('#diferenca_crescimento')?.value || ''
        };
    }
    
    saveDesenvolvimentoForm() {
        const form = document.getElementById('form-desenvolvimento-data');
        if (!form) return;
        
        this.formData.desenvolvimento = {
            sentou_sem_apoio: form.querySelector('input[name="sentou_sem_apoio"]:checked')?.value || '',
            idade_sentou: form.querySelector('#idade_sentou')?.value || '',
            engatinhou: form.querySelector('input[name="engatinhou"]:checked')?.value || '',
            idade_engatinhou: form.querySelector('#idade_engatinhou')?.value || '',
            comecou_andar: form.querySelector('input[name="comecou_andar"]:checked')?.value || '',
            idade_andou: form.querySelector('#idade_andou')?.value || '',
            controle_esfincteres: form.querySelector('input[name="controle_esfincteres"]:checked')?.value || '',
            idade_controle: form.querySelector('#idade_controle')?.value || '',
            balbuciou: form.querySelector('input[name="balbuciou"]:checked')?.value || '',
            idade_balbucio: form.querySelector('#idade_balbucio')?.value || '',
            primeiras_palavras: form.querySelector('input[name="primeiras_palavras"]:checked')?.value || '',
            idade_primeiras_palavras: form.querySelector('#idade_primeiras_palavras')?.value || '',
            montou_frases: form.querySelector('input[name="montou_frases"]:checked')?.value || '',
            idade_frases: form.querySelector('#idade_frases')?.value || '',
            frases_completas: form.querySelector('input[name="frases_completas"]:checked')?.value || '',
            sorriu_interacoes: form.querySelector('input[name="sorriu_interacoes"]:checked')?.value || '',
            interage_criancas: form.querySelector('input[name="interage_criancas"]:checked')?.value || '',
            introducao_alimentar: form.querySelector('input[name="introducao_alimentar"]:checked')?.value || '',
            alimenta_sozinho: form.querySelector('input[name="alimenta_sozinho"]:checked')?.value || '',
            habitos_alimentares: form.querySelector('#habitos_alimentares')?.value || ''
        };
    }
    
    saveObservacaoForm() {
        const form = document.getElementById('form-observacao-data');
        if (!form) return;
        
        this.formData.observacao = {
            observacoes_clinicas: form.querySelector('#observacoes_clinicas')?.value || '',
            anexos: Array.from(form.querySelectorAll('.attachment-name')).map(el => el.textContent)
        };
    }
    
    saveAllData() {
        this.saveQueixaForm();
        this.saveAntecedentesForm();
        this.saveDesenvolvimentoForm();
        this.saveObservacaoForm();
        
        console.log('💾 Todos os dados salvos:', this.formData);
    }
    
    saveToLocalStorage() {
        try {
            localStorage.setItem(config.storageKey, JSON.stringify(this.formData));
        } catch (e) {
            console.error('❌ Erro ao salvar no localStorage:', e);
        }
    }
    
    loadSavedData() {
        try {
            const savedData = localStorage.getItem(config.storageKey);
            if (savedData) {
                Object.assign(this.formData, JSON.parse(savedData));
                console.log('📂 Dados recuperados do localStorage');
            }
        } catch (e) {
            console.error('❌ Erro ao carregar dados do localStorage:', e);
        }
    }
    
    loadTabData(tabName) {
        // Esta função seria chamada ao mudar de aba
        // Implemente conforme necessário
    }
    
    salvarComoPacienteAtivo() {
        if (confirm('Deseja salvar este pré-cadastro como paciente ativo?')) {
            this.saveAllData();
            
            console.log('🔄 Enviando dados para paciente ativo:', this.formData);
            
            this.showNotification('Paciente salvo como ativo com sucesso!', 'success');
            
            localStorage.removeItem(config.storageKey);
            
            setTimeout(() => {
                window.location.href = 'painel_adm_pacientes.php';
            }, 2000);
        }
    }
    
    salvarComoPacientePendente() {
        if (confirm('Deseja salvar este pré-cadastro como paciente pendente?')) {
            this.saveAllData();
            
            console.log('⏳ Enviando dados para paciente pendente:', this.formData);
            
            this.showNotification('Paciente salvo como pendente com sucesso!', 'success');
            
            localStorage.removeItem(config.storageKey);
            
            setTimeout(() => {
                window.location.href = 'painel_pacientes_pendentes.php';
            }, 2000);
        }
    }
    
    showNotification(message, type = 'success') {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        let icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'info') icon = 'info-circle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    getTabDisplayName(tabId) {
        const tabNames = {
            'identificacao': 'Identificação',
            'queixa': 'Queixa',
            'antecedente': 'Antecedente',
            'desenvolvimento': 'Desenvolvimento',
            'observacao': 'Observação Clínica'
        };
        
        return tabNames[tabId] || tabId;
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.painelPreCadastro = new PainelPreCadastro();
        
        // Expor funções globais para os botões HTML
        window.salvarComoPacienteAtivo = () => window.painelPreCadastro.salvarComoPacienteAtivo();
        window.salvarComoPacientePendente = () => window.painelPreCadastro.salvarComoPacientePendente();
        
    } catch (error) {
        console.error('❌ Erro ao inicializar Painel de Pré-Cadastro:', error);
    }
});