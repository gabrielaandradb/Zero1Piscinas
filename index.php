<?php
session_start();

$usuario = isset($_SESSION['ClassUsuarios']) ? $_SESSION['ClassUsuarios'] : null;

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Zero1 Piscinas</title>
    <link rel="stylesheet" href="css/estilo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
        }
        .index {
            max-width: 1000px;
            margin: 50px auto;
            padding: 50px;
            text-align: center;
        }
        h1, h2 {
            color: #0077b6;
        }
        h1 {
            font-size: 32px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .p-estilo {
            font-size: 18px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 25px;
        }
        .usuario-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end; /* Alinha tudo à direita */
            -top: 20px;
        }

        .saudacao-login {
            font-size: 22px; /* Aumenta o tamanho da fonte */
            color: #000000; /* Cor preta */
            font-weight: bold; /* Deixa o nome em negrito */
            margin-right: 0px; /* Adiciona espaço entre o nome e o botão */
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50; /* Cor de fundo do botão */
            color: white; /* Cor do texto do botão */
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .btn:hover {
            background-color: #45a049; /* Cor do botão ao passar o mouse */
        }

        .menu {
            list-style: none;
            display: flex;
            gap: 15px;
            margin: 0;
            padding: 0;
            justify-content: right;
        }
        .menu li a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            font-size: 16px;
        }
        .menu li a:hover {
            text-decoration: underline;
        }
        .servicos-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            padding: 50px;
        }
        .servicos-container h2 {
            font-size: 36px;
            color: #0077b6;
            margin-bottom: 40px;
            font-weight: bold;
        }
        .servicos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .servico {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        .servico img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .servico:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .servico h3 {
            font-size: 22px;
            color: #0072ff;
            margin-top: 20px;
            text-transform: uppercase;
        }
        .servico p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 15px;
            }
            .servicos {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 480px) {
            .servicos {
                grid-template-columns: 1fr;
            }
            .saudacao-login {
                font-size: 20px;
                color: #000000;
                text-align: left;
                padding: 2px 0 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="index">
        <h1>Bem-vindo à Zero1 Piscinas!</h1>
        <div class="usuario-info">
    <?php if ($usuario): ?>
        <p class="saudacao-login"><?= htmlspecialchars($usuario['nome']); ?></p>
        <a href="logout.php" class="btn">Sair</a>
    <?php else: ?>
        <a href="LoginCadastro.php" class="btn">Login/Cadastro</a>
    <?php endif; ?>
</div>
        <br><br>
        <nav>
            <ul class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#servicos">Serviços</a></li>
                <li><a href="index.php#quem-somos">Sobre nós</a></li>
                <?php if ($usuario): ?>
                    <li><a href="Clientes.php">Meu Perfil</a></li>
                <?php else: ?>
                    <li><a href="LoginCadastro.php">Meu Perfil</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <br>

        <p class="p-estilo">
            Bem-vindo ao Zero1 Piscinas! Estamos aqui para facilitar o cuidado 
            com sua piscina, conectando você aos melhores profissionais para 
            serviços de limpeza, manutenção e reparo. Explore nossas soluções 
            personalizadas e aproveite uma experiência prática, segura e eficiente. 
            Estamos prontos para ajudar a manter sua piscina sempre 
            em perfeitas condições!
        </p>
        <div class="servicos-container" id="servicos">
    <h2>Nossos Serviços</h2>
    <div class="servicos">
        <div class="servico">
            <img src="img/limpeza2.jpg" alt="Limpeza de Piscina">
            <h3>Limpeza de Piscinas</h3>
            <p>Realizamos uma limpeza completa para garantir a pureza da água e a segurança dos banhistas.</p>
        </div>

        <div class="servico">
            <img src="img/manutencao2.jpg" alt="Manutenção de Piscinas">
            <h3>Manutenção</h3>
            <p>Oferecemos um serviço abrangente para manter sua piscina em ótimas condições.</p>
        </div>

        <div class="servico">
            <img src="img/reparo1.jpg" alt="Reparo de Piscinas">
            <h3>Reparos</h3>
            <p>Corrigimos vazamentos e realizamos consertos em equipamentos, garantindo que sua piscina esteja sempre funcional.</p>
        </div>

        <div class="servico">
            <img src="img/aquecimento1.webp" alt="Aquecimento de Piscinas">
            <h3>Aquecimento de Piscinas</h3>
            <p>Instalamos sistemas de aquecimento para você aproveitar sua piscina durante todo o ano.</p>
        </div>

        <div class="servico">
            <img src="img/acabamentos1.png" alt="Acabamentos e Bordas">
            <h3>Acabamentos e Bordas</h3>
            <p>Personalize sua piscina com materiais de alta qualidade e bordas cimentícias para maior durabilidade.</p>
        </div>

        <div class="servico">
            <img src="img/construcao1.jpg" alt="Construção e Reforma de Piscinas">
            <h3>Construção e Reforma</h3>
            <p>Construímos e reformamos piscinas de diferentes estilos e tamanhos para atender às suas necessidades.</p>
        </div>

        <div class="servico">
            <img src="img/capa1.jpg" alt="Instalação de Capas Protetoras">
            <h3>Instalação de Capas Protetoras</h3>
            <p>Instalamos capas de proteção de alta resistência para maior segurança e economia de limpeza.</p>
        </div>

        <div class="servico">
            <img src="img/automacao1.jpg" alt="Automação de Piscinas">
            <h3>Automação de Piscinas</h3>
            <p>Automatize o controle da sua piscina com sistemas modernos para iluminação, temperatura e filtragem.</p>
        </div>

        <div class="servico">
            <img src="img/tratamento1.jpg" alt="Tratamento Avançado da Água">
            <h3>Tratamento Avançado da Água</h3>
            <p>Oferecemos soluções de alta tecnologia para garantir máxima qualidade e segurança da água.</p>
        </div>
    </div>
    <br><br>
    <a href="Orcamento.php" class="btn">Solicitar Orçamento</a>
</div>

            <br><br>
            <div class="quem-somos" id="quem-somos" style="text-align: center; margin: 50px 0;">
                <h2>Quem Somos</h2>
        <p class="p-estilo">
                    Na Zero1 Piscinas, acreditamos que uma piscina bem cuidada é sinônimo de lazer, saúde e segurança. 
                    Com anos de experiência no mercado, nossa missão é oferecer soluções inovadoras e acessíveis 
                    para a manutenção, construção e personalização de piscinas, garantindo a satisfação de nossos clientes.
                </p>
         <p class="p-estilo">
                    Nossa equipe é composta por profissionais altamente capacitados que estão prontos para atender às 
                    suas necessidades com agilidade e excelência. Trabalhamos com tecnologia de ponta e um compromisso 
                    inabalável com a qualidade e o meio ambiente.
                </p>

                <footer>
        <p>© 2025 Zero1 Piscinas. Todos os direitos reservados.</p>
                </footer>
            </div>
        </div>
    </div>
</body>
</html>
