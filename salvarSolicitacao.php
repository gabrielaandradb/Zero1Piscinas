<?php
session_start();

// Verifica se o usuário está logado e é um cliente
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['mensagemSolicitacao'] = 'Acesso negado. Faça login como cliente.';
    header('Location: LoginCadastro.php');
    exit;
}

// Dados da conexão
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

// ID do usuário logado
$usuarioId = intval($_SESSION['ClassUsuarios']['id']);

// Verifica se usuário existe e é cliente
$stmt = $Conexao->prepare("SELECT COUNT(*) FROM usuarios WHERE id = ? AND tipo_usuario = 'cliente'");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$stmt->bind_result($usuarioExiste);
$stmt->fetch();
$stmt->close();

if (!$usuarioExiste) {
    $_SESSION['mensagemSolicitacao'] = 'Usuário inválido ou não autorizado.';
    header('Location: Clientes.php');
    exit;
}

// Verifica se o usuário já está na tabela clientes
$stmt = $Conexao->prepare("SELECT COUNT(*) FROM clientes WHERE id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$stmt->bind_result($clienteExiste);
$stmt->fetch();
$stmt->close();

if (!$clienteExiste) {
    // Insere na tabela clientes
    $stmt = $Conexao->prepare("INSERT INTO clientes (id) VALUES (?)");
    $stmt->bind_param('i', $usuarioId);
    if (!$stmt->execute()) {
        $_SESSION['mensagemSolicitacao'] = 'Erro ao registrar cliente: ' . $stmt->error;
        $stmt->close();
        $Conexao->close();
        header('Location: Clientes.php');
        exit;
    }
    $stmt->close();
}

// Recebe dados do formulário
$tamanho = htmlspecialchars(trim($_POST['tamanho']));
$tipo = htmlspecialchars(trim($_POST['tipo']));
$profundidade = htmlspecialchars(trim($_POST['profundidade']));
$dataInstalacao = htmlspecialchars(trim($_POST['dataInstalacao']));
$servico = htmlspecialchars(trim($_POST['servico']));
$preco = floatval($_POST['preco']); // Você calcula isso antes ou vem do formulário

// Depois de receber $tamanho e $servico do POST:
$tamanho = strtolower(trim($_POST['tamanho']));
$servico = trim($_POST['servico']);

$tabelaPrecos = [
    "Limpeza de Piscinas" => ["pequena" => 150, "media" => 250, "grande" => 350],
    "Manutenção" => ["pequena" => 200, "media" => 300, "grande" => 400],
    "Reparos" => ["pequena" => 300, "media" => 450, "grande" => 600],
    "Aquecimento de Piscinas" => ["pequena" => 600, "media" => 700, "grande" => 800],
    "Instalação de Capas Protetoras" => ["pequena" => 100, "media" => 150, "grande" => 200],
    "Tratamento Avançado da Água" => ["pequena" => 250, "media" => 350, "grande" => 450],
];

if (isset($tabelaPrecos[$servico]) && isset($tabelaPrecos[$servico][$tamanho])) {
    $preco = $tabelaPrecos[$servico][$tamanho];
} else {
    $preco = 0.00;
}


// Insere solicitação na tabela piscinas
$stmt = $Conexao->prepare("INSERT INTO piscinas (cliente_id, tamanho, tipo, profundidade, data_instalacao, servico_desejado, preco) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    $_SESSION['mensagemSolicitacao'] = 'Erro na preparação da query: ' . $Conexao->error;
    $Conexao->close();
    header('Location: Clientes.php');
    exit;
}

$stmt->bind_param('isssssd', $usuarioId, $tamanho, $tipo, $profundidade, $dataInstalacao, $servico, $preco);

if ($stmt->execute()) {
    $_SESSION['mensagemSolicitacao'] = 'Solicitação registrada com sucesso!';
} else {
    $_SESSION['mensagemSolicitacao'] = 'Erro ao registrar solicitação: ' . $stmt->error;
}

$stmt->close();
$Conexao->close();

header('Location: Clientes.php');
exit;
