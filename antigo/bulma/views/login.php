<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/bulma/css/bulma.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
</head>
<body>
    <section class="hero is-fullheight is-light">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-4">
                        <div class="box">
                            <h1 class="title has-text-centered">Login</h1>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="notification is-danger">
                                    <?= htmlspecialchars($_SESSION['error']); ?>
                                    <?php unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>

                            <form action="../controllers/authController.php?action=login" method="POST">
                                <div class="field">
                                    <label class="label">Usuário</label>
                                    <div class="control">
                                        <input class="input" type="text" name="usuario" placeholder="Digite seu usuário" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Senha</label>
                                    <div class="control">
                                        <input class="input" type="password" name="senha" placeholder="Digite sua senha" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <button class="button is-link is-fullwidth" type="submit">Entrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
