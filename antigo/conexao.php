<?php
$servername = "localhost";
$username = "u956739147_ssauto";
$password = "Ssauto2020";
$dbname = "u956739147_ssauto";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
?>
