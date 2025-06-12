<?php
session_start();
require_once 'Conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['ClassUsuarios']['id'])) {
    header('Location: LoginCadastro.php');
    exit;
}

$usuarioId = $_SESSION['ClassUsuarios']['id'];

// Conexão com banco de dados
$conexao = Conexao::getInstance();

// Busca os serviços mais recentes contratados pelo cliente logado
$sqlServico = "
    SELECT s.tipo_servico, s.descricao, s.preco, s.estatus, p.tamanho, p.tipo AS tipo_piscina, u.nome AS cliente_nome, u.email AS cliente_email
    FROM servicos s
    JOIN piscinas p ON s.piscina_id = p.id
    JOIN usuarios u ON p.cliente_id = u.id
    WHERE u.id = :usuario_id
    ORDER BY s.data_solicitacao DESC
    LIMIT 1
";

$stmtServico = $conexao->prepare($sqlServico);
$stmtServico->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
$stmtServico->execute();

$servico = $stmtServico->fetch(PDO::FETCH_ASSOC);

// Verifica se há serviços associados
if (!$servico) {
    echo "Nenhum serviço contratado encontrado para este usuário.";
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
        <p><strong>Status:</strong> <?= htmlspecialchars($servico['estatus']) ?></p>
        <p><strong>Valor Total:</strong> R$ <?= number_format($servico['preco'], 2, ',', '.') ?></p>

        <!-- Botão do PayPal -->
        <div id="paypal-button-container"></div>
    </div>

    <script src="https://sandbox.paypal.com/sdk/js?client-id=AUoP8cys_BnXZ7OYianLBlZa02TEjN3N7qU8HhMSv_HAMSN6TWV1Iz0aj3ez6yusRUQDv_AHupyCi9b6&currency=BRL"></script>
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

                fetch('ConfirmarPagamento.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ orderID: data.orderID })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pagamento confirmado no servidor!');
                        window.location.href = 'SucessoPagamento.php';
                    } else {
                        alert('Erro ao confirmar pagamento: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Erro ao comunicar com o servidor:', err);
                    alert('Erro ao comunicar com o servidor. Por favor, tente novamente.');
                });
            });
        },
        onCancel: function() {
            alert('Pagamento cancelado. Você pode tentar novamente ou alterar o serviço.');
        },
        onError: function(err) {
            console.error('Erro no pagamento:', err);
            alert('Ocorreu um erro durante o pagamento. Por favor, tente novamente.');
        }
    }).render('#paypal-button-container');
    </script>

</body>
</html>
