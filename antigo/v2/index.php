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

// Obter estatísticas para o dashboard
$conexao = conectarDB();

// Total de motoboys
$sql = "SELECT COUNT(*) as total FROM motoboys";
$resultado = $conexao->query($sql);
$totalMotoboys = $resultado->fetch_assoc()['total'];

// Total de movimentos
$sql = "SELECT COUNT(*) as total FROM movimentos";
$resultado = $conexao->query($sql);
$totalMovimentos = $resultado->fetch_assoc()['total'];

// Movimentos pendentes (sem retorno)
$sql = "SELECT COUNT(*) as total FROM movimentos WHERE data_hora_retorno IS NULL";
$resultado = $conexao->query($sql);
$movimentosPendentes = $resultado->fetch_assoc()['total'];

// Movimentos de hoje
$sql = "SELECT COUNT(*) as total FROM movimentos WHERE DATE(data_hora_saida) = CURDATE()";
$resultado = $conexao->query($sql);
$movimentosHoje = $resultado->fetch_assoc()['total'];

// Últimos movimentos
$sql = "SELECT m.*, mb.nome as motoboy_nome, us.nome as usuario_saida, ur.nome as usuario_retorno 
        FROM movimentos m
        JOIN motoboys mb ON m.motoboy_id = mb.id
        JOIN usuarios us ON m.usuario_saida_id = us.id
        LEFT JOIN usuarios ur ON m.usuario_retorno_id = ur.id
        ORDER BY m.data_hora_saida DESC
        LIMIT 5";
$resultado = $conexao->query($sql);
$ultimosMovimentos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $ultimosMovimentos[] = $row;
    }
}

$conexao->close();

// Incluir cabeçalho
include 'includes/header.php';
?>

<h1 class="ui header">
    <i class="dashboard icon"></i>
    <div class="content">Dashboard</div>
</h1>

<div class="ui divider"></div>

<!-- Estatísticas -->
<div class="ui four statistics dashboard-stats">
    <div class="statistic">
        <div class="value">
            <i class="users icon"></i> <?php echo $totalMotoboys; ?>
        </div>
        <div class="label">Motoboys</div>
    </div>
    <div class="statistic">
        <div class="value">
            <i class="exchange icon"></i> <?php echo $totalMovimentos; ?>
        </div>
        <div class="label">Total Movimentos</div>
    </div>
    <div class="statistic">
        <div class="value">
            <i class="clock icon"></i> <?php echo $movimentosPendentes; ?>
        </div>
        <div class="label">Pendentes</div>
    </div>
    <div class="statistic">
        <div class="value">
            <i class="calendar day icon"></i> <?php echo $movimentosHoje; ?>
        </div>
        <div class="label">Hoje</div>
    </div>
</div>

<!-- Registrar Novo Movimento -->
<div class="ui blue segment">
    <h3 class="ui header">
        <i class="plus icon"></i>
        <div class="content">Registrar Saída</div>
    </h3>

    <form class="ui form" method="post" action="processar_saida.php" onsubmit="return validarFormMovimento()">
        <div class="fields">
            <div class="five wide field">
                <label>Motoboy</label>
                <select name="motoboy_id" id="motoboy_id" class="ui dropdown">
                    <option value="">Selecione</option>
                    <?php 
                    $motoboys = obterMotoboys();
                    foreach ($motoboys as $motoboy) {
                        echo "<option value=\"{$motoboy['id']}\">{$motoboy['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="eight wide field">
                <label>Descrição</label>
                <input type="text" name="descricao" id="descricao" placeholder="Informe o destino ou finalidade">
            </div>
            <div class="three wide field">
                <label>&nbsp;</label>
                <button type="submit" class="ui blue fluid submit button">Registrar</button>
            </div>
        </div>
    </form>
</div>

<!-- Últimos Movimentos -->
<h3 class="ui header">
    <i class="history icon"></i>
    <div class="content">Últimos Movimentos</div>
</h3>

<table class="ui celled table">
    <thead>
        <tr>
            <th>Motoboy</th>
            <th>Descrição</th>
            <th>Saída</th>
            <th>Retorno</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($ultimosMovimentos)): ?>
        <tr>
            <td colspan="5" class="center aligned">Nenhum movimento registrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($ultimosMovimentos as $movimento): ?>
        <?php $pendente = empty($movimento['data_hora_retorno']); ?>
        <tr class="<?php echo $pendente ? 'movimento-pendente' : ''; ?>">
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
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="ui center aligned basic segment">
    <a href="movimentos.php" class="ui blue button">
        <i class="list icon"></i> Ver Todos os Movimentos
    </a>
</div>

<?php include 'includes/footer.php'; ?>