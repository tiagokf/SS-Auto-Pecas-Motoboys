<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit;
}
require_once("conexao.php");


// Carregando os motoboys
$query = "SELECT * FROM motoboys";
$result = mysqli_query($conn, $query);
$motoboys = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style.css">

  <title>Motoboys - SSMotoboys</title>
</head>

<body>
  <?php include('painel-sidebar.php'); ?>

  <div class="main-content">
    <?php include('painel-navbar.php'); ?>

    <div class="container">
      <h2 class="my-3">Motoboys</h2>
      <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMotoboyModal">Adicionar
        Motoboy</button>

      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($motoboys as $motoboy): ?>
            <tr>
              <td>
                <?= $motoboy['id'] ?>
              </td>
              <td>
                <?= $motoboy['nome'] ?>
              </td>
              <td>
                <!--<button class="btn btn-warning btn-sm"
                  onclick="openEditModalMotoboy(<?php echo $motoboy['id'] ?>, '<?php echo $motoboy['nome'] ?>')">Editar</button>-->
                <button class="btn btn-danger btn-sm"
                  onclick="openDeleteModalMotoboy(<?php echo $motoboy['id'] ?>)">Excluir</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Aqui você pode adicionar os modais de adicionar, editar e excluir motoboys -->
  <!--Modal de adicionar Motoboy -->

  <div class="modal fade" id="addMotoboyModal" tabindex="-1" aria-labelledby="addMotoboyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addMotoboyModalLabel">Adicionar Motoboy</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addMotoboyForm">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary" id="addMotoboyBtn">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar motoboy -->
  <div class="modal fade" id="editMotoboyModal" tabindex="-1" aria-labelledby="editMotoboyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editMotoboyForm">
          <div class="modal-header">
            <h5 class="modal-title" id="editMotoboyModalLabel">Editar Motoboy</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <input type="hidden" name="edit_motoboy_id" id="edit_motoboy_id">
              <label for="edit_motoboy_name">Nome</label>
              <input type="text" class="form-control" name="edit_motoboy_name" id="edit_motoboy_name" required>
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

  <!-- Modal para excluir motoboy -->
  <div class="modal fade" id="deleteMotoboyModal" tabindex="-1" aria-labelledby="deleteMotoboyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="deleteMotoboyForm">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteMotoboyModalLabel">Excluir Motoboy</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="delete_motoboy_id" id="delete_motoboy_id">
            <p>Tem certeza que deseja excluir este motoboy?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Excluir</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="script.js"></script>
</body>

</html>