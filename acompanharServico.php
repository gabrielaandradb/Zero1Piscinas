<?php
session_start();
require_once 'Conexao.php';

// Verifica se usuário está logado
if (!isset($_SESSION['ClassUsuarios']['id'])) {
    header('Location: LoginCadastro.php');
    exit;
}

// Se recebeu piscina_id via GET, salva na sessão e redireciona
if (isset($_GET['piscina_id'])) {
    $_SESSION['piscina_id'] = intval($_GET['piscina_id']);
    header('Location: acompanharServico.php');
    exit;
}

$conexao = Conexao::getInstance();

// Busca as piscinas do cliente logado
$queryPiscinas = "
    SELECT 
        p.id, 
        p.tipo, 
        p.tamanho, 
        p.profundidade, 
        p.data_instalacao, 
        p.servico_desejado, 
        p.status, 
        u.nome AS cliente_nome, 
        u.email AS cliente_email, 
        u.endereco AS cliente_endereco 
    FROM piscinas p
    JOIN usuarios u ON p.cliente_id = u.id
    WHERE p.cliente_id = :cliente_id
    ORDER BY p.data_instalacao ASC
";

$stmtPiscinas = $conexao->prepare($queryPiscinas);
$stmtPiscinas->bindParam(':cliente_id', $_SESSION['ClassUsuarios']['id'], PDO::PARAM_INT);
$stmtPiscinas->execute();
$piscinas = $stmtPiscinas->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">
    <title>Minhas Piscinas e Serviços</title>
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

        .menu nav {
            margin-top: 10px;
        }

        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
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
            color: #005f8a;
        }

        .content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .header {
            left: 20px; /* Margem esquerda */
            width: 1250px; /* Largura fixa */
            padding: 10px; /* Espaçamento interno */
            background-color: white; /* Fundo claro */
            border-radius: 8px; /* Cantos arredondados */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra para destaque */
            text-align: left; /* Alinha texto à esquerda */
            font-family: Arial, sans-serif; /* Fonte padrão */
        }

        /* Estilo do título */
        .header h1 {
            font-size: 1.6em; /* Tamanho do título */
            margin: 0 0 10px; /* Margem inferior do título */
            color: #333; /* Cor escura */
        }

        .header-text .welcome {
            font-size: 1.1em; /* Tamanho do subtítulo */
            color: #555; /* Cor neutra */
            line-height: 0.1; /* Altura da linha */
            margin-bottom: 20px; /* Espaço entre o texto e o botão */
        }

        .header .btn {
            background-color: #0077b6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .header .btn:hover {
            background-color: #005f8a;
            transform: scale(1.05);
        }

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

        .card p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

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
            background-color: Plum;
            border: 1px solid #b0e0e6;
            border-radius: 5px;
            color: green;
            text-align: center;
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
        <div class="menu">
            <h2>Zero1 Piscinas <br> <img src="img/logo1.png" alt="Logo"></h2>
            <ul>
                <li><a href="Clientes.php">Voltar</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
            <div id="perfil" class="card">
                <h2>Meus Dados</h2>
                <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['ClassUsuarios']['nome']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['ClassUsuarios']['email']); ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($_SESSION['ClassUsuarios']['telefone']); ?></p>
                <p><strong>Endereço:</strong> <?= htmlspecialchars($_SESSION['ClassUsuarios']['endereco']); ?></p>
                <button class="btn" onclick="window.location.href='editarClientes.php';">
                    <img src="img/editar-usuario.png" alt="Editar"> Editar Informações
                </button>
            </div>
        </div>
        <div class="content">
            <div class="header">
                <h1>Acompanhar Serviço</h1>
                <p>Gerencie suas informações e acompanhe os serviços solicitados.</p>
            </div>
            <?php foreach ($piscinas as $piscina): ?>
    <div class="card">
        <!-- Detalhes da Piscina -->
        <div class="detalhes-piscina">
            <h3>Formulário enviado:</h3>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($piscina['cliente_nome']); ?> (<?= htmlspecialchars($piscina['cliente_email']); ?>)</p>
            <p><strong>Endereço:</strong> <?= htmlspecialchars($piscina['cliente_endereco'] ?: 'Endereço não informado'); ?></p>
            <p><strong>Tamanho:</strong> <?= htmlspecialchars($piscina['tamanho']); ?></p>
            <p><strong>Tipo:</strong> <?= htmlspecialchars($piscina['tipo']); ?></p>
            <p><strong>Profundidade:</strong> <?= htmlspecialchars($piscina['profundidade']); ?></p>
            <p><strong>Data de Instalação:</strong> <?= date('d/m/Y', strtotime($piscina['data_instalacao'])); ?></p>
            <p><strong>Serviço Desejado:</strong> <?= htmlspecialchars($piscina['servico_desejado']); ?></p>
        
        <div class="actions">

                <button class="btn" onclick="window.location.href='excluirSolicitacao.php';">
                    <img src="img/excluir.png" alt="Excluir"> Excluir Solicitação</button>
            </div>
        </div>
<br>
        <!-- Serviços Solicitados -->
         <form action="Pagamento.php" method="GET">
        <div class="servicos-solicitados">
            <h3>Serviços Solicitados:</h3>
            <?php
            $queryServicos = "
                SELECT tipo_servico, descricao, estatus, data_execucao, preco 
                FROM servicos 
                WHERE piscina_id = :piscina_id 
                ORDER BY data_solicitacao DESC
            ";
            $stmtServicos = $conexao->prepare($queryServicos);
            $stmtServicos->bindParam(':piscina_id', $piscina['id'], PDO::PARAM_INT);
            $stmtServicos->execute();
            $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (empty($servicos)): ?>
                <p><em>Sem resposta do profissional, aguarde.</em></p>
            <?php else: ?>
                <?php foreach ($servicos as $servico): ?>
                    <div class="servico-item">
                        <p><strong>Resposta do profissional:</strong> <?= nl2br(htmlspecialchars($servico['descricao'])); ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($servico['estatus']); ?></p>
                        <p><strong>Data de Execução:</strong> <?= $servico['data_execucao'] ? date('d/m/Y H:i', strtotime($servico['data_execucao'])) : 'Não executado'; ?></p>
                        <p><strong>Valor:</strong> R$ <?= number_format($servico['preco'], 2, ',', '.'); ?></p>
                <!-- botão -->       
                 
                
            <?php if ($servico['estatus'] === 'concluido'): ?>
                <button class="btn" onclick="window.location.href='Pagamento.php';">
                Realizar Pagamento
                </button>
            <?php elseif ($servico['estatus'] === 'cancelado'): ?>
                <button class="btn" onclick="window.location.href='Confirmar.php';">
                Confirmar
                </button>
            <?php endif; ?>
            <?php if (empty($piscinas)): ?>
    <p>Você ainda não solicitou nenhum serviço. <a href="Clientes.php">Clique aqui para solicitar</a>.</p>
<?php endif; ?>
            </form>

                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

            
        </div>
    </div>
</body>
</html>