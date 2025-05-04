// assets/js/main.js - Funções gerais usadas em todo o sistema

/**
 * Função para mostrar um modal
 * @param {string} modalId - ID do modal a ser exibido
 */
function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

/**
 * Função para esconder um modal
 * @param {string} modalId - ID do modal a ser escondido
 */
function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

/**
 * Função para formatar data/hora no formato para input datetime-local
 * @param {string} dataHora - Data/hora no formato do banco de dados
 * @returns {string} Data/hora formatada para input datetime-local
 */
function formatarDataHoraParaInput(dataHora) {
    if (!dataHora) return '';
    
    // Criar um objeto Date com a data/hora recebida
    const data = new Date(dataHora);
    
    // Obter os componentes da data no fuso horário local
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const dia = String(data.getDate()).padStart(2, '0');
    const hora = String(data.getHours()).padStart(2, '0');
    const minuto = String(data.getMinutes()).padStart(2, '0');
    
    // Formatar no padrão YYYY-MM-DDTHH:MM esperado pelo input datetime-local
    return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
}


/**
 * Inicializa funções após o carregamento do DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    // Esconder alertas após 5 segundos
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