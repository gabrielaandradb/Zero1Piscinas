<?php
session_start();
require_once 'Conexao.php';

// Verifica se o usuário está logado e é cliente
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: LoginCadastro.php');
    exit;
}

// Aqui você deve garantir que o id da piscina que o cliente quer acompanhar esteja definido na sessão
// Ajuste para sua lógica real, por exemplo: $_SESSION['piscina_id'] = (int)$_GET['piscina_id']; em outra página.
$piscinaId = $_SESSION['piscina_id'] ?? null;

if (!$piscinaId) {
    echo "Nenhum serviço selecionado para acompanhamento.";
    exit;
}

$conexao = Conexao::getInstance();

// Busca informações da piscina para mostrar status geral
$sqlPiscina = "SELECT id, status FROM piscinas WHERE id = :id AND cliente_id = :cliente_id";
$stmtPiscina = $conexao->prepare($sqlPiscina);
$stmtPiscina->bindParam(':id', $piscinaId, PDO::PARAM_INT);
$stmtPiscina->bindParam(':cliente_id', $_SESSION['ClassUsuarios']['id'], PDO::PARAM_INT);
$stmtPiscina->execute();
$piscina = $stmtPiscina->fetch(PDO::FETCH_ASSOC);

if (!$piscina) {
    echo "Piscina não encontrada ou não pertence ao cliente logado.";
    exit;
}

// Busca os serviços relacionados à piscina
$sqlServicos = "
    SELECT s.id, s.tipo_servico, s.descricao, s.estatus, s.data_solicitacao, s.data_execucao, s.preco,
           p.nome AS profissional_nome
    FROM servicos s
    INNER JOIN profissionais p ON s.profissional_id = p.id
    WHERE s.piscina_id = :piscina_id
    ORDER BY s.data_solicitacao DESC
";
$stmtServicos = $conexao->prepare($sqlServicos);
$stmtServicos->bindParam(':piscina_id', $piscinaId, PDO::PARAM_INT);
$stmtServicos->execute();
$servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Acompanhar Serviços da Piscina #<?= htmlspecialchars($piscinaId) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #007BFF; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .status-pendente { color: orange; }
        .status-em_andamento { color: blue; }
        .status-concluido { color: green; }
        .status-cancelado { color: red; }
    </style>
</head>
<body>
    <h1>Acompanhar Serviços da Piscina #<?= htmlspecialchars($piscinaId) ?></h1>

    <p><strong>Status geral da solicitação:</strong> <?= htmlspecialchars($piscina['status']) ?></p>

    <?php if (count($servicos) === 0): ?>
        <p>Não há serviços cadastrados para esta piscina ainda.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Serviço</th>
                    <th>Tipo de Serviço</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Data Solicitação</th>
                    <th>Data Execução</th>
                    <th>Preço (R$)</th>
                    <th>Profissional Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $servico): ?>
                    <tr>
                        <td><?= htmlspecialchars($servico['id']) ?></td>
                        <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $servico['tipo_servico']))) ?></td>
                        <td><?= nl2br(htmlspecialchars($servico['descricao'])) ?></td>
                        <td class="status-<?= htmlspecialchars($servico['estatus']) ?>">
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $servico['estatus']))) ?>
                        </td>
                        <td><?= htmlspecialchars($servico['data_solicitacao']) ?></td>
                        <td><?= $servico['data_execucao'] ? htmlspecialchars($servico['data_execucao']) : '-' ?></td>
                        <td><?= $servico['preco'] !== null ? number_format($servico['preco'], 2, ',', '.') : '-' ?></td>
                        <td><?= htmlspecialchars($servico['profissional_nome']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
