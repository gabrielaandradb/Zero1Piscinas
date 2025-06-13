<?php 
session_start();

// Verifique se o profissional está logado
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] !== 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

require_once 'Conexao.php';
$conexao = Conexao::getInstance();

$solicitacao_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$solicitacao_id) {
    echo "ID de solicitação inválido!";
    exit;
}

$profissional_id = $_SESSION['ClassUsuarios']['id'];

if (!$profissional_id) {
    echo "Erro: ID do profissional não encontrado na sessão.";
    exit;
}

// Obter detalhes da solicitação
$query_solicitacao = "
    SELECT 
        piscinas.*, 
        usuarios.nome AS cliente_nome, 
        usuarios.email AS cliente_email
    FROM piscinas
    INNER JOIN usuarios ON piscinas.cliente_id = usuarios.id
    WHERE piscinas.id = :solicitacao_id AND piscinas.status = 'pendente';
";
$stmt = $conexao->prepare($query_solicitacao);
$stmt->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    echo "Solicitação não encontrada ou já respondida.";
    exit;
}

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_STRING);
    $tipo_servico = $solicitacao['servico_desejado'];
    $preco = $solicitacao['preco'];
    $data_execucao = date('Y-m-d H:i:s');

    // Ajustar valores para status "cancelado"
    if ($status === 'cancelado') {
        $comentario = null; // Remover descrição
        $preco = null;      // Não registrar preço
        $data_execucao = null; // Remover data de execução
    }

    try {
    $conexao->beginTransaction();

    // Confirma se o profissional existe
    $query_profissional = "SELECT id FROM profissionais WHERE id = :profissional_id";
    $stmt_profissional = $conexao->prepare($query_profissional);
    $stmt_profissional->bindParam(':profissional_id', $profissional_id, PDO::PARAM_INT);
    $stmt_profissional->execute();

    if (!$stmt_profissional->fetch()) {
        throw new Exception("Profissional não registrado.");
    }

    // Atualizar a tabela piscinas
    $query_update = "
        UPDATE piscinas 
        SET status = :status, resposta = :resposta
        WHERE id = :solicitacao_id;
    ";
    $stmt_update = $conexao->prepare($query_update);
    $stmt_update->bindParam(':status', $status);
    $stmt_update->bindParam(':resposta', $comentario);
    $stmt_update->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // Inserir na tabela servicos
    $query_servicos = "
        INSERT INTO servicos (piscina_id, profissional_id, tipo_servico, descricao, estatus)
        VALUES (:piscina_id, :profissional_id, :tipo_servico, :descricao, :estatus);
    ";
    $stmt_servicos = $conexao->prepare($query_servicos);
    $stmt_servicos->bindParam(':piscina_id', $solicitacao_id, PDO::PARAM_INT);
    $stmt_servicos->bindParam(':profissional_id', $profissional_id, PDO::PARAM_INT);
    $stmt_servicos->bindParam(':tipo_servico', $tipo_servico, PDO::PARAM_STR);
    $stmt_servicos->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $stmt_servicos->bindParam(':estatus', $status, PDO::PARAM_STR);
    $stmt_servicos->execute();

    $conexao->commit();

    $mensagem_sucesso = "Resposta enviada com sucesso!";
} catch (Exception $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }
    $mensagem_erro = "Erro ao enviar a resposta: " . $e->getMessage();
}


}
?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="img/logo.webp" type="image/x-icon">
    <title>Responder Solicitação</title>
    <link rel="stylesheet" href="css/estilo.css" />
</head>
<body>
    <div class="form-container">
        <h2>Responder Solicitação</h2>

        <?php if ($mensagem_sucesso): ?>
            <p style="color: green; font-weight: bold;">
                <?= htmlspecialchars($mensagem_sucesso) ?>
            </p>
        <?php elseif ($mensagem_erro): ?>
            <p style="color: red; font-weight: bold;">
                <?= htmlspecialchars($mensagem_erro) ?>
            </p>
        <?php endif; ?>

        <p><strong>Cliente:</strong> <?= htmlspecialchars($solicitacao['cliente_nome']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($solicitacao['cliente_email']) ?></p>

        <form method="POST" action="">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="pendente" >Pendente</option>
                <option value="concluido" <?= ($solicitacao['status'] === 'concluido') ? 'selected' : '' ?>>Concluído</option>
                <option value="cancelado" <?= ($solicitacao['status'] === 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
            </select>

            <label for="comentario">Resposta</label>
            <textarea name="comentario" id="comentario" rows="5" required>
                <?= htmlspecialchars($solicitacao['resposta'] ?? '') ?>
            </textarea>

            <input type="submit" value="Enviar" />
        </form>
    </div>
    
</body>
</html>