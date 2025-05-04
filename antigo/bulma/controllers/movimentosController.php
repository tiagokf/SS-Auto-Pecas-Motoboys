<?php
require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    // Adicionar movimento
    $stmt = $pdo->prepare("
        INSERT INTO movimentos (descricao, motoboy_id, usuario_saida_id, data_hora_saida) 
        VALUES (:descricao, :motoboy_id, :usuario_saida_id, :data_hora_saida)
    ");
    $stmt->execute([
        'descricao' => $_POST['descricao'],
        'motoboy_id' => $_POST['motoboy_id'],
        'usuario_saida_id' => $_POST['usuario_saida_id'],
        'data_hora_saida' => $_POST['data_hora_saida'],
    ]);

    header("Location: ../views/movimentos.php");
    exit();
}

if ($action === 'edit') {
    // Editar movimento
    $stmt = $pdo->prepare("
        UPDATE movimentos 
        SET descricao = :descricao, motoboy_id = :motoboy_id, usuario_saida_id = :usuario_saida_id,
            usuario_retorno_id = :usuario_retorno_id, data_hora_saida = :data_hora_saida, data_hora_retorno = :data_hora_retorno
        WHERE id = :id
    ");
    $stmt->execute([
        'descricao' => $_POST['descricao'],
        'motoboy_id' => $_POST['motoboy_id'],
        'usuario_saida_id' => $_POST['usuario_saida_id'],
        'usuario_retorno_id' => !empty($_POST['usuario_retorno_id']) ? $_POST['usuario_retorno_id'] : null,
        'data_hora_saida' => $_POST['data_hora_saida'],
        'data_hora_retorno' => !empty($_POST['data_hora_retorno']) ? $_POST['data_hora_retorno'] : null,
        'id' => $_POST['id'],
    ]);

    header("Location: ../views/movimentos.php");
    exit();
}

if ($action === 'delete') {
    // Excluir movimento
    $stmt = $pdo->prepare("DELETE FROM movimentos WHERE id = :id");
    $stmt->execute(['id' => $_GET['id']]);

    header("Location: ../views/movimentos.php");
    exit();
}

// Redirecionar para a página principal se nenhuma ação for encontrada
header("Location: ../views/movimentos.php");
exit();
