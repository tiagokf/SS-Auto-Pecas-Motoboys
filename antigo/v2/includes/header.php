<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Motoboys</title>
    <!-- Semantic UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="ui container">
        <div class="ui blue inverted menu">
            <div class="header item">
                <i class="motorcycle icon"></i> Sistema de Motoboys
            </div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="index.php" class="item">
                <i class="home icon"></i> Início
            </a>
            <a href="movimentos.php" class="item">
                <i class="exchange icon"></i> Movimentos
            </a>
            <a href="motoboys.php" class="item">
                <i class="users icon"></i> Motoboys
            </a>
            <?php if (isset($_SESSION['usuario_admin']) && $_SESSION['usuario_admin']): ?>
            <a href="usuarios.php" class="item">
                <i class="user circle icon"></i> Usuários
            </a>
            <?php endif; ?>
            <div class="right menu">
                <div class="item">
                    <i class="user icon"></i> <?php echo $_SESSION['usuario_nome']; ?>
                </div>
                <a href="logout.php" class="item">
                    <i class="sign out alternate icon"></i> Sair
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="ui segment">
            <!-- Conteúdo da página será inserido aqui -->
        </div>
    </div>
</body>

</html>