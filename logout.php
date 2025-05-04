<?php
// logout.php
require_once 'includes/config.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Se quiser destruir completamente a sessão, apague também o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: login.php');
exit;