<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';



if (isset($_POST['id'])) {
  $user_id = $_POST['id'];

  $query = "DELETE FROM usuarios WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $user_id);

  if ($stmt->execute()) {
    echo "Usuário excluído com sucesso!";
  } else {
    echo "Erro ao excluir usuário: " . $stmt->error;
  }

  $stmt->close();
} else {
  echo "ID do usuário não fornecido";
}

?>
