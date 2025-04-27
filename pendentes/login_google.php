<?php
require_once 'vendor/autoload.php'; // Se estiver usando o Google API Client Library para PHP

// Receba o token enviado do frontend
$data = json_decode(file_get_contents("php://input"));

if (isset($data->token)) {
    $token = $data->token;

    $client = new Google_Client();
    $client->setClientId('964075037995-62drlhckrr5cfbu79ets39tpk02ei1h4.apps.googleusercontent.com'); // Substitua pelo seu client ID

    $payload = $client->verifyIdToken($token);
    if ($payload) {
        // Token válido, login bem-sucedido
        // Acesse as informações do usuário do Google
        $email = $payload['email'];
        $nome = $payload['name'];
        $google_id = $payload['sub'];

        // Verifique se o usuário já está registrado na sua base de dados
        $conn = new mysqli('localhost', 'root', '', 'Zero1Piscinas');
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM usuarios WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Usuário já registrado, fazer login
            $usuario = $result->fetch_assoc();
            $_SESSION['ClassUsuarios'] = $usuario;
            echo json_encode(['success' => true]);
        } else {
            // Usuário não registrado, criar novo registro
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, 'cliente')");
            $senha = ''; // Senha vazia, pois está logando via Google
            $stmt->bind_param('sss', $nome, $email, $senha);
            if ($stmt->execute()) {
                $_SESSION['ClassUsuarios'] = $stmt->insert_id;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            $stmt->close();
        }

        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Token inválido']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Token não enviado']);
}
?>
