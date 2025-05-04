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
    header("Location: motoboys.php");
    exit;
}

// Obter e validar dados do formulário
$nome = limparDados($_POST['nome'] ?? '');
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar dados
if (empty($nome)) {
    $_SESSION['erro'] = "O nome do motoboy é obrigatório.";
    header("Location: motoboys.php");
    exit;
}

$conexao = conectarDB();

if ($id > 0) {
    // Atualização de motoboy existente
    $sql = "UPDATE motoboys SET nome = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $nome, $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['sucesso'] = "Motoboy atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi feita ou motoboy não encontrado.";
    }
    
    $stmt->close();
} else {
    // Inserção de novo motoboy
    $sql = "INSERT INTO motoboys (nome) VALUES (?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['sucesso'] = "Motoboy cadastrado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar motoboy. Tente novamente.";
    }
    
    $stmt->close();
}

$conexao->close();

// Redirecionar para a página de motoboys
header("Location: motoboys.php");
exit;
?>