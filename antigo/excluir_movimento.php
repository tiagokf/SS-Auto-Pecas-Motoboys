<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_POST['movimento_id'])) {
    header("Location: movimentos.php");
    exit;
}

include 'conexao.php';

$movimento_id = $_POST['movimento_id'];

$sql = "DELETE FROM movimentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movimento_id);
$stmt->execute();

header("Location: movimentos.php");
?>
