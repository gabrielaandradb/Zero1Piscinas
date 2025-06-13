<?php
session_start();
require_once 'Conexao.php';

$conexao = Conexao::getInstance();

$pedidoId = filter_input(INPUT_POST, 'pedidoId', FILTER_VALIDATE_INT);
$mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

if (!$pedidoId || !$mensagem) {
    die('Dados inválidos.');
}

// Verificar se o pedido existe na tabela piscinas
$query_verificar = "SELECT id FROM piscinas WHERE id = :pedidoId";
$stmt_verificar = $conexao->prepare($query_verificar);
$stmt_verificar->bindParam(':pedidoId', $pedidoId, PDO::PARAM_INT);
$stmt_verificar->execute();

if ($stmt_verificar->rowCount() == 0) {
    die('Pedido não encontrado. Por favor, verifique o ID do pedido.');
}

$remetente = isset($_SESSION['usuario_tipo']) ? $_SESSION['usuario_tipo'] : 'cliente';

// Inserir a mensagem
$query_inserir = "INSERT INTO mensagens (pedido_id, remetente, mensagem, data_envio) VALUES (:pedido_id, :remetente, :mensagem, NOW())";
$stmt_inserir = $conexao->prepare($query_inserir);
$stmt_inserir->bindParam(':pedido_id', $pedidoId, PDO::PARAM_INT);
$stmt_inserir->bindParam(':remetente', $remetente, PDO::PARAM_STR);
$stmt_inserir->bindParam(':mensagem', $mensagem, PDO::PARAM_STR);

if ($stmt_inserir->execute()) {
    header("Location: acompanharServico.php?pedidoId=$pedidoId");
    exit;
} else {
    die('Erro ao enviar mensagem.');
}
?>
