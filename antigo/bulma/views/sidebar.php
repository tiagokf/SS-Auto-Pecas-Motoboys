<button id="toggle-sidebar">
    <i class="fas fa-bars"></i>
</button>

<div class="columns">
<!-- Sidebar -->
    <aside class="menu sidebar" id="side-menu">
        <div class="menu-brand">
            <h1 class="title is-4 has-text-white">SS Auto Peças</h1>
        </div>
        <div class="menu-user">
            <figure>
                <i class="fas fa-user-circle fa-3x"></i>
            </figure>
            <p class="has-text-white">
                <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?>
            </p>
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php"><span class="icon"><i class="fas fa-home"></i></span>Dashboard</a></li>
            <li><a href="usuarios.php"><span class="icon"><i class="fas fa-users"></i></span>Usuários</a></li>
            <li><a href="motoboys.php"><span class="icon"><i class="fas fa-motorcycle"></i></span>Motoboys</a></li>
            <li><a href="movimentos.php"><span class="icon"><i class="fas fa-list"></i></span>Movimentos</a></li>
            <li><a href="../controllers/authController.php?action=logout" class="has-text-danger"><span class="icon"><i class="fas fa-sign-out-alt"></i></span>Sair</a></li>
        </ul>
    </aside>
</div>
