<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: views/login.php");
    exit();
}

// Redireciona para o dashboard
header("Location: views/dashboard.php");
exit();
