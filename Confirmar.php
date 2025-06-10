<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['servico_id'])) {
    $servico_id = intval($_POST['servico_id']);

    // Aqui você faz o update no banco para confirmar o serviço
    // Exemplo:
    require_once 'Conexao.php';
    $conexao = Conexao::getInstance();
    $update = $conexao->prepare("UPDATE servicos SET estatus = 'confirmado' WHERE id = :id");
    $update->bindParam(':id', $servico_id);
    $update->execute();

    // Definindo mensagem de sucesso
    $_SESSION['mensagem_sucesso'] = "Serviço confirmado com sucesso!";

    // Redireciona para a página de acompanhar serviço (ou onde quiser)
    header('Location: acompanharServico.php');
    exit;
}
?>
