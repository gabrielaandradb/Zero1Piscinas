<?php
session_start();

// Verifica se o usuário está logado e é um cliente
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['mensagemSolicitacao'] = 'Acesso negado. Faça login como cliente.';
    header('Location: LoginCadastro.php');
    exit;
}

// Configuração da conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'Zero1Piscinas';

$Conexao = new mysqli($host, $user, $password, $database);

if ($Conexao->connect_error) {
    $_SESSION['mensagemSolicitacao'] = 'Erro de conexão com o banco de dados.';
    header('Location: Clientes.php');
    exit;
}

// Obtem o ID do cliente logado
$usuarioId = intval($_SESSION['ClassUsuarios']['id']);

// Recupera a última solicitação feita pelo cliente
$queryUltimaSolicitacao = "
    SELECT id 
    FROM piscinas 
    WHERE cliente_id = ? 
    ORDER BY id DESC 
    LIMIT 1
";
$stmt = $Conexao->prepare($queryUltimaSolicitacao);
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$stmt->bind_result($ultimaSolicitacaoId);
$stmt->fetch();
$stmt->close();

if (!$ultimaSolicitacaoId) {
    $_SESSION['mensagemSolicitacao'] = 'Nenhuma solicitação encontrada para exclusão.';
    header('Location: Clientes.php');
    exit;
}

// Exclui a última solicitação
$queryExcluirSolicitacao = "
    DELETE FROM piscinas 
    WHERE id = ? AND cliente_id = ?
";
$stmtExcluir = $Conexao->prepare($queryExcluirSolicitacao);
$stmtExcluir->bind_param('ii', $ultimaSolicitacaoId, $usuarioId);

if ($stmtExcluir->execute()) {
    $_SESSION['mensagemSolicitacao'] = 'Solicitação excluída com sucesso.';
} else {
    $_SESSION['mensagemSolicitacao'] = 'Erro ao excluir solicitação: ' . $stmtExcluir->error;
}

$stmtExcluir->close();
$Conexao->close();

// Redireciona para a página do cliente
header('Location: Clientes.php');
exit;
