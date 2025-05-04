// assets/js/motoboys.js - Funções específicas para o módulo de motoboys
/**
 * Função para editar um motoboy
 * @param {number} id - ID do motoboy
 * @param {string} nome - Nome do motoboy
 */
function editarMotoboy(id, nome) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nome').value = nome;
    showModal('modal-editar');
}

/**
 * Função para confirmar exclusão de um motoboy
 * @param {number} id - ID do motoboy a ser excluído
 */
function confirmarExclusao(id) {
    document.getElementById('delete-id').value = id;
    
    // Atualizar a mensagem de confirmação para ser mais clara sobre o que acontecerá
    const mensagemConfirmacao = document.querySelector('#modal-excluir p.mb-4');
    if (mensagemConfirmacao) {
        mensagemConfirmacao.innerHTML = 'Tem certeza que deseja excluir este motoboy? <strong>Todos os registros relacionados a este motoboy serão transferidos para o motoboy "Sistema"</strong>. Esta ação não pode ser desfeita.';
    }
    
    showModal('modal-excluir');
}

// Inicializa funções quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Pode adicionar inicializações específicas para a página de motoboys aqui
    
    // Verificar se há mensagens de feedback e removê-las após 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 1s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 1000);
        });
    }, 5000);
});