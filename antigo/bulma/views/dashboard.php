<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$db = new Database();
$pdo = $db->connect();

$totalMotoboys = $pdo->query("SELECT COUNT(*) AS total FROM motoboys")->fetch(PDO::FETCH_ASSOC)['total'];
$totalMovimentos = $pdo->query("SELECT COUNT(*) AS total FROM movimentos")->fetch(PDO::FETCH_ASSOC)['total'];
$totalUsuarios = $pdo->query("SELECT COUNT(*) AS total FROM usuarios")->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/bulma/css/bulma.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script defer src="../assets/js/main.js"></script>
</head>
<body>
    <div class="columns">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Conteúdo Principal -->
        <div class="column main-content">
            <section class="section">
                <div class="container">
                    <h1 class="title has-text-white">Dashboard</h1>
                    <div class="columns is-multiline">
                        <!-- Card 1 -->
                        <div class="column is-4">
                            <div class="box has-text-centered">
                                <p class="title"><?= htmlspecialchars($totalMotoboys); ?></p>
                                <p>Motoboys</p>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="column is-4">
                            <div class="box has-text-centered">
                                <p class="title"><?= htmlspecialchars($totalMovimentos); ?></p>
                                <p>Movimentos</p>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="column is-4">
                            <div class="box has-text-centered">
                                <p class="title"><?= htmlspecialchars($totalUsuarios); ?></p>
                                <p>Usuários</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
