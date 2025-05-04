/**
 * Script principal do Sistema de Gerenciamento de Motoboys
 */

// Funções para controle de modais
function abrirModal(modalId) {
    $('#' + modalId).modal('show');
}

function fecharModal(modalId) {
    $('#' + modalId).modal('hide');
}

// Formatar data e hora
function formatarDataHora(dataString) {
    if (!dataString) return '';
    
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR');
}

// Confirmar ações potencialmente destrutivas
function confirmarAcao(mensagem, callback) {
    if (confirm(mensagem)) {
        callback();
    }
}

// Validar formulário de movimento
function validarFormMovimento() {
    let valido = true;
    
    if ($('#motoboy_id').val() === '') {
        $('#motoboy_id').parent().addClass('error');
        valido = false;
    } else {
        $('#motoboy_id').parent().removeClass('error');
    }
    
    if ($('#descricao').val() === '') {
        $('#descricao').parent().addClass('error');
        valido = false;
    } else {
        $('#descricao').parent().removeClass('error');
    }
    
    return valido;
}

// Exibir mensagem de notificação
function mostrarNotificacao(mensagem, tipo = 'info') {
    const notification = $(`
        <div class="ui ${tipo} floating message">
            <i class="close icon"></i>
            <div class="content">${mensagem}</div>
        </div>
    `);
    
    $('body').append(notification);
    
    notification.find('.close').on('click', function() {
        notification.transition('fade');
        setTimeout(() => notification.remove(), 300);
    });
    
    setTimeout(() => {
        notification.transition('fade');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Inicializações quando o documento estiver pronto
$(document).ready(function() {
    // Inicializar tabelas com ordenação
    $('.ui.sortable.table').tablesort();
    
    // Inicializar calendários
    $('.ui.calendar').calendar({
        type: 'date',
        firstDayOfWeek: 0,
        text: {
            days: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
            months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            today: 'Hoje',
            now: 'Agora',
            am: 'AM',
            pm: 'PM'
        },
        formatter: {
            date: function(date, settings) {
                if (!date) return '';
                const dia = date.getDate().toString().padStart(2, '0');
                const mes = (date.getMonth() + 1).toString().padStart(2, '0');
                const ano = date.getFullYear();
                return `${dia}/${mes}/${ano}`;
            }
        }
    });
    
    // Configurar formulários com validação
    $('.ui.form').form();
}); 