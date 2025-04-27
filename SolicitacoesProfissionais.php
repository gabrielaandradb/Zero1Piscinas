<?php
session_start();
include 'Conexao.php';

// Verifique se o usuário está logado e é um profissional
if (!isset($_SESSION['ClassUsuarios']) || $_SESSION['tipo_usuario'] != 'profissional') {
    header('Location: LoginCadastro.php');
    exit;
}

// Buscar solicitações no banco de dados
$sql = "SELECT p.id, u.nome AS cliente_nome, p.tamanho, p.tipo, p.profundidade, 
               p.data_instalacao, p.servico_desejado, p.foto_piscina, p.status 
        FROM piscinas p 
        JOIN usuarios u ON p.cliente_id = u.id
        WHERE p.status = 'pendente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de Clientes</title>
</head>
<body>
    <h1>Solicitações de Orçamento</h1>
    <table border="1">
        <tr>
            <th>Cliente</th>
            <th>Tamanho</th>
            <th>Tipo</th>
            <th>Profundidade</th>
            <th>Data Instalação</th>
            <th>Serviço</th>
            <th>Foto</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['cliente_nome']); ?></td>
            <td><?php echo htmlspecialchars($row['tamanho']); ?></td>
            <td><?php echo htmlspecialchars($row['tipo']); ?></td>
            <td><?php echo htmlspecialchars($row['profundidade']); ?></td>
            <td><?php echo htmlspecialchars($row['data_instalacao']); ?></td>
            <td><?php echo htmlspecialchars($row['servico_desejado']); ?></td>
            <td>
                <?php if ($row['foto_piscina']): ?>
                    <a href="<?php echo htmlspecialchars($row['foto_piscina']); ?>" target="_blank">Ver Foto</a>
                <?php endif; ?>
            </td>
            <td>
                <form action="ResponderSolicitacao.php" method="post">
                    <input type="hidden" name="piscina_id" value="<?php echo $row['id']; ?>">
                    <textarea name="resposta" placeholder="Digite sua resposta"></textarea>
                    <button type="submit">Responder</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
