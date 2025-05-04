<?php
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_admin']) || !$_SESSION['usuario_admin']) {
    header("Location: index.php");
    exit;
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['erro'] = "ID do usuário não fornecido.";
    header("Location: usuarios.php");
    exit;
}

$id = intval($_GET['id']);

// Verificar se não está tentando excluir o próprio usuário
if ($id === $_SESSION['usuario_id']) {
    $_SESSION['erro'] = "Você não pode excluir seu próprio usuário.";
    header("Location: usuarios.php");
    exit;
}

$conexao = conectarDB();

// Verificar se o usuário possui movimentos associados
$sql = "SELECT 
            (SELECT COUNT(*) FROM movimentos WHERE usuario_saida_id = ?) as saidas,
            (SELECT COUNT(*) FROM movimentos WHERE usuario_retorno_id = ?) as retornos";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($resultado['saidas'] > 0 || $resultado['retornos'] > 0) {
    $_SESSION['erro'] = "Este usuário não pode ser excluído pois possui movimentos associados.";
    header("Location: usuarios.php");
    exit;
}

// Excluir o usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['sucesso'] = "Usuário excluído com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao excluir usuário. Usuário não encontrado ou já foi excluído.";
}

$stmt->close();
$conexao->close();

// Redirecionar para a página de usuários
header("Location: usuarios.php");
exit;
?>