<?php
require_once '../config/Database.php';
require_once '../models/Transacao.php';

class TransacaoController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Transacao($db);
    }

    public function listarTransacoes() {
        $result = $this->model->listar();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarTransacao($descricao, $valor, $data, $tipo) {
        $this->model->descricao = $descricao;
        $this->model->valor = $valor;
        $this->model->data = $data;
        $this->model->tipo = $tipo;
        return $this->model->criar();
    }
}
?>
