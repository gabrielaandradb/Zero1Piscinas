<?php
session_start();

if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['ClassUsuarios']['tipo_usuario'] != 'cliente') {
    header('Location: LoginCadastro.php');
    exit;
}

include 'Conexao.php'; 

$usuario = $_SESSION['ClassUsuarios'];

$conexao = Conexao::getInstance();

$mensagem = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'atualizar') {
        // Atualização de dados
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $endereco = $_POST['endereco'];

        $sql = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':id', $usuario['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {

            $_SESSION['ClassUsuarios']['nome'] = $nome;
            $_SESSION['ClassUsuarios']['email'] = $email;
            $_SESSION['ClassUsuarios']['telefone'] = $telefone;
            $_SESSION['ClassUsuarios']['endereco'] = $endereco;

            $mensagem = 'Informações atualizadas com sucesso!';
        } else {
            $mensagemErro = 'Erro ao atualizar as informações.';
        }
    } elseif ($_POST['acao'] === 'excluir') {
        // Exclusão de conta
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $usuario['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            session_destroy();
            header('Location: LoginCadastro.php?mensagem=Conta excluída com sucesso.');
            exit;
        } else {
            $mensagemErro = 'Erro ao excluir a conta. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="index-container">
        <h1>Editar Perfil</h1>

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

        <!-- Formulário -->
        <div class="form-container">
            <form action="editarClientes.php" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']); ?>" required>

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario['telefone']); ?>" required>

                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($usuario['endereco']); ?>" required>

                <div class="buttons-container">
                    <button type="button" class="btn-voltar" onclick="window.location.href='Clientes.php';">Voltar</button>
                    <button type="submit" name="acao" value="atualizar" class="btn-salvar">Salvar</button>
                </div>
            </form>

            <!-- Exclusão de conta -->
            <div class="delete-container" style="margin-top: 20px; padding: 10px; background-color: #ffe5e5; border: 1px solid #ffcccc; border-radius: 5px;">
                <p><strong>Atenção:</strong> Ao excluir sua conta, todos os seus dados serão permanentemente removidos do sistema. Esta ação não pode ser desfeita.</p>
                <form action="editarClientes.php" method="post" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')">
                    <input type="hidden" name="acao" value="excluir">
                    <button type="submit" class="btn-delete" style="background-color: #ff4d4d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        Excluir Conta
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>