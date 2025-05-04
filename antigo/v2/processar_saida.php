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

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Obter e validar dados do formulário
$motoboy_id = isset($_POST['motoboy_id']) ? intval($_POST['motoboy_id']) : 0;
$descricao = limparDados($_POST['descricao'] ?? '');
$usuario_id = $_SESSION['usuario_id'];

// Validar dados
$erro = null;
if ($motoboy_id <= 0) {
    $erro = "Selecione um motoboy válido.";
} elseif (empty($descricao)) {
    $erro = "Informe a descrição da saída.";
}

// Se houver erro, redirecionar com mensagem
if ($erro) {
    $_SESSION['erro'] = $erro;
    header("Location: index.php");
    exit;
}

// Registrar a saída
$sucesso = registrarSaida($motoboy_id, $usuario_id, $descricao);

if ($sucesso) {
    $_SESSION['sucesso'] = "Saída registrada com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao registrar saída. Tente novamente.";
}

// Redirecionar para a página inicial
header("Location: index.php");
exit;
?>