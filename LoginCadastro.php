<?php
session_start();

// Verifique se o formulário de login foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_login']) && isset($_POST['senha_login'])) {
    // Aqui você deve fazer a consulta ao banco de dados para verificar as credenciais
    // Exemplo de validação:
    $email = $_POST['email_login'];
    $senha = $_POST['senha_login'];

    // Conectar ao banco de dados
    $conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Consulta para verificar as credenciais
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Se encontrar o usuário, armazenar na sessão
        $usuario = $result->fetch_assoc();
        $_SESSION['ClassUsuarios'] = $usuario;
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario']; // Armazene o tipo de usuário

        // Redirecionar para a página correta
        if ($usuario['tipo_usuario'] == 'profissional') {
            header('Location: Profissionais.php');
            exit;
        } else if ($usuario['tipo_usuario'] == 'cliente') {
            header('Location: Clientes.php');
            exit;
        }
    } else {
        // Se o login falhar, exibir uma mensagem de erro
        $erro = "E-mail ou senha incorretos!";
    }
} else {
    $erro = ''; // Inicializa a variável de erro
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Cadastro</title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="form-container">
        <div id="login-form">
            <form method="post" action="">
                <h3>Login</h3>
                <label for="email_login">E-mail:</label>
                <input type="email" id="email_login" name="email_login" placeholder="seuemail@exemplo.com" required>

                <label for="senha_login">Senha:</label>
                <input type="password" id="senha_login" name="senha_login" placeholder="senha" required>

                <input type="submit" value="Entrar">
                <div class="form-switch">
                    <a href="javascript:void(0);" onclick="mostrarCadastro()">Não tem uma conta? Cadastre-se</a>
                </div>

                <!-- Mensagem de erro -->
                <?php if ($erro): ?>
                <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>
            </form>
        </div>

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
