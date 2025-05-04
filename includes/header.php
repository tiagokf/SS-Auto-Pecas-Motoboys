<?php
// includes/header.php
require_once 'db.php';
requireLogin();
$user = getLoggedUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            z-index: 40;
        }
        .main-content {
            margin-left: 16rem;
            /* 256px ou 16rem para corresponder à largura da sidebar */
        }
        <blade media|%20(max-width%3A%20768px)%20%7B%0D>.sidebar {
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
        }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-0 left-0 z-50 m-4">
        <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
    <div class="flex h-screen">