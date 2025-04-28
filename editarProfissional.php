<?php
session_start();

if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

require_once 'Conexao.php'; 
$conexao = Conexao::getInstance();

$id_profissional = $_SESSION['ClassUsuarios'];

$query = "SELECT * FROM usuarios WHERE id = :id_profissional AND tipo_usuario = 'profissional'";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
$stmt->execute();

$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    echo "Profissional não encontrado!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    $updateQuery = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id_profissional";
    $stmtUpdate = $conexao->prepare($updateQuery);
    $stmtUpdate->bindParam(':nome', $nome);
    $stmtUpdate->bindParam(':email', $email);
    $stmtUpdate->bindParam(':telefone', $telefone);
    $stmtUpdate->bindParam(':endereco', $endereco);
    $stmtUpdate->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);

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
    <title>Editar Perfil - Profissional</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="index-container">
        <h1>Editar Dados Pessoais</h1>

        <!-- Formulário para editar os dados do profissional -->
        <form action="editarProfissional.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($profissional['nome']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($profissional['email']); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($profissional['telefone']); ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($profissional['endereco']); ?>" required>

            <input type="submit" value="Salvar Alterações" class="btn">
        </form>

        <!-- Botão para voltar -->
        <div class="actions">
            <a href="Profissionais.php" class="btn">Voltar</a>
        </div>
    </div>
</body>
</html>
