<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

if (empty($token)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Token do Google não foi recebido.']);
    exit;
}

// Verificar o token com a API do Google
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
$response = file_get_contents($url);
$user_info = json_decode($response, true);

if (!isset($user_info['email'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Token inválido ou expirado.']);
    exit;
}

$email = $user_info['email'];
$nome = $user_info['name'];

// Permitir apenas usuários que não são "profissionais"
if (str_ends_with($email, '@profissional.com')) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Cadastro automático disponível apenas para clientes.']);
    exit;
}

// Verificar se o usuário já existe
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Se o usuário não existir, criar um novo registro sem senha
    $tipo_usuario = 'cliente'; // Apenas clientes são permitidos

    $sql_insert = "INSERT INTO usuarios (nome, email, tipo_usuario) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('sss', $nome, $email, $tipo_usuario);

    if (!$stmt_insert->execute()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar usuário: ' . $conn->error]);
        exit;
    }
}

// Recuperar os dados do usuário
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $_SESSION['ClassUsuarios'] = $usuario;
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

    // Redirecionar para a página principal
    echo json_encode(['sucesso' => true, 'redirect' => 'index.php']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar login.']);
}

$conn->close();
?>
