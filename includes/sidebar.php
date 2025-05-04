<?php
// includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<div id="sidebar"
    class="sidebar bg-blue-800 text-white w-64 space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform lg:relative lg:translate-x-0">
    <div class="flex items-center justify-center mb-8">
        <i class="fas fa-motorcycle text-white text-2xl mr-2"></i>
        <h2 class="text-xl font-bold">SSAuto Peças</h2>
    </div>
    <nav>
        <a href="<?php echo SITE_URL; ?>/index.php"
            class="block py-2.5 px-4 rounded transition duration-200 <?php echo $currentPage == 'index.php' ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
        <a href="<?php echo SITE_URL; ?>/motoboys/index.php"
            class="block py-2.5 px-4 rounded transition duration-200 <?php echo strpos($currentPage, 'motoboys') !== false ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
            <i class="fas fa-motorcycle mr-2"></i>Motoboys
        </a>
        <a href="<?php echo SITE_URL; ?>/movimentos/index.php"
            class="block py-2.5 px-4 rounded transition duration-200 <?php echo strpos($currentPage, 'movimentos') !== false ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
            <i class="fas fa-exchange-alt mr-2"></i>Movimentos
        </a>
        <a href="<?php echo SITE_URL; ?>/usuarios/index.php"
            class="block py-2.5 px-4 rounded transition duration-200 <?php echo strpos($currentPage, 'usuarios') !== false ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
            <i class="fas fa-users mr-2"></i>Usuários
        </a>
        <a href="<?php echo SITE_URL; ?>/relatorios/index.php" 
            class="block py-2.5 px-4 rounded transition duration-200 <?php echo strpos($currentPage, 'relatorios') !== false ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
            <i class="fas fa-chart-bar mr-2"></i>Relatórios
        </a>
    </nav>
    <div class="absolute bottom-0 left-0 right-0 p-4">
        <div class="border-t border-blue-700 pt-4">
            <div class="flex items-center mb-4">
                <div class="bg-blue-600 rounded-full w-10 h-10 flex items-center justify-center mr-3">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="text-sm font-medium"><?php echo $_SESSION['user_nome']; ?></p>
                </div>
            </div>
            <a href="<?php echo SITE_URL; ?>/perfil.php"
                class="block text-center py-2 px-4 rounded bg-blue-700 hover:bg-blue-600 transition duration-200 mb-2">
                <i class="fas fa-user-cog mr-2"></i>Meu Perfil
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php"
                class="block text-center py-2 px-4 rounded bg-blue-700 hover:bg-blue-600 transition duration-200">
                <i class="fas fa-sign-out-alt mr-2"></i>Sair
            </a>
        </div>
    </div>
</div>
<!-- Main content -->
<div class="main-content flex-1" </div>