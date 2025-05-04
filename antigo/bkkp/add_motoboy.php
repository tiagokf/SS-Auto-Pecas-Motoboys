<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: index.php");
  exit;
}

require_once 'conexao.php';

$nome = mysqli_real_escape_string($conn, $_POST['nome']);

$query = "INSERT INTO motoboys (nome) VALUES ('$nome')";

if (mysqli_query($conn, $query)) {
    echo 'success';
} else {
    echo 'error';
}

mysqli_close($conn);
