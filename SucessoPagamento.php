<?php
session_start();

if (!isset($_SESSION['ClassUsuarios']['id'])) {
    header('Location: LoginCadastro.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">
    <title>Pagamento Concluído</title>
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
            text-decoration: none;
        }
        .btn:hover {
            background-color: #005f8a;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pagamento Concluído</h1>
        <p>Seu pagamento foi processado com sucesso!</p>
        <p>Obrigado por escolher nossos serviços.</p>
        <a href="index.php" class="btn">Voltar ao Início</a>
    </div>
</body>
</html>
