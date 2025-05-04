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

// Verificar se o ID do movimento foi fornecido
$movimento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($movimento_id <= 0) {
    $_SESSION['erro'] = "ID de movimento inválido.";
    header("Location: index.php");
    exit;
}

// Obter dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Verificar se o movimento existe e está pendente
$conexao = conectarDB();
$sql = "SELECT id FROM movimentos WHERE id = ? AND data_hora_retorno IS NULL";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $movimento_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erro'] = "Movimento não encontrado ou já concluído.";
    header("Location: index.php");
    exit;
}

// Registrar o retorno
$sucesso = registrarRetorno($movimento_id, $usuario_id);

if ($sucesso) {
    $_SESSION['sucesso'] = "Retorno registrado com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao registrar retorno. Tente novamente.";
}

// Fechar conexão
$stmt->close();
$conexao->close();

// Redirecionar para a página anterior ou index
$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $referer");
exit;
?>