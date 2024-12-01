<?php

require_once '../config/Database.php';
require_once '../app/models/transacao.php';


$db = new Database();
$conn = $db->connect();


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $transacao = new Transacao($conn);

   
    $transacao->id = $id;

   
    $query = "DELETE FROM transacoes WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $transacao->id);

    if ($stmt->execute()) {
        
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao excluir a transação.";
    }
} else {
    echo "ID da transação não fornecido.";
}
?>
