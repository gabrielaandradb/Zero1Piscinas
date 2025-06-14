<?php
session_start();
require_once 'Conexao.php';

if (!isset($_SESSION['ClassUsuarios']['id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['orderID'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID não fornecido.']);
    exit;
}

$orderID = $input['orderID'];
$usuarioId = $_SESSION['ClassUsuarios']['id'];

try {
    $conexao = Conexao::getInstance();

    // Obter o ID do serviço concluído mais recente e o valor
    $sqlServico = "
        SELECT s.id, s.preco AS valor
        FROM servicos s
        JOIN piscinas p ON s.piscina_id = p.id
        JOIN usuarios u ON p.cliente_id = u.id
        WHERE u.id = :usuario_id AND s.estatus = 'concluido'
        ORDER BY s.data_execucao DESC
        LIMIT 1
    ";
    $stmtServico = $conexao->prepare($sqlServico);
    $stmtServico->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmtServico->execute();

    $servico = $stmtServico->fetch(PDO::FETCH_ASSOC);
    if (!$servico) {
        throw new Exception('Nenhum serviço concluído encontrado para este usuário.');
    }

    $statusPagamento = 'pago';
    $valorPago = $servico['valor'];
    $dataPagamento = date('Y-m-d H:i:s');

    $conexao->beginTransaction();

    // Registrar pagamento
    $sqlPagamento = "
        INSERT INTO pagamentos (servico_id, estatus, transacao_id, data_pagamento, valor_pago)
        VALUES (:servico_id, :estatus, :transacao_id, :data_pagamento, :valor_pago)
    ";
    $stmtPagamento = $conexao->prepare($sqlPagamento);
    $stmtPagamento->bindParam(':servico_id', $servico['id'], PDO::PARAM_INT);
    $stmtPagamento->bindParam(':estatus', $statusPagamento, PDO::PARAM_STR);
    $stmtPagamento->bindParam(':transacao_id', $orderID, PDO::PARAM_STR);
    $stmtPagamento->bindParam(':data_pagamento', $dataPagamento, PDO::PARAM_STR);
    $stmtPagamento->bindParam(':valor_pago', $valorPago, PDO::PARAM_STR);
    $stmtPagamento->execute();

    // Atualizar o pagamento existente de "pendente" para "pago"
$sqlAtualizarPagamento = "
    UPDATE pagamentos
    SET estatus = 'pago', 
        transacao_id = :transacao_id, 
        data_pagamento = :data_pagamento
    WHERE servico_id = :servico_id AND estatus = 'pendente'
";
$stmtAtualizarPagamento = $conexao->prepare($sqlAtualizarPagamento);
$stmtAtualizarPagamento->bindParam(':transacao_id', $orderID, PDO::PARAM_STR);
$stmtAtualizarPagamento->bindParam(':data_pagamento', $dataPagamento, PDO::PARAM_STR);
$stmtAtualizarPagamento->bindParam(':servico_id', $servico['id'], PDO::PARAM_INT);
$stmtAtualizarPagamento->execute();



    $conexao->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Reverter transação em caso de erro
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
