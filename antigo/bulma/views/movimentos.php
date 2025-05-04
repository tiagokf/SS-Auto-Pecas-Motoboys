<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();
$pdo = $db->connect();

// Quantidade de registros por página (padrão: 100)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$offset = ($page - 1) * $limit;

// Obter total de registros com busca
$totalQuery = "SELECT COUNT(*) AS total FROM movimentos 
    LEFT JOIN motoboys ON movimentos.motoboy_id = motoboys.id
    LEFT JOIN usuarios u1 ON movimentos.usuario_saida_id = u1.id
    LEFT JOIN usuarios u2 ON movimentos.usuario_retorno_id = u2.id
    WHERE movimentos.descricao LIKE :search
       OR motoboys.nome LIKE :search
       OR u1.nome LIKE :search
       OR u2.nome LIKE :search";

$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute(['search' => "%$search%"]);
$totalMovimentos = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obter movimentos com busca e paginação
$query = "
    SELECT movimentos.id, movimentos.descricao, movimentos.data_hora_saida, movimentos.data_hora_retorno,
           motoboys.nome AS motoboy, u1.nome AS usuario_saida, u2.nome AS usuario_retorno
    FROM movimentos
    LEFT JOIN motoboys ON movimentos.motoboy_id = motoboys.id
    LEFT JOIN usuarios u1 ON movimentos.usuario_saida_id = u1.id
    LEFT JOIN usuarios u2 ON movimentos.usuario_retorno_id = u2.id
    WHERE movimentos.descricao LIKE :search
       OR motoboys.nome LIKE :search
       OR u1.nome LIKE :search
       OR u2.nome LIKE :search
    LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$movimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter motoboys e usuários para os selects
$motoboys = $pdo->query("SELECT id, nome FROM motoboys")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentos</title>
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
                    <h1 class="title">Movimentos</h1>
                    <div class="is-flex is-justify-content-space-between is-align-items-center mb-4">
                        <div>
                            <button class="button is-link" id="btn-add-movimento">
                                <i class="fas fa-plus"></i> Adicionar Movimento
                            </button>
                            <div class="select is-small" style="margin-left: 1rem; display: inline-block;">
                                <select id="records-per-page">
                                    <option value="50" <?= $limit === 50 ? 'selected' : ''; ?>>50</option>
                                    <option value="100" <?= $limit === 100 ? 'selected' : ''; ?>>100</option>
                                    <option value="200" <?= $limit === 200 ? 'selected' : ''; ?>>200</option>
                                </select>
                            </div>
                        </div>
                        <form action="" method="GET">
                            <div class="field has-addons">
                                <div class="control">
                                    <input class="input" type="text" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search); ?>">
                                </div>
                                <div class="control">
                                    <button class="button is-info">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="table is-striped is-hoverable is-fullwidth">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Motoboy</th>
                                    <th>Usuário Saída</th>
                                    <th>Data/Hora Saída</th>
                                    <th>Usuário Retorno</th>
                                    <th>Data/Hora Retorno</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimentos as $movimento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($movimento['id']); ?></td>
                                        <td><?= htmlspecialchars($movimento['descricao']); ?></td>
                                        <td><?= htmlspecialchars($movimento['motoboy']); ?></td>
                                        <td><?= htmlspecialchars($movimento['usuario_saida']); ?></td>
                                        <td><?= htmlspecialchars($movimento['data_hora_saida']); ?></td>
                                        <td><?= htmlspecialchars($movimento['usuario_retorno'] ?: '-'); ?></td>
                                        <td><?= htmlspecialchars($movimento['data_hora_retorno'] ?: '-'); ?></td>
                                        <td>
                                            <button class="button is-small is-info btn-edit-movimento" 
                                                    data-id="<?= $movimento['id']; ?>" 
                                                    data-descricao="<?= htmlspecialchars($movimento['descricao']); ?>"
                                                    data-motoboy="<?= $movimento['motoboy_id']; ?>"
                                                    data-usuario_saida="<?= $movimento['usuario_saida_id']; ?>"
                                                    data-usuario_retorno="<?= $movimento['usuario_retorno_id']; ?>"
                                                    data-data_hora_saida="<?= $movimento['data_hora_saida']; ?>"
                                                    data-data_hora_retorno="<?= $movimento['data_hora_retorno']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="../controllers/movimentosController.php?action=delete&id=<?= $movimento['id']; ?>" 
                                               class="button is-small is-danger" onclick="return confirm('Deseja excluir este movimento?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <nav class="pagination is-centered mt-4" role="navigation" aria-label="pagination">
                        <a class="pagination-previous" href="?page=<?= max(1, $page - 1); ?>&limit=<?= $limit; ?>&search=<?= htmlspecialchars($search); ?>">Anterior</a>
                        <a class="pagination-next" href="?page=<?= $page + 1; ?>&limit=<?= $limit; ?>&search=<?= htmlspecialchars($search); ?>">Próximo</a>
                    </nav>
                </div>
            </section>
        </div>
    </div>

    <!-- Modais de Adicionar e Editar -->
    <?php include 'modais/movimentosModais.php'; ?>

    <script src="../assets/js/movimentos.js"></script>
    <script>
        // Alterar número de registros por página
        document.getElementById('records-per-page').addEventListener('change', function () {
            const limit = this.value;
            window.location.href = `?page=1&limit=${limit}&search=<?= htmlspecialchars($search); ?>`;
        });
    </script>
</body>
</html>
