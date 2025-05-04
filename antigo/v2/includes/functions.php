<?php
require_once __DIR__ . '/../config/database.php';

// Função para limpar entradas de dados
function limparDados($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

// Função para verificar se o usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Função para obter lista de motoboys
function obterMotoboys() {
    $conexao = conectarDB();
    $sql = "SELECT * FROM motoboys ORDER BY nome ASC";
    $resultado = $conexao->query($sql);
    
    $motoboys = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $motoboys[] = $row;
        }
    }
    
    $conexao->close();
    return $motoboys;
}

// Função para obter lista de usuários
function obterUsuarios() {
    $conexao = conectarDB();
    $sql = "SELECT * FROM usuarios ORDER BY nome ASC";
    $resultado = $conexao->query($sql);
    
    $usuarios = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    
    $conexao->close();
    return $usuarios;
}

// Função para obter movimentos
function obterMovimentos($limit = 20) {
    $conexao = conectarDB();
    $sql = "SELECT m.*, mb.nome as motoboy_nome, us.nome as usuario_saida, ur.nome as usuario_retorno 
            FROM movimentos m
            JOIN motoboys mb ON m.motoboy_id = mb.id
            JOIN usuarios us ON m.usuario_saida_id = us.id
            LEFT JOIN usuarios ur ON m.usuario_retorno_id = ur.id
            ORDER BY m.data_hora_saida DESC
            LIMIT ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $movimentos = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $movimentos[] = $row;
        }
    }
    
    $stmt->close();
    $conexao->close();
    return $movimentos;
}

// Função para registrar saída de motoboy
function registrarSaida($motoboy_id, $usuario_id, $descricao) {
    $conexao = conectarDB();
    $sql = "INSERT INTO movimentos (motoboy_id, usuario_saida_id, data_hora_saida, descricao) 
            VALUES (?, ?, NOW(), ?)";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iis", $motoboy_id, $usuario_id, $descricao);
    $resultado = $stmt->execute();
    
    $stmt->close();
    $conexao->close();
    return $resultado;
}

// Função para registrar retorno de motoboy
function registrarRetorno($movimento_id, $usuario_id) {
    $conexao = conectarDB();
    $sql = "UPDATE movimentos SET usuario_retorno_id = ?, data_hora_retorno = NOW() 
            WHERE id = ? AND data_hora_retorno IS NULL";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $movimento_id);
    $resultado = $stmt->execute();
    
    $stmt->close();
    $conexao->close();
    return $resultado;
}
?>