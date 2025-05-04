<?php
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_admin']) || !$_SESSION['usuario_admin']) {
    header("Location: index.php");
    exit;
}

// Obter lista de usuários
$conexao = conectarDB();
$sql = "SELECT id, nome FROM usuarios ORDER BY nome ASC";
$resultado = $conexao->query($sql);

// Processar formulário de adição/edição
$erro = '';
$sucesso = '';
$usuario_edicao = null;

// Caso de edição: carregar dados do usuário
if (isset($_GET['editar']) && !empty($_GET['editar'])) {
    $id = intval($_GET['editar']);
    
    // Não carregamos a senha por segurança
    $sql = "SELECT id, nome FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario_edicao = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$usuario_edicao) {
        $erro = "Usuário não encontrado.";
    }
}

// Incluir cabeçalho
include 'includes/header.php';
?>

<div class="ui grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="user circle icon"></i>
            <div class="content">Usuários</div>
        </h1>
    </div>
    <div class="six wide column right aligned">
        <a href="index.php" class="ui basic button">
            <i class="arrow left icon"></i> Voltar
        </a>
        <button class="ui blue button" onclick="abrirModal('modal-usuario')">
            <i class="plus icon"></i> Novo Usuário
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

<!-- Lista de Usuários -->
<table class="ui celled table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado->num_rows === 0): ?>
        <tr>
            <td colspan="3" class="center aligned">Nenhum usuário cadastrado.</td>
        </tr>
        <?php else: ?>
        <?php while ($usuario = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo $usuario['id']; ?></td>
            <td><?php echo $usuario['nome']; ?></td>
            <td class="center aligned">
                <div class="action-buttons">
                    <a href="?editar=<?php echo $usuario['id']; ?>" class="ui tiny blue button">
                        <i class="edit icon"></i> Editar
                    </a>
                    <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="ui tiny red button"
                        onclick="return confirm('Tem certeza que deseja excluir este usuário? Essa ação não pode ser desfeita.')">
                        <i class="trash icon"></i> Excluir
                    </a>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal de Adição/Edição de Usuário -->
<div class="ui modal" id="modal-usuario">
    <div class="header">
        <?php if ($usuario_edicao): ?>
        Editar Usuário
        <?php else: ?>
        Novo Usuário
        <?php endif; ?>
    </div>
    <div class="content">
        <form id="modal-form" class="ui form" method="post" action="processar_usuario.php">
            <?php if ($usuario_edicao): ?>
            <input type="hidden" name="id" value="<?php echo $usuario_edicao['id']; ?>">
            <?php endif; ?>

            <div class="field">
                <label>Nome</label>
                <input type="text" name="nome" placeholder="Nome de usuário" required
                    value="<?php echo $usuario_edicao ? $usuario_edicao['nome'] : ''; ?>">
            </div>

            <div class="field">
                <label><?php echo $usuario_edicao ? 'Nova Senha (deixe em branco para não alterar)' : 'Senha'; ?></label>
                <input type="password" name="senha" placeholder="Senha"
                    <?php echo $usuario_edicao ? '' : 'required'; ?>>
            </div>

            <?php if ($usuario_edicao): ?>
            <div class="field">
                <label>Confirme a Nova Senha</label>
                <input type="password" name="confirma_senha" placeholder="Confirme a senha">
            </div>
            <?php else: ?>
            <div class="field">
                <label>Confirme a Senha</label>
                <input type="password" name="confirma_senha" placeholder="Confirme a senha" required>
            </div>
            <?php endif; ?>

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

<?php if ($usuario_edicao): ?>
<script>
    $(document).ready(function () {
        abrirModal('modal-usuario');
    });
</script>
<?php endif; ?>

<?php
$conexao->close();
include 'includes/footer.php';
?>