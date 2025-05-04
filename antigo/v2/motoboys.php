<?php
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obter lista de motoboys
$conexao = conectarDB();
$sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM movimentos WHERE motoboy_id = m.id) as total_movimentos,
        (SELECT COUNT(*) FROM movimentos WHERE motoboy_id = m.id AND data_hora_retorno IS NULL) as movimentos_pendentes
        FROM motoboys m
        ORDER BY m.nome ASC";
$resultado = $conexao->query($sql);

// Processar formulário de adição/edição
$erro = '';
$sucesso = '';
$motoboy_edicao = null;

// Caso de edição: carregar dados do motoboy
if (isset($_GET['editar']) && !empty($_GET['editar'])) {
    $id = intval($_GET['editar']);
    
    $sql = "SELECT * FROM motoboys WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $motoboy_edicao = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$motoboy_edicao) {
        $erro = "Motoboy não encontrado.";
    }
}

// Incluir cabeçalho
include 'includes/header.php';
?>

<div class="ui grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="users icon"></i>
            <div class="content">Motoboys</div>
        </h1>
    </div>
    <div class="six wide column right aligned">
        <a href="index.php" class="ui basic button">
            <i class="arrow left icon"></i> Voltar
        </a>
        <button class="ui blue button" onclick="abrirModal('modal-motoboy')">
            <i class="plus icon"></i> Novo Motoboy
        </button>
    </div>
</div>

<!-- Mensagens de sucesso ou erro -->
<?php if (isset($_SESSION['sucesso'])): ?>
<div class="ui success message">
    <i class="close icon"></i>
    <div class="content">
        <p><?php echo $_SESSION['sucesso']; ?></p>
    </div>
</div>
<?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['erro'])): ?>
<div class="ui negative message">
    <i class="close icon"></i>
    <div class="content">
        <p><?php echo $_SESSION['erro']; ?></p>
    </div>
</div>
<?php unset($_SESSION['erro']); ?>
<?php endif; ?>

<!-- Lista de Motoboys -->
<table class="ui celled table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Total Movimentos</th>
            <th>Movimentos Pendentes</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado->num_rows === 0): ?>
        <tr>
            <td colspan="5" class="center aligned">Nenhum motoboy cadastrado.</td>
        </tr>
        <?php else: ?>
        <?php while ($motoboy = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo $motoboy['id']; ?></td>
            <td><?php echo $motoboy['nome']; ?></td>
            <td><?php echo $motoboy['total_movimentos']; ?></td>
            <td>
                <?php if ($motoboy['movimentos_pendentes'] > 0): ?>
                <span class="ui yellow label"><?php echo $motoboy['movimentos_pendentes']; ?></span>
                <?php else: ?>
                0
                <?php endif; ?>
            </td>
            <td class="center aligned">
                <div class="action-buttons">
                    <a href="?editar=<?php echo $motoboy['id']; ?>" class="ui tiny blue button">
                        <i class="edit icon"></i> Editar
                    </a>
                    <a href="excluir_motoboy.php?id=<?php echo $motoboy['id']; ?>" class="ui tiny red button"
                        onclick="return confirm('Tem certeza que deseja excluir este motoboy? Essa ação não pode ser desfeita.')">
                        <i class="trash icon"></i> Excluir
                    </a>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal de Adição/Edição de Motoboy -->
<div class="ui modal" id="modal-motoboy">
    <div class="header">
        <?php if ($motoboy_edicao): ?>
        Editar Motoboy
        <?php else: ?>
        Novo Motoboy
        <?php endif; ?>
    </div>
    <div class="content">
        <form class="ui form" method="post" action="processar_motoboy.php">
            <?php if ($motoboy_edicao): ?>
            <input type="hidden" name="id" value="<?php echo $motoboy_edicao['id']; ?>">
            <?php endif; ?>

            <div class="field">
                <label>Nome</label>
                <input type="text" name="nome" placeholder="Nome do motoboy" required
                    value="<?php echo $motoboy_edicao ? $motoboy_edicao['nome'] : ''; ?>">
            </div>

            <div class="ui error message"></div>
        </form>
    </div>
    <div class="actions">
        <div class="ui black deny button">
            Cancelar
        </div>
        <button type="submit" form="modal-form" class="ui positive right labeled icon button">
            Salvar
            <i class="checkmark icon"></i>
        </button>
    </div>
</div>

<?php if ($motoboy_edicao): ?>
<script>
    $(document).ready(function () {
        abrirModal('modal-motoboy');
    });
</script>
<?php endif; ?>

<?php
$conexao->close();
include 'includes/footer.php';
?>