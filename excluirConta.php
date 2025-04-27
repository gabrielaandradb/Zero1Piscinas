<?php
session_start();
require 'Conexao.php'; // Inclua a conexão ao banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuarioId'])) {
    $usuarioId = $_POST['usuarioId'];

    // Verificar se o ID é válido
    if (!is_numeric($usuarioId)) {
        $_SESSION['mensagemErro'] = 'ID de usuário inválido.';
        header('Location: editarClientes.php');
        exit;
    }

    // Comando SQL para exclusão
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $Conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $usuarioId);

        if ($stmt->execute()) {
            // Destruir sessão após excluir a conta
            session_destroy();
            header('Location: LoginCadastro.php?mensagem=Conta excluída com sucesso.');
            exit;
        } else {
            $_SESSION['mensagemErro'] = 'Erro ao excluir a conta. Tente novamente.';
            header('Location: editarClientes.php');
            exit;
        }
    } else {
        $_SESSION['mensagemErro'] = 'Erro interno no servidor.';
        header('Location: editarClientes.php');
        exit;
    }
} else {
    $_SESSION['mensagemErro'] = 'Ação inválida.';
    header('Location: editarClientes.php');
    exit;
}
