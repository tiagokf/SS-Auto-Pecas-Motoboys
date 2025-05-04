<?php
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['erro'] = "ID do motoboy não fornecido.";
    header("Location: motoboys.php");
    exit;
}

$id = intval($_GET['id']);
$conexao = conectarDB();

// Verificar se o motoboy possui movimentos associados
$sql = "SELECT COUNT(*) as total FROM movimentos WHERE motoboy_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$total = $resultado->fetch_assoc()['total'];
$stmt->close();

if ($total > 0) {
    $_SESSION['erro'] = "Este motoboy não pode ser excluído pois possui movimentos associados.";
    header("Location: motoboys.php");
    exit;
}

// Excluir o motoboy
$sql = "DELETE FROM motoboys WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['sucesso'] = "Motoboy excluído com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao excluir motoboy. Motoboy não encontrado ou já foi excluído.";
}

$stmt->close();
$conexao->close();

// Redirecionar para a página de motoboys
header("Location: motoboys.php");
exit;
?>