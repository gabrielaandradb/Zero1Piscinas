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

        // Verifica se o email termina com @profissional.com
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email_login']) && isset($_POST['senha_login'])) {
        $email = $_POST['email_login'];
        $senha = $_POST['senha_login'];

        // Determina o tipo de usuário com base no sufixo do e-mail
$tipo_usuario = (str_ends_with($email, '@profissional.com')) ? 'profissional' : 'cliente';

        // Consulta ao banco de dados
        $sql = "SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $tipo_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verifica se a senha informada corresponde ao hash da senha armazenada
            if (password_verify($senha, $usuario['senha'])) {
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
        } else {
            $erro = "Usuário não encontrado!";
        }

        $stmt->close();
    }
}
    }

    // Verifique se o formulário de cadastro foi enviado
    if (isset($_POST['email_cad']) && isset($_POST['senha_cad'])) {
    $nome = $_POST['nome_cad'];
    $email = $_POST['email_cad'];
    $telefone = $_POST['telefone_cad'];
    $endereco = $_POST['endereco_cad'];
    $senha = $_POST['senha_cad'];

    // Criptografa a senha antes de salvar
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Determina o tipo de usuário com base no e-mail
    $tipo_usuario = (str_ends_with($email, '@profissional.com')) ? 'profissional' : 'cliente';

    // Insere os dados no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, telefone, endereco, senha, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $nome, $email, $telefone, $endereco, $senha_hash, $tipo_usuario);

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
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <title>Login/Cadastro</title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/script.js" defer></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function handleCredentialResponse(response) {
            console.log("Response do Google:", response); // Verificar o objeto retornado
            if (response.credential) {
                console.log("Token JWT recebido:", response.credential);

    // API LOGIN COM GOOGLE        

            fetch('google_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: response.credential })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    alert("Login bem-sucedido!");
                    window.location.href = data.redirect;
                } else {
                    alert("Erro no login com Google: " + data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro ao validar token do Google:", err);
            });
        }else {
        console.error("Token do Google não gerado.");
    }
}

        window.onload = function () {
            google.accounts.id.initialize({
                client_id: '964075037995-0h99suu4muk4b085dvfnq59jossrabih.apps.googleusercontent.com', 
                callback: handleCredentialResponse
            });

            google.accounts.id.renderButton(
                document.getElementById("google-signin"),
                { theme: "outline", size: "large" } 
            );
        };
    </script>
 
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/pt_BR/sdk.js"></script>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '669538649268923', 
                cookie: true,
                xfbml: true,
                version: 'v15.0'
            });
        };

        function loginFacebook() {
            FB.login(function(response) {
                if (response.authResponse) {
                    const accessToken = response.authResponse.accessToken;

                    fetch('login_facebook.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ token: accessToken })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Login com Facebook bem-sucedido!');
                            window.location.href = data.redirect || 'index.php';
                        } else {
                            alert('Erro no login com Facebook: ' + data.message);
                        }
                    })
                    .catch(err => console.error('Erro ao fazer login com Facebook:', err));
                } else {
                    alert('Login com Facebook cancelado ou falhou.');
                }
            }, { scope: 'email', auth_type: 'reauthenticate' });
        }
    </script>
</head>
<body>
    <div class="form-container">
        
        <!-- Formulário de Login -->
        <div id="login-form">
            <form method="post" action="">
                <h2>Login</h2>
                <label for="email_login">E-mail:</label>
                <input type="email" id="email_login" name="email_login" placeholder="seuemail@exemplo.com" required>
                <label for="senha_login">Senha:</label>
                <input type="password" id="senha_login" name="senha_login" placeholder="senha" required>
                

<?php if ($erro): ?>
                    <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                    <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
                <?php endif; ?>



                <input type="submit" value="Entrar">

                <div class="form-switch">
                    <a href="javascript:void(0);" onclick="mostrarCadastro()">Não tem uma conta? Cadastre-se</a>
                </div>
<!-- Botão Login com Google -->
<div id="google-signin" style="cursor:pointer;">
    <button style="background:white; color:black; border:auto; padding:10px 20px; cursor:pointer; font-size:16px; border-radius:4px;">
        Fazer login com Google
    </button>
</div>

<br>

<!-- Botão Login com Facebook -->
<button type="button" onclick="loginFacebook()" style="background:white; color:black; border:auto; padding:10px 20px; cursor:pointer; font-size:16px; border-radius:4px;">
  <img src="https://www.facebook.com/images/fb_icon_325x325.png" alt="Facebook" style="width:20px; vertical-align:middle; margin-right:8px;">
  Fazer login com Facebook
</button>


                
            </form>
        </div>

        <!-- Formulário de Cadastro -->
        <div id="cadastro-form" class="hidden">
            <form method="post" action="">
                <h2>Cadastro</h2>
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
            </form>
        </div>
    </div>
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
