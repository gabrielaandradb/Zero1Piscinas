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
$sqlPiscinas = "SELECT id, tipo, tamanho, status, resposta FROM piscinas WHERE cliente_id = :cliente_id";
$stmtPiscinas = $conexao->prepare($sqlPiscinas);
$stmtPiscinas->bindParam(':cliente_id', $_SESSION['ClassUsuarios']['id'], PDO::PARAM_INT);
$stmtPiscinas->execute();
$piscinas = $stmtPiscinas->fetchAll(PDO::FETCH_ASSOC);

// Tabela de preços
$tabelaPrecos = [
    'pequena' => [
        'Limpeza de Piscinas' => 150.00,
        'Manutenção' => 200.00,
        'Reparos' => 300.00,
        'Aquecimento de Piscinas' => 2000.00,
        'Instalação de Capas Protetoras' => 500.00,
        'Tratamento da Água' => 800.00
    ],
    'media' => [
        'Limpeza de Piscinas' => 250.00,
        'Manutenção' => 300.00,
        'Reparos' => 450.00,
        'Aquecimento de Piscinas' => 3000.00,
        'Instalação de Capas Protetoras' => 800.00,
        'Tratamento da Água' => 1200.00
    ],
    'grande' => [
        'Limpeza de Piscinas' => 350.00,
        'Manutenção' => 400.00,
        'Reparos' => 600.00,
        'Aquecimento de Piscinas' => 4000.00,
        'Instalação de Capas Protetoras' => 1000.00,
        'Tratamento da Água' => 1500.00
    ]
];

// Função para determinar a categoria da piscina
function getCategoriaTamanho($tamanho) {
    if ($tamanho <= 10) {
        return 'pequena';
    } elseif ($tamanho <= 25) {
        return 'media';
    } else {
        return 'grande';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/estilo.css">
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
            <h2>Zero1 Piscinas <br> <img src="img/logo1.png" alt="Ícone"></h2>
            <ul>
                <li><a href="Clientes.php">Voltar</a></li>
                <li><a href="Clientes.php">Voltar</a></li>
            </ul>
            <!-- Seção Meus Dados -->
        <div id="meus-dados" class="card">
            <h2>Meus Dados</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['nome']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['telefone']); ?></p>
            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['endereco']); ?></p>
            <button class="btn" onclick="window.location.href='editarClientes.php';">
                <img src="img/editar-usuario.png" alt="Editar-usuario">  Editar Informações</button>
        </div>
        </div>
        
        <div>
            <div class="header">
                <h1>Acompanhar Serviço</h1>
                <div class="header-text"><br>
             <p class="welcome" >Aqui você pode gerenciar suas informações e acompanhar os serviços.</p>
                </div>
                <a href="logout.php" class="btn">Sair <img src="img/sair.png" alt="sair"></a>
            </div>
            <?php if (count($piscinas) === 0): ?>
                <div class="card">
                    <p>Você ainda não cadastrou nenhuma piscina.</p>
                </div>
            <?php else: ?>
                <?php foreach ($piscinas as $piscina): ?>
                    <div class="card">
                        
                        
                        <h3 class="servicos-title">Serviços solicitados:</h3>
                        <?php
                        $sqlServicos = "SELECT tipo_servico, descricao, estatus, data_execucao, preco FROM servicos WHERE piscina_id = :piscina_id ORDER BY data_solicitacao DESC";
                        $stmtServicos = $conexao->prepare($sqlServicos);
                        $stmtServicos->bindParam(':piscina_id', $piscina['id'], PDO::PARAM_INT);
                        $stmtServicos->execute();
                        $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (count($servicos) === 0): ?>
                            <p>Sem resposta do profissional, aguarde!</p>
                        <?php else: ?>
                            <?php foreach ($servicos as $servico): ?>
                                <div class="servico-item">
                        <p><strong>Tipo de piscina:</strong> <?= htmlspecialchars($piscina['tipo']) ?></p>
                                    <p><strong>Resposta do profissional:</strong> <?= nl2br(htmlspecialchars($servico['descricao'])) ?></p>
                                    <p><strong>Status:</strong> <?= htmlspecialchars($servico['estatus']) ?></p>
                                    <p><strong>Data execução:</strong> <?= $servico['data_execucao'] ? date('d/m/Y H:i', strtotime($servico['data_execucao'])) : 'Não executado' ?></p>
                                    <p><strong>Preço:</strong> R$ <?= number_format($servico['preco'], 2, ',', '.') ?></p>
                                
                                 <!-- Botão de Confirmar -->
                <form action="Pagamento.php" method="GET">
                    <button class="btn" type="submit">
                        Realizar Pagamento
                    </button>
                </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>