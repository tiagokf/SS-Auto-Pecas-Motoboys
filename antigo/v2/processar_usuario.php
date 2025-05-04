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

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: usuarios.php");
    exit;
}

// Obter e validar dados do formulário
$nome = limparDados($_POST['nome'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar dados
if (empty($nome)) {
    $_SESSION['erro'] = "O nome de usuário é obrigatório.";
    header("Location: usuarios.php");
    exit;
}

// Verificar se as senhas coincidem quando necessário
if (($id === 0 || !empty($senha)) && $senha !== $confirma_senha) {
    $_SESSION['erro'] = "As senhas não coincidem.";
    header("Location: usuarios.php" . ($id > 0 ? "?editar=$id" : ""));
    exit;
}

$conexao = conectarDB();

if ($id > 0) {
    // Atualização de usuário existente
    if (!empty($senha)) {
        // Atualizar nome e senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $nome, $senha_hash, $id);
    } else {
        // Atualizar apenas o nome
        $sql = "UPDATE usuarios SET nome = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $nome, $id);
    }
    
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['sucesso'] = "Usuário atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi feita ou usuário não encontrado.";
    }
    
    $stmt->close();
} else {
    // Verificar se o nome de usuário já existe
    $sql = "SELECT id FROM usuarios WHERE nome = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $_SESSION['erro'] = "Este nome de usuário já está em uso.";
        header("Location: usuarios.php");
        exit;
    }
    
    $stmt->close();
    
    // Inserção de novo usuário
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, senha) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $nome, $senha_hash);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['sucesso'] = "Usuário cadastrado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar usuário. Tente novamente.";
    }
    
    $stmt->close();
}

$conexao->close();

// Redirecionar para a página de usuários
header("Location: usuarios.php");
exit;
?>