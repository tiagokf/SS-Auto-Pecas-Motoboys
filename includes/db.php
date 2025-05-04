<?php
// includes/db.php
require_once 'config.php';

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Função para escapar dados de entrada
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string($data);
}

// Função para executar consultas
function query($sql) {
    global $conn;
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Erro na consulta: " . $conn->error);
    }
    
    return $result;
}

// Função para obter um único registro
function fetchOne($sql) {
    $result = query($sql);
    return $result->fetch_assoc();
}

// Função para obter múltiplos registros
function fetchAll($sql) {
    $result = query($sql);
    $rows = [];
    
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}