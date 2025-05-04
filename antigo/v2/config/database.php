<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Altere conforme necessário
define('DB_PASS', 'root');                   // Defina uma senha segura
define('DB_NAME', 'sspecas');  
define('DB_PORT', '3306');

// Função auxiliar para conexão com o banco de dados
function conectarDB() {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }
    
    $conexao->set_charset("utf8mb4");
    return $conexao;
}
?>