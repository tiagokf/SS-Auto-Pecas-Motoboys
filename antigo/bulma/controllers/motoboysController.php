<?php
require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $nome = $_POST['nome'];

    $stmt = $pdo->prepare("INSERT INTO motoboys (nome) VALUES (:nome)");
    $stmt->execute(['nome' => $nome]);

    header("Location: ../views/motoboys.php");
    exit();
}

if ($action === 'edit') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];

    $stmt = $pdo->prepare("UPDATE motoboys SET nome = :nome WHERE id = :id");
    $stmt->execute(['nome' => $nome, 'id' => $id]);

    header("Location: ../views/motoboys.php");
    exit();
}

if ($action === 'delete') {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM motoboys WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: ../views/motoboys.php");
    exit();
}
