<?php
// api/movimentos.php
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
        criarMovimento();
        break;
    case 'update':
        atualizarMovimento();
        break;
    case 'return':
        registrarRetorno();
        break;
    case 'batch_return':
        registrarRetornoEmLote();
        break;
    case 'delete':
        excluirMovimento();
        break;
    case 'get':
        obterMovimento();
        break;
    case 'search':
        pesquisarMovimentos();
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        exit('Ação inválida');
}

// Função para criar um novo movimento (saída)
function criarMovimento() {
    // Validar dados de entrada
    if (!isset($_POST['motoboy_id']) || !is_numeric($_POST['motoboy_id']) || 
        !isset($_POST['descricao']) || trim($_POST['descricao']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Dados inválidos');
    }
    
    $motoboy_id = (int) $_POST['motoboy_id'];
    $descricao = sanitize($_POST['descricao']);
    $usuario_saida_id = $_SESSION['user_id'];
    $data_hora_saida = date('Y-m-d H:i:s');
    
    // Verificar se o motoboy existe
    $checkSql = "SELECT COUNT(*) as total FROM motoboys WHERE id = $motoboy_id";
    $check = fetchOne($checkSql);
    
    if ($check['total'] != 1) {
        header('HTTP/1.1 400 Bad Request');
        exit('Motoboy não encontrado');
    }
    
    // Verificar se o motoboy já está em um movimento aberto
    $checkMovimentoSql = "SELECT COUNT(*) as total FROM movimentos 
                          WHERE motoboy_id = $motoboy_id AND data_hora_retorno IS NULL";
    $checkMovimento = fetchOne($checkMovimentoSql);
    
    if ($checkMovimento['total'] > 0) {
        // Redirecionamento com erro
        header('Location: ../movimentos/index.php?error=already_out');
        exit;
    }
    
    // Inserir no banco de dados
    $sql = "INSERT INTO movimentos (motoboy_id, usuario_saida_id, data_hora_saida, descricao) 
            VALUES ($motoboy_id, $usuario_saida_id, '$data_hora_saida', '$descricao')";
    
    if (query($sql)) {
        // Recuperar os filtros atuais para preservá-los no redirecionamento
        $filtros = construirParametrosFiltro();
        
        // Redirecionamento após sucesso
        header('Location: ../movimentos/index.php?success=create' . $filtros);
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao registrar saída');
    }
}

// Função para registrar o retorno de um movimento
function registrarRetorno() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    $usuario_retorno_id = $_SESSION['user_id'];
    $data_hora_retorno = date('Y-m-d H:i:s');
    
    // Verificar se o movimento existe e está aberto
    $checkSql = "SELECT COUNT(*) as total FROM movimentos 
                 WHERE id = $id AND data_hora_retorno IS NULL";
    $check = fetchOne($checkSql);
    
    if ($check['total'] != 1) {
        // Redirecionamento com erro
        header('Location: ../movimentos/index.php?error=not_found');
        exit;
    }
    
    // Atualizar no banco de dados
    $sql = "UPDATE movimentos 
            SET usuario_retorno_id = $usuario_retorno_id, data_hora_retorno = '$data_hora_retorno' 
            WHERE id = $id";
    
    if (query($sql)) {
        // Recuperar os filtros atuais para preservá-los no redirecionamento
        $filtros = construirParametrosFiltro();
        
        // Redirecionamento após sucesso
        header('Location: ../movimentos/index.php?success=return' . $filtros);
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao registrar retorno');
    }
}

// Função para registrar retorno em lote
function registrarRetornoEmLote() {
    // Verificar se foram selecionados movimentos
    if (!isset($_POST['movimentos']) || !is_array($_POST['movimentos']) || empty($_POST['movimentos'])) {
        // Redirecionamento com erro
        header('Location: ../movimentos/index.php?error=no_selection');
        exit;
    }
    
    // Obter IDs dos movimentos selecionados
    $movimento_ids = array_map('intval', $_POST['movimentos']);
    $usuario_retorno_id = $_SESSION['user_id'];
    $data_hora_retorno = date('Y-m-d H:i:s');
    
    // Iniciar uma transação para garantir que todos os retornos sejam registrados
    global $conn;
    $conn->begin_transaction();
    
    try {
        // Verificar se todos os movimentos existem e estão abertos
        $ids_string = implode(',', $movimento_ids);
        $checkSql = "SELECT id FROM movimentos 
                     WHERE id IN ($ids_string) AND data_hora_retorno IS NULL";
        $movimentos_validos = fetchAll($checkSql);
        
        // Extrair apenas os IDs válidos
        $ids_validos = array_column($movimentos_validos, 'id');
        
        if (empty($ids_validos)) {
            throw new Exception('Nenhum movimento válido para registrar retorno');
        }
        
        // Atualizar todos os movimentos válidos de uma vez
        $ids_validos_string = implode(',', $ids_validos);
        $sql = "UPDATE movimentos 
                SET usuario_retorno_id = $usuario_retorno_id, data_hora_retorno = '$data_hora_retorno' 
                WHERE id IN ($ids_validos_string)";
        
        if (!query($sql)) {
            throw new Exception('Erro ao registrar retornos');
        }
        
        // Confirmar a transação
        $conn->commit();
        
        // Recuperar os filtros atuais para preservá-los no redirecionamento
        $filtros = construirParametrosFiltro();
        
        // Redirecionamento após sucesso
        header('Location: ../movimentos/index.php?success=batch_return' . $filtros);
        exit;
    } catch (Exception $e) {
        // Reverter a transação em caso de erro
        $conn->rollback();
        
        // Redirecionamento com erro
        header('Location: ../movimentos/index.php?error=batch_error');
        exit;
    }
}

// Função para excluir um movimento
function excluirMovimento() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    
    // Verificar se o usuário tem permissão para excluir
    // Aqui poderia ter uma verificação adicional de autorização
    
    // Excluir do banco de dados
    $sql = "DELETE FROM movimentos WHERE id = $id";
    
    if (query($sql)) {
        // Recuperar os filtros atuais para preservá-los no redirecionamento
        $filtros = construirParametrosFiltro();
        
        // Redirecionamento após sucesso
        header('Location: ../movimentos/index.php?success=delete' . $filtros);
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao excluir movimento');
    }
}

// Função para pesquisar movimentos
function pesquisarMovimentos() {
    // Esta função poderia ser usada para uma busca Ajax
    // que retornaria JSON em vez de redirecionar o usuário
    
    // Verificar parâmetros de pesquisa
    $descricao = isset($_POST['descricao']) ? sanitize($_POST['descricao']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'todos';
    $motoboy_id = isset($_POST['motoboy_id']) ? (int)$_POST['motoboy_id'] : 0;
    $data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
    $data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';
    
    // Construir a consulta SQL
    $sql = "
        SELECT m.id, mb.nome as motoboy, u1.nome as usuario_saida, 
               u2.nome as usuario_retorno, m.data_hora_saida, 
               m.data_hora_retorno, m.descricao
        FROM movimentos m
        JOIN motoboys mb ON m.motoboy_id = mb.id
        JOIN usuarios u1 ON m.usuario_saida_id = u1.id
        LEFT JOIN usuarios u2 ON m.usuario_retorno_id = u2.id
        WHERE 1=1
    ";
    
    // Adicionar filtros
    if (!empty($descricao)) {
        $sql .= " AND m.descricao LIKE '%$descricao%'";
    }
    
    if ($status === 'abertos') {
        $sql .= " AND m.data_hora_retorno IS NULL";
    } elseif ($status === 'finalizados') {
        $sql .= " AND m.data_hora_retorno IS NOT NULL";
    }
    
    if ($motoboy_id > 0) {
        $sql .= " AND m.motoboy_id = $motoboy_id";
    }
    
    if (!empty($data_inicio)) {
        $data_inicio_formatada = date('Y-m-d 00:00:00', strtotime($data_inicio));
        $sql .= " AND m.data_hora_saida >= '$data_inicio_formatada'";
    }
    
    if (!empty($data_fim)) {
        $data_fim_formatada = date('Y-m-d 23:59:59', strtotime($data_fim));
        $sql .= " AND m.data_hora_saida <= '$data_fim_formatada'";
    }
    
    $sql .= " ORDER BY m.data_hora_saida DESC";
    
    // Executar a consulta
    $movimentos = fetchAll($sql);
    
    // Retornar os resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($movimentos);
    exit;
}

// Função auxiliar para construir uma string de query com filtros
function construirParametrosFiltro() {
    $filtros = '';
    
    // Verificar se há parâmetros de filtro na URL atual
    if (isset($_SERVER['HTTP_REFERER'])) {
        $url_parts = parse_url($_SERVER['HTTP_REFERER']);
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $params);
            
            // Remover parâmetros de success/error
            unset($params['success']);
            unset($params['error']);
            
            // Reconstruir a string de query
            if (!empty($params)) {
                $filtros = '&' . http_build_query($params);
            }
        }
    }
    
    return $filtros;
}

// Função para obter dados de um movimento específico
function obterMovimento() {
    // Validar dados de entrada
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        header('HTTP/1.1 400 Bad Request');
        exit('ID é obrigatório');
    }
    
    $id = (int) $_POST['id'];
    
    // Buscar movimento no banco de dados
    $sql = "SELECT m.*, mb.nome as motoboy_nome
            FROM movimentos m
            JOIN motoboys mb ON m.motoboy_id = mb.id
            WHERE m.id = $id";
    
    $movimento = fetchOne($sql);
    
    if ($movimento) {
        // Retornar dados em formato JSON
        header('Content-Type: application/json');
        echo json_encode($movimento);
        exit;
    } else {
        header('HTTP/1.1 404 Not Found');
        exit('Movimento não encontrado');
    }
}

// Função para atualizar um movimento existente
function atualizarMovimento() {
    // Validar dados de entrada básicos
    if (!isset($_POST['id']) || !is_numeric($_POST['id']) || 
        !isset($_POST['motoboy_id']) || !is_numeric($_POST['motoboy_id']) || 
        !isset($_POST['usuario_saida_id']) || !is_numeric($_POST['usuario_saida_id']) || 
        !isset($_POST['data_hora_saida']) || trim($_POST['data_hora_saida']) === '' || 
        !isset($_POST['descricao']) || trim($_POST['descricao']) === '') {
        header('HTTP/1.1 400 Bad Request');
        exit('Dados básicos incompletos ou inválidos');
    }
    
    $id = (int) $_POST['id'];
    $motoboy_id = (int) $_POST['motoboy_id'];
    $usuario_saida_id = (int) $_POST['usuario_saida_id'];
    $descricao = sanitize($_POST['descricao']);
    
    // Formatar data e hora de saída
    $data_hora_saida = date('Y-m-d H:i:s', strtotime($_POST['data_hora_saida']));
    
    // Verificar se o movimento existe
    $checkSql = "SELECT COUNT(*) as total FROM movimentos WHERE id = $id";
    $check = fetchOne($checkSql);
    
    if ($check['total'] != 1) {
        header('HTTP/1.1 404 Not Found');
        exit('Movimento não encontrado');
    }
    
    // Verificar se o movimento tem retorno
    $tem_retorno = !isset($_POST['sem_retorno']) || $_POST['sem_retorno'] !== 'on';
    
    // Preparar a consulta SQL
    $sql = "UPDATE movimentos SET 
            motoboy_id = $motoboy_id, 
            usuario_saida_id = $usuario_saida_id, 
            data_hora_saida = '$data_hora_saida', 
            descricao = '$descricao'";
    
    if ($tem_retorno && isset($_POST['data_hora_retorno']) && !empty($_POST['data_hora_retorno']) && 
        isset($_POST['usuario_retorno_id']) && !empty($_POST['usuario_retorno_id'])) {
        $usuario_retorno_id = (int) $_POST['usuario_retorno_id'];
        $data_hora_retorno = date('Y-m-d H:i:s', strtotime($_POST['data_hora_retorno']));
        
        // Verificar se a data de retorno é posterior à data de saída
        if (strtotime($data_hora_retorno) < strtotime($data_hora_saida)) {
            header('HTTP/1.1 400 Bad Request');
            exit('A data/hora de retorno deve ser posterior à data/hora de saída');
        }
        
        $sql .= ", usuario_retorno_id = $usuario_retorno_id, data_hora_retorno = '$data_hora_retorno'";
    } else {
        // Se não tem retorno, setar valores como NULL
        $sql .= ", usuario_retorno_id = NULL, data_hora_retorno = NULL";
    }
    
    $sql .= " WHERE id = $id";
    
    // Executar a atualização
    if (query($sql)) {
        // Recuperar os filtros atuais para preservá-los no redirecionamento
        $filtros = construirParametrosFiltro();
        
        // Redirecionamento após sucesso
        header('Location: ../movimentos/index.php?success=update' . $filtros);
        exit;
    } else {
        // Em caso de erro
        header('HTTP/1.1 500 Internal Server Error');
        exit('Erro ao atualizar movimento');
    }
}