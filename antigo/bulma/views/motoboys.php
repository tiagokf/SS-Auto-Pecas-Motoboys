<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();
$pdo = $db->connect();

// Lógica para obter motoboys
$stmt = $pdo->query("SELECT * FROM motoboys");
$motoboys = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motoboys</title>
    <link rel="stylesheet" href="../assets/bulma/css/bulma.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script defer src="../assets/js/main.js"></script>
</head>
<body>
    <div class="columns">
        <?php include 'sidebar.php'; ?>
        <div class="column main-content">
            <section class="section">
                <div class="container">
                    <h1 class="title has-text-white">Motoboys</h1>
                    <button class="button is-link mb-4" id="btn-add-motoboy">
                        <i class="fas fa-plus"></i> Adicionar Motoboy
                    </button>
                    <table class="table is-striped is-hoverable is-fullwidth">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($motoboys as $motoboy): ?>
                                <tr>
                                    <td><?= htmlspecialchars($motoboy['id']); ?></td>
                                    <td><?= htmlspecialchars($motoboy['nome']); ?></td>
                                    <td>
                                        <button class="button is-small is-info btn-edit-motoboy" 
                                                data-id="<?= $motoboy['id']; ?>" 
                                                data-nome="<?= htmlspecialchars($motoboy['nome']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="../controllers/motoboysController.php?action=delete&id=<?= $motoboy['id']; ?>" 
                                           class="button is-small is-danger" onclick="return confirm('Deseja excluir este motoboy?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal de Adicionar Motoboy -->
    <div class="modal" id="modal-add-motoboy">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Adicionar Motoboy</p>
                <button class="delete" aria-label="close" id="close-add-motoboy"></button>
            </header>
            <form action="../controllers/motoboysController.php?action=add" method="POST">
                <section class="modal-card-body">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" required>
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Salvar</button>
                    <button type="button" class="button" id="cancel-add-motoboy">Cancelar</button>
                </footer>
            </form>
        </div>
    </div>

    <script src="../assets/js/motoboys.js"></script>
</body>
</html>
