<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
require_once 'conexao.php';

$nome = $_POST['nome'];
$senha = $_POST['senha'];

$sql = "SELECT id, nome FROM usuarios WHERE nome = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nome, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    header("Location: painel.php");
} else {
    header("Location: index.php?erro=1");
}
$stmt->close();
$conn->close();
?>