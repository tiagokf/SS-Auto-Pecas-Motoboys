<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();
$pdo = $db->connect();

// Lógica para obter usuários
$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
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
                    <h1 class="title has-text-white">Usuários</h1>
                    <button class="button is-link mb-4" id="btn-add-user">
                        <i class="fas fa-plus"></i> Adicionar Usuário
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
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id']); ?></td>
                                    <td><?= htmlspecialchars($usuario['nome']); ?></td>
                                    <td>
                                        <button class="button is-small is-info btn-edit-user" 
                                                data-id="<?= $usuario['id']; ?>" 
                                                data-nome="<?= htmlspecialchars($usuario['nome']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="../controllers/usuariosController.php?action=delete&id=<?= $usuario['id']; ?>" 
                                           class="button is-small is-danger" onclick="return confirm('Deseja excluir este usuário?')">
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

    <!-- Modal de Adicionar Usuário -->
    <div class="modal" id="modal-add-user">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Adicionar Usuário</p>
                <button class="delete" aria-label="close" id="close-add-user"></button>
            </header>
            <form action="../controllers/usuariosController.php?action=add" method="POST">
                <section class="modal-card-body">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Senha</label>
                        <div class="control">
                            <input class="input" type="password" name="senha" required>
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Salvar</button>
                    <button type="button" class="button" id="cancel-add-user">Cancelar</button>
                </footer>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Usuário -->
    <div class="modal" id="modal-edit-user">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Editar Usuário</p>
                <button class="delete" aria-label="close" id="close-edit-user"></button>
            </header>
            <form action="../controllers/usuariosController.php?action=edit" method="POST">
                <section class="modal-card-body">
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" id="edit-user-nome" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Senha (Deixe em branco para não alterar)</label>
                        <div class="control">
                            <input class="input" type="password" name="senha">
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Salvar</button>
                    <button type="button" class="button" id="cancel-edit-user">Cancelar</button>
                </footer>
            </form>
        </div>
    </div>

    <script src="../assets/js/usuarios.js"></script>
</body>
</html>
