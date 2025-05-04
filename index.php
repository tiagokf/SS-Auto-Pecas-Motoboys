<?php
// index.php (Dashboard)
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Consultas para o dashboard
$totalMotoboys = fetchOne("SELECT COUNT(*) as total FROM motoboys")['total'];
$totalUsuarios = fetchOne("SELECT COUNT(*) as total FROM usuarios")['total'];
$totalMovimentos = fetchOne("SELECT COUNT(*) as total FROM movimentos")['total'];
$movimentosAbertos = fetchOne("SELECT COUNT(*) as total FROM movimentos WHERE data_hora_retorno IS NULL")['total'];

// Movimentos recentes
$recentMovimentos = fetchAll("
    SELECT m.id, mb.nome as motoboy, u1.nome as usuario_saida, 
           m.data_hora_saida, m.data_hora_retorno, m.descricao
    FROM movimentos m
    JOIN motoboys mb ON m.motoboy_id = mb.id
    JOIN usuarios u1 ON m.usuario_saida_id = u1.id
    LEFT JOIN usuarios u2 ON m.usuario_retorno_id = u2.id
    ORDER BY m.data_hora_saida DESC
    LIMIT 5
");
?>

<!-- Conteúdo Principal -->
<div class="overflow-x-hidden overflow-y-auto bg-gray-100 flex-1">
    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>

        <!-- Cards de Estatísticas -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-600 bg-opacity-75 text-white">
                        <i class="fas fa-motorcycle text-xl"></i>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700"><?php echo $totalMotoboys; ?></h4>
                        <div class="text-gray-500">Motoboys</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-600 bg-opacity-75 text-white">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700"><?php echo $totalUsuarios; ?></h4>
                        <div class="text-gray-500">Usuários</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-600 bg-opacity-75 text-white">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700"><?php echo $totalMovimentos; ?></h4>
                        <div class="text-gray-500">Total Movimentos</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-600 bg-opacity-75 text-white">
                        <i class="fas fa-hourglass-half text-xl"></i>
                    </div>
                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700"><?php echo $movimentosAbertos; ?></h4>
                        <div class="text-gray-500">Movimentos Abertos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movimentos Recentes -->
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h4 class="text-gray-600 text-xl font-medium">Movimentos Recentes</h4>
                <a href="<?php echo SITE_URL; ?>/movimentos/index.php"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    Ver Todos
                </a>
            </div>

            <div class="mt-4">
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Motoboy
                                </th>
                                <th
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Saída
                                </th>
                                <th
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($recentMovimentos)): ?>
                            <tr>
                                <td colspan="4" class="py-4 px-6 text-center text-gray-500">
                                    Nenhum movimento registrado.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($recentMovimentos as $movimento): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <?php echo htmlspecialchars($movimento['motoboy']); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php echo htmlspecialchars($movimento['descricao']); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_saida'])); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ($movimento['data_hora_retorno']): ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Finalizado
                                    </span>
                                    <?php else: ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Em Andamento
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?><?php