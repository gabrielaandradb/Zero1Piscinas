<?php
session_start();
header('Content-Type: application/json');

// Decodificar o corpo da requisição JSON
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

// Verificar se o token foi enviado
if (!$token) {
    echo json_encode(['success' => false, 'message' => 'Token não enviado']);
    exit;
}

// Consultar a API do Facebook
$url = 'https://graph.facebook.com/me?access_token=' . urlencode($token) . '&fields=id,name,email';
$response = @file_get_contents($url);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar-se à API do Facebook']);
    exit;
}

$userData = json_decode($response, true);

// Verificar se os dados do usuário foram retornados
if (!isset($userData['email'])) {
    echo json_encode(['success' => false, 'message' => 'E-mail não retornado pela API do Facebook']);
    exit;
}

$email = $userData['email'];
$nome = $userData['name'] ?? 'Cliente Facebook';

// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');

// Verificar conexão com o banco
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados']);
    exit;
}

// Verificar se o usuário já está cadastrado
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuário já existe
    $usuario = $result->fetch_assoc();
    $_SESSION['ClassUsuarios'] = $usuario;
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

    echo json_encode(['success' => true, 'redirect' => 'index.php']);
} else {
    // Usuário não existe, inserir no banco de dados
    $senha_aleatoria = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
    $tipo_usuario = 'cliente';

    $stmt_insert = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param('ssss', $nome, $email, $senha_aleatoria, $tipo_usuario);

    if ($stmt_insert->execute()) {
        // Recuperar o usuário recém-criado
        $stmt_select = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt_select->bind_param('s', $email);
        $stmt_select->execute();
        $result_new = $stmt_select->get_result();

        if ($result_new->num_rows > 0) {
            $usuario = $result_new->fetch_assoc();
            $_SESSION['ClassUsuarios'] = $usuario;
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            echo json_encode(['success' => true, 'redirect' => 'index.php']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao recuperar usuário após cadastro.']);
        }
        $stmt_select->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $stmt_insert->error]);
    }
    $stmt_insert->close();
}

$stmt->close();
$conn->close();
?>
