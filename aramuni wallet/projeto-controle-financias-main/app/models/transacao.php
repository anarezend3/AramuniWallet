<?php
class Transacao {
    private $conn;
    private $table = "transacoes";

    public $id;
    public $descricao;
    public $valor;
    public $data;
    public $tipo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY data DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table . " (descricao, valor, data, tipo) VALUES (:descricao, :valor, :data, :tipo)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":data", $this->data);
        $stmt->bindParam(":tipo", $this->tipo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
