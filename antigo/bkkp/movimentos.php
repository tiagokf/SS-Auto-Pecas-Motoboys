<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: index.php");
  exit;
}

include 'conexao.php';
// Função para buscar movimentos do banco de dados
function buscarMovimentos($conn, $filtroDataInicio = null, $filtroDataFinal = null, $limit = 10)
{
  $where = [];

  if ($filtroDataInicio) {
    $where[] = "DATE(data_hora_saida) >= '$filtroDataInicio'";
  }

  if ($filtroDataFinal) {
    $where[] = "DATE(data_hora_saida) <= '$filtroDataFinal'";
  }

  $whereClause = "";
  if (!empty($where)) {
    $whereClause = "WHERE " . implode(" AND ", $where);
  }

  $sql = "SELECT movimentos.*, motoboys.nome as motoboy_nome, usuario_saida.nome as usuario_saida_nome, usuario_retorno.nome as usuario_retorno_nome
          FROM movimentos
          JOIN motoboys ON movimentos.motoboy_id = motoboys.id
          JOIN usuarios as usuario_saida ON movimentos.usuario_saida_id = usuario_saida.id
          JOIN usuarios as usuario_retorno ON movimentos.usuario_retorno_id = usuario_retorno.id
          $whereClause
          ORDER BY data_hora_saida DESC
          LIMIT $limit";

  $query = mysqli_query($conn, $sql);

  $movimentos = [];
  while ($row = mysqli_fetch_assoc($query)) {
    $movimentos[] = $row;
  }

  return $movimentos;
}

// Recupera as datas do formulário de filtro
$filtroDataInicio = isset($_GET['filtro_data_inicio']) ? $_GET['filtro_data_inicio'] : null;
$filtroDataFinal = isset($_GET['filtro_data_final']) ? $_GET['filtro_data_final'] : null;

// Query para buscar os movimentos com as datas filtradas
$sql = "SELECT m.id, m.descricao, m.data_hora_saida, m.data_hora_retorno, 
               motoboy.nome AS motoboy_nome, 
               usuario_saida.nome AS usuario_saida_nome, 
               usuario_retorno.nome AS usuario_retorno_nome 
        FROM movimentos m 
        INNER JOIN motoboys motoboy ON motoboy.id = m.motoboy_id 
        INNER JOIN usuarios usuario_saida ON usuario_saida.id = m.usuario_saida_id 
        LEFT JOIN usuarios usuario_retorno ON usuario_retorno.id = m.usuario_retorno_id 
        WHERE 1=1";

// Adiciona as condições de filtro, se existirem
if ($filtroDataInicio) {
  $filtroDataInicio = date('Y-m-d H:i:s', strtotime($filtroDataInicio));
  $sql .= " AND m.data_hora_saida >= '$filtroDataInicio'";
}
if ($filtroDataFinal) {
  $filtroDataFinal = date('Y-m-d H:i:s', strtotime($filtroDataFinal . ' + 1 day'));
  $sql .= " AND m.data_hora_saida < '$filtroDataFinal'";
}

// Executa a query
$result = $conn->query($sql);
$movimentos = $result->fetch_all(MYSQLI_ASSOC);

// Pegar a data do filtro e a quantidade de registros a serem exibidos
$filtroData = isset($_GET['filtro_data']) ? $_GET['filtro_data'] : null;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

// Buscar movimentos
$movimentos = buscarMovimentos($conn, $filtroDataInicio, $filtroDataFinal, $limit);
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
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
      <h2 class="my-3">Movimentos</h2>
      <div class="row mb-3" justify-content-center>
        <div class="col-md-3" justify-content-center>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMovimentoModal">
            Adicionar Movimento
          </button>
        </div>
        <div class="col-md-9">
          <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
              <label for="filtro_data_inicio" class="form-label">Data Inicial</label>
              <input type="date" class="form-control" id="filtro_data_inicio" name="filtro_data_inicio"
                value="<?php echo $filtroDataInicio ?>">
            </div>
            <div class="col-md-4">
              <label for="filtro_data_final" class="form-label">Data Final</label>
              <input type="date" class="form-control" id="filtro_data_final" name="filtro_data_final"
                value="<?php echo $filtroDataFinal ?>">
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
          </form>
        </div>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Motoboy</th>
            <th>Usuário Saída</th>
            <th>Usuário Retorno</th>
            <th>Data/Hora Saída</th>
            <th>Data/Hora Retorno</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($movimentos as $movimento): ?>
            <tr>
              <td>
                <?php echo $movimento['motoboy_nome'] ?>
              </td>
              <td>
                <?php echo $movimento['usuario_saida_nome'] ?>
              </td>
              <td>
                <?php echo $movimento['usuario_retorno_nome'] ?>
              </td>
              <td>
                <?php echo $movimento['data_hora_saida'] ? date('d/m/Y H:i', strtotime($movimento['data_hora_saida'])) : 'N/A'; ?>
              </td>
              <td>
                <?php echo $movimento['data_hora_retorno'] ? date('d/m/Y H:i', strtotime($movimento['data_hora_retorno'])) : 'N/A'; ?>
              </td>
              <td>
                <?php echo $movimento['descricao'] ?>
              </td>
              <td>

                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editMovimentoModal" data-movimento-id="<?php echo $movimento['id'] ?>"
                  data-motoboy-id="<?php echo $movimento['motoboy_id'] ?>"
                  data-usuario-saida-id="<?php echo $movimento['usuario_saida_id'] ?>"
                  data-usuario-retorno-id="<?php echo $movimento['usuario_retorno_id'] ?>"
                  data-data-hora-saida="<?php echo $movimento['data_hora_saida'] ?>"
                  data-data-hora-retorno="<?php echo $movimento['data_hora_retorno'] ?>"
                  data-descricao="<?php echo $movimento['descricao'] ?>"
                  onclick="openEditMovimentoModal('<?php echo $movimento['id'] ?>', '<?php echo $movimento['motoboy_id'] ?>', '<?php echo $movimento['usuario_saida_id'] ?>', '<?php echo $movimento['usuario_retorno_id'] ?>', '<?php echo $movimento['data_hora_saida'] ?>', '<?php echo $movimento['data_hora_retorno'] ?>', '<?php echo addslashes($movimento['descricao']) ?>')">
                  Editar
                </button>

                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                  data-bs-target="#modalExcluirMovimento"
                  data-movimento-id="<?php echo $movimento['id']; ?>">Excluir</button>

              </td>
            </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="row mt-3">
        <div class="col-md-6">
          <form method="GET">
            <div class="form-row">
              <div class="col">
                <select name="limit" class="form-control">
                  <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10 Registros</option>
                  <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50 Registros</option>
                  <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100 Registros</option>
                </select>
              </div>
              <div class="col">
                <button type="submit" class="btn btn-primary">Alterar quantidade de registros</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal Excluir Movimento -->
      <div class="modal fade" id="modalExcluirMovimento" tabindex="-1" role="dialog"
        aria-labelledby="modalExcluirMovimentoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalExcluirMovimentoLabel">Excluir Movimento</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Você tem certeza que deseja excluir esse movimento?</p>
            </div>
            <div class="modal-footer">
              <form action="excluir_movimento.php" method="post">
                <input type="hidden" id="movimento_id" name="movimento_id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Excluir</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Fim do Modal Excluir Movimento -->

    </div>
  </div>

  <!-- Modal Adicionar Movimento -->
  <div class="modal fade" id="addMovimentoModal" tabindex="-1" aria-labelledby="addMovimentoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="addMovimentoForm">
          <div class="modal-header">
            <h5 class="modal-title" id="addMovimentoModalLabel">Adicionar Movimento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="add_motoboy_id">Motoboy</label>
              <select class="form-control" name="add_motoboy_id" id="add_motoboy_id" required>
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do motoboy -->
              </select>
            </div>
            <div class="form-group">
              <label for="add_usuario_saida_id">Usuário Saída</label>
              <select class="form-control" name="add_usuario_saida_id" id="add_usuario_saida_id" required>
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do usuário -->
              </select>
            </div>
            <div class="form-group">
              <label for="add_usuario_retorno_id">Usuário Retorno</label>
              <select class="form-control" name="add_usuario_retorno_id" id="add_usuario_retorno_id">
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do usuário -->
              </select>
            </div>
            <div class="form-group">
              <label for="add_data_hora_saida">Data/Hora Saída</label>
              <input type="datetime-local" class="form-control" name="add_data_hora_saida" id="add_data_hora_saida"
                required>
            </div>
            <div class="form-group">
              <label for="add_data_hora_retorno">Data/Hora Retorno</label>
              <input type="datetime-local" class="form-control" name="add_data_hora_retorno" id="add_data_hora_retorno">
            </div>
            <div class="form-group">
              <label for="add_descricao">Descrição</label>
              <textarea class="form-control" name="add_descricao" id="add_descricao" required></textarea>
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
  <!-- Fim do Modal Adicionar Movimento -->
  <!-- Modal Editar Movimento -->
  <!-- Inserir o código do modal para editar movimento aqui -->
  <!-- Modal Editar Movimento -->
  <div class="modal fade" id="editMovimentoModal" tabindex="-1" aria-labelledby="editMovimentoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editMovimentoForm">
          <div class="modal-header">
            <h5 class="modal-title" id="editMovimentoModalLabel">Editar Movimento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="edit_motoboy_id">Motoboy</label>
              <input type="hidden" name="edit_movimento_id" id="edit_movimento_id">
              <select class="form-control" name="edit_motoboy_id" id="edit_motoboy_id" required>
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do motoboy -->
              </select>
            </div>
            <div class="form-group">
              <label for="edit_usuario_saida_id">Usuário Saída</label>
              <select class="form-control" name="edit_usuario_saida_id" id="edit_usuario_saida_id" required>
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do usuário -->
              </select>
            </div>
            <div class="form-group">
              <label for="edit_usuario_retorno_id">Usuário Retorno</label>
              <select class="form-control" name="edit_usuario_retorno_id" id="edit_usuario_retorno_id">
                <!-- opções do select serão preenchidas dinamicamente via AJAX com o nome do usuário -->
              </select>
            </div>
            <div class="form-group">
              <label for="edit_data_hora_saida">Data/Hora Saída</label>
              <input type="datetime-local" class="form-control" name="edit_data_hora_saida" id="edit_data_hora_saida"
                required>
            </div>
            <div class="form-group">
              <label for="edit_data_hora_retorno">Data/Hora Retorno</label>
              <input type="datetime-local" class="form-control" name="edit_data_hora_retorno"
                id="edit_data_hora_retorno">
            </div>
            <div class="form-group">
              <label for="edit_descricao">Descrição</label>
              <textarea class="form-control" name="edit_descricao" id="edit_descricao" required></textarea>
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
  <!-- Fim do Modal Editar Movimento -->
</body>
<script>
  function openEditMovimentoModal(movimentoId, motoboyId, usuarioSaidaId, usuarioRetornoId, dataHoraSaida, dataHoraRetorno, descricao) {
    jQuery('#edit_movimento_id').val(movimentoId);
    jQuery('#edit_motoboy_id').val(motoboyId);
    jQuery('#edit_usuario_saida_id').val(usuarioSaidaId);
    jQuery('#edit_usuario_retorno_id').val(usuarioRetornoId);
    jQuery('#edit_data_hora_saida').val(dataHoraSaida);
    jQuery('#edit_data_hora_retorno').val(dataHoraRetorno);
    jQuery('#edit_descricao').val(descricao);
    jQuery('#editMovimentoModal').modal('show');
  }
</script>

</html>