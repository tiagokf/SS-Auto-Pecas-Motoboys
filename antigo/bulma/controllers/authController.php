<?php
session_start();

// Habilita exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Usuario.php';
require_once '../config/database.php';

$action = $_GET['action'] ?? '';

try {
    // Inicializa a conexão com o banco de dados
    $db = new Database();
    $pdo = $db->connect();

    $usuario = new Usuario($pdo);

    switch ($action) {
        case 'login':
            $nome = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if ($usuario->login($nome, $senha)) {
                $_SESSION['user_id'] = $usuario->getUserId();
                $_SESSION['user_name'] = $usuario->getUserName();

                header("Location: ../views/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = 'Usuário ou senha inválidos';
                header("Location: ../views/login.php");
                exit();
            }
            break;

        case 'logout':
            session_unset();
            session_destroy();
            header("Location: ../views/login.php");
            exit();

        default:
            header("Location: ../views/login.php");
            exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Erro ao processar a solicitação: ' . $e->getMessage();
    header("Location: ../views/login.php");
    exit();
}
