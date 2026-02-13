// painel_planoterapeutico.js

document.addEventListener('DOMContentLoaded', function() {
    // ===== ELEMENTOS PRINCIPAIS =====
    const searchInput = document.getElementById('searchPatient');
    const tableRows = document.querySelectorAll('.patients-table tbody tr');
    
    // Modais
    const attachPEIModal = document.getElementById('attachPEIModal');
    const viewPEIModal = document.getElementById('viewPEIModal');
    const allModals = document.querySelectorAll('.modal');
    
    // Botões da tabela
    const attachButtons = document.querySelectorAll('.btn-attach-pei');
    const viewButtons = document.querySelectorAll('.btn-view-pei');
    
    // Elementos de formulário
    const fileInput = document.getElementById('peiFile');
    const selectedFileDiv = document.getElementById('selectedFile');
    const selectedFileName = document.getElementById('selectedFileName');
    const removeFileButton = document.getElementById('removeFile');
    const savePEIButton = document.getElementById('btnSavePEI');
    
    // Dados atuais
    let currentPatient = null;
    let currentFile = null;

    // ===== FUNCIONALIDADES PRINCIPAIS =====

    // Busca de pacientes
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            tableRows.forEach(row => {
                const patientName = row.querySelector('.patient-name strong')?.textContent.toLowerCase() || '';
                const responsible = row.querySelector('.responsible-text')?.textContent.toLowerCase() || '';
                const phone = row.querySelector('.contact-phone')?.textContent.toLowerCase() || '';
                
                if (patientName.includes(searchTerm) || responsible.includes(searchTerm) || phone.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ===== MODAL: ANEXAR PEI =====
    attachButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentPatient = {
                id: this.getAttribute('data-patient-id'),
                name: this.getAttribute('data-patient-name')
            };
            
            document.getElementById('modalPatientName').textContent = currentPatient.name;
            
            // Resetar upload
            if (fileInput) fileInput.value = '';
            if (selectedFileDiv) selectedFileDiv.style.display = 'none';
            
            showModal(attachPEIModal);
        });
    });

    // ===== MODAL: VISUALIZAR PEI =====
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const hasPEI = this.getAttribute('data-pei-anexado') === 'true';
            
            currentPatient = {
                id: this.getAttribute('data-patient-id'),
                name: this.getAttribute('data-patient-name')
            };
            
            currentFile = this.getAttribute('data-pei-arquivo');
            
            // Preencher informações
            document.getElementById('viewPatientName').textContent = currentPatient.name;
            
            // Determinar conteúdo do PEI
            const peiContent = document.getElementById('peiContent');
            
            if (hasPEI && currentFile) {
                const fileExtension = currentFile.split('.').pop().toLowerCase();
                let icon = 'fa-file-pdf';
                let type = 'PDF';
                
                if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                    icon = 'fa-file-image';
                    type = 'Imagem';
                } else if (['doc', 'docx'].includes(fileExtension)) {
                    icon = 'fa-file-word';
                    type = 'Documento';
                }
                
                peiContent.innerHTML = `
                    <div class="pep-file-preview">
                        <i class="fas ${icon}"></i>
                        <p class="pep-file-name">${currentFile}</p>
                        <p class="file-info">Arquivo ${type} - PEI anexado</p>
                        <div class="pep-actions">
                            <button class="pep-btn pep-btn-download" onclick="window.location.href='/download/pei/${encodeURIComponent(currentFile)}'">
                                <i class="fas fa-download"></i> Baixar
                            </button>
                            <button class="pep-btn pep-btn-print" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                `;
            } else {
                peiContent.innerHTML = `
                    <div class="no-pei-message">
                        <i class="fas fa-file-excel"></i>
                        <h4>Nenhum PEI anexado</h4>
                        <p>Este paciente ainda não possui PEI para o mês atual.</p>
                    </div>
                `;
            }
            
            showModal(viewPEIModal);
        });
    });

    // ===== MANIPULAÇÃO DE ARQUIVOS =====
    if (fileInput && selectedFileDiv && selectedFileName && removeFileButton) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                selectedFileName.textContent = file.name;
                selectedFileDiv.style.display = 'flex';
                
                if (!validateFile(file)) {
                    this.value = '';
                    selectedFileDiv.style.display = 'none';
                    alert('Arquivo inválido. Verifique o formato e tamanho.');
                }
            }
        });

        removeFileButton.addEventListener('click', function() {
            fileInput.value = '';
            selectedFileDiv.style.display = 'none';
        });
    }

    // ===== SALVAR PEI =====
    if (savePEIButton) {
        savePEIButton.addEventListener('click', function() {
            if (!fileInput || !fileInput.files.length) {
                alert('Por favor, selecione um arquivo primeiro.');
                return;
            }

            const file = fileInput.files[0];
            
            if (!validateFile(file)) {
                return;
            }

            // Simular upload
            console.log('Enviando PEI:', {
                paciente: currentPatient.name,
                arquivo: file.name
            });

            // Mostrar loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            setTimeout(() => {
                alert(`PEI enviado com sucesso para ${currentPatient.name}`);
                closeAllModals();
                
                // Resetar botão
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save"></i> Salvar PEI';
                
                // Recarregar página
                setTimeout(() => {
                    location.reload();
                }, 500);
            }, 1500);
        });
    }

    // ===== FUNÇÕES UTILITÁRIAS =====
    function showModal(modal) {
        if (!modal) return;
        closeAllModals();
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAllModals() {
        allModals.forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = 'auto';
    }

    // Fechar modais
    document.querySelectorAll('.modal-close, .cancel-btn, .close-btn').forEach(btn => {
        btn.addEventListener('click', closeAllModals);
    });

    // Fechar ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeAllModals();
        }
    });

    function validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];

        if (file.size > maxSize) {
            alert('Arquivo muito grande. Máximo: 10MB');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('Formato não permitido. Use PDF, DOC, DOCX, JPG ou PNG.');
            return false;
        }

        return true;
    }

    // ===== ATALHOS DE TECLADO =====
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
        
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            if (searchInput) searchInput.focus();
        }
    });

    console.log('Painel Plano Terapêutico inicializado!');
});