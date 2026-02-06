// js/form-handlers.js - Manipulação e validação de formulários

class FormHandler {
    constructor() {
        this.formData = {
            queixa: {},
            antecedentes: {},
            desenvolvimento: {},
            observacao: {}
        };
    }
    
    // Validação de campos obrigatórios
    validateRequiredFields(formId) {
        const form = document.getElementById(formId);
        if (!form) return true;
        
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.highlightError(field);
                isValid = false;
            } else {
                this.removeErrorHighlight(field);
            }
        });
        
        return isValid;
    }
    
    highlightError(field) {
        field.style.borderColor = '#ef4444';
        field.style.backgroundColor = '#fef2f2';
        
        // Adicionar mensagem de erro se não existir
        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
            const errorMsg = document.createElement('div');
            errorMsg.className = 'error-message';
            errorMsg.style.color = '#ef4444';
            errorMsg.style.fontSize = '0.875rem';
            errorMsg.style.marginTop = '4px';
            errorMsg.textContent = 'Este campo é obrigatório';
            field.parentNode.insertBefore(errorMsg, field.nextSibling);
        }
    }
    
    removeErrorHighlight(field) {
        field.style.borderColor = '';
        field.style.backgroundColor = '';
        
        // Remover mensagem de erro
        const errorMsg = field.nextElementSibling;
        if (errorMsg && errorMsg.classList.contains('error-message')) {
            errorMsg.remove();
        }
    }
    
    // Validação de email
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Validação de telefone (Brasil)
    validatePhone(phone) {
        const re = /^\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}$/;
        return re.test(phone);
    }
    
    // Validação de CPF
    validateCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }
        
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            sum += parseInt(cpf.charAt(i)) * (10 - i);
        }
        
        let remainder = 11 - (sum % 11);
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(9))) return false;
        
        sum = 0;
        for (let i = 0; i < 10; i++) {
            sum += parseInt(cpf.charAt(i)) * (11 - i);
        }
        
        remainder = 11 - (sum % 11);
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(10))) return false;
        
        return true;
    }
    
    // Formatação de dados
    formatFormData(formId) {
        const form = document.getElementById(formId);
        if (!form) return {};
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        return data;
    }
    
    // Limpar formulário
    clearForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.reset();
        
        // Limpar highlights de erro
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            this.removeErrorHighlight(field);
        });
        
        // Limpar checkboxes e radios específicos
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
        
        const radios = form.querySelectorAll('input[type="radio"]');
        radios.forEach(radio => radio.checked = false);
    }
    
    // Salvar formulário temporariamente
    saveFormData(formId, storageKey) {
        const data = this.formatFormData(formId);
        try {
            localStorage.setItem(storageKey, JSON.stringify(data));
            console.log(`📝 Formulário ${formId} salvo temporariamente`);
            return true;
        } catch (e) {
            console.error(`❌ Erro ao salvar formulário ${formId}:`, e);
            return false;
        }
    }
    
    // Carregar formulário salvo
    loadFormData(formId, storageKey) {
        try {
            const savedData = localStorage.getItem(storageKey);
            if (savedData) {
                const data = JSON.parse(savedData);
                this.populateForm(formId, data);
                console.log(`📂 Formulário ${formId} carregado`);
                return true;
            }
        } catch (e) {
            console.error(`❌ Erro ao carregar formulário ${formId}:`, e);
        }
        return false;
    }
    
    // Preencher formulário com dados
    populateForm(formId, data) {
        const form = document.getElementById(formId);
        if (!form || !data) return;
        
        Object.keys(data).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (!field) return;
            
            if (field.type === 'checkbox' || field.type === 'radio') {
                const values = Array.isArray(data[key]) ? data[key] : [data[key]];
                values.forEach(value => {
                    const option = form.querySelector(`[name="${key}"][value="${value}"]`);
                    if (option) option.checked = true;
                });
            } else {
                field.value = data[key];
            }
        });
    }
    
    // Verificar se formulário foi modificado
    isFormModified(formId, originalData) {
        const currentData = this.formatFormData(formId);
        return JSON.stringify(currentData) !== JSON.stringify(originalData);
    }
    
    // Contador de caracteres em tempo real
    setupCharacterCounter(textareaId, counterId, maxLength) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);
        
        if (!textarea || !counter) return;
        
        const updateCounter = () => {
            const length = textarea.value.length;
            counter.textContent = length;
            
            if (length > maxLength) {
                counter.style.color = '#ef4444';
                textarea.style.borderColor = '#ef4444';
            } else if (length > maxLength * 0.8) {
                counter.style.color = '#f59e0b';
                textarea.style.borderColor = '#f59e0b';
            } else {
                counter.style.color = '#3b82f6';
                textarea.style.borderColor = '';
            }
        };
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Inicializar
    }
}

// Exportar para uso global
window.FormHandler = FormHandler;