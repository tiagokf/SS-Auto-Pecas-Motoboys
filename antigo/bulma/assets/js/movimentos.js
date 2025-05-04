// Modal de Adicionar Movimento
document.getElementById('btn-add-movimento').addEventListener('click', () => {
    document.getElementById('modal-add-movimento').classList.add('is-active');
});

// Fechar Modal
document.getElementById('close-add-movimento').addEventListener('click', () => {
    document.getElementById('modal-add-movimento').classList.remove('is-active');
});
document.getElementById('cancel-add-movimento').addEventListener('click', () => {
    document.getElementById('modal-add-movimento').classList.remove('is-active');
});
