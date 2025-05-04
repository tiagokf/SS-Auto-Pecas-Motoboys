// Abrir modal de adicionar motoboy
document.getElementById('btn-add-motoboy').addEventListener('click', () => {
    document.getElementById('modal-add-motoboy').classList.add('is-active');
});

// Fechar modal de adicionar motoboy
document.getElementById('close-add-motoboy').addEventListener('click', () => {
    document.getElementById('modal-add-motoboy').classList.remove('is-active');
});
document.getElementById('cancel-add-motoboy').addEventListener('click', () => {
    document.getElementById('modal-add-motoboy').classList.remove('is-active');
});

// Abrir modal de editar motoboy
document.querySelectorAll('.btn-edit-motoboy').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');

        document.getElementById('edit-motoboy-id').value = id;
        document.getElementById('edit-motoboy-nome').value = nome;

        document.getElementById('modal-edit-motoboy').classList.add('is-active');
    });
});

// Fechar modal de editar motoboy
document.getElementById('close-edit-motoboy').addEventListener('click', () => {
    document.getElementById('modal-edit-motoboy').classList.remove('is-active');
});
document.getElementById('cancel-edit-motoboy').addEventListener('click', () => {
    document.getElementById('modal-edit-motoboy').classList.remove('is-active');
});
