// assets/js/usuarios.js - Funções específicas para o módulo de usuários
/**
 * Função para editar um usuário, buscando dados via API
 * @param {number} id - ID do usuário
 */
function editarUsuario(id) {
    // Fazer uma requisição para obter os dados do usuário
    fetch('../api/usuarios.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get&id=${id}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao obter dados do usuário');
        }
        return response.json();
    })
    .then(usuario => {
        // Preencher o formulário com os dados do usuário
        document.getElementById('edit-id').value = usuario.id;
        document.getElementById('edit-nome').value = usuario.nome;
        document.getElementById('edit-senha').value = usuario.senha;
        
        // Exibir o modal
        showModal('modal-editar');
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao obter dados do usuário');
    });
}

/**
 * Função para confirmar exclusão de um usuário
 * @param {number} id - ID do usuário a ser excluído
 */
function confirmarExclusao(id) {
    document.getElementById('delete-id').value = id;
    
    // Atualizar a mensagem de confirmação para ser mais clara sobre o que acontecerá
    const mensagemConfirmacao = document.querySelector('#modal-excluir p.mb-4');
    if (mensagemConfirmacao) {
        mensagemConfirmacao.innerHTML = 'Tem certeza que deseja excluir este usuário? <strong>Todos os registros relacionados a este usuário serão transferidos para o usuário "Sistema"</strong>. Esta ação não pode ser desfeita.';
    }
    
    showModal('modal-excluir');
}

// Inicializa funções quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Validação para o formulário de adição
    const formAdicionar = document.getElementById('form-adicionar');
    if (formAdicionar) {
        formAdicionar.addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha');
            
            // Verificar se o campo existe antes de acessar seu valor
            if (confirmarSenha && senha !== confirmarSenha.value) {
                e.preventDefault();
                alert('As senhas não coincidem!');
            }
        });
    }
    
    // Validação para o formulário de edição
    const formEditar = document.getElementById('form-editar');
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            const senha = document.getElementById('edit-senha').value;
            const confirmarSenha = document.getElementById('edit-confirmar_senha');
            
            // Verificar se o campo existe antes de acessar seu valor
            if (confirmarSenha && senha && senha !== confirmarSenha.value) {
                e.preventDefault();
                alert('As senhas não coincidem!');
            }
        });
    }
});