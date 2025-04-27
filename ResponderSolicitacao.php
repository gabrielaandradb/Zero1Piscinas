<?php
session_start();
include 'Conexao.php';

// Verifique se o usuário está logado e é um profissional
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

// Dados do formulário
$piscina_id = $_POST['piscina_id'];
$resposta = $_POST['resposta'];

// Atualizar a resposta no banco de dados
$sql = "UPDATE piscinas SET resposta = ?, status = 'respondido' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $resposta, $piscina_id);

if ($stmt->execute()) {
    $_SESSION['mensagemSolicitacao'] = "Resposta enviada com sucesso!";
} else {
    $_SESSION['mensagemSolicitacao'] = "Erro ao enviar resposta.";
}

header('Location: SolicitacoesProfissionais.php');
exit;
?>
