<?php
session_start();

// Verifique se o profissional está logado
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

// Conexão com o banco de dados
require_once 'Conexao.php';
$conexao = Conexao::getInstance();

// Obtenha o ID da solicitação
$solicitacao_id = $_GET['id'];

// Verifique se a solicitação existe
$query = "
    SELECT s.id, s.tipo_servico, s.descricao, s.estatus, s.preco, s.data_solicitacao, s.data_execucao, p.servico_desejado AS piscina_nome 
    FROM servicos s
    JOIN piscinas p ON s.piscina_id = p.id
    WHERE s.id = :solicitacao_id AND s.profissional_id = :id_profissional
";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
$stmt->bindParam(':id_profissional', $_SESSION['ClassUsuarios'], PDO::PARAM_INT);
$stmt->execute();

$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrar a solicitação, redireciona de volta
if (!$solicitacao) {
    header('Location: Profissionais.php');
    exit;
}

// Processar resposta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resposta = $_POST['resposta'];
    $estatus = $_POST['estatus'];
    $data_execucao = $_POST['data_execucao'];

    // Atualiza o status, a resposta e a data de execução
    $update_query = "
        UPDATE servicos 
        SET estatus = :estatus, resposta = :resposta, data_execucao = :data_execucao
        WHERE id = :solicitacao_id
    ";
    
    $stmt_update = $conexao->prepare($update_query);
    $stmt_update->bindParam(':resposta', $resposta, PDO::PARAM_STR);
    $stmt_update->bindParam(':estatus', $estatus, PDO::PARAM_STR);
    $stmt_update->bindParam(':data_execucao', $data_execucao, PDO::PARAM_STR);
    $stmt_update->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
    
    if ($stmt_update->execute()) {
        $_SESSION['mensagem'] = "Resposta e status enviados com sucesso!";
        header('Location: gerenciamentoProfissional.php');
        exit;
    } else {
        echo "Erro ao enviar resposta.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Responder Solicitação</title>
</head>
<body>
    <h1>Responder Solicitação</h1>
    <p><strong>Serviço Desejado:</strong> <?= htmlspecialchars($solicitacao['piscina_nome']); ?></p>
    <p><strong>Tipo de Serviço:</strong> <?= htmlspecialchars($solicitacao['tipo_servico']); ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($solicitacao['descricao']); ?></p>
    <p><strong>Data Solicitação:</strong> <?= htmlspecialchars($solicitacao['data_solicitacao']); ?></p>
    <p><strong>Preço:</strong> R$ <?= number_format($solicitacao['preco'], 2, ',', '.'); ?></p>

    <form method="post">
        <label for="resposta">Resposta:</label><br>
        <textarea name="resposta" rows="4" cols="50" required></textarea><br><br>
        
        <label for="estatus">Status:</label><br>
        <select name="estatus" required>
            <option value="em_andamento" <?= $solicitacao['estatus'] == 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="concluido" <?= $solicitacao['estatus'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
            <option value="cancelado" <?= $solicitacao['estatus'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select><br><br>

        <label for="data_execucao">Data de Execução:</label><br>
        <input type="datetime-local" name="data_execucao" value="<?= date('Y-m-d\TH:i', strtotime($solicitacao['data_execucao'])); ?>"><br><br>

        <input type="submit" value="Enviar Resposta e Atualizar Status">
    </form>
</body>
</html>