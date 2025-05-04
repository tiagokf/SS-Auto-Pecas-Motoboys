<?php
// includes/config.php
session_start();

// Definir o fuso horário para Brasil (São Paulo)
date_default_timezone_set('America/Sao_Paulo');

// Detectar ambiente (desenvolvimento ou produção)
$is_dev_environment = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');

// Configurações do Banco de Dados
if ($is_dev_environment) {
    // Ambiente de Desenvolvimento
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // Usuário padrão do MySQL local
    define('DB_PASS', 'root'); // Senha para ambiente local
    define('DB_NAME', 'sspecas'); // Nome do banco local
    define('SITE_URL', 'http://localhost:8063');
} else {
    // Ambiente de Produção
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u956739147_ssauto');
    define('DB_PASS', 'Ssauto2020');
    define('DB_NAME', 'u956739147_ssauto');
    define('SITE_URL', 'https://sspecasgv.com.br');
}

// Configurações da Aplicação
define('SITE_NAME', 'Sistema de Controle de Motoboys');

// Verificação de autenticação
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirecionamento se não estiver logado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

// Função para obter dados do usuário logado
function getLoggedUser() {
    if (isLoggedIn()) {
        global $conn;
        $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}