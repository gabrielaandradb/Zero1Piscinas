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

// Coleta os dados enviados pelo formulário
$usuarioId = intval($_SESSION['ClassUsuarios']['id']);
$tamanho = htmlspecialchars(trim($_POST['tamanho']));
$tipo = htmlspecialchars(trim($_POST['tipo']));
$profundidade = htmlspecialchars(trim($_POST['profundidade']));
$dataInstalacao = htmlspecialchars(trim($_POST['dataInstalacao']));
$servico = htmlspecialchars(trim($_POST['servico']));
$preco = floatval($_POST['preco']); 



// Valida se o usuário é um cliente válido
$verificarUsuarioStmt = $Conexao->prepare("
    SELECT COUNT(*) 
    FROM usuarios 
    WHERE id = ? AND tipo_usuario = 'cliente'
");
$verificarUsuarioStmt->bind_param('i', $usuarioId);
$verificarUsuarioStmt->execute();
$verificarUsuarioStmt->bind_result($usuarioExiste);
$verificarUsuarioStmt->fetch();
$verificarUsuarioStmt->close();

if (!$usuarioExiste) {
    $_SESSION['mensagemSolicitacao'] = 'Usuário inválido ou não autorizado.';
    header('Location: Clientes.php');
    exit;
}

// Verifica e insere o cliente na tabela `clientes` (se necessário)
$verificarClienteStmt = $Conexao->prepare("
    SELECT COUNT(*) 
    FROM clientes 
    WHERE id = ?
");
$verificarClienteStmt->bind_param('i', $usuarioId);
$verificarClienteStmt->execute();
$verificarClienteStmt->bind_result($clienteExiste);
$verificarClienteStmt->fetch();
$verificarClienteStmt->close();

if (!$clienteExiste) {
    $inserirClienteStmt = $Conexao->prepare("
        INSERT INTO clientes (id) 
        VALUES (?)
    ");
    $inserirClienteStmt->bind_param('i', $usuarioId);
    if (!$inserirClienteStmt->execute()) {
        $_SESSION['mensagemSolicitacao'] = 'Erro ao registrar cliente: ' . $inserirClienteStmt->error;
        header('Location: Clientes.php');
        exit;
    }
    $inserirClienteStmt->close();
}

// Insere a solicitação na tabela `piscinas`
$stmt = $Conexao->prepare("
    INSERT INTO piscinas (cliente_id, tamanho, tipo, profundidade, data_instalacao, servico_desejado) 
    VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    $_SESSION['mensagemSolicitacao'] = 'Erro na preparação da query: ' . $Conexao->error;
    header('Location: Clientes.php');
    exit;
}

$stmt->bind_param('isssss', $usuarioId, $tamanho, $tipo, $profundidade, $dataInstalacao, $servico);

if ($stmt->execute()) {
    $_SESSION['mensagemSolicitacao'] = 'Solicitação registrada com sucesso!';
} else {
    $_SESSION['mensagemSolicitacao'] = 'Erro ao registrar solicitação: ' . $stmt->error;
}

$stmt->close();
$Conexao->close();
header('Location: Clientes.php');
exit;
