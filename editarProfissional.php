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
$id_profissional = $_SESSION['ClassUsuarios'];

// Consulta para pegar os dados do profissional
$query = "SELECT * FROM usuarios WHERE id = :id_profissional AND tipo_usuario = 'profissional'";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
$stmt->execute();

// Verifique se os dados foram encontrados
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    echo "Profissional não encontrado!";
    exit;
}

// Atualiza os dados do profissional caso o formulário seja enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    // Atualiza os dados no banco de dados
    $updateQuery = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id_profissional";
    $stmtUpdate = $conexao->prepare($updateQuery);
    $stmtUpdate->bindParam(':nome', $nome);
    $stmtUpdate->bindParam(':email', $email);
    $stmtUpdate->bindParam(':telefone', $telefone);
    $stmtUpdate->bindParam(':endereco', $endereco);
    $stmtUpdate->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);

    if ($stmtUpdate->execute()) {
        // Redireciona de volta para o perfil após a atualização
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
