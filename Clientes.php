<?php
session_start();
require_once 'Conexao.php';

// Exemplo para configurar a sessão com base em uma piscina selecionada
if (isset($_GET['piscina_id'])) {
    $_SESSION['piscina_id'] = intval($_GET['piscina_id']);
    header('Location: acompanharServico.php');
    exit;
}

if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header('Location: LoginCadastro.php');
    exit;
}

$mensagemSolicitacao = isset($_SESSION['mensagemSolicitacao']) ? $_SESSION['mensagemSolicitacao'] : '';
unset($_SESSION['mensagemSolicitacao']);

$infoPendente = empty($_SESSION['ClassUsuarios']['telefone']) || empty($_SESSION['ClassUsuarios']['endereco']);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero1 Piscinas - Clientes</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">

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
            align-items: center;  
            overflow-x: hidden; 
        }

        .container {
            width: 100%;   
            min-width: 100vw; 
            display: flex;
            height: auto;
            padding-left: 300px;   
            margin: 0; 
        }

        .menu {
            position: fixed; /* Fixa a barra lateral na tela */
            top: 0;
            left: 0;
            height: 100%; /* Faz com que a barra lateral ocupe toda a altura */
            width: 300px; 
            background-color: #E0F7FA; 
            color: #374151; 
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: left;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra para destaque */
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
            left: 20px; 
            width: 1250px; 
            padding: 10px; 
            background-color: white;
            border-radius: 8px; 
            text-align: left; 
            font-family: Arial, sans-serif; 
        }

        /* Estilo do título */
        .header h1 {
            font-size: 1.6em; 
            margin: 0 0 10px; 
            color: #333; 
        }

        .header-text .welcome {
            font-size: 1.1em; 
            color: #555; 
            line-height: 0.1; 
            margin-bottom: 20px; 
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
            text-align: center;
            margin: 20px; 
            padding: 10px; 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
            border-radius: 5px;
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

        .whatsapp-container {
            text-align: center;
            margin-top: 50px;
            font-family: Arial, sans-serif;
        }

        .whatsapp-container a {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px;
        }

        .whatsapp-container a:hover {
            background-color: #1ebd5a;
        }
        
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabelaPrecos = {
            "Limpeza de Piscinas": { "pequena": 150, "media": 250, "grande": 350 },
            "Manutenção": { "pequena": 200, "media": 300, "grande": 400 },
            "Reparos": { "pequena": 300, "media": 450, "grande": 600 },
            "Aquecimento de Piscinas": { "pequena": 600, "media": 700, "grande": 800 },
            "Instalação de Capas Protetoras": { "pequena": 100, "media": 150, "grande": 200 },
            "Tratamento Avançado da Água": { "pequena": 250, "media": 350, "grande": 450 }
        };

        const tamanhoSelect = document.getElementById('tamanho');
        const servicoSelect = document.getElementById('servico');
        const valorServicoDisplay = document.createElement('div');
        valorServicoDisplay.style.marginTop = '20px';
        valorServicoDisplay.style.fontWeight = 'bold';

        const form = document.querySelector('form');
        form.appendChild(valorServicoDisplay);

        function calcularValor() {
            const tamanho = tamanhoSelect.value;
            const servico = servicoSelect.value;

            if (tamanho && servico && tabelaPrecos[servico] && tabelaPrecos[servico][tamanho]) {
                const valor = tabelaPrecos[servico][tamanho];
                valorServicoDisplay.textContent = `Valor do Serviço: R$ ${valor.toFixed(2).replace('.', ',')}`;
            } else {
                valorServicoDisplay.textContent = '';
            }
        }

        tamanhoSelect.addEventListener('change', calcularValor);
        servicoSelect.addEventListener('change', calcularValor);
    });
</script>

</head>
<body>


<div class="container">
    <!-- Sidebar -->
    <div class="menu">
        <h2>Zero1 Piscinas <br> <img src="img/logo1.png" alt="Ícone"></h2>
        <ul>
            <li><a href="index.php">Voltar</a></li>
            <li><a href="acompanharServico.php">Acompanhar Serviço</a></li>
        </ul>

        <!-- Meus Dados -->
        <div id="perfil" class="card">
            <h2>Meus Dados</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['nome']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['telefone']); ?></p>
            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['endereco']); ?></p>

            <?php 
    // Verifica se telefone ou endereco estão vazios
    if (empty($_SESSION['ClassUsuarios']['telefone']) || empty($_SESSION['ClassUsuarios']['endereco'])): ?>
        <p style="color: red; font-weight: bold; margin-top: 10px;">
            Informações pendentes. Por favor, complete seus dados.
        </p>
    <?php endif; ?>
            <button class="btn" onclick="window.location.href='editarClientes.php';">
                <img src="img/editar-usuario.png" alt="Editar-usuario">  Editar Informações</button>
                
        </div>
        
    </div>

    <!-- Conteúdo principal -->
    <div>
        <div class="header">
            <h1>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['ClassUsuarios']['nome']); ?></h1>
            <div class="header-text"><br>
             <p class="welcome" >Aqui você pode gerenciar suas informações e acompanhar os serviços.</p>
        </div>
    <a href="logout.php" class="btn">Sair <img src="img/sair.png" alt="sair"></a>

    <br><br>
    
</div>


        <!-- Seção Minha Piscina -->
        <div id="minha-piscina" class="card">
            <h2>Detalhes da Minha Piscina</h2>
            <?php if ($mensagemSolicitacao): ?>
                <div class="mensagem">
                    <p><?php echo htmlspecialchars($mensagemSolicitacao); ?></p>
                </div>
            <?php endif; ?>
            <form action="salvarSolicitacao.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="usuarioId" value="<?php echo htmlspecialchars($_SESSION['ClassUsuarios']['id']); ?>">

                <label for="tamanho">Tamanho da Piscina:</label>
                <select id="tamanho" name="tamanho" required>
                    <option value=""></option>
                    <option value="pequena">Pequena (até 10m²)</option>
                    <option value="media">Média (até 25m²)</option>
                    <option value="grande">Grande (acima de 25m²)</option>
                </select>
                <label for="tipo">Tipo de Piscina:</label>
                
                <select id="tipo" name="tipo" required>
                    <option value=""></option>
                    <option value="Fibra de Vidro">Fibra de Vidro</option>
                    <option value="Vinil">Vinil</option>
                    <option value="Plastico/ Lona"> Plástico/ Lona</option>
                    <option value="Alvenaria">Alvenaria</option>
                    <option value="Areia">Areia</option>
                    <option value="Outro">Outro</option>
                </select>

                <label for="profundidade">Profundidade:</label>
                <input type="text" id="profundidade" name="profundidade" required>

                <label for="dataInstalacao">Data de Instalação:</label>
                <input type="date" id="dataInstalacao" name="dataInstalacao" required>

                <label for="servico">Serviço desejado:</label>
                <select id="servico" name="servico" required>
                    <option value=""></option>
                    <option value="Limpeza de Piscinas">Limpeza de Piscinas</option>
                    <option value="Manutenção">Manutenção</option>
                    <option value="Reparos">Reparos</option>
                    <option value="Aquecimento de Piscinas">Aquecimento de Piscinas</option>
                    <option value="Instalação de Capas Protetoras">Instalação de Capas Protetoras</option>
                    <option value="Tratamento Avançado da Água">Tratamento da Água</option>
                </select>
                <input type="hidden" id="preco" name="preco">

                
                <br><br>
                <?php if ($infoPendente): ?>
                <p style="color: red; font-weight: bold;">
                    Você não pode solicitar um serviço até completar seu telefone e endereço.
                </p>
                <?php endif; ?>

                <button type="submit" class="btn" <?php echo $infoPendente ? 'disabled' : ''; ?>>Solicitar Serviço</button>
            </form>
            <br>

            
            <!-- Whastapp -->
                <div class="whatsapp-container">
                <p>📞 Para quaisquer dúvidas ou informações, entre em contato conosco pelo WhatsApp:</p>
                <a href="https://wa.me/5561998916927?text=Olá,%20gostaria%20de%20tirar%20uma%20dúvida." target="_blank">
                Falar no WhatsApp
               </a>
              </div>
            
        </div>
    </div>
</div>
</body>
</html>