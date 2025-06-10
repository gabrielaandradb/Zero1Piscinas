<?php
session_start();
require_once 'Conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['ClassUsuarios']['id'])) {
    header('Location: LoginCadastro.php');
    exit;
}

// Verifica se o ID do serviço foi enviado
if (!isset($_GET['servico_id'])) {
    echo "Nenhum serviço selecionado.";
    exit;
}

$conexao = Conexao::getInstance();
$servicoId = intval($_GET['servico_id']);

// Busca os detalhes do serviço
$sqlServico = "SELECT s.tipo_servico, s.descricao, s.preco, p.tipo AS tipo_piscina 
               FROM servicos s 
               INNER JOIN piscinas p ON s.piscina_id = p.id 
               WHERE s.id = :servico_id";
$stmtServico = $conexao->prepare($sqlServico);
$stmtServico->bindParam(':servico_id', $servicoId, PDO::PARAM_INT);
$stmtServico->execute();
$servico = $stmtServico->fetch(PDO::FETCH_ASSOC);

// Verifica se o serviço existe
if (!$servico) {
    echo "Serviço não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">
    <title>Pagamento</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #F7F9FC;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .card h1 {
            font-size: 24px;
            color: #0077b6;
            margin-bottom: 20px;
        }

        .card p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .btn {
            background-color: #0077b6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #005f8a;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pagamento</h1>
        <p><strong>Tipo de Piscina:</strong> <?= htmlspecialchars($servico['tipo_piscina']) ?></p>
        <p><strong>Serviço:</strong> <?= htmlspecialchars($servico['tipo_servico']) ?></p>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($servico['descricao'])) ?></p>
        <p><strong>Preço:</strong> R$ <?= number_format($servico['preco'], 2, ',', '.') ?></p>

        <!-- Botão do PayPal -->
        <div id="paypal-button-container"></div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=SB_CLIENT_ID&currency=BRL"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($servico['preco'], 2, '.', '') ?>'
                        },
                        description: "Pagamento do serviço: <?= htmlspecialchars($servico['tipo_servico']) ?>",
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Pagamento realizado com sucesso por ' + details.payer.name.given_name);
                    // Aqui você pode redirecionar ou salvar no banco
                });
            },
            onCancel: function() {
                alert('Pagamento cancelado.');
            },
            onError: function(err) {
                console.error(err);
                alert('Ocorreu um erro durante o pagamento.');
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
