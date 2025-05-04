<?php
class Usuario {
    private $pdo;
    private $userId;
    private $userName;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($nome, $senha) {
        $stmt = $this->pdo->prepare("
            SELECT id, nome, senha 
            FROM usuarios 
            WHERE nome = :nome
        ");
        $stmt->execute(['nome' => $nome]);
        $user = $stmt->fetch();

        if ($user && $user['senha'] === $senha) {
            $this->userId = $user['id'];
            $this->userName = $user['nome'];
            return true;
        }
        return false;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getUserName() {
        return $this->userName;
    }
}
