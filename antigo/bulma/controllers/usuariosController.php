<?php
require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, senha) VALUES (:nome, :senha)");
    $stmt->execute(['nome' => $nome, 'senha' => $senha]);

    header("Location: ../views/usuarios.php");
    exit();
}

if ($action === 'edit') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    if (!empty($senha)) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, senha = :senha WHERE id = :id");
        $stmt->execute(['nome' => $nome, 'senha' => $senha, 'id' => $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
        $stmt->execute(['nome' => $nome, 'id' => $id]);
    }

    header("Location: ../views/usuarios.php");
    exit();
}

if ($action === 'delete') {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: ../views/usuarios.php");
    exit();
}
