<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: index.php");
  exit;
}

include 'conexao.php';

// Função para buscar movimentos do banco de dados
function buscarMovimentos($conn, $filtroDataInicio = null, $filtroDataFinal = null, $limit = 10)
{
  $filtroDataInicio = date('Y-m-d');
  $filtroDataFinal = date('Y-m-d');
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
$filtroDataInicio = date('Y-m-d');
$filtroDataFinal = date('Y-m-d');

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
  $filtroDataInicio = date('Y-m-d', strtotime($filtroDataInicio));
  $sql .= " AND m.data_hora_saida >= '$filtroDataInicio'";
}
if ($filtroDataFinal) {
  $filtroDataFinal = date('Y-m-d', strtotime($filtroDataFinal . ' + 1 day'));
  $sql .= " AND m.data_hora_saida < '$filtroDataFinal'";
}

// Executa a query
$result = $conn->query($sql);
$movimentos = $result->fetch_all(MYSQLI_ASSOC);

// Pegar a data do filtro e a quantidade de registros a serem exibidos
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
      <h2 class="my-3">Movimentos de hoje</h2>
      <!-- Exibir a listagem de movimentos aqui -->
      <table class="table">
        <thead>
          <tr>
            <th>Motoboy</th>
            <th>Usuário Saída</th>
            <th>Usuário Retorno</th>
            <th>Data/Hora Saída</th>
            <th>Data/Hora Retorno</th>
            <th>Descrição</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($movimentos as $movimento): ?>
            <tr>
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

            </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>