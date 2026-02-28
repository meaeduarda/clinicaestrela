// evolucao_upload.js
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fotos-upload');
    const anexosLista = document.getElementById('anexos-lista');
    
    if (fileInput && anexosLista) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach((file, index) => {
                // Verificar tamanho (máximo 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`Arquivo "${file.name}" excede 10MB. Não será anexado.`);
                    return;
                }
                
                // Verificar tipo
                const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert(`Arquivo "${file.name}" não é um tipo permitido. Apenas imagens e PDF.`);
                    return;
                }
                
                // Criar elemento do anexo
                const anexoItem = document.createElement('div');
                anexoItem.className = 'anexo-item';
                anexoItem.dataset.index = index;
                
                // Ícone baseado no tipo
                let icon = 'fa-file-image';
                if (file.type === 'application/pdf') {
                    icon = 'fa-file-pdf';
                } else if (file.type.includes('image')) {
                    icon = 'fa-file-image';
                }
                
                // Tamanho formatado
                const tamanho = (file.size / 1024).toFixed(1) + ' KB';
                
                anexoItem.innerHTML = `
                    <div class="anexo-icon">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="anexo-info">
                        <div class="anexo-nome">${file.name}</div>
                        <div class="anexo-tamanho">${tamanho}</div>
                    </div>
                    <div class="anexo-remove" onclick="removerAnexo(this)">
                        <i class="fas fa-times-circle"></i>
                    </div>
                `;
                
                anexosLista.appendChild(anexoItem);
            });
            
            // Limpar o input para permitir selecionar os mesmos arquivos novamente
            fileInput.value = '';
        });
    }
});

// Função para remover anexo da lista visual
function removerAnexo(element) {
    const anexoItem = element.closest('.anexo-item');
    if (anexoItem) {
        anexoItem.remove();
    }
}

// Função para visualizar evolução (placeholder - pode ser expandida)
function visualizarEvolucao(id) {
    // Aqui você pode implementar um modal ou redirecionar para uma página de visualização
    window.location.href = 'visualizar_evolucao.php?id=' + id;
}

// Função para editar evolução
function editarEvolucao(id) {
    window.location.href = 'editar_evolucao.php?id=' + id;
}

// Função para gerar PDF
function gerarPDF(id) {
    window.location.href = 'gerar_pdf_evolucao.php?id=' + id;
}