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
    $movimento_id = $_POST['edit_movimento_id'];
    $motoboy_id = $_POST['edit_motoboy_id'];
    $usuario_saida_id = $_POST['edit_usuario_saida_id'];

    if (isset($_POST['edit_usuario_retorno_id'])) {
        $usuario_retorno_id = $_POST['edit_usuario_retorno_id'];
    } else {
        $usuario_retorno_id = null;
    }

    if (isset($_POST['edit_data_hora_saida'])) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['edit_data_hora_saida']);
        $data_hora_saida = $date->format('Y-m-d H:i:s');
    } else {
        $data_hora_saida = null;
    }

    if (isset($_POST['edit_data_hora_retorno']) && !empty($_POST['edit_data_hora_retorno'])) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['edit_data_hora_retorno']);
        $data_hora_retorno = $date->format('Y-m-d H:i:s');
    } else {
        $data_hora_retorno = null;
    }

    $descricao = $_POST['edit_descricao'];

    // Atualiza o registro do movimento no banco de dados
    $sql = "UPDATE movimentos SET motoboy_id='$motoboy_id', usuario_saida_id='$usuario_saida_id', usuario_retorno_id='$usuario_retorno_id', data_hora_saida='$data_hora_saida', data_hora_retorno='$data_hora_retorno', descricao='$descricao' WHERE id='$movimento_id'";

    if ($conn->query($sql) === TRUE) {
        echo 'Movimento atualizado com sucesso';
    } else {
        echo 'Erro ao atualizar o movimento: Fale com o Tiago sobre isso';
    }
} else {
    // Se o método de requisição não for POST, retorna um erro
    http_response_code(405);
    echo 'Método não permitido';
}

// Fecha a conexão com o banco de dados
$conn->close();
?>