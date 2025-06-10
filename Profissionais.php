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
$id_profissional = $_SESSION['ClassUsuarios']['id'];

// Consulta para pegar os dados do profissional
$query = "SELECT * FROM usuarios WHERE id = :id_profissional AND tipo_usuario = 'profissional'";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
$stmt->execute();

// Verifique se os dados foram encontrados
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar se o e-mail do profissional termina com @profissional.com
if (!$profissional || !str_ends_with($profissional['email'], '@profissional.com')) {
    echo "Profissional não encontrado ou e-mail inválido!";
    exit;
}


$ordem = isset($_GET['ordem']) && $_GET['ordem'] === 'ASC' ? 'ASC' : 'DESC';

// Atualize a consulta para ordenar com base na data_solicitacao
$query_piscinas = "
    SELECT 
        piscinas.*, 
        usuarios.nome AS cliente_nome, 
        usuarios.email AS cliente_email
    FROM piscinas
    INNER JOIN usuarios ON piscinas.cliente_id = usuarios.id
    WHERE piscinas.status = 'pendente'
    ORDER BY piscinas.data_solicitacao $ordem;
";
$stmt_piscinas = $conexao->prepare($query_piscinas);
$stmt_piscinas->execute();

$piscinas = $stmt_piscinas->fetchAll(PDO::FETCH_ASSOC);


// Consulta para pegar todos os clientes cadastrados
$query_clientes = "SELECT * FROM usuarios WHERE tipo_usuario = 'cliente'";
$stmt_clientes = $conexao->prepare($query_clientes);
$stmt_clientes->execute();

// Armazene os resultados dos clientes
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

$ordem = isset($_GET['ordem']) && $_GET['ordem'] === 'ASC' ? 'ASC' : 'DESC';

// Consulta para pegar piscinas com o endereço do cliente
$query_piscinas = "
    SELECT 
        p.id,
        u.nome AS cliente_nome,
        u.email AS cliente_email,
        u.endereco AS cliente_endereco,
        p.tamanho,
        p.tipo,
        p.profundidade,
        p.data_instalacao,
        p.servico_desejado,
        p.status,
        p.preco
    FROM piscinas p
    JOIN usuarios u ON p.cliente_id = u.id
    WHERE p.status = 'pendente'
    ORDER BY p.data_solicitacao $ordem;
";

$stmt_piscinas = $conexao->prepare($query_piscinas);
$stmt_piscinas->execute();
$piscinas = $stmt_piscinas->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">
    <title>Gerenciamento - Profissional</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffffff;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;  /* Garante que o conteúdo da página se alinhe no centro horizontalmente */
            overflow-x: hidden; /* Impede a rolagem lateral */
        }

        .container {
            width: 100%;   /* A largura será 100% da largura disponível */
            min-width: 100vw; /* Garante que o conteúdo ocupe a largura total da tela */
            display: flex;
            height: auto;
            padding-left: 300px;    /* Remove o padding */
            margin: 0;     /* Remove a margem */
        }


        .menu {
            position: fixed; /* Fixa a barra lateral na tela */
            top: 0;
            left: 0;
            height: 100%; /* Faz com que a barra lateral ocupe toda a altura */
            width: 300px; /* Define a largura fixa */
            background-color: #E0F7FA; 
            color: #374151; 
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: left;
            overflow-y: auto; /* Adiciona rolagem se necessário */
        }

        .menu h2 {
            font-size: 30px;
            margin: 0;
            color: #0077b6;
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
            color: black;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }

        .menu ul li a:hover {
            text-decoration: underline;
            color: #black;
        }

        p a {
            color: #0077b6;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
        }

        /* Conteúdo Principal */
        .content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100%;
            overflow-y: auto;  /* Permite rolar a página se o conteúdo for grande */
        }

        .header {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            background-color: #ffffff;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            font-size: 24px;
            color: black;
            margin: 0; /* Remove qualquer margem extra */
        }

        .header-text {
            margin-top: 5px;
        }

        .header .welcome {
            font-size: 18px;
            color: #0077b6;
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
            flex-grow: 1;
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

        }

        .btn:hover, .btn-dados:hover {
            background: #005f8a;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .card {
                width: 100%;
            }
        }

        .btn {
            background-color: #0077b6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #005f8a;
            transform: scale(1.05);
        }

        .mensagem {
            margin: 20px;
            padding: 15px;
            background-color: greenyellow;
            border: 1px solid #b0e0e6;
            border-radius: 5px;
            color: green;
            text-align: center;
        }

        h2 img {
            width: 60px;
            height: 60px;
            vertical-align: middle;
            margin-left: 10px;
        }

        button img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-left: 10px;
        }

        #perfil {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            width: 100%; /* Garante que ele se adapte ao layout */
            max-width: 1200px; /* Limita o tamanho máximo */
        }

        #perfil h2 {
            font-size: 22px;
            color: #0077b6;
            margin-bottom: 15px;
            text-align: center; /* Centraliza o título dentro do card */
        }

        #perfil p {
            font-size: 16px;
            color: #374151;
            line-height: 1.5;
        }

        #perfil .btn-dados {
            display: block;
            margin: 20px auto; /* Centraliza o botão */
            padding: 10px 20px;
            background-color: #0077b6;
            color: #ffffff;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        #perfil .btn-dados:hover {
            background-color: #005f8a;
            transform: scale(1.05);
        }

        a img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-left: 10px;
        }

    </style>
</head>
<body>

<div class="container">

    <!-- Menu -->
    <div class="menu">
        <h2>Zero1 Piscinas <br><img src="img/logo1.png" alt="Ícone"></h2>

        <nav>
            <ul>
                <li><a href="#usuarios">Usuários Cadastrados</a></li>
                <li><a href="#formularios">Formulários Recebidos</a></li>
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
            <button class="btn" onclick="window.location.href='editarProfissional.php';">
                <img src="img/editar-usuario.png" alt="Editar-usuario">  Editar Informações</button>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <!-- Cabeçalho -->
        <div class="header">
             <h1>Bem-vindo Profissional, <strong><?= htmlspecialchars($profissional['nome']); ?></strong></h1>
        <div class="header-text">
             <p class="welcome">Gerenciamento de Serviços</p>
        </div>
    <a href="logout.php" class="btn">Sair <img src="img/sair.png" alt="sair"></a>
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

    <!-- Opções de ordenação -->
<p>
    Ordenar por data de solicitação: 
    <a href="?ordem=ASC">Mais antigos</a> | 
    <a href="?ordem=DESC">Mais recentes</a>
</p>
<hr><br>

    <?php if (!empty($piscinas)): ?>
    <?php foreach ($piscinas as $piscina): ?>
        <div class="formulario">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($piscina['cliente_nome']); ?> (<?= htmlspecialchars($piscina['cliente_email']); ?>)</p>
            <p><strong>Endereço:</strong> 
                <?= !empty($piscina['cliente_endereco']) 
                    ? htmlspecialchars($piscina['cliente_endereco']) 
                    : 'Endereço não informado'; ?>
            </p>
            <p><strong>Tamanho:</strong> <?= htmlspecialchars($piscina['tamanho']); ?></p>
            <p><strong>Tipo:</strong> <?= htmlspecialchars($piscina['tipo']); ?></p>
            <p><strong>Profundidade:</strong> <?= htmlspecialchars($piscina['profundidade']); ?></p>
            <p><strong>Data de instalação desejada:</strong> <?= date('d/m/Y', strtotime($piscina['data_instalacao'])); ?></p>
            <p><strong>Serviço desejado:</strong> <?= htmlspecialchars($piscina['servico_desejado']); ?></p>
            <p><a href="ResponderSolicitacao.php?id=<?= $piscina['id']; ?>" class="btn">Responder</a></p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma solicitação pendente.</p>
<?php endif; ?>

</div>


    </div>
</div>
</body>
</html>
