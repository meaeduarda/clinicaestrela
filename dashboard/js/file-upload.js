// js/file-upload.js - Gerenciador de upload de arquivos

class FileUploadManager {
    constructor() {
        this.attachments = [];
        this.maxFiles = 10;
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        this.init();
    }
    
    init() {
        this.setupAttachmentUpload();
        this.setupDragAndDrop();
    }
    
    setupAttachmentUpload() {
        const btnAddAttachment = document.getElementById('btn-add-attachment');
        const fileUpload = document.getElementById('file-upload');
        
        if (btnAddAttachment && fileUpload) {
            btnAddAttachment.addEventListener('click', () => {
                fileUpload.click();
            });
            
            fileUpload.addEventListener('change', (e) => {
                this.handleFileUpload(e.target.files);
            });
        }
    }
    
    setupDragAndDrop() {
        const attachmentsList = document.getElementById('attachments-list');
        
        if (!attachmentsList) return;
        
        attachmentsList.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.highlightDropZone(true);
        });
        
        attachmentsList.addEventListener('dragleave', (e) => {
            e.preventDefault();
            this.highlightDropZone(false);
        });
        
        attachmentsList.addEventListener('drop', (e) => {
            e.preventDefault();
            this.highlightDropZone(false);
            
            if (e.dataTransfer.files.length) {
                this.handleFileUpload(e.dataTransfer.files);
            }
        });
    }
    
    highlightDropZone(highlight) {
        const attachmentsList = document.getElementById('attachments-list');
        if (!attachmentsList) return;
        
        if (highlight) {
            attachmentsList.style.backgroundColor = '#f0f9ff';
            attachmentsList.style.borderColor = '#3b82f6';
            attachmentsList.style.borderStyle = 'dashed';
        } else {
            attachmentsList.style.backgroundColor = '';
            attachmentsList.style.borderColor = '';
            attachmentsList.style.borderStyle = '';
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
        
        // Verificar limite de arquivos
        const existingFiles = attachmentsList.querySelectorAll('.attachment-item').length;
        const remainingSlots = this.maxFiles - existingFiles;
        
        if (files.length > remainingSlots) {
            this.showError(`Você só pode adicionar mais ${remainingSlots} arquivo(s). Limite máximo: ${this.maxFiles} arquivos.`);
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
        if (!this.allowedTypes.includes(file.type)) {
            this.showError(`Arquivo "${file.name}" não suportado. Use PDF, JPG ou PNG.`);
            return;
        }
        
        // Validar tamanho
        if (file.size > this.maxFileSize) {
            this.showError(`Arquivo "${file.name}" muito grande. Tamanho máximo: ${this.formatFileSize(this.maxFileSize)}.`);
            return;
        }
        
        // Adicionar à lista de anexos
        this.attachments.push({
            file: file,
            id: Date.now() + Math.random(),
            uploaded: false
        });
        
        // Criar elemento visual
        const attachmentElement = this.createAttachmentElement(file);
        attachmentsList.appendChild(attachmentElement);
        
        console.log(`📎 Arquivo adicionado: ${file.name} (${this.formatFileSize(file.size)})`);
    }
    
    createAttachmentElement(file) {
        const div = document.createElement('div');
        div.className = 'attachment-item';
        div.dataset.fileName = file.name;
        
        // Formatar tamanho do arquivo
        const fileSize = this.formatFileSize(file.size);
        
        // Determinar ícone baseado no tipo
        let iconClass = 'fa-file';
        if (file.type === 'application/pdf') {
            iconClass = 'fa-file-pdf';
            div.classList.add('attachment-pdf');
        } else if (file.type.startsWith('image/')) {
            iconClass = 'fa-file-image';
            div.classList.add('attachment-image');
        }
        
        div.innerHTML = `
            <div class="attachment-info">
                <i class="fas ${iconClass} attachment-icon"></i>
                <div class="attachment-details">
                    <span class="attachment-name" title="${file.name}">${this.truncateFileName(file.name)}</span>
                    <span class="attachment-size">${fileSize}</span>
                </div>
            </div>
            <div class="attachment-actions">
                <button type="button" class="btn-preview-attachment" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn-remove-attachment" title="Remover">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Adicionar eventos
        const removeBtn = div.querySelector('.btn-remove-attachment');
        removeBtn.addEventListener('click', () => {
            this.removeAttachment(file.name, div);
        });
        
        const previewBtn = div.querySelector('.btn-preview-attachment');
        previewBtn.addEventListener('click', () => {
            this.previewFile(file);
        });
        
        return div;
    }
    
    removeAttachment(fileName, element) {
        // Remover da lista de anexos
        const index = this.attachments.findIndex(att => att.file.name === fileName);
        if (index > -1) {
            this.attachments.splice(index, 1);
        }
        
        // Remover elemento visual
        element.remove();
        
        // Mostrar mensagem "nenhum anexo" se lista estiver vazia
        const attachmentsList = document.getElementById('attachments-list');
        if (attachmentsList && attachmentsList.querySelectorAll('.attachment-item').length === 0) {
            const noAttachments = attachmentsList.querySelector('.no-attachments');
            if (noAttachments) {
                noAttachments.style.display = 'block';
            }
        }
        
        console.log(`🗑️ Arquivo removido: ${fileName}`);
    }
    
    previewFile(file) {
        if (file.type === 'application/pdf') {
            // Para PDF, abrir em nova janela ou embed
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, '_blank');
        } else if (file.type.startsWith('image/')) {
            // Para imagens, mostrar em modal
            this.showImagePreview(file);
        } else {
            // Para outros tipos, tentar download
            this.downloadFile(file);
        }
    }
    
    showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const modal = document.createElement('div');
            modal.className = 'image-preview-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                cursor: pointer;
            `;
            
            modal.innerHTML = `
                <div class="image-container" style="max-width: 90%; max-height: 90%;">
                    <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 100%;">
                    <div style="color: white; text-align: center; margin-top: 10px;">
                        ${file.name} (${this.formatFileSize(file.size)})
                    </div>
                </div>
            `;
            
            modal.addEventListener('click', () => {
                modal.remove();
            });
            
            document.body.appendChild(modal);
        };
        reader.readAsDataURL(file);
    }
    
    downloadFile(file) {
        const url = URL.createObjectURL(file);
        const a = document.createElement('a');
        a.href = url;
        a.download = file.name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    
    truncateFileName(fileName, maxLength = 30) {
        if (fileName.length <= maxLength) return fileName;
        const extension = fileName.split('.').pop();
        const nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));
        const truncated = nameWithoutExt.substring(0, maxLength - extension.length - 3);
        return `${truncated}...${extension}`;
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    showError(message) {
        if (window.painelPreCadastro && window.painelPreCadastro.showNotification) {
            window.painelPreCadastro.showNotification(message, 'error');
        } else {
            alert(message);
        }
    }
    
    // Métodos para obter dados dos anexos
    getAttachments() {
        return this.attachments.map(att => ({
            name: att.file.name,
            size: att.file.size,
            type: att.file.type,
            lastModified: att.file.lastModified
        }));
    }
    
    getAttachmentsFormData() {
        const formData = new FormData();
        this.attachments.forEach((att, index) => {
            formData.append(`anexo_${index}`, att.file);
        });
        return formData;
    }
    
    clearAttachments() {
        this.attachments = [];
        const attachmentsList = document.getElementById('attachments-list');
        if (attachmentsList) {
            attachmentsList.innerHTML = `
                <div class="no-attachments">
                    <i class="fas fa-paperclip"></i>
                    <p>Nenhum anexo adicionado</p>
                </div>
            `;
        }
    }
}

// Exportar para uso global
window.FileUploadManager = FileUploadManager;