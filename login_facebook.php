<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

if (!$token) {
    echo json_encode(['success' => false, 'message' => 'Token não enviado']);
    exit;
}

// Obtendo informações do usuário do Facebook
$url = 'https://graph.facebook.com/me?access_token=' . urlencode($token) . '&fields=id,name,email';
$response = file_get_contents($url);
$userData = json_decode($response, true);

if (isset($userData['email'])) {
    $email = $userData['email'];
    $nome = $userData['name'] ?? 'Cliente Facebook';

    $conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados']);
        exit;
    }

    // Verificar se o e-mail já está cadastrado
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    // Usuário encontrado, loga automaticamente
    $usuario = $result->fetch_assoc();
    $_SESSION['ClassUsuarios'] = $usuario;
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
    $redirect = 'index.php';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    // Usuário não encontrado: criar automaticamente como cliente
    $senha_aleatoria = password_hash(uniqid(), PASSWORD_DEFAULT);
    $tipo_usuario = 'cliente';

    $sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('ssss', $nome, $email, $senha_aleatoria, $tipo_usuario);

    if ($stmt_insert->execute()) {
        // Buscar o usuário recém criado
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
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $conn->error]);
    }
}
}