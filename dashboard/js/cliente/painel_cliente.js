document.addEventListener('DOMContentLoaded', function() {
    // Menu Mobile
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
    }

    // Função de notificação
    function mostrarNotificacao(mensagem, tipo = 'success') {
        const notification = document.getElementById('notification');
        const messageSpan = document.getElementById('notificationMessage');
        const icon = notification.querySelector('i');
        
        notification.className = 'notification show';
        if (tipo === 'success') {
            notification.classList.add('success');
            icon.className = 'fas fa-check-circle';
        } else {
            notification.classList.add('error');
            icon.className = 'fas fa-exclamation-circle';
        }
        
        messageSpan.textContent = mensagem;
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // ==================== UPLOAD DE FOTO ====================
    const btnEditPhoto = document.getElementById('btnEditPhoto');
    const modalFoto = document.getElementById('modalFoto');
    const closeModalFoto = document.getElementById('closeModalFoto');
    const cancelFoto = document.getElementById('cancelFoto');
    const uploadAreaFoto = document.getElementById('uploadAreaFoto');
    const fileInputFoto = document.getElementById('fileInputFoto');
    const previewContainerFoto = document.getElementById('previewContainerFoto');
    const previewImageFoto = document.getElementById('previewImageFoto');
    const saveFotoBtn = document.getElementById('saveFoto');
    const patientPhoto = document.getElementById('patientPhoto');

    let selectedFotoFile = null;

    if (btnEditPhoto) {
        btnEditPhoto.addEventListener('click', function() {
            if (modalFoto) {
                modalFoto.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    }

    function closeFotoModal() {
        if (modalFoto) {
            modalFoto.classList.remove('active');
            document.body.style.overflow = '';
            resetFotoUpload();
        }
    }

    if (closeModalFoto) {
        closeModalFoto.addEventListener('click', closeFotoModal);
    }
    
    if (cancelFoto) {
        cancelFoto.addEventListener('click', closeFotoModal);
    }

    function resetFotoUpload() {
        if (uploadAreaFoto) uploadAreaFoto.style.display = 'block';
        if (previewContainerFoto) previewContainerFoto.style.display = 'none';
        if (previewImageFoto) previewImageFoto.src = '';
        if (saveFotoBtn) saveFotoBtn.disabled = true;
        if (fileInputFoto) fileInputFoto.value = '';
        selectedFotoFile = null;
    }

    if (uploadAreaFoto) {
        uploadAreaFoto.addEventListener('click', function() {
            if (fileInputFoto) fileInputFoto.click();
        });

        uploadAreaFoto.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#3b82f6';
            this.style.backgroundColor = '#eef2ff';
        });

        uploadAreaFoto.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = '#f8fafc';
        });

        uploadAreaFoto.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = '#f8fafc';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFotoFile(files[0]);
            }
        });
    }

    if (fileInputFoto) {
        fileInputFoto.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFotoFile(this.files[0]);
            }
        });
    }

    function handleFotoFile(file) {
        if (!file.type.match('image.*')) {
            mostrarNotificacao('Por favor, selecione uma imagem válida.', 'error');
            return;
        }
        
        if (file.size > 3 * 1024 * 1024) {
            mostrarNotificacao('A imagem deve ter no máximo 3MB.', 'error');
            return;
        }

        selectedFotoFile = file;

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImageFoto) previewImageFoto.src = e.target.result;
            if (uploadAreaFoto) uploadAreaFoto.style.display = 'none';
            if (previewContainerFoto) previewContainerFoto.style.display = 'block';
            if (saveFotoBtn) saveFotoBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    if (saveFotoBtn) {
        saveFotoBtn.addEventListener('click', function() {
            if (!selectedFotoFile) return;

            saveFotoBtn.disabled = true;
            saveFotoBtn.textContent = 'Enviando...';

            const formData = new FormData();
            formData.append('foto', selectedFotoFile);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (patientPhoto) patientPhoto.src = data.foto + '?t=' + new Date().getTime();
                    mostrarNotificacao(data.message, 'success');
                    closeFotoModal();
                } else {
                    mostrarNotificacao(data.message, 'error');
                    saveFotoBtn.disabled = false;
                    saveFotoBtn.textContent = 'Salvar foto';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao enviar a imagem. Tente novamente.', 'error');
                saveFotoBtn.disabled = false;
                saveFotoBtn.textContent = 'Salvar foto';
            });
        });
    }

    // ==================== UPLOAD DE BACKGROUND ====================
    const btnChangeBg = document.getElementById('btnChangeBg');
    const modalBg = document.getElementById('modalBackground');
    const closeModalBg = document.getElementById('closeModalBg');
    const cancelBg = document.getElementById('cancelBg');
    const uploadAreaBg = document.getElementById('uploadAreaBg');
    const fileInputBg = document.getElementById('fileInputBg');
    const previewContainerBg = document.getElementById('previewContainerBg');
    const previewImageBg = document.getElementById('previewImageBg');
    const saveBgBtn = document.getElementById('saveBg');
    const bgOptions = document.querySelectorAll('.bg-option');
    const contentArea = document.getElementById('contentArea');

    let selectedBgFile = null;
    let selectedBgType = 'default';
    let selectedGradient = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

    if (btnChangeBg) {
        btnChangeBg.addEventListener('click', function() {
            if (modalBg) {
                modalBg.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    }

    function closeBgModal() {
        if (modalBg) {
            modalBg.classList.remove('active');
            document.body.style.overflow = '';
            resetBgUpload();
        }
    }

    if (closeModalBg) {
        closeModalBg.addEventListener('click', closeBgModal);
    }
    
    if (cancelBg) {
        cancelBg.addEventListener('click', closeBgModal);
    }

    function resetBgUpload() {
        if (uploadAreaBg) uploadAreaBg.style.display = 'block';
        if (previewContainerBg) previewContainerBg.style.display = 'none';
        if (previewImageBg) previewImageBg.src = '';
        if (saveBgBtn) saveBgBtn.disabled = true;
        if (fileInputBg) fileInputBg.value = '';
        selectedBgFile = null;
        
        if (bgOptions) {
            bgOptions.forEach(opt => opt.classList.remove('selected'));
        }
    }

    if (bgOptions) {
        bgOptions.forEach(option => {
            option.addEventListener('click', function() {
                bgOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                
                selectedBgType = this.getAttribute('data-bg');
                selectedGradient = this.getAttribute('data-gradient');
                
                if (contentArea) {
                    contentArea.style.background = selectedGradient;
                    contentArea.style.backgroundSize = 'cover';
                    contentArea.style.backgroundPosition = 'center';
                }
                
                localStorage.setItem('clienteBgType', selectedBgType);
                localStorage.setItem('clienteBgGradient', selectedGradient);
                
                selectedBgFile = null;
                if (saveBgBtn) saveBgBtn.disabled = false;
            });
        });
    }

    if (uploadAreaBg) {
        uploadAreaBg.addEventListener('click', function() {
            if (fileInputBg) fileInputBg.click();
        });

        uploadAreaBg.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#3b82f6';
            this.style.backgroundColor = '#eef2ff';
        });

        uploadAreaBg.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = '#f8fafc';
        });

        uploadAreaBg.addEventListener('drop', function(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleBgFile(files[0]);
            }
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = '#f8fafc';
        });
    }

    if (fileInputBg) {
        fileInputBg.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleBgFile(this.files[0]);
            }
        });
    }

    function handleBgFile(file) {
        if (!file.type.match('image.*')) {
            mostrarNotificacao('Por favor, selecione uma imagem válida.', 'error');
            return;
        }
        
        if (file.size > 3 * 1024 * 1024) {
            mostrarNotificacao('A imagem deve ter no máximo 3MB.', 'error');
            return;
        }

        selectedBgFile = file;
        selectedBgType = 'custom';

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImageBg) previewImageBg.src = e.target.result;
            if (uploadAreaBg) uploadAreaBg.style.display = 'none';
            if (previewContainerBg) previewContainerBg.style.display = 'block';
            if (saveBgBtn) saveBgBtn.disabled = false;
            
            if (bgOptions) {
                bgOptions.forEach(opt => opt.classList.remove('selected'));
            }
        };
        reader.readAsDataURL(file);
    }

    if (saveBgBtn) {
        saveBgBtn.addEventListener('click', function() {
            if (selectedBgType === 'custom' && selectedBgFile) {
                saveBgBtn.disabled = true;
                saveBgBtn.textContent = 'Enviando...';

                const formData = new FormData();
                formData.append('background', selectedBgFile);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (contentArea) {
                            contentArea.style.backgroundImage = `url('${data.background}?t=${new Date().getTime()}')`;
                            contentArea.style.backgroundSize = 'cover';
                            contentArea.style.backgroundPosition = 'center';
                        }
                        
                        localStorage.removeItem('clienteBgGradient');
                        
                        mostrarNotificacao(data.message, 'success');
                        closeBgModal();
                    } else {
                        mostrarNotificacao(data.message, 'error');
                        saveBgBtn.disabled = false;
                        saveBgBtn.textContent = 'Salvar fundo';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('Erro ao enviar a imagem. Tente novamente.', 'error');
                    saveBgBtn.disabled = false;
                    saveBgBtn.textContent = 'Salvar fundo';
                });
            } else {
                closeBgModal();
            }
        });
    }

    // Carregar background salvo
    const savedBgGradient = localStorage.getItem('clienteBgGradient');
    if (savedBgGradient && contentArea) {
        contentArea.style.background = savedBgGradient;
        contentArea.style.backgroundSize = 'cover';
        contentArea.style.backgroundPosition = 'center';
    }

    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === modalFoto) {
            closeFotoModal();
        }
        if (e.target === modalBg) {
            closeBgModal();
        }
    });
});