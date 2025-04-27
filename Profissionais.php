<?php
session_start();

// Verifique se o usuário está logado e é um profissional
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

// Conexão com o banco de dados
require_once 'Conexao.php'; // Supondo que o arquivo de conexão se chame 'Conexao.php'
$conexao = Conexao::getInstance();

// Pegue o ID do profissional da sessão
$id_profissional = $_SESSION['ClassUsuarios'];

// Consulta para pegar os dados do profissional
$query = "SELECT * FROM usuarios WHERE id = :id_profissional AND tipo_usuario = 'profissional'";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
$stmt->execute();

// Verifique se os dados foram encontrados
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    echo "Profissional não encontrado!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento - Profissional</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        /* Estilo Geral */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            display: grid;
            grid-template-columns: 250px 1fr;
            height: auto;
        }

        /* Menu */
        .menu {
            background-color: #E0F7FA;
            color: #374151;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .menu h2 {
            font-size: 30px;
            margin: 0;
            color: #1F2937;
        }

        .menu nav ul {
            list-style: none;
            padding: 0;
            margin: 20px 0 0 0;
        }

        .menu ul li {
            margin-bottom: 10px;
        }

        .menu ul li a {
            color: #0077b6;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }

        .menu ul li a:hover {
            text-decoration: underline;
            color: #005f8a;
        }

        /* Conteúdo Principal */
        .content {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 20px;
            padding: 20px;
        }

        /* Cabeçalho */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            font-size: 24px;
            color: black;
        }

        .header .welcome {
            font-size: 18px;
            color: #0077b6;
        }

        .header .logout {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .header .logout:hover {
            background-color: #d32f2f;
        }

        /* Cards */
        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card h2 {
            font-size: 22px;
            color: #0077b6;
        }

        .card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .card table th, .card table td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .card table th {
            background-color: #f4f4f4;
        }

        .card p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        /* Botões */
        .btn, .btn-dados {
            padding: 10px 20px;
            background: #0077b6;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 20px;
            transition: background 0.3s, transform 0.3s;
        }

        .btn:hover, .btn-dados:hover {
            background: #005f8a;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .menu {
                align-items: center;
                padding: 10px;
            }

            .card {
                width: 95%;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Menu -->
    <div class="menu">
        <h2>Zero1 Piscinas</h2>
        <nav>
            <ul>
                <li><a href="#usuarios">Usuários Cadastrados</a></li>
                <li><a href="#formularios">Formulários Recebidos</a></li>
                <li><a href="#perfil">Meus Dados</a></li>
            </ul>
        </nav>

        <!-- Meus Dados -->
        <div id="perfil" class="card">
            <h2>Meus Dados</h2>
            <p><strong>Nome:</strong> <?= htmlspecialchars($profissional['nome']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($profissional['email']); ?></p>
            <p><strong>Telefone:</strong> <?= htmlspecialchars($profissional['telefone']); ?></p>
            <p><strong>Endereço:</strong> <?= htmlspecialchars($profissional['endereco']); ?></p>
            <br>
            <input type="button" value="Editar informações" class="btn-dados" onclick="window.location.href='perfilProfissional.php';">
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <!-- Cabeçalho -->
        <div class="header">
            <h1>Bem-vindo, <strong><?= htmlspecialchars($profissional['nome']); ?></strong></h1>
            <p class="welcome">Gerenciamento de Serviços</p>
            <a href="logout.php" class="btn">Sair</a>
        </div>

        <!-- Usuários Cadastrados -->
        <div id="usuarios" class="card">
            <h2>Usuários Cadastrados</h2>
            <?php if (!empty($clientes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['nome']); ?></td>
                        <td><?= htmlspecialchars($cliente['email']); ?></td>
                        <td><?= htmlspecialchars($cliente['telefone']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>Nenhum cliente cadastrado.</p>
            <?php endif; ?>
        </div>

        <!-- Formulários dos Clientes -->
        <div id="formularios" class="card">
            <h2>Formulários Recebidos</h2>
            <?php if (!empty($formularios)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tamanho da Piscina</th>
                        <th>Tipo de Piscina</th>
                        <th>Profundidade</th>
                        <th>Data de Instalação</th>
                        <th>Endereço</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formularios as $formulario): ?>
                    <tr>
                        <td><?= htmlspecialchars($formulario['clienteNome']); ?></td>
                        <td><?= htmlspecialchars($formulario['tamanho'] ?? 'Não informado'); ?></td>
                        <td><?= htmlspecialchars($formulario['tipo'] ?? 'Não informado'); ?></td>
                        <td><?= htmlspecialchars($formulario['profundidade'] ?? 'Não informado'); ?></td>
                        <td><?= htmlspecialchars($formulario['dataInstalacao'] ?? 'Não informado'); ?></td>
                        <td><?= htmlspecialchars($formulario['enderecoCliente'] ?? 'Não informado'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>Nenhum formulário recebido.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
