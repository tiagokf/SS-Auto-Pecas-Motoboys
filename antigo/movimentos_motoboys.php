<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: index.php");
  exit;
}

require_once 'conexao.php';

// Query para selecionar os motoboys do banco de dados
$sql = "SELECT id, nome FROM motoboys";
$resultado = mysqli_query($conexao, $sql);

// Verifica se a query foi executada com sucesso
if (!$resultado) {
  echo "Erro ao executar a query: " . mysqli_error($conexao);
  exit();
}

// Cria um array para armazenar os resultados da query
$motoboys = array();

// Adiciona cada resultado da query ao array de motoboys
while ($row = mysqli_fetch_assoc($resultado)) {
  $motoboys[] = $row;
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao);

// Retorna os motoboys como JSON
header('Content-Type: application/json');
echo json_encode($motoboys);
?>
