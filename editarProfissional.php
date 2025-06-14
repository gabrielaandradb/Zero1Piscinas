<?php
session_start();

// Verifica se o usuário está logado como profissional
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

require_once 'Conexao.php';
$conexao = Conexao::getInstance();

// Verifica o ID do profissional na sessão
$id_profissional = $_SESSION['ClassUsuarios']; // ID do profissional logado
if (empty($id_profissional)) {
    echo "ID de profissional inválido ou sessão não configurada!";
    exit;
}

$query = "SELECT * FROM usuarios WHERE id = :id AND tipo_usuario = 'profissional' AND email LIKE '%@profissional.com' LIMIT 1";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id', $id_profissional, PDO::PARAM_INT);
$stmt->execute();

$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    echo "Profissional não encontrado ou email não permitido!";
    exit;
}

// Atualiza os dados do profissional caso o formulário seja enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    if (strpos($email, '@profissional.com') === false) {
        echo "O email deve terminar em '@profissional.com'!";
        exit;
    }

    $updateQuery = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id";
    $stmtUpdate = $conexao->prepare($updateQuery);
    $stmtUpdate->bindParam(':nome', $nome);
    $stmtUpdate->bindParam(':email', $email);
    $stmtUpdate->bindParam(':telefone', $telefone);
    $stmtUpdate->bindParam(':endereco', $endereco);
    $stmt->bindParam(':id', $usuario['id'], PDO::PARAM_INT);

    if ($stmtUpdate->execute()) {
        header('Location: Profissionais.php');
        exit;
    } else {
        echo "Erro ao atualizar os dados!";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">

    <title>Editar Perfil - Profissional</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="index-container">
        <h1>Editar Dados Pessoais</h1>
        <!-- Mensagens -->
        <?php if (!empty($mensagem)): ?>
            <div style="margin: 20px; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                <?= htmlspecialchars($mensagem); ?>
            </div>
        <?php elseif (!empty($mensagemErro)): ?>
            <div style="margin: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
                <?= htmlspecialchars($mensagemErro); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
        <form action="editarProfissional.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($profissional['nome']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($profissional['email']); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($profissional['telefone']); ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($profissional['endereco']); ?>" required>

            <div class="buttons-container">
            <button type="button" class="btn-voltar" onclick="window.location.href='Profissionais.php';">Voltar</button>
            <button type="submit" name="acao" value="atualizar" class="btn-salvar">Salvar</button>
            </div>
</div>
        </form>
    </div>
</body>
</html>
