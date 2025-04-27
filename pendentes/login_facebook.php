<?php
// Receber o token de acesso enviado pelo frontend
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];

// Fazer uma requisição para a API do Facebook para validar o token
$url = 'https://graph.facebook.com/me?access_token=' . $token . '&fields=id,name,email';

$response = file_get_contents($url);
$userData = json_decode($response, true);

// Se a resposta for válida
if (isset($userData['email'])) {
    $email = $userData['email'];

    // Conectar ao banco de dados
    $conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Verifique se o usuário já está registrado no banco de dados
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Usuário já existe, faça o login
        $usuario = $result->fetch_assoc();
        session_start();
        $_SESSION['ClassUsuarios'] = $usuario;
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        echo json_encode(['success' => true]);  // Login bem-sucedido
    } else {
        // Usuário não existe, retorna erro
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao validar o token']);
}
?>
