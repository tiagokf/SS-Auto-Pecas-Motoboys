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

// Definir limite de registros por página
$porPagina = 20;

// Obter página atual
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $porPagina;

// Obter filtros
$filtroMotoboy = isset($_GET['motoboy']) ? intval($_GET['motoboy']) : 0;
$filtroPendente = isset($_GET['pendente']) ? ($_GET['pendente'] === '1') : false;
$filtroData = isset($_GET['data']) ? $_GET['data'] : '';

// Consulta base
$sqlBase = "FROM movimentos m
           JOIN motoboys mb ON m.motoboy_id = mb.id
           JOIN usuarios us ON m.usuario_saida_id = us.id
           LEFT JOIN usuarios ur ON m.usuario_retorno_id = ur.id
           WHERE 1=1";

// Aplicar filtros
$params = [];
$tiposParams = "";

if ($filtroMotoboy > 0) {
    $sqlBase .= " AND mb.id = ?";
    $params[] = $filtroMotoboy;
    $tiposParams .= "i";
}

if ($filtroPendente) {
    $sqlBase .= " AND m.data_hora_retorno IS NULL";
}

if (!empty($filtroData)) {
    $dataFormatada = date('Y-m-d', strtotime(str_replace('/', '-', $filtroData)));
    $sqlBase .= " AND DATE(m.data_hora_saida) = ?";
    $params[] = $dataFormatada;
    $tiposParams .= "s";
}

// Conexão com o banco
$conexao = conectarDB();

// Contar total de registros
$sqlCount = "SELECT COUNT(*) as total " . $sqlBase;
$stmtCount = $conexao->prepare($sqlCount);

if (!empty($params)) {
    $stmtCount->bind_param($tiposParams, ...$params);
}

$stmtCount->execute();
$totalRegistros = $stmtCount->get_result()->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $porPagina);

// Obter movimentos
$sql = "SELECT m.*, mb.nome as motoboy_nome, us.nome as usuario_saida, ur.nome as usuario_retorno " . 
       $sqlBase . " ORDER BY m.data_hora_saida DESC LIMIT ?, ?";

$paramsPaginados = $params;
$paramsPaginados[] = $offset;
$paramsPaginados[] = $porPagina;
$tiposParamsPaginados = $tiposParams . "ii";

$stmt = $conexao->prepare($sql);
$stmt->bind_param($tiposParamsPaginados, ...$paramsPaginados);
$stmt->execute();
$movimentos = $stmt->get_result();

// Obter lista de motoboys para o filtro
$sqlMotoboys = "SELECT id, nome FROM motoboys ORDER BY nome ASC";
$motoboys = $conexao->query($sqlMotoboys);

// Incluir cabeçalho
include 'includes/header.php';
?>

<div class="ui grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="exchange icon"></i>
            <div class="content">Movimentos</div>
        </h1>
    </div>
    <div class="six wide column right aligned">
        <a href="index.php" class="ui basic button">
            <i class="arrow left icon"></i> Voltar
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="ui blue segment">
    <form class="ui form" method="get" action="movimentos.php">
        <div class="fields">
            <div class="four wide field">
                <label>Motoboy</label>
                <select name="motoboy" class="ui dropdown">
                    <option value="0">Todos</option>
                    <?php while ($motoboy = $motoboys->fetch_assoc()): ?>
                    <option value="<?php echo $motoboy['id']; ?>"
                        <?php echo ($filtroMotoboy == $motoboy['id']) ? 'selected' : ''; ?>>
                        <?php echo $motoboy['nome']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="four wide field">
                <label>Data</label>
                <div class="ui calendar" id="data-filtro">
                    <div class="ui input left icon">
                        <i class="calendar icon"></i>
                        <input type="text" name="data" placeholder="DD/MM/AAAA" value="<?php echo $filtroData; ?>">
                    </div>
                </div>
            </div>
            <div class="four wide field">
                <label>Status</label>
                <select name="pendente" class="ui dropdown">
                    <option value="">Todos</option>
                    <option value="1" <?php echo $filtroPendente ? 'selected' : ''; ?>>Pendentes</option>
                    <option value="0"
                        <?php echo ($filtroPendente === false && isset($_GET['pendente'])) ? 'selected' : ''; ?>>
                        Concluídos</option>
                </select>
            </div>
            <div class="four wide field">
                <label>&nbsp;</label>
                <button type="submit" class="ui blue fluid button">Filtrar</button>
            </div>
        </div>
    </form>
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

<!-- Lista de Movimentos -->
<table class="ui celled table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Motoboy</th>
            <th>Descrição</th>
            <th>Saída</th>
            <th>Retorno</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($movimentos->num_rows === 0): ?>
        <tr>
            <td colspan="6" class="center aligned">Nenhum movimento encontrado.</td>
        </tr>
        <?php else: ?>
        <?php while ($movimento = $movimentos->fetch_assoc()): ?>
        <?php $pendente = empty($movimento['data_hora_retorno']); ?>
        <tr class="<?php echo $pendente ? 'movimento-pendente' : ''; ?>">
            <td><?php echo $movimento['id']; ?></td>
            <td><?php echo $movimento['motoboy_nome']; ?></td>
            <td><?php echo $movimento['descricao']; ?></td>
            <td>
                <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_saida'])); ?>
                <br>
                <small>Por: <?php echo $movimento['usuario_saida']; ?></small>
            </td>
            <td>
                <?php if ($pendente): ?>
                <span class="ui yellow label">Pendente</span>
                <?php else: ?>
                <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_retorno'])); ?>
                <br>
                <small>Por: <?php echo $movimento['usuario_retorno']; ?></small>
                <?php endif; ?>
            </td>
            <td class="center aligned">
                <?php if ($pendente): ?>
                <a href="processar_retorno.php?id=<?php echo $movimento['id']; ?>" class="ui tiny green button">
                    <i class="check icon"></i> Registrar Retorno
                </a>
                <?php else: ?>
                <button class="ui tiny disabled button">Concluído</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Paginação -->
<?php if ($totalPaginas > 1): ?>
<div class="ui center aligned basic segment">
    <div class="ui pagination menu">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a class="<?php echo ($i == $pagina) ? 'active' : ''; ?> item"
            href="movimentos.php?pagina=<?php echo $i; ?>&motoboy=<?php echo $filtroMotoboy; ?>&pendente=<?php echo $filtroPendente ? '1' : ''; ?>&data=<?php echo $filtroData; ?>">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<?php
// Fechar conexões
$stmt->close();
$stmtCount->close();
$conexao->close();

include 'includes/footer.php';
?>