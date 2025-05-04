<?php
// api/auth.php
require_once '../includes/db.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método não permitido');
}

// Obter a ação a ser executada
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'login':
        realizarLogin();
        break;
    case 'logout':
        realizarLogout();
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        exit('Ação inválida');
}

// Função para realizar login
function realizarLogin() {
    // Validar dados de entrada
    if (!isset($_POST['nome']) || trim($_POST['nome']) === '' || 
        !isset($_POST['senha']) || trim($_POST['senha']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Nome de usuário e senha são obrigatórios');
    }
    
    $nome = sanitize($_POST['nome']);
    $senha = sanitize($_POST['senha']);
    
    // Consulta para verificar o usuário
    $sql = "SELECT id, nome, senha FROM usuarios WHERE nome = '$nome'";
    $user = fetchOne($sql);
    
    if ($user && $senha === $user['senha']) {
        // Login bem-sucedido
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        
        // Redirecionamento após sucesso
        header('Location: ../index.php');
        exit;
    } else {
        // Redirecionamento com erro
        header('Location: ../login.php?error=invalid');
        exit;
    }
}

// Função para realizar logout
function realizarLogout() {
    session_start();
    
    // Destruir todas as variáveis de sessão
    $_SESSION = array();
    
    // Se quiser destruir completamente a sessão, apague também o cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Finalmente, destruir a sessão
    session_destroy();
    
    // Redirecionamento após sucesso
    header('Location: ../login.php');
    exit;
}