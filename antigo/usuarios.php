<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: index.php");
  exit;
}

// inclua aqui a conexão com o banco de dados
require_once 'conexao.php';

// Carregando os usuários
$query = "SELECT * FROM usuarios";
$result = mysqli_query($conn, $query);
$usuarios = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="font/css/all.min.css">

  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>

  <title>
    <?php echo $_SESSION['usuario_nome']; ?> - SSMotoboys
  </title>
</head>

<body>
  <?php include 'painel-sidebar.php'; ?>
  <div class="main-content">
    <?php include 'painel-navbar.php'; ?>

    <div class="container">
      <h2 class="my-3">Usuários</h2>

      <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Adicionar Usuário
      </button>

      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $usuario): ?>
            <tr>
              <td>
                <?= $usuario['id'] ?>
              </td>
              <td>
                <?= $usuario['nome'] ?>
              </td>
              <td>
                <button class="btn btn-warning btn-sm"
                  onclick="openEditModal(<?php echo $usuario['id'] ?>, '<?php echo $usuario['nome'] ?>', '<?php echo $usuario['senha'] ?>')">Editar</button>
                <button class="btn btn-danger btn-sm"
                  onclick="openDeleteModal(<?php echo $usuario['id'] ?>)">Excluir</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  </div>
  <!--Modal de adicionar usuário -->

  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Adicionar Usuário</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUserForm">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
              <label for="senha" class="form-label">Senha</label>
              <input type="text" class="form-control" id="senha" name="senha" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary" id="addUserBtn">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar usuário -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editUserForm">
          <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_user_id" id="edit_user_id">
            <div class="form-group">
              <label for="edit_user_name">Nome</label>
              <input type="text" class="form-control" name="edit_user_name" id="edit_user_name" required>
            </div>
            <div class="form-group">
              <label for="edit_user_senha" class="form-label">Senha</label>
              <input type="text" class="form-control" name="edit_user_senha" id="edit_user_senha" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para excluir usuário -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="deleteUserForm">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteUserModalLabel">Excluir Usuário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="delete_user_id" id="delete_user_id">
            <p>Tem certeza que deseja excluir este usuário?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Excluir</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>