<?php
// api/usuarios.php
require_once '../includes/db.php';
requireLogin();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método não permitido');
}

// Obter a ação a ser executada
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'create':
        criarUsuario();
        break;
    case 'update':
        atualizarUsuario();
        break;
    case 'delete':
        excluirUsuario();
        break;
    case 'get':
        obterUsuario();
        break;
    case 'change_password':
    alterarSenha();
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        exit('Ação inválida');
}

// Função para criar um novo usuário
function criarUsuario() {
    // Validar dados de entrada
    if (!isset($_POST['nome']) || trim($_POST['nome']) === '' || 
        !isset($_POST['senha']) || trim($_POST['senha']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Nome e senha são obrigatórios');
    }
    
    $nome = sanitize($_POST['nome']);
    $senha = sanitize($_POST['senha']);
    
    // Verificar se o nome de usuário já existe
    $checkSql = "SELECT COUNT(*) as total FROM usuarios WHERE nome = '$nome'";
    $check = fetchOne($checkSql);
    
    if ($check['total'] > 0) {
        // Redirecionamento com erro
        header('Location: ../usuarios/index.php?error=duplicate');
        exit;
    }
    
    // Inserir no banco de dados
    $sql = "INSERT INTO usuarios (nome, senha) VALUES ('$nome', '$senha')";
    
    if (query($sql)) {
        // Redirecionamento após sucesso
        header('Location: ../usuarios/index.php?success=create');
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao criar usuário');
    }
}

// Função para atualizar um usuário existente
function atualizarUsuario() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id']) || 
        !isset($_POST['nome']) || trim($_POST['nome']) === '' ||
        !isset($_POST['senha']) || trim($_POST['senha']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('ID, nome e senha são obrigatórios');
    }
    
    $id = (int) $_POST['id'];
    $nome = sanitize($_POST['nome']);
    $senha = sanitize($_POST['senha']);
    
    // Verificar se o usuário existe
    $checkExistsSql = "SELECT COUNT(*) as total FROM usuarios WHERE id = $id";
    $checkExists = fetchOne($checkExistsSql);
    
    if ($checkExists['total'] != 1) {
        // Redirecionamento com erro
        header('Location: ../usuarios/index.php?error=not_found');
        exit;
    }
    
    // Verificar se o nome de usuário já existe para outro usuário
    $checkSql = "SELECT COUNT(*) as total FROM usuarios WHERE nome = '$nome' AND id != $id";
    $check = fetchOne($checkSql);
    
    if ($check['total'] > 0) {
        // Redirecionamento com erro
        header('Location: ../usuarios/index.php?error=duplicate');
        exit;
    }
    
    // Atualizar no banco de dados
    $sql = "UPDATE usuarios SET nome = '$nome', senha = '$senha' WHERE id = $id";
    
    if (query($sql)) {
        // Redirecionamento após sucesso
        header('Location: ../usuarios/index.php?success=update');
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao atualizar usuário');
    }
}

// Função para excluir um usuário
function excluirUsuario() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    
    // Verificar se é o próprio usuário logado
    if ($_SESSION['user_id'] == $id) {
        // Redirecionamento com erro
        header('Location: ../usuarios/index.php?error=self_delete');
        exit;
    }
    
    // Verificar se está tentando excluir o usuário Sistema (ID 1)
    if ($id == 1) {
        // Redirecionamento com erro
        header('Location: ../usuarios/index.php?error=system_user');
        exit;
    }
    
    // ID do usuário Sistema para transferência de registros
    $usuarioSistemaId = 1;
    
    // Iniciar uma transação para garantir integridade dos dados
    query("START TRANSACTION");
    
    try {
        // Transferir todos os registros para o usuário Sistema (ID 1)
        // Movimentos onde o usuário é referenciado como usuário de saída
        query("UPDATE movimentos SET usuario_saida_id = $usuarioSistemaId WHERE usuario_saida_id = $id");
        
        // Movimentos onde o usuário é referenciado como usuário de retorno
        query("UPDATE movimentos SET usuario_retorno_id = $usuarioSistemaId WHERE usuario_retorno_id = $id");
        
        // Se houver outras tabelas relacionadas, adicione aqui comandos similares
        // Por exemplo:
        // query("UPDATE atividades SET usuario_id = $usuarioSistemaId WHERE usuario_id = $id");
        // query("UPDATE logs SET usuario_id = $usuarioSistemaId WHERE usuario_id = $id");
        
        // Finalmente excluir o usuário
        $sql = "DELETE FROM usuarios WHERE id = $id";
        
        if (query($sql)) {
            // Commit da transação
            query("COMMIT");
            // Redirecionamento após sucesso
            header('Location: ../usuarios/index.php?success=delete');
            exit;
        } else {
            throw new Exception('Erro ao excluir usuário');
        }
    } catch (Exception $e) {
        // Rollback em caso de erro
        query("ROLLBACK");
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao excluir usuário: ' . $e->getMessage());
    }
}

// Função para obter dados de um usuário específico
function obterUsuario() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    
    // Buscar usuário no banco de dados
    $sql = "SELECT id, nome, senha FROM usuarios WHERE id = $id";
    $usuario = fetchOne($sql);
    
    if ($usuario) {
        // Retornar dados em formato JSON
        header('Content-Type: application/json');
        echo json_encode($usuario);
        exit;
    } else {
        header('HTTP/1.1 404 Not Found');
        exit('Usuário não encontrado');
    }
}

// Função para alterar a senha do usuário
function alterarSenha() {
    // Validar dados de entrada
    if (!isset($_POST['nova_senha']) || trim($_POST['nova_senha']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Nova senha é obrigatória');
    }
    
    $novaSenha = sanitize($_POST['nova_senha']);
    $userId = $_SESSION['user_id'];
    
    // Atualizar senha no banco de dados
    $sql = "UPDATE usuarios SET senha = '$novaSenha' WHERE id = $userId";
    
    if (query($sql)) {
        // Redirecionamento após sucesso
        header('Location: ../perfil.php?success=password_changed');
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao alterar senha');
    }
}