<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

// Verificar se já está logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Processar o formulário de login
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparDados($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($nome) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        $conexao = conectarDB();
        
        $sql = "SELECT id, nome, senha FROM usuarios WHERE nome = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Verificar senha - aceita tanto senha criptografada quanto senha em texto plano
            if (password_verify($senha, $usuario['senha']) || $senha === $usuario['senha']) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_admin'] = true; // Simplificado, em produção seria baseado em um campo da tabela
                
                header("Location: index.php");
                exit;
            } else {
                $erro = 'Nome de usuário ou senha incorretos.';
            }
        } else {
            $erro = 'Nome de usuário ou senha incorretos.';
        }
        
        $stmt->close();
        $conexao->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gerenciamento de Motoboys</title>
    <!-- Semantic UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="ui container login-container">
        <div class="ui raised segment">
            <h2 class="ui blue header center aligned">
                <i class="motorcycle icon"></i>
                <div class="content">Sistema de Gerenciamento de Motoboys</div>
            </h2>

            <?php if (!empty($erro)): ?>
            <div class="ui negative message">
                <i class="close icon"></i>
                <div class="header">Erro de Login</div>
                <p><?php echo $erro; ?></p>
            </div>
            <?php endif; ?>

            <form class="ui form" method="post" action="login.php">
                <div class="field">
                    <label>Nome de Usuário</label>
                    <div class="ui left icon input">
                        <input type="text" name="nome" placeholder="Nome de usuário">
                        <i class="user icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Senha</label>
                    <div class="ui left icon input">
                        <input type="password" name="senha" placeholder="Senha">
                        <i class="lock icon"></i>
                    </div>
                </div>
                <button class="ui fluid large blue submit button" type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Semantic UI JS -->
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.message .close').on('click', function () {
                $(this).closest('.message').transition('fade');
            });
        });
    </script>
</body>

</html>