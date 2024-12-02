<?php

require_once '../config/Database.php';
require_once '../app/models/transacao.php'; 

$db = new Database();
$conn = $db->connect();


function getTransactions($conn) {
    $stmt = $conn->prepare("SELECT * FROM transacoes ORDER BY data DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function calculateBalance($transactions) {
    $income = 0;
    $expense = 0;

    
    if ($transactions) {
        foreach ($transactions as $transaction) {
            if ($transaction['valor'] > 0) {
                $income += $transaction['valor'];
            } else {
                $expense += $transaction['valor'];
            }
        }
    }

    $total = $income + $expense;
    return ['income' => $income, 'expense' => $expense, 'total' => $total];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['description'];
    $valor = $_POST['amount'];
    $data = $_POST['date'];

    
    $transacao = new transacao($conn);
    $transacao->descricao = $descricao;
    $transacao->valor = $valor;
    $transacao->data = $data;
    $transacao->tipo = $valor > 0 ? 'entrada' : 'saida'; 

    
    if ($transacao->criar()) {
        header('Location: index.php'); 
        exit();
    } else {
        echo "Erro ao salvar transação.";
    }
}

$transactions = getTransactions($conn);
$balance = calculateBalance($transactions);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200&family=Poppins:ital,wght@0,100;0,200;0,300;0,400&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <h1 id="logo">Controle Financeiro</h1>
    </header>

    <main class="container">
       
        <section id="balance">
    <h2 class="sr-only">Balanço</h2>

    <div class="card">
        <h3>
            <span>Entradas</span>
            <img src="../assets/income.svg" alt="entradas">
        </h3>
        <p id="incomeDisplay">R$ 0,00</p> 
    </div>

    <div class="card">
        <h3>
            <span>Saídas</span>
            <img src="../assets/expense.svg" alt="saídas">
        </h3>
        <p id="expenseDisplay">R$ 0,00</p> 
    </div>

    <div class="card total">
        <h3>
            <span>Total</span>
            <img class="dindin" src="../assets/dollar-currency-symbol.png" alt="total">
        </h3>
        <p id="totalDisplay">R$ 0,00</p> 
    </div>                                  
</section>


        
        <a href="#" onclick="Modal.open()" class="button new">+ Nova Transação</a>

        
        <div id="busca">
            <input type="text" placeholder="Buscar..." class="maxWidth input" id="myInput" onkeyup="tableSearch()" name="search" />
        </div>

        
        <section id="transaction">
            <h2 class="sr-only">Transações</h2>
            <table class="table" id="data-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['descricao']) ?></td>
                            <td class="<?= $transaction['valor'] > 0 ? 'income' : 'expense' ?>">
                                R$ <?= number_format($transaction['valor'], 2, ',', '.') ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($transaction['data'])) ?></td>
                            <td>
                                <a href="delete_transaction.php?id=<?= $transaction['id'] ?>" class="button delete">Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    
    <div class="modal-overlay">
        <div class="modal">
            <div id="form">
                <h2>Nova Transação</h2>
                <form action="index.php" method="POST">
                    <div class="input-group">
                        <label for="description">Descrição</label>
                        <input type="text" id="description" name="description" placeholder="Descrição" required />
                    </div>

                    <div class="input-group">
                        <label for="amount">Valor</label>
                        <input type="number" step="0.01" id="amount" name="amount" placeholder="0,00" required />
                        <small>Use o sinal - (negativo) para despesas</small>
                    </div>

                    <div class="input-group">
                        <label for="date">Data</label>
                        <input type="date" id="date" name="date" required />
                    </div>
                    
                    <div class="input-group actions">
                        <a href="#" onclick="Modal.close()" class="button cancel">Cancelar</a>
                        <button type="submit" class="button save">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../public/scripts.js"></script>
</body>
</html>
