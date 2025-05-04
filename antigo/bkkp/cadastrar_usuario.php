<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

$nome = $_POST['nome_cadastro'];
$senha = $_POST['senha_cadastro'];

$sql = "INSERT INTO usuarios (nome, senha) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nome, $senha);

if ($stmt->execute()) {
    header("Location: index.php?sucesso=1");
} else {
    header("Location: index.php?erro=2");
}

$stmt->close();
$conn->close();
?>
