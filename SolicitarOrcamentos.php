<?php
session_start();
include 'Conexao.php';

// Verifique se o usuário está logado e é cliente
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header('Location: LoginCadastro.php');
    exit;
}

try {
    // Dados do formulário
    $cliente_id = $_SESSION['ClassUsuarios']['id'];
    $tamanho = $_POST['tamanho'];
    $tipo = $_POST['tipo'];
    $profundidade = $_POST['profundidade'];
    $dataInstalacao = $_POST['dataInstalacao'];
    $servico = $_POST['servico'];
    $fotoPiscina = null;

    // Verificar e mover o arquivo enviado
    if (isset($_FILES['fotoPiscina']) && $_FILES['fotoPiscina']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fotoPiscina = $uploadDir . basename($_FILES['fotoPiscina']['name']);
        if (!move_uploaded_file($_FILES['fotoPiscina']['tmp_name'], $fotoPiscina)) {
            throw new Exception("Falha ao mover o arquivo enviado.");
        }
    }

    // Insere a solicitação no banco de dados
    $conn = new PDO('mysql:host=localhost;dbname=seubanco', 'usuario', 'senha');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO piscinas (cliente_id, tamanho, tipo, profundidade, data_instalacao, servico_desejado, foto_piscina) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cliente_id, $tamanho, $tipo, $profundidade, $dataInstalacao, $servico, $fotoPiscina]);

    $_SESSION['mensagemSolicitacao'] = "Sua solicitação foi enviada com sucesso!";
} catch (Exception $e) {
    $_SESSION['mensagemSolicitacao'] = "Erro: " . $e->getMessage();
}

header('Location: index.php');
exit;

?>
