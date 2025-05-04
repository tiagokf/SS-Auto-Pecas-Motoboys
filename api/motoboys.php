<?php
// api/motoboys.php
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
        criarMotoboy();
        break;
    case 'update':
        atualizarMotoboy();
        break;
    case 'delete':
        excluirMotoboy();
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        exit('Ação inválida');
}

// Função para criar um novo motoboy
function criarMotoboy() {
    // Validar dados de entrada
    if (!isset($_POST['nome']) || trim($_POST['nome']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Nome é obrigatório');
    }
    
    $nome = sanitize($_POST['nome']);
    
    // Verificar se já existe um motoboy com esse nome
    $checkSql = "SELECT COUNT(*) as total FROM motoboys WHERE nome = '$nome'";
    $check = fetchOne($checkSql);
    
    if ($check['total'] > 0) {
        // Redirecionamento com erro
        header('Location: ../motoboys/index.php?error=duplicate');
        exit;
    }
    
    // Inserir no banco de dados
    $sql = "INSERT INTO motoboys (nome) VALUES ('$nome')";
    
    if (query($sql)) {
        // Redirecionamento após sucesso
        header('Location: ../motoboys/index.php?success=create');
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao criar motoboy');
    }
}

// Função para atualizar um motoboy existente
function atualizarMotoboy() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id']) || 
        !isset($_POST['nome']) || trim($_POST['nome']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('ID e nome são obrigatórios');
    }
    
    $id = (int) $_POST['id'];
    $nome = sanitize($_POST['nome']);
    
    // Verificar se o motoboy existe
    $checkExistsSql = "SELECT COUNT(*) as total FROM motoboys WHERE id = $id";
    $checkExists = fetchOne($checkExistsSql);
    
    if ($checkExists['total'] != 1) {
        // Redirecionamento com erro
        header('Location: ../motoboys/index.php?error=not_found');
        exit;
    }
    
    // Verificar se já existe outro motoboy com esse nome
    $checkDuplicateSql = "SELECT COUNT(*) as total FROM motoboys WHERE nome = '$nome' AND id != $id";
    $checkDuplicate = fetchOne($checkDuplicateSql);
    
    if ($checkDuplicate['total'] > 0) {
        // Redirecionamento com erro
        header('Location: ../motoboys/index.php?error=duplicate');
        exit;
    }
    
    // Atualizar no banco de dados
    $sql = "UPDATE motoboys SET nome = '$nome' WHERE id = $id";
    
    if (query($sql)) {
        // Redirecionamento após sucesso
        header('Location: ../motoboys/index.php?success=update');
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao atualizar motoboy');
    }
}

// Função para excluir um motoboy
function excluirMotoboy() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    
    // Verificar se o motoboy existe
    $checkExistsSql = "SELECT COUNT(*) as total FROM motoboys WHERE id = $id";
    $checkExists = fetchOne($checkExistsSql);
    
    if ($checkExists['total'] != 1) {
        // Redirecionamento com erro
        header('Location: ../motoboys/index.php?error=not_found');
        exit;
    }
    
    // Verificar se está tentando excluir o motoboy Sistema (ID 1)
    if ($id == 1) {
        // Redirecionamento com erro
        header('Location: ../motoboys/index.php?error=system_motoboy');
        exit;
    }
    
    // ID do motoboy Sistema para transferência de registros
    $motoboySistemaId = 1;
    
    // Iniciar uma transação para garantir integridade dos dados
    query("START TRANSACTION");
    
    try {
        // Transferir todos os registros para o motoboy Sistema (ID 1)
        // Movimentos onde o motoboy é referenciado
        query("UPDATE movimentos SET motoboy_id = $motoboySistemaId WHERE motoboy_id = $id");
        
        // Se houver outras tabelas relacionadas, adicione aqui comandos similares
        // Por exemplo:
        // query("UPDATE entregas SET motoboy_id = $motoboySistemaId WHERE motoboy_id = $id");
        
        // Finalmente excluir o motoboy
        $sql = "DELETE FROM motoboys WHERE id = $id";
        
        if (query($sql)) {
            // Commit da transação
            query("COMMIT");
            // Redirecionamento após sucesso
            header('Location: ../motoboys/index.php?success=delete');
            exit;
        } else {
            throw new Exception('Erro ao excluir motoboy');
        }
    } catch (Exception $e) {
        // Rollback em caso de erro
        query("ROLLBACK");
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao excluir motoboy: ' . $e->getMessage());
    }
}