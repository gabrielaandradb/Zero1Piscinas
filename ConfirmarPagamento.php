<?php
session_start();
require_once 'Conexao.php';

// Verifique se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Recebe dados JSON do corpo da requisição
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['orderID'])) {
    echo json_encode(['success' => false, 'message' => 'ID da ordem não fornecido']);
    exit;
}

$orderID = $data['orderID'];

// Dados do sandbox PayPal (client id e secret da sua app sandbox)
$clientId = 'AUoP8cys_BnXZ7OYianLBlZa02TEjN3N7qU8HhMSv_HAMSN6TWV1Iz0aj3ez6yusRUQDv_AHupyCi9b6';
$secret = 'EOuEjlXoumq-yf4wVBEh1X3hQHa4ZvvBGNhfMMqVgairV0a4XLRLwlyLozNHWeNToiWjOmVLzIOJGyas';

// Função para obter token OAuth2 do PayPal
function getAccessToken($clientId, $secret) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return null;
    }
    curl_close($ch);
    $json = json_decode($response, true);
    return $json['access_token'] ?? null;
}

// Função para verificar detalhes da ordem
function getOrderDetails($accessToken, $orderID) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return null;
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Obtem token
$accessToken = getAccessToken($clientId, $secret);
if (!$accessToken) {
    echo json_encode(['success' => false, 'message' => 'Erro ao obter token do PayPal']);
    exit;
}

// Obtem detalhes do pedido
$orderDetails = getOrderDetails($accessToken, $orderID);

if (!$orderDetails) {
    echo json_encode(['success' => false, 'message' => 'Erro ao consultar ordem no PayPal']);
    exit;
}

// Verifica se o status do pedido está COMPLETED (pagamento efetuado)
if ($orderDetails['status'] === 'COMPLETED') {
    // Aqui você pode atualizar o status do pedido no seu banco de dados
    // Exemplo genérico:
    /*
    $conexao = Conexao::getInstance();
    $stmt = $conexao->prepare("UPDATE pedidos SET status = 'pago', pay_order_id = :orderID WHERE servico_id = :servico_id AND usuario_id = :usuario_id");
    $stmt->execute([
        ':orderID' => $orderID,
        ':servico_id' => $_SESSION['servico_id'],
        ':usuario_id' => $_SESSION['ClassUsuarios']['id']
    ]);
    */

    echo json_encode(['success' => true, 'message' => 'Pagamento confirmado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Pagamento não confirmado. Status: ' . $orderDetails['status']]);
}
