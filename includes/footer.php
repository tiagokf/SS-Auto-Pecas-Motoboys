</div> <!-- Fechamento do div main-content -->
</div> <!-- Fechamento do div flex h-screen -->
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
<script>
    // Script para controle do menu mobile
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('open');
    });
    // Fechar o menu ao clicar fora em dispositivos móveis
    document.addEventListener('click', function (event) {
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        if (window.innerWidth < 1024 &&
            !sidebar.contains(event.target) &&
            !mobileMenuButton.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    });
    // Funções para mostrar/esconder modais
    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    function hideModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    // Fechar modais ao clicar fora
    document.addEventListener('click', function (event) {
        document.querySelectorAll('.modal').forEach(function (modal) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
    // Evitar propagação de cliques nos modais
    document.querySelectorAll('.modal-content').forEach(function (content) {
        content.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });
</script>
</body>
</html>