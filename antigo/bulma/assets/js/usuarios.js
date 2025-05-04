document.getElementById('btn-add-user').addEventListener('click', () => {
    document.getElementById('modal-add-user').classList.add('is-active');
});

document.getElementById('close-add-user').addEventListener('click', () => {
    document.getElementById('modal-add-user').classList.remove('is-active');
});
document.getElementById('cancel-add-user').addEventListener('click', () => {
    document.getElementById('modal-add-user').classList.remove('is-active');
});

document.querySelectorAll('.btn-edit-user').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');

        document.getElementById('edit-user-id').value = id;
        document.getElementById('edit-user-nome').value = nome;

        document.getElementById('modal-edit-user').classList.add('is-active');
    });
});

document.getElementById('close-edit-user').addEventListener('click', () => {
    document.getElementById('modal-edit-user').classList.remove('is-active');
});
document.getElementById('cancel-edit-user').addEventListener('click', () => {
    document.getElementById('modal-edit-user').classList.remove('is-active');
});
