// js/modal-handler.js - Gerenciador de modais e notificações

class ModalManager {
    constructor() {
        this.modals = {};
        this.init();
    }
    
    init() {
        this.setupPhotoModal();
        this.setupGlobalEvents();
    }
    
    setupPhotoModal() {
        const modal = document.getElementById('photoModal');
        if (!modal) return;
        
        this.modals.photo = modal;
        
        const photoUploadOverlay = document.querySelector('.photo-upload-overlay');
        const modalClose = modal.querySelector('.modal-close');
        const cancelBtn = modal.querySelector('.btn-cancel');
        const uploadBtn = modal.querySelector('.btn-upload');
        const fileInput = modal.querySelector('#foto_paciente');
        const photoPreview = modal.querySelector('#photoPreview');
        
        // Abrir modal
        if (photoUploadOverlay) {
            photoUploadOverlay.addEventListener('click', () => {
                this.openModal('photo');
            });
        }
        
        // Fechar modal
        const closeModal = () => this.closeModal('photo');
        
        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        
        // Preview da foto selecionada
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) this.handlePhotoSelection(file, photoPreview);
            });
        }
        
        // Upload da foto
        if (uploadBtn) {
            uploadBtn.addEventListener('click', () => {
                this.handlePhotoUpload(fileInput, uploadBtn, photoPreview);
            });
        }
        
        // Fechar modal ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal('photo');
            }
        });
    }
    
    setupGlobalEvents() {
        // Fechar modais com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }
    
    openModal(modalName) {
        const modal = this.modals[modalName];
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevenir scroll
        }
    }
    
    closeModal(modalName) {
        const modal = this.modals[modalName];
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Restaurar scroll
            
            // Limpar inputs do modal
            const fileInput = modal.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
        }
    }
    
    closeAllModals() {
        Object.keys(this.modals).forEach(modalName => {
            this.closeModal(modalName);
        });
    }
    
    handlePhotoSelection(file, photoPreview) {
        if (!file || !photoPreview) return;
        
        // Validar tamanho (2MB)
        if (file.size > config.maxTamanhoFoto) {
            this.showAlert('A foto deve ter no máximo 2MB.');
            return;
        }
        
        // Validar tipo
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            this.showAlert('Formato de imagem não suportado. Use JPG, PNG, GIF ou WebP.');
            return;
        }
        
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    handlePhotoUpload(fileInput, uploadBtn, photoPreview) {
        if (!fileInput || fileInput.files.length === 0) {
            this.showAlert('Por favor, selecione uma foto.');
            return;
        }
        
        const file = fileInput.files[0];
        
        // Simular upload
        const originalText = uploadBtn.innerHTML;
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = (e) => {
                // Atualizar foto do paciente
                const patientPhoto = document.querySelector('.patient-photo img');
                if (patientPhoto) {
                    patientPhoto.src = e.target.result;
                }
                
                // Atualizar preview no modal
                if (photoPreview) {
                    photoPreview.src = e.target.result;
                }
                
                // Mostrar notificação
                if (window.painelPreCadastro) {
                    window.painelPreCadastro.showNotification('Foto atualizada com sucesso!', 'success');
                }
                
                // Restaurar botão
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = originalText;
                
                // Fechar modal
                this.closeModal('photo');
            };
            reader.readAsDataURL(file);
        }, 1500);
    }
    
    // Sistema de notificações
    showNotification(message, type = 'info', duration = 3000) {
        // Remover notificações existentes
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        // Criar notificação
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        // Ícone baseado no tipo
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Animação de entrada
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Botão de fechar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Auto-remover após duração
        if (duration > 0) {
            setTimeout(() => {
                this.hideNotification(notification);
            }, duration);
        }
    }
    
    hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
    
    // Alertas personalizados
    showAlert(message, title = 'Atenção', type = 'info') {
        const modal = document.createElement('div');
        modal.className = 'custom-alert-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        
        let icon = 'info-circle';
        let iconColor = '#3b82f6';
        
        if (type === 'success') {
            icon = 'check-circle';
            iconColor = '#10b981';
        } else if (type === 'error') {
            icon = 'exclamation-circle';
            iconColor = '#ef4444';
        } else if (type === 'warning') {
            icon = 'exclamation-triangle';
            iconColor = '#f59e0b';
        }
        
        modal.innerHTML = `
            <div class="alert-content" style="
                background: white;
                padding: 24px;
                border-radius: 12px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            ">
                <div class="alert-header" style="
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 16px;
                ">
                    <i class="fas fa-${icon}" style="
                        color: ${iconColor};
                        font-size: 24px;
                    "></i>
                    <h3 style="margin: 0; color: #1f2937;">${title}</h3>
                </div>
                <div class="alert-body" style="
                    margin-bottom: 24px;
                    color: #4b5563;
                    line-height: 1.5;
                ">
                    ${message}
                </div>
                <div class="alert-footer" style="
                    display: flex;
                    justify-content: flex-end;
                    gap: 12px;
                ">
                    <button class="btn-alert-ok" style="
                        padding: 10px 20px;
                        background: ${iconColor};
                        color: white;
                        border: none;
                        border-radius: 6px;
                        cursor: pointer;
                        font-weight: 500;
                    ">OK</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Fechar ao clicar no botão
        const okBtn = modal.querySelector('.btn-alert-ok');
        okBtn.addEventListener('click', () => {
            modal.remove();
        });
        
        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    // Confirm dialog
    showConfirm(message, title = 'Confirmação') {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'confirm-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            `;
            
            modal.innerHTML = `
                <div class="confirm-content" style="
                    background: white;
                    padding: 24px;
                    border-radius: 12px;
                    max-width: 400px;
                    width: 90%;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                ">
                    <div class="confirm-header" style="
                        margin-bottom: 16px;
                    ">
                        <h3 style="margin: 0; color: #1f2937;">${title}</h3>
                    </div>
                    <div class="confirm-body" style="
                        margin-bottom: 24px;
                        color: #4b5563;
                        line-height: 1.5;
                    ">
                        ${message}
                    </div>
                    <div class="confirm-footer" style="
                        display: flex;
                        justify-content: flex-end;
                        gap: 12px;
                    ">
                        <button class="btn-confirm-cancel" style="
                            padding: 10px 20px;
                            background: #6b7280;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: 500;
                        ">Cancelar</button>
                        <button class="btn-confirm-ok" style="
                            padding: 10px 20px;
                            background: #3b82f6;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: 500;
                        ">Confirmar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Eventos dos botões
            const cancelBtn = modal.querySelector('.btn-confirm-cancel');
            const okBtn = modal.querySelector('.btn-confirm-ok');
            
            cancelBtn.addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });
            
            okBtn.addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });
            
            // Fechar ao clicar fora
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });
            
            // Fechar com ESC
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', handleEsc);
                    modal.remove();
                    resolve(false);
                }
            };
            
            document.addEventListener('keydown', handleEsc);
        });
    }
}

// Exportar para uso global
window.ModalManager = ModalManager;

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.modalManager = new ModalManager();
});