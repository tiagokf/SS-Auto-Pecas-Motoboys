<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

if (isset($_POST['id']) && isset($_POST['nome']) && isset($_POST['senha'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $query = "UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    // Corrija a ordem dos parâmetros e adicione o marcador de tipo para a senha
    $stmt->bind_param("ssi", $nome, $senha, $id);

    if ($stmt->execute()) {
        echo "Usuário atualizado com sucesso";
    } else {
        echo "Erro ao atualizar o usuário";
    }

    $stmt->close();
} else {
    echo "Dados inválidos";
}
?>
