<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

// Busca todos os motoboys
$sql = "SELECT id, nome FROM motoboys";
$result = $conn->query($sql);

// Inicializa um array vazio para armazenar os dados dos motoboys
$motoboys = array();

// Preenche o array com os dados dos motoboys
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $motoboys[] = array(
            'id' => $row["id"],
            'nome' => $row["nome"]
        );
    }
}

// Define o cabeçalho de resposta como JSON
header('Content-Type: application/json');

// Retorna os dados no formato JSON
echo json_encode($motoboys);

$conn->close();
?>