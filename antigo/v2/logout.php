<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpar todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header("Location: login.php");
exit;
?>