<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$erro = '';
$sucesso = '';


// Verifique se o usuário já está logado
$usuario_logado = isset($_SESSION['ClassUsuarios']) ? $_SESSION['ClassUsuarios'] : null;

// Verifique se o formulário de login foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email_login']) && isset($_POST['senha_login'])) {
        $email = $_POST['email_login'];
        $senha = $_POST['senha_login'];

        $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $_SESSION['ClassUsuarios'] = $usuario;
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] == 'profissional') {
                header('Location: Profissionais.php');
                exit;
            } else if ($usuario['tipo_usuario'] == 'cliente') {
                header('Location: index.php');
                exit;
            }
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    }

    // Verifique se o formulário de cadastro foi enviado
    if (isset($_POST['email_cad']) && isset($_POST['senha_cad'])) {
        $nome = $_POST['nome_cad'];
        $email = $_POST['email_cad'];
        $telefone = $_POST['telefone_cad'];
        $endereco = $_POST['endereco_cad'];
        $senha = $_POST['senha_cad'];

        $sql = "INSERT INTO usuarios (nome, email, telefone, endereco, senha, tipo_usuario) VALUES (?, ?, ?, ?, ?, 'cliente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $nome, $email, $telefone, $endereco, $senha);

        if ($stmt->execute()) {
            $sucesso = "Cadastro realizado com sucesso! Faça login.";
        } else {
            $erro = "Erro ao cadastrar usuário: " . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Cadastro</title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/script.js" defer></script>

    <script async defer crossorigin="anonymous" 
        src="https://connect.facebook.net/pt_BR/sdk.js"></script>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '1339744507246506', // Use seu App ID
                cookie: true, // Habilita cookies
                xfbml: true, // Renderiza plugins sociais automaticamente
                version: 'v16.0' // Versão da API Graph
            });
        };
    </script>

<script>
    function loginFacebook() {
        FB.login(function(response) {
            if (response.authResponse) {
                console.log('Login bem-sucedido:', response);
                FB.api('/me', { fields: 'name,email' }, function(userInfo) {
                    console.log('Informações do usuário:', userInfo);
                    // Aqui você pode enviar userInfo.name e userInfo.email para o servidor
                    alert('Olá, ' + userInfo.name + '! Seu login foi bem-sucedido.');
                });
            } else {
                console.log('Usuário cancelou ou não autorizou o login.');
                alert('Login cancelado ou não autorizado.');
            }
        }, { scope: 'email' }); // Solicita acesso ao e-mail do usuário
    }
</script>

</head>
<body>
<div class="form-container">


    <!-- Formulário de Login -->
<div id="login-form">
    <form method="post" action="">
        <h3>Login</h3>
        <label for="email_login">E-mail:</label>
        <input type="email" id="email_login" name="email_login" placeholder="seuemail@exemplo.com" required>

        <label for="senha_login">Senha:</label>
        <input type="password" id="senha_login" name="senha_login" placeholder="senha" required>


        <div id="fb-root"></div>
<button onclick="loginFacebook()">Login com Facebook</button>


        <input type="submit" value="Entrar">
        <div class="form-switch">
            <a href="javascript:void(0);" onclick="mostrarCadastro()">Não tem uma conta? Cadastre-se</a>
        </div>

        <!-- Mensagens -->
        <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>
    </form>
</div>

<!-- Formulário de Cadastro -->
<div id="cadastro-form" class="hidden">
    <form method="post" action="">
        <h3>Cadastro</h3>

        <label for="nome_cad">Nome completo:</label>
        <input type="text" id="nome_cad" name="nome_cad" placeholder="Seu nome completo" required>

        <label for="email_cad">E-mail:</label>
        <input type="email" id="email_cad" name="email_cad" placeholder="seuemail@exemplo.com" required>

        <label for="telefone_cad">Telefone:</label>
        <input type="tel" id="telefone_cad" name="telefone_cad" placeholder="(xx) xxxxx-xxxx" required>

        <label for="endereco_cad">Endereço:</label>
        <input type="text" id="endereco" name="endereco_cad" placeholder="Endereço completo" required>

        <label for="senha_cad">Senha:</label>
        <input type="password" id="senha_cad" name="senha_cad" placeholder="ex: 123456" required>

        <input type="submit" value="Cadastrar">

        <div class="form-switch">
            <a href="javascript:void(0);" onclick="mostrarLogin()">Já tem uma conta? Faça login</a>
        </div>

        <!-- Mensagens -->
        <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>
    </form>

</div>
    </div>
</>
    <script>
        function mostrarLogin() {
            document.getElementById("login-form").classList.remove("hidden");
            document.getElementById("cadastro-form").classList.add("hidden");
        }

        function mostrarCadastro() {
            document.getElementById("login-form").classList.add("hidden");
            document.getElementById("cadastro-form").classList.remove("hidden");
        }
    </script>
</body>
</html>
