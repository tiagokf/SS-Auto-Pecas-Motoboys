<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

if (isset($_POST['id'])) {
  $motoboy_id = $_POST['id'];

  $query = "DELETE FROM motoboys WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $motoboy_id);

  if ($stmt->execute()) {
    echo "Motoboy excluído com sucesso!";
  } else {
    echo "Erro ao excluir Motoboy: " . $stmt->error;
  }

  $stmt->close();
} else {
  echo "ID do Motoboy não fornecido";
}

?>
