<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header('Location: LoginCadastro.php');
    exit;
}

// Conexão com o banco de dados
$host = 'localhost'; // Host do servidor
$user = 'root'; // Usuário do banco de dados
$password = ''; // Senha do banco de dados
$database = 'Zero1Piscinas'; // Nome do banco de dados

// Estabelece conexão com o banco
$Conexao = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($Conexao->connect_error) {
    die('Erro de conexão: ' . $Conexao->connect_error);
}

// Coleta os dados enviados pelo formulário
$usuarioId = $_POST['usuarioId'];
$tamanho = $_POST['tamanho'];
$tipo = $_POST['tipo'];
$profundidade = $_POST['profundidade'];
$dataInstalacao = $_POST['dataInstalacao'];
$servico = $_POST['servico'];
$fotoPiscina = null;

// Faz o upload da foto, se existir
if (!empty($_FILES['fotoPiscina']['name'])) {
    $targetDir = 'uploads/'; // Diretório para salvar as imagens
    $fotoPiscina = $targetDir . basename($_FILES['fotoPiscina']['name']);
    
    if (!move_uploaded_file($_FILES['fotoPiscina']['tmp_name'], $fotoPiscina)) {
        $_SESSION['mensagemSolicitacao'] = 'Erro ao fazer upload da imagem.';
        header('Location: Clientes.php');
        exit;
    }
}

// Prepara a query para inserir os dados
$stmt = $Conexao->prepare("
    INSERT INTO piscinas (cliente_id, tamanho, tipo, profundidade, data_instalacao, servico_desejado, foto_piscina) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

// Verifica se a query foi preparada corretamente
if (!$stmt) {
    die('Erro na preparação da query: ' . $Conexao->error);
}

// Executa a query com os parâmetros
$stmt->bind_param('issssss', $usuarioId, $tamanho, $tipo, $profundidade, $dataInstalacao, $servico, $fotoPiscina);
if ($stmt->execute()) {
    $_SESSION['mensagemSolicitacao'] = 'Solicitação registrada com sucesso!';
} else {
    $_SESSION['mensagemSolicitacao'] = 'Erro ao registrar solicitação: ' . $stmt->error;
}

// Fecha a conexão e redireciona de volta à página do cliente
$stmt->close();
$Conexao->close();
header('Location: Clientes.php');
exit;
