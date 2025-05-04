jQuery(document).ready(function () {
  //sidebar navbar
  jQuery('.toggle-sidebar, .toggle-sidebar-btn').click(function () {
    jQuery('.sidebar').toggleClass('collapsed');
    jQuery('.main-content').toggleClass('collapsed');
    jQuery('.toggle-sidebar-btn i').toggleClass('fa-chevron-left fa-chevron-right');
  });

  // Submeter o formulário de adição de usuário
  jQuery('#addUserBtn').click(function () {

    var nome = jQuery('#nome').val();
    var senha = jQuery('#senha').val();

    // Fazer a requisição AJAX para adicionar o usuário no banco de dados
    jQuery.ajax({
      type: "POST",
      url: "add_user.php",
      data: {
        nome: nome,
        senha: senha
      },
      success: function (data) {
        console.log(data)
        // Atualizar a tabela de usuários após a adição
        location.reload();

        // Fechar o modal
        jQuery('#addUserModal').modal('hide');
      },
      error: function (error) {
        console.log(error);
        alert("Erro ao adicionar o usuário");
      }
    });
  });

  // Submeter o formulário de adição de usuário
  jQuery('#addMotoboyBtn').click(function () {

    var nome = jQuery('#nome').val();
    // Fazer a requisição AJAX para adicionar o usuário no banco de dados
    jQuery.ajax({
      type: "POST",
      url: "add_motoboy.php",
      data: {
        nome: nome
      },
      success: function (data) {
        console.log(data)
        // Atualizar a tabela de Motoboys após a adição
        location.reload();

        // Fechar o modal
        jQuery('#addMotoboyModal').modal('hide');
      },
      error: function (error) {
        console.log(error);
        alert("Erro ao adicionar o Motoboy");
      }
    });
  });

});

// Função para abrir o modal de edição em usuários
function openEditModal(userId, userName, userSenha) {
  jQuery('#edit_user_id').val(userId);
  jQuery('#edit_user_name').val(userName);
  jQuery('#edit_user_senha').val(userSenha);
  jQuery('#editUserModal').modal('show');
}

// Função para abrir o modal de exclusão em usuários
function openDeleteModal(userId) {
  jQuery('#delete_user_id').val(userId);
  jQuery('#deleteUserModal').modal('show');
}

// Função para abrir o modal de exclusão em usuários
function openDeleteModalMotoboy(motoboyId) {
  jQuery('#delete_motoboy_id').val(motoboyId);
  jQuery('#deleteMotoboyModal').modal('show');
}

$(document).ready(function () {

  // Submeter o formulário de edição de usuário
  jQuery('#editUserForm').submit(function (event) {
    event.preventDefault();

    var id = jQuery('#edit_user_id').val();
    var nome = jQuery('#edit_user_name').val();
    var senha = jQuery('#edit_user_senha').val();

    // Fazer a requisição AJAX para editar o usuário no banco de dados
    jQuery.ajax({
      type: "POST",
      url: "edit_user.php",
      data: {
        id: id,
        nome: nome,
        senha: senha
      },
      success: function (data) {
        console.log(data);
        // Atualizar a tabela de usuários após a edição
        location.reload();
      },
      error: function (error) {
        console.log(error);
        alert("Erro ao editar o usuário");
      }
    });
  });
});


$(document).ready(function () {

  // Submeter o formulário de exclusão de usuário
  jQuery('#deleteUserForm').submit(function (event) {
    event.preventDefault();
    // Implementar a lógica para excluir o usuário do banco de dados

    const user_id = jQuery('#delete_user_id').val();

    jQuery.ajax({
      url: 'delete_user.php',
      method: 'POST',
      data: { id: user_id },
      success: function (response) {
        console.log(response);
        location.reload(); // Atualiza a página para exibir a lista de usuários atualizada
      },
      error: function (xhr, status, error) {
        console.error(xhr, status, error);
      },
    });
    // Fechar o modal e atualizar a tabela após a exclusão
    jQuery('#deleteUserModal').modal('hide');
  });
});

$(document).ready(function () {

  // Submeter o formulário de exclusão de Motoboy
  jQuery('#deleteMotoboyForm').submit(function (event) {
    event.preventDefault();
    // Implementar a lógica para excluir o motoboy do banco de dados

    const motoboy_id = jQuery('#delete_motoboy_id').val();

    jQuery.ajax({
      url: 'delete_motoboy.php',
      method: 'POST',
      data: { id: motoboy_id },
      success: function (response) {
        console.log(response);
        location.reload(); // Atualiza a página para exibir a lista de motoboys atualizada
      },
      error: function (xhr, status, error) {
        console.error(xhr, status, error);
      },
    });
    // Fechar o modal e atualizar a tabela após a exclusão
    jQuery('#deleteMotoboyModal').modal('hide');
  });
});


jQuery(document).ready(function () {
  jQuery('#modalExcluirMovimento').on('show.bs.modal', function (event) {
    var button = jQuery(event.relatedTarget);
    var movimentoId = button.data('movimento-id');
    var modal = jQuery(this);
    modal.find('#movimento_id').val(movimentoId);
  });
});



jQuery.noConflict();
(function ($) {
  // Agora você pode usar $ como um alias para jQuery neste escopo
  $(document).ready(function () {
    jQuery.ajax({
      url: 'ler_motoboys.php', // Substitua pelo endpoint correto do seu servidor
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        data.forEach(function (motoboy) {
          jQuery('#add_motoboy_id').append(`<option value="${motoboy.id}">${motoboy.nome}</option>`);
        });
      }
    });
  });
})(jQuery);

jQuery.noConflict();
(function ($) {
  // Agora você pode usar $ como um alias para jQuery neste escopo
  $(document).ready(function () {
    jQuery.ajax({
      url: 'ler_motoboys.php', // Substitua pelo endpoint correto do seu servidor
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        data.forEach(function (motoboy) {
          jQuery('#edit_motoboy_id').append(`<option value="${motoboy.id}">${motoboy.nome}</option>`);
        });
      }
    });
  });
})(jQuery);

// Declaração da função carregarUsuarios fora do escopo da função anônima
function carregarUsuarios() {
  jQuery.ajax({
    url: 'ler_usuarios.php',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      data.forEach(function (usuario) {
        const usuarioOption = `<option value="${usuario.id}">${usuario.nome}</option>`;
        jQuery('#add_usuario_saida_id').append(usuarioOption);
        jQuery('#add_usuario_retorno_id').append(usuarioOption);
      });
    },
    error: function () {
      console.error('Erro ao carregar os dados dos usuários.');
    }
  });
}

// Declaração da função carregarUsuarios fora do escopo da função anônima
function carregarUsMovimentos() {
  jQuery.ajax({
    url: 'ler_usuarios.php',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      data.forEach(function (usuario) {
        const usuarioOption = `<option value="${usuario.id}">${usuario.nome}</option>`;
        jQuery('#edit_usuario_saida_id').append(usuarioOption);
        jQuery('#edit_usuario_retorno_id').append(usuarioOption);
      });
    },
    error: function () {
      console.error('Erro ao carregar os dados dos usuários.');
    }
  });
}

jQuery(document).ready(function () {
  // Carregar os dados dos usuários ao abrir o modal
  jQuery('#addMovimentoModal').on('show.bs.modal', function () {
    carregarUsuarios();
  });

  jQuery(document).ready(function () {
    // Carregar os dados dos usuários ao abrir o modal
    jQuery('#editMovimentoModal').on('show.bs.modal', function () {
      carregarUsMovimentos();
    });

    // Limpar os campos de seleção de usuários ao fechar o modal
    jQuery('#addMovimentoModal').on('hidden.bs.modal', function () {
      jQuery('#add_usuario_saida_id').empty();
      jQuery('#add_usuario_retorno_id').empty();
    });

    // Limpar os campos de seleção de usuários ao fechar o modal de editar movimentos
    jQuery('#editMovimentoModal').on('hidden.bs.modal', function () {
      jQuery('#edit_usuario_saida_id').empty();
      jQuery('#edit_usuario_retorno_id').empty();
    });

    // Implemente aqui o código para lidar com o envio do formulário (form submission)
    jQuery('#addMovimentoForm').on('submit', function (event) {
      event.preventDefault();

      // Capture os dados do formulário
      let formData = jQuery(this).serialize();
      const motoboyId = jQuery('#add_motoboy_id').val();
      const usuarioSaidaId = jQuery('#add_usuario_saida_id').val();
      const usuarioRetornoId = jQuery('#add_usuario_retorno_id').val();
      const dataHoraSaida = jQuery('#add_data_hora_saida').val();
      const dataHoraRetorno = jQuery('#add_data_hora_retorno').val();
      const descricao = jQuery('#add_descricao').val();

      //console.log(descricao);
      // Adicione os IDs de motoboy e usuário aos dados do formulário
      formData += `&motoboy_id=${motoboyId}&usuario_saida_id=${usuarioSaidaId}&usuario_retorno_id=${usuarioRetornoId}&data_hora_saida=${dataHoraSaida}&data_hora_retorno=${dataHoraRetorno}&descricao=${descricao}`;

      // Envie os dados para o servidor
      jQuery.ajax({
        type: 'POST',
        url: 'add_movimento.php',
        data: formData,
        success: function (data) {
          console.log(data);
          // Atualize a tabela de movimentos após a adição
          location.reload();
        },
        error: function (error) {
          console.log(error);
          alert('Erro ao adicionar o movimento');
        },
      });
    });
  });

  // Implemente aqui o código para lidar com a edição do formulário (form submission)
  jQuery('#editMovimentoForm').on('submit', function (event) {
    event.preventDefault();

    // Capture os dados do formulário
    let formData = jQuery(this).serialize();
    const movimentoId = jQuery('#edit_movimento_id').val();
    const motoboyId = jQuery('#edit_motoboy_id').val();
    const usuarioSaidaId = jQuery('#edit_usuario_saida_id').val();
    const usuarioRetornoId = jQuery('#edit_usuario_retorno_id').val();
    const dataHoraSaida = jQuery('#edit_data_hora_saida').val();
    const dataHoraRetorno = jQuery('#edit_data_hora_retorno').val();
    const descricao = jQuery('#edit_descricao').val();
    //console.log(movimentoId);
    // Adicione os IDs de motoboy e usuário aos dados do formulário
    formData += `&movimento_id=${movimentoId}&motoboy_id=${motoboyId}&usuario_saida_id=${usuarioSaidaId}&usuario_retorno_id=${usuarioRetornoId}&data_hora_saida=${dataHoraSaida}&data_hora_retorno=${dataHoraRetorno}&descricao=${descricao}`;

    // Envie os dados para o servidor
    jQuery.ajax({
      type: 'POST',
      url: 'edit_movimento.php',
      data: formData,
      success: function (data) {
        console.log(data);
        // Atualize a tabela de movimentos após a edição
        location.reload();
      },
      error: function (error) {
        console.log(error);
        alert('Erro ao editar o movimento');
      },
    });
  });
});
