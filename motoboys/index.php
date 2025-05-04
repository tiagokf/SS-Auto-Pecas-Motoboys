<?php
// motoboys/index.php
$pageTitle = 'Motoboys';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Buscar todos os motoboys
$motoboys = fetchAll("SELECT * FROM motoboys ORDER BY nome");

// Mensagens de feedback
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!-- Conteúdo Principal -->
<div class="overflow-x-hidden overflow-y-auto bg-gray-100 flex-1">
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">Motoboys</h3>

            <button onclick="showModal('modal-adicionar')"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none">
                <i class="fas fa-plus mr-2"></i>Adicionar Motoboy
            </button>
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
                                echo 'Motoboy adicionado com sucesso!';
                                break;
                            case 'update':
                                echo 'Motoboy atualizado com sucesso!';
                                break;
                            case 'delete':
                                echo 'Motoboy excluído com sucesso!';
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
                            case 'duplicate':
                                echo 'Já existe um motoboy com esse nome!';
                                break;
                            case 'system_motoboy':
                                echo 'O motoboy Sistema não pode ser excluído!';
                                break;
                            case 'in_use':
                                echo 'Este motoboy não pode ser excluído pois possui movimentos registrados!';
                                break;
                            case 'not_found':
                                echo 'Motoboy não encontrado!';
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

        <div class="mt-8">
            <!-- Tabela de Motoboys -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($motoboys)): ?>
                        <tr>
                            <td colspan="3" class="py-4 px-6 text-center text-gray-500">
                                Nenhum motoboy cadastrado.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($motoboys as $motoboy): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <?php echo $motoboy['id']; ?>
                            </td>
                            <td class="py-4 px-6">
                                <?php echo htmlspecialchars($motoboy['nome']); ?>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button
                                    onclick="editarMotoboy(<?php echo $motoboy['id']; ?>, '<?php echo htmlspecialchars($motoboy['nome']); ?>')"
                                    class="text-blue-600 hover:text-blue-900 mr-3" title="Editar Motoboy">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmarExclusao(<?php echo $motoboy['id']; ?>)"
                                    class="text-red-600 hover:text-red-900" title="Excluir Motoboy">
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

<!-- Modal Adicionar Motoboy -->
<div id="modal-adicionar"
    class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Adicionar Motoboy</h4>
            <button onclick="hideModal('modal-adicionar')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="form-adicionar" method="POST" action="../api/motoboys.php">
            <input type="hidden" name="action" value="create">

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 font-medium mb-2">Nome</label>
                <input type="text" id="nome" name="nome"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-adicionar')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Motoboy -->
<div id="modal-editar"
    class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Editar Motoboy</h4>
            <button onclick="hideModal('modal-editar')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="form-editar" method="POST" action="../api/motoboys.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit-id" name="id">

            <div class="mb-4">
                <label for="edit-nome" class="block text-gray-700 font-medium mb-2">Nome</label>
                <input type="text" id="edit-nome" name="nome"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-editar')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div id="modal-excluir"
    class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium">Confirmar Exclusão</h4>
            <button onclick="hideModal('modal-excluir')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="mb-4">Tem certeza que deseja excluir este motoboy? <strong>Todos os registros relacionados a este motoboy serão transferidos para o motoboy "Sistema"</strong>. Esta ação não pode ser desfeita.</p>

        <form id="form-excluir" method="POST" action="../api/motoboys.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" id="delete-id" name="id">

            <div class="flex justify-end">
                <button type="button" onclick="hideModal('modal-excluir')"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md mr-2 hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Excluir
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editarMotoboy(id, nome) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nome').value = nome;
        showModal('modal-editar');
    }

    function confirmarExclusao(id) {
        document.getElementById('delete-id').value = id;
        showModal('modal-excluir');
    }

    // Verificar se há mensagens de feedback e removê-las após 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 1000);
            });
        }, 5000);
    });
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/motoboys.js"></script>
<?php require_once '../includes/footer.php'; ?>