<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
require_once 'conexao.php';

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
// Captura os dados do formulário
    if (isset($_POST['motoboy_id'])) {
        $motoboy_id = $_POST['motoboy_id'];
    } else {
        $motoboy_id = null;
    }

    if (isset($_POST['usuario_saida_id'])) {
        $usuario_saida_id = $_POST['usuario_saida_id'];
    } else {
        $usuario_saida_id = null;
    }

    if (isset($_POST['usuario_retorno_id'])) {
        $usuario_retorno_id = $_POST['usuario_retorno_id'];
    } else {
        $usuario_retorno_id = null;
    }

    if (isset($_POST['data_hora_saida'])) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['data_hora_saida']);
        $data_hora_saida = $date->format('Y-m-d H:i:s');
    } else {
        $data_hora_saida = null;
    }
    
    if (isset($_POST['data_hora_retorno']) && !empty($_POST['data_hora_retorno'])) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['data_hora_retorno']);
        $data_hora_retorno = $date->format('Y-m-d H:i:s');
    } else {
        $data_hora_retorno = null;
    }

    if (isset($_POST['descricao'])) {
        $descricao = $_POST['descricao'];
    } else {
        $descricao = null;
    }
    echo "Descrição:";
    var_dump($descricao);


    $sql = "INSERT INTO movimentos (motoboy_id, usuario_saida_id, usuario_retorno_id, data_hora_saida, data_hora_retorno, descricao) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iissss", $motoboy_id, $usuario_saida_id, $usuario_retorno_id, $data_hora_saida, $data_hora_retorno, $descricao);
    
        if ($stmt->execute()) {
            echo 'Movimento adicionado com sucesso';
        } else {
            echo 'Erro ao adicionar o movimento: ' . $stmt->error;
        }
    
        $stmt->close();
    } else {
        echo 'Erro ao preparar a consulta: Solicite suporte do Tiago';
    }
    

} else {
    // Se o método de requisição não for POST, retorna um erro
    http_response_code(405);
    echo 'Método não permitido';
}

// Fecha a conexão com o banco de dados
$conn->close();
?>