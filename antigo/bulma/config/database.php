<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'u956739147_ssauto';
    private $username = 'u956739147_ssauto';
    private $password = 'Ssauto2020';
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
        return $this->conn;
    }
}
