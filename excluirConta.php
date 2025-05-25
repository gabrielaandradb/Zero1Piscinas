<?php
session_start();
require 'Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuarioId'])) {
    $usuarioId = $_POST['usuarioId'];

    if (!is_numeric($usuarioId)) {
        $_SESSION['mensagemErro'] = 'ID de usuário inválido.';
        header('Location: editarClientes.php');
        exit;
    }

    $conexao = Conexao::getInstance();

    try {
        $conexao->beginTransaction();

        // 1. Excluir piscinas relacionadas ao cliente
        $sqlPiscinas = "DELETE FROM piscinas WHERE cliente_id = :id";
        $stmtPiscinas = $conexao->prepare($sqlPiscinas);
        $stmtPiscinas->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        $stmtPiscinas->execute();

        // 2. Excluir solicitações relacionadas ao cliente (ajuste o nome da tabela/coluna se necessário)
        $sqlSolicitacoes = "DELETE FROM solicitacoes WHERE cliente_id = :id";
        $stmtSolicitacoes = $conexao->prepare($sqlSolicitacoes);
        $stmtSolicitacoes->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        $stmtSolicitacoes->execute();

        // 3. Excluir cliente
        $sqlClientes = "DELETE FROM clientes WHERE id = :id";
        $stmtClientes = $conexao->prepare($sqlClientes);
        $stmtClientes->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        $stmtClientes->execute();

        // 4. Excluir usuário
        $sqlUsuarios = "DELETE FROM usuarios WHERE id = :id";
        $stmtUsuarios = $conexao->prepare($sqlUsuarios);
        $stmtUsuarios->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        $stmtUsuarios->execute();

        $conexao->commit();

        session_destroy();
        header('Location: LoginCadastro.php?mensagem=Conta excluída com sucesso.');
        exit;

    } catch (PDOException $e) {
        $conexao->rollBack();
        $_SESSION['mensagemErro'] = 'Erro ao excluir a conta: ' . $e->getMessage();
        header('Location: editarClientes.php');
        exit;
    }
} else {
    $_SESSION['mensagemErro'] = 'Ação inválida.';
    header('Location: editarClientes.php');
    exit;
}
