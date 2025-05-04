<?php
// login.php
require_once 'includes/db.php';

// Se já estiver logado, redireciona para o dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $senha = sanitize($_POST['senha']);
    
    // Consulta para verificar o usuário
    $sql = "SELECT id, nome, senha FROM usuarios WHERE nome = '$nome'";
    $user = fetchOne($sql);
    
    if ($user && $senha === $user['senha']) {
        // Login bem-sucedido
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        
        header('Location: index.php');
        exit;
    } else {
        $error = 'Nome de usuário ou senha incorretos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%232563eb" fill-opacity="0.05" fill-rule="evenodd"/%3E%3C/svg%3E');
        }
        
        .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 2rem 0;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .card-body {
            background: white;
            padding: 2rem;
        }
        
        .logo-circle {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            position: relative;
            margin-bottom: -45px;
            border: 6px solid #1e40af;
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        
        .label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .error-box {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .error-icon {
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="card w-full max-w-md">
            <div class="card-header">
                <h1 class="text-3xl font-bold mb-2"><?php echo SITE_NAME; ?></h1>
                <p class="text-blue-100">Sistema de Controle de Motoboys</p>
                
                <div class="logo-circle">
                    <i class="fas fa-motorcycle text-blue-700 text-4xl"></i>
                </div>
            </div>
            
            <div class="card-body pt-12">
                <h2 class="text-xl font-semibold text-center text-gray-800 mb-6">Acesso ao Sistema</h2>
                
                <?php if ($error): ?>
                <div class="error-box">
                    <span class="error-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span><?php echo $error; ?></span>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="input-group">
                        <label for="nome" class="label">Nome de Usuário</label>
                        <div class="relative">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input id="nome" name="nome" type="text" required class="form-input" placeholder="Digite seu nome de usuário">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="senha" class="label">Senha</label>
                        <div class="relative">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input id="senha" name="senha" type="password" required class="form-input" placeholder="Digite sua senha">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar no Sistema
                    </button>
                </form>
                
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>&copy; <?php echo date('Y'); ?> - Todos os direitos reservados</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Adicionar alguma validação de formulário se necessário
        document.querySelector('form').addEventListener('submit', function (e) {
            const nome = document.getElementById('nome').value.trim();
            const senha = document.getElementById('senha').value.trim();

            if (!nome || !senha) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    </script>
</body>
</html>