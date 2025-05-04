<?php
// movimentos/index.php
$pageTitle = 'Movimentos';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Definir datas padrão (últimos 30 dias)
$data_fim_default = date('Y-m-d');
$data_inicio_default = date('Y-m-d', strtotime('-7 days'));

// Parâmetros de filtragem
$filtro_status = isset($_GET['status']) ? $_GET['status'] : 'todos';
$filtro_motoboy = isset($_GET['motoboy']) ? (int)$_GET['motoboy'] : 0;
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : $data_inicio_default;
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : $data_fim_default;
$filtro_descricao = isset($_GET['descricao']) ? trim($_GET['descricao']) : '';

// Mensagens de feedback
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Construção da consulta SQL com filtros
$sql = "
    SELECT m.id, mb.nome as motoboy, mb.id as motoboy_id, u1.nome as usuario_saida, 
           u2.nome as usuario_retorno, m.data_hora_saida, 
           m.data_hora_retorno, m.descricao
    FROM movimentos m
    JOIN motoboys mb ON m.motoboy_id = mb.id
    JOIN usuarios u1 ON m.usuario_saida_id = u1.id
    LEFT JOIN usuarios u2 ON m.usuario_retorno_id = u2.id
    WHERE 1=1
";

// Adicionar filtros à consulta
if ($filtro_status === 'abertos') {
    $sql .= " AND m.data_hora_retorno IS NULL";
} elseif ($filtro_status === 'finalizados') {
    $sql .= " AND m.data_hora_retorno IS NOT NULL";
}

if ($filtro_motoboy > 0) {
    $sql .= " AND m.motoboy_id = $filtro_motoboy";
}

if (!empty($filtro_data_inicio)) {
    $data_inicio = date('Y-m-d 00:00:00', strtotime($filtro_data_inicio));
    $sql .= " AND m.data_hora_saida >= '$data_inicio'";
}

if (!empty($filtro_data_fim)) {
    $data_fim = date('Y-m-d 23:59:59', strtotime($filtro_data_fim));
    $sql .= " AND m.data_hora_saida <= '$data_fim'";
}

// Adicionar filtro de pesquisa por descrição (LIKE)
if (!empty($filtro_descricao)) {
    $descricao_busca = sanitize($filtro_descricao);
    $sql .= " AND m.descricao LIKE '%$descricao_busca%'";
}

$sql .= " ORDER BY m.data_hora_saida DESC";

// Buscar movimentos com os filtros aplicados
$movimentos = fetchAll($sql);

// Verificar se existem movimentos abertos
$temMovimentosAbertos = false;
$motoboysComMovimentosAbertos = [];

foreach ($movimentos as $movimento) {
    if (!$movimento['data_hora_retorno']) {
        $temMovimentosAbertos = true;
        
        // Agrupar movimentos abertos por motoboy
        if (!isset($motoboysComMovimentosAbertos[$movimento['motoboy_id']])) {
            $motoboysComMovimentosAbertos[$movimento['motoboy_id']] = [
                'nome' => $movimento['motoboy'],
                'movimentos' => []
            ];
        }
        
        $motoboysComMovimentosAbertos[$movimento['motoboy_id']]['movimentos'][] = [
            'id' => $movimento['id'],
            'descricao' => $movimento['descricao'],
            'data_hora_saida' => $movimento['data_hora_saida']
        ];
    }
}

// Buscar motoboys para o filtro e formulários
$motoboys = fetchAll("SELECT id, nome FROM motoboys ORDER BY nome");

// Buscar usuários para os formulários
$usuarios = fetchAll("SELECT id, nome FROM usuarios ORDER BY nome");
?>

<!-- Conteúdo Principal -->
<div class="overflow-x-hidden overflow-y-auto bg-gray-100 flex-1">
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">Movimentos</h3>

            <div>
                <?php if ($temMovimentosAbertos): ?>
                <button onclick="showModal('modal-retorno-lote')"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none mr-2">
                    <i class="fas fa-check-double mr-2"></i>Retorno em Lote
                </button>
                <?php endif; ?>
                <button onclick="showModal('modal-adicionar')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none">
                    <i class="fas fa-plus mr-2"></i>Registrar Saída
                </button>
            </div>
        </div>

        <!-- Mensagens de feedback -->
        <?php if ($success): ?>
        <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <p>
                        <?php 
                        switch ($success) {
                            case 'create':
                                echo 'Movimento registrado com sucesso!';
                                break;
                            case 'return':
                                echo 'Retorno registrado com sucesso!';
                                break;
                            case 'batch_return':
                                echo 'Retornos em lote registrados com sucesso!';
                                break;
                            case 'delete':
                                echo 'Movimento excluído com sucesso!';
                                break;
                            default:
                                echo 'Operação realizada com sucesso!';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="ml-3">
                    <p>
                        <?php 
                        switch ($error) {
                            case 'already_out':
                                echo 'Este motoboy já possui um movimento em aberto!';
                                break;
                            case 'not_found':
                                echo 'Movimento não encontrado!';
                                break;
                            case 'no_selection':
                                echo 'Nenhum movimento foi selecionado para retorno em lote!';
                                break;
                            default:
                                echo 'Ocorreu um erro na operação!';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="md:col-span-1 lg:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="todos" <?php echo $filtro_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="abertos" <?php echo $filtro_status === 'abertos' ? 'selected' : ''; ?>>Abertos
                        </option>
                        <option value="finalizados" <?php echo $filtro_status === 'finalizados' ? 'selected' : ''; ?>>
                            Finalizados</option>
                    </select>
                </div>

                <div class="md:col-span-1 lg:col-span-1">
                    <label for="motoboy" class="block text-sm font-medium text-gray-700 mb-1">Motoboy</label>
                    <select id="motoboy" name="motoboy" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="0">Todos</option>
                        <?php foreach ($motoboys as $motoboy): ?>
                        <option value="<?php echo $motoboy['id']; ?>"
                            <?php echo $filtro_motoboy == $motoboy['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($motoboy['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Campo de pesquisa por descrição -->
                <div class="md:col-span-1 lg:col-span-2">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar Descrição</label>
                    <input type="text" id="descricao" name="descricao" value="<?php echo htmlspecialchars($filtro_descricao); ?>"
                        placeholder="Número do pedido, cliente, etc."
                        class="w-full border border-gray-300 rounded-md p-2">
                </div>

                <div class="md:col-span-1 lg:col-span-1">
                    <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $filtro_data_inicio; ?>"
                        class="w-full border border-gray-300 rounded-md p-2">
                </div>

                <div class="md:col-span-1 lg:col-span-1">
                    <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                    <input type="date" id="data_fim" name="data_fim" value="<?php echo $filtro_data_fim; ?>"
                        class="w-full border border-gray-300 rounded-md p-2">
                </div>

                <div class="md:col-span-3 lg:col-span-6 flex justify-end">
                    <button type="button" onclick="limparFiltros()" 
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded mr-2 hover:bg-gray-100">
                        <i class="fas fa-undo mr-2"></i>Limpar Filtros
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Resultados da pesquisa -->
        <div class="mt-4">
            <p class="text-sm text-gray-600">
                <strong>Total encontrado:</strong> <?php echo count($movimentos); ?> movimentos
                <?php if (!empty($filtro_descricao)): ?>
                    contendo "<strong><?php echo htmlspecialchars($filtro_descricao); ?></strong>" na descrição
                <?php endif; ?>
            </p>
        </div>

        <div class="mt-4">
            <!-- Tabela de Movimentos -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motoboy
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descrição
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data/Hora Saída
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário Saída
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário Retorno
                            </th>
                            <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($movimentos)): ?>
                        <tr>
                            <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                                Nenhum movimento encontrado.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($movimentos as $movimento): ?>
                        <tr class="hover:bg-gray-50 <?php echo !$movimento['data_hora_retorno'] ? 'bg-yellow-50' : ''; ?>">
                            <td class="py-3 px-4">
                                <?php echo $movimento['id']; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php echo htmlspecialchars($movimento['motoboy']); ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php 
                                // Destaque do termo pesquisado na descrição
                                $descricao = htmlspecialchars($movimento['descricao']);
                                if (!empty($filtro_descricao)) {
                                    $descricao = preg_replace('/(' . preg_quote($filtro_descricao, '/') . ')/i', 
                                                           '<span class="bg-yellow-200">$1</span>', 
                                                           $descricao);
                                }
                                echo $descricao;
                                ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_saida'])); ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php echo htmlspecialchars($movimento['usuario_saida']); ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($movimento['data_hora_retorno']): ?>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Finalizado em
                                    <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_retorno'])); ?>
                                </span>
                                <?php else: ?>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Em Andamento
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($movimento['data_hora_retorno']): ?>
                                    <?php echo htmlspecialchars($movimento['usuario_retorno']); ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <?php if (!$movimento['data_hora_retorno']): ?>
                                <button onclick="registrarRetorno(<?php echo $movimento['id']; ?>)"
                                    class="text-green-600 hover:text-green-900 mr-3" title="Registrar Retorno">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <?php endif; ?>
                                <button onclick="editarMovimento(<?php echo $movimento['id']; ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3" title="Editar Movimento">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmarExclusao(<?php echo $movimento['id']; ?>)"
                                    class="text-red-600 hover:text-red-900 mr-3" title="Excluir Movimento">
                                    <i class="fas fa-trash"></i>
                                </button>
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

<!-- Modal Registrar Saída -->
<div id="modal-adicionar"
    class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Registrar Saída</h4>
            <button onclick="hideModal('modal-adicionar')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="form-adicionar" method="POST" action="../api/movimentos.php">
            <input type="hidden" name="action" value="create">

            <div class="mb-4">
                <label for="motoboy_id" class="block text-gray-700 font-medium mb-2">Motoboy</label>
                <select id="motoboy_id" name="motoboy_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="">Selecione um motoboy</option>
                    <?php foreach ($motoboys as $motoboy): ?>
                    <option value="<?php echo $motoboy['id']; ?>">
                        <?php echo htmlspecialchars($motoboy['nome']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="descricao" class="block text-gray-700 font-medium mb-2">Descrição</label>
                <input type="text" id="descricao" name="descricao"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-adicionar')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Registrar Saída
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Registrar Retorno -->
<div id="modal-retorno" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Registrar Retorno</h4>
            <button onclick="hideModal('modal-retorno')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="mb-4">Deseja registrar o retorno deste movimento?</p>

        <form id="form-retorno" method="POST" action="../api/movimentos.php">
            <input type="hidden" name="action" value="return">
            <input type="hidden" id="return-id" name="id">

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-retorno')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Confirmar Retorno
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Retorno em Lote -->
<div id="modal-retorno-lote" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Registrar Retorno em Lote</h4>
            <button onclick="hideModal('modal-retorno-lote')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-4">
            <p class="text-gray-700 mb-2">Selecione os movimentos para registrar o retorno:</p>
            
            <form id="form-retorno-lote" method="POST" action="../api/movimentos.php">
                <input type="hidden" name="action" value="batch_return">
                
                <?php if (empty($motoboysComMovimentosAbertos)): ?>
                <p class="text-gray-500 italic">Não há movimentos abertos para registrar retorno.</p>
                <?php else: ?>
                    <div class="max-h-96 overflow-y-auto mt-4">
                        <?php foreach ($motoboysComMovimentosAbertos as $motoboyId => $motoboy): ?>
                            <div class="border rounded-lg mb-4 bg-gray-50">
                                <div class="p-3 bg-blue-50 border-b rounded-t-lg flex items-center">
                                    <input type="checkbox" 
                                           id="select-all-motoboy-<?php echo $motoboyId; ?>" 
                                           class="select-all-motoboy mr-2"
                                           data-motoboy-id="<?php echo $motoboyId; ?>"
                                           onchange="toggleMotoboy(<?php echo $motoboyId; ?>)">
                                    <label for="select-all-motoboy-<?php echo $motoboyId; ?>" class="font-medium cursor-pointer flex-1">
                                        <?php echo htmlspecialchars($motoboy['nome']); ?>
                                    </label>
                                    <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">
                                        <?php echo count($motoboy['movimentos']); ?> movimento(s)
                                    </span>
                                </div>
                                <div class="p-3">
                                    <?php foreach ($motoboy['movimentos'] as $movimento): ?>
                                        <div class="flex items-center py-2 border-b last:border-0">
                                            <input type="checkbox" 
                                                   name="movimentos[]" 
                                                   value="<?php echo $movimento['id']; ?>" 
                                                   id="movimento-<?php echo $movimento['id']; ?>"
                                                   class="movimento-checkbox motoboy-<?php echo $motoboyId; ?>-checkbox mr-2"
                                                   onchange="updateMotoboysCheckbox(<?php echo $motoboyId; ?>)">
                                            <label for="movimento-<?php echo $movimento['id']; ?>" class="cursor-pointer flex-1">
                                                <span class="block text-sm font-medium"><?php echo htmlspecialchars($movimento['descricao']); ?></span>
                                                <span class="text-xs text-gray-500">Saída: <?php echo date('d/m/Y H:i', strtotime($movimento['data_hora_saida'])); ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 flex items-center">
                        <input type="checkbox" id="select-all" class="mr-2" onchange="toggleSelectAll()">
                        <label for="select-all" class="cursor-pointer">Selecionar todos os movimentos</label>
                    </div>
                    
                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="hideModal('modal-retorno-lote')"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-registrar-retorno-lote"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-check-double mr-2"></i>Registrar Retornos
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>


<!-- Modal Editar Movimento -->
<div id="modal-editar-movimento" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-4xl p-6"> <!-- Aumentado para max-w-4xl (era max-w-lg) -->
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Editar Movimento</h4>
            <button onclick="hideModal('modal-editar-movimento')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="form-editar-movimento" method="POST" action="../api/movimentos.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit-movimento-id" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit-motoboy-id" class="block text-gray-700 font-medium mb-2">Motoboy</label>
                    <select id="edit-motoboy-id" name="motoboy_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">Selecione um motoboy</option>
                        <?php foreach ($motoboys as $motoboy): ?>
                        <option value="<?php echo $motoboy['id']; ?>">
                            <?php echo htmlspecialchars($motoboy['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="edit-data-hora-saida" class="block text-gray-700 font-medium mb-2">Data/Hora Saída</label>
                    <input type="datetime-local" id="edit-data-hora-saida" name="data_hora_saida"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit-usuario-saida-id" class="block text-gray-700 font-medium mb-2">Usuário Saída</label>
                    <select id="edit-usuario-saida-id" name="usuario_saida_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">Selecione um usuário</option>
                        <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id']; ?>">
                            <?php echo htmlspecialchars($usuario['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center mt-6">
                    <input type="checkbox" id="edit-sem-retorno" name="sem_retorno" class="mr-2">
                    <label for="edit-sem-retorno" class="text-sm text-gray-600">Movimento sem retorno (em andamento)</label>
                </div>
            </div>

            <!-- Campo de descrição expandido para ocupar toda a largura -->
            <div class="mb-4">
                <label for="edit-descricao" class="block text-gray-700 font-medium mb-2">Descrição</label>
                <textarea id="edit-descricao" name="descricao"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    rows="4" required></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit-data-hora-retorno" class="block text-gray-700 font-medium mb-2">Data/Hora Retorno</label>
                    <input type="datetime-local" id="edit-data-hora-retorno" name="data_hora_retorno"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit-usuario-retorno-id" class="block text-gray-700 font-medium mb-2">Usuário Retorno</label>
                    <select id="edit-usuario-retorno-id" name="usuario_retorno_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione um usuário</option>
                        <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id']; ?>">
                            <?php echo htmlspecialchars($usuario['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" onclick="hideModal('modal-editar-movimento')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div id="modal-excluir" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Confirmar Exclusão</h4>
            <button onclick="hideModal('modal-excluir')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="mb-4">Tem certeza que deseja excluir este movimento? Esta ação não pode ser desfeita.</p>

        <form id="form-excluir" method="POST" action="../api/movimentos.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" id="delete-id" name="id">

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-excluir')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Excluir
                </button>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo SITE_URL; ?>/assets/js/movimentos.js"></script>
<?php require_once '../includes/footer.php'; ?>