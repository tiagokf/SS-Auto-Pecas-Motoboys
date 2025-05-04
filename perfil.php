<?php
// perfil.php
$pageTitle = 'Meu Perfil';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Buscar dados do usuário logado
$userId = $_SESSION['user_id'];
$usuario = fetchOne("SELECT id, nome, senha FROM usuarios WHERE id = $userId");

// Verificar mensagens de feedback
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!-- Conteúdo Principal -->
<div class="overflow-x-hidden overflow-y-auto bg-gray-100 flex-1">
    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">Meu Perfil</h3>

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
                            case 'password_changed':
                                echo 'Senha alterada com sucesso!';
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
                            case 'wrong_password':
                                echo 'Senha atual incorreta.';
                                break;
                            case 'passwords_dont_match':
                                echo 'As novas senhas não coincidem.';
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
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-600 rounded-full w-16 h-16 flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-semibold text-gray-700">
                            <?php echo htmlspecialchars($usuario['nome']); ?></h4>
                        <p class="text-gray-500">ID: <?php echo $usuario['id']; ?></p>
                    </div>
                </div>

                <hr class="my-6">

                <div class="mb-6">
                    <h5 class="text-lg font-medium mb-2">Informações da Conta</h5>
                    <div class="bg-gray-50 p-4 rounded border">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nome de Usuário</p>
                                <p class="font-medium"><?php echo htmlspecialchars($usuario['nome']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Senha Atual</p>
                                <p class="font-medium"><?php echo htmlspecialchars($usuario['senha']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6">

                <h5 class="text-lg font-medium mb-4">Alterar Senha</h5>

                <form action="api/usuarios.php" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-4">
                        <label for="nova_senha" class="block text-gray-700 font-medium mb-2">Nova Senha</label>
                        <input type="text" id="nova_senha" name="nova_senha"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

<?php require_once 'includes/footer.php'; ?>