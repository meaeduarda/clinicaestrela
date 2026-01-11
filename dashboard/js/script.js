document.addEventListener('DOMContentLoaded', function() {
    // --- 1. LÓGICA DE DATA (Mínimo hoje) ---
    const dataInput = document.getElementById('data');
    if (dataInput) {
        const today = new Date().toISOString().split('T')[0];
        if (!dataInput.value) dataInput.value = today;
        dataInput.min = today;
    }

    // --- 2. MÁSCARAS (Telefone e CPF) ---
    const telInput = document.getElementById('telefone');
    if (telInput) {
        telInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0,11);
            if (v.length <= 10) {
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2').replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = v;
        });
    }

    const cpfInput = document.getElementById('cpf_responsavel');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0,11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v;
        });
    }

    // --- 3. BOTÃO LIMPAR ---
    const btnLimpar = document.getElementById('btn-limpar');
    if (btnLimpar) {
        btnLimpar.addEventListener('click', function() {
            const form = document.getElementById('form-agendamento');
            const inputs = form.querySelectorAll('input[type="text"], input[type="tel"], input[type="date"]');
            inputs.forEach(input => {
                if(input.type === 'date') {
                    input.value = new Date().toISOString().split('T')[0];
                } else {
                    input.value = '';
                }
            });
            const select = form.querySelector('select');
            if(select) select.selectedIndex = 0;
            const erroMsg = document.querySelector('.mensagem');
            if(erroMsg) erroMsg.style.display = 'none';
        });
    }
});

// --- 4. FUNÇÕES GLOBAIS ---

function fecharModal() {
    const modal = document.getElementById('modalConfirmacao');
    if (modal) modal.style.display = 'none';
}

function confirmarClick(elemento, protocolo) {
    if (elemento.classList.contains('btn-confirmado-disabled')) return;

    // Envia requisição silenciosa para a mesma página (visita_agendamento.php)
    fetch('visita_agendamento.php?ajax_confirmar=' + protocolo)
        .then(() => {
            elemento.classList.add('btn-confirmado-disabled');
            elemento.innerHTML = '<i class="fa-solid fa-check-double"></i> Enviado';
            elemento.style.pointerEvents = 'none';
        })
        .catch(err => console.error('Erro ao processar confirmação:', err));
}