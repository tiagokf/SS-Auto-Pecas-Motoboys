// Função para formatar data e hora para o formato local do input datetime-local
// function formatarDataHoraParaInput(dataHora) {
//     if (!dataHora) return '';
    
//     // Criar um objeto Date com a data/hora recebida
//     const data = new Date(dataHora);
    
//     // Obter os componentes da data no fuso horário local
//     const ano = data.getFullYear();
//     const mes = String(data.getMonth() + 1).padStart(2, '0');
//     const dia = String(data.getDate()).padStart(2, '0');
//     const hora = String(data.getHours()).padStart(2, '0');
//     const minuto = String(data.getMinutes()).padStart(2, '0');
    
//     // Formatar no padrão YYYY-MM-DDTHH:MM esperado pelo input datetime-local
//     return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
// }

// Função para obter a data e hora atual no formato correto para input datetime-local
function getDataHoraAtual() {
    const agora = new Date();
    
    // Formatar a data no fuso horário local (não em UTC)
    const ano = agora.getFullYear();
    const mes = String(agora.getMonth() + 1).padStart(2, '0');
    const dia = String(agora.getDate()).padStart(2, '0');
    const hora = String(agora.getHours()).padStart(2, '0');
    const minuto = String(agora.getMinutes()).padStart(2, '0');
    
    // Formato: YYYY-MM-DDTHH:MM
    return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
}

/**
 * Função para registrar retorno de um movimento
 * @param {number} id - ID do movimento
 */
function registrarRetorno(id) {
    document.getElementById('return-id').value = id;
    showModal('modal-retorno');
}

/**
 * Função para confirmar exclusão de um movimento
 * @param {number} id - ID do movimento a ser excluído
 */
function confirmarExclusao(id) {
    document.getElementById('delete-id').value = id;
    showModal('modal-excluir');
}

/**
 * Função para limpar filtros
 */
function limparFiltros() {
    // Redirecionar para a página sem os parâmetros de filtro
    window.location.href = 'index.php';
}

/**
 * Função para editar um movimento, buscando dados via API
 * @param {number} id - ID do movimento
 */
function editarMovimento(id) {
    // Fazer uma requisição para obter os dados do movimento
    fetch('../api/movimentos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get&id=${id}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao obter dados do movimento');
        }
        return response.json();
    })
    .then(movimento => {
        // Preencher o formulário com os dados do movimento
        document.getElementById('edit-movimento-id').value = movimento.id;
        document.getElementById('edit-motoboy-id').value = movimento.motoboy_id;
        
        // Se o campo descricao for um input, definir value, se for textarea, definir textContent
        const descricaoElement = document.getElementById('edit-descricao');
        if (descricaoElement.tagName.toLowerCase() === 'textarea') {
            descricaoElement.textContent = movimento.descricao;
        } else {
            descricaoElement.value = movimento.descricao;
        }
        
        document.getElementById('edit-usuario-saida-id').value = movimento.usuario_saida_id;
        
        // Converter data/hora para o formato local
        const dataSaida = formatarDataHoraParaInput(movimento.data_hora_saida);
        document.getElementById('edit-data-hora-saida').value = dataSaida;
        
        // Preencher dados de retorno se existirem
        if (movimento.data_hora_retorno) {
            const dataRetorno = formatarDataHoraParaInput(movimento.data_hora_retorno);
            document.getElementById('edit-data-hora-retorno').value = dataRetorno;
            document.getElementById('edit-usuario-retorno-id').value = movimento.usuario_retorno_id;
            document.getElementById('edit-sem-retorno').checked = false;
            
            // Habilitar campos de retorno
            document.getElementById('edit-data-hora-retorno').disabled = false;
            document.getElementById('edit-usuario-retorno-id').disabled = false;
        } else {
            document.getElementById('edit-data-hora-retorno').value = '';
            document.getElementById('edit-usuario-retorno-id').value = '';
            document.getElementById('edit-sem-retorno').checked = true;
            
            // Desabilitar campos de retorno
            document.getElementById('edit-data-hora-retorno').disabled = true;
            document.getElementById('edit-usuario-retorno-id').disabled = true;
        }
        
        // Mostrar o modal
        showModal('modal-editar-movimento');
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao obter dados do movimento');
    });
}

/**
 * Funções para seleção em lote
 */
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const allCheckboxes = document.querySelectorAll('.movimento-checkbox');
    const motoboyCheckboxes = document.querySelectorAll('.select-all-motoboy');
    
    allCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    motoboyCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSubmitButton();
}

function toggleMotoboy(motoboyId) {
    const motoboyCheckbox = document.getElementById(`select-all-motoboy-${motoboyId}`);
    const movimentoCheckboxes = document.querySelectorAll(`.motoboy-${motoboyId}-checkbox`);
    
    movimentoCheckboxes.forEach(checkbox => {
        checkbox.checked = motoboyCheckbox.checked;
    });
    
    updateSubmitButton();
    updateSelectAllCheckbox();
}

function updateMotoboysCheckbox(motoboyId) {
    const motoboyCheckbox = document.getElementById(`select-all-motoboy-${motoboyId}`);
    const movimentoCheckboxes = document.querySelectorAll(`.motoboy-${motoboyId}-checkbox`);
    
    let allChecked = true;
    let allUnchecked = true;
    
    movimentoCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            allUnchecked = false;
        } else {
            allChecked = false;
        }
    });
    
    motoboyCheckbox.checked = allChecked;
    motoboyCheckbox.indeterminate = !allChecked && !allUnchecked;
    
    updateSubmitButton();
    updateSelectAllCheckbox();
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select-all');
    const allCheckboxes = document.querySelectorAll('.movimento-checkbox');
    
    let allChecked = true;
    let allUnchecked = true;
    
    allCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            allUnchecked = false;
        } else {
            allChecked = false;
        }
    });
    
    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = !allChecked && !allUnchecked;
}

function updateSubmitButton() {
    const submitButton = document.getElementById('btn-registrar-retorno-lote');
    const anyChecked = Array.from(document.querySelectorAll('.movimento-checkbox')).some(checkbox => checkbox.checked);
    
    submitButton.disabled = !anyChecked;
}

// Inicializa funções quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar estado dos botões na carga da página
    const checkboxes = document.querySelectorAll('.movimento-checkbox');
    if (checkboxes.length > 0) {
        updateSubmitButton();
    }
    
    // Adicionar evento para o checkbox de sem retorno
    const checkboxSemRetorno = document.getElementById('edit-sem-retorno');
    if (checkboxSemRetorno) {
        checkboxSemRetorno.addEventListener('change', function() {
            const dataHoraRetorno = document.getElementById('edit-data-hora-retorno');
            const usuarioRetornoId = document.getElementById('edit-usuario-retorno-id');
            
            if (this.checked) {
                // Se marcado, desabilitar e limpar campos de retorno
                dataHoraRetorno.disabled = true;
                dataHoraRetorno.value = '';
                usuarioRetornoId.disabled = true;
                usuarioRetornoId.value = '';
            } else {
                // Se desmarcado, habilitar campos de retorno
                dataHoraRetorno.disabled = false;
                usuarioRetornoId.disabled = false;
                
                // Definir data/hora atual para o campo de retorno
                dataHoraRetorno.value = getDataHoraAtual();
                
                // Definir o usuário atual como padrão se estiver vazio
                if (!usuarioRetornoId.value) {
                    const usuarioLogadoSelect = document.getElementById('edit-usuario-saida-id');
                    const usuarioAtualId = usuarioLogadoSelect.options[usuarioLogadoSelect.selectedIndex].value;
                    usuarioRetornoId.value = usuarioAtualId;
                }
            }
        });
    }
    
    // Adicionar validação ao formulário de edição
    const formEditarMovimento = document.getElementById('form-editar-movimento');
    if (formEditarMovimento) {
        formEditarMovimento.addEventListener('submit', function(event) {
            const checkboxSemRetorno = document.getElementById('edit-sem-retorno');
            const dataHoraRetorno = document.getElementById('edit-data-hora-retorno');
            const usuarioRetornoId = document.getElementById('edit-usuario-retorno-id');
            
            if (!checkboxSemRetorno.checked) {
                // Se o movimento tem retorno, validar os campos de retorno
                if (!dataHoraRetorno.value) {
                    event.preventDefault();
                    alert('Por favor, preencha a data/hora de retorno');
                    return;
                }
                
                if (!usuarioRetornoId.value) {
                    event.preventDefault();
                    alert('Por favor, selecione o usuário de retorno');
                    return;
                }
                
                // Verificar se a data de retorno é posterior à data de saída
                const dataSaida = new Date(document.getElementById('edit-data-hora-saida').value);
                const dataRetorno = new Date(dataHoraRetorno.value);
                
                if (dataRetorno < dataSaida) {
                    event.preventDefault();
                    alert('A data/hora de retorno deve ser posterior à data/hora de saída');
                    return;
                }
            }
        });
    }
});