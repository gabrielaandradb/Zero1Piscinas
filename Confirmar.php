<?php
session_start();
require_once 'Conexao.php';

if (!isset($_SESSION['ClassUsuarios']['id'])) {
    header('Location: LoginCadastro.php');
    exit;
}

if (isset($_POST['servico_id'])) {
    $servico_id = intval($_POST['servico_id']);
    $cliente_id = intval($_SESSION['ClassUsuarios']['id']);
    $conexao = Conexao::getInstance();

    try {
        // Inicia uma transação
        $conexao->beginTransaction();

        // Localiza a piscina associada ao serviço
        $queryPiscina = "
            SELECT piscina_id 
            FROM servicos 
            WHERE id = :servico_id
        ";
        $stmtPiscina = $conexao->prepare($queryPiscina);
        $stmtPiscina->bindParam(':servico_id', $servico_id, PDO::PARAM_INT);
        $stmtPiscina->execute();
        $piscina_id = $stmtPiscina->fetchColumn();

        if ($piscina_id) {
            // Remove os serviços associados à piscina
            $queryDeleteServicos = "DELETE FROM servicos WHERE piscina_id = :piscina_id";
            $stmtDeleteServicos = $conexao->prepare($queryDeleteServicos);
            $stmtDeleteServicos->bindParam(':piscina_id', $piscina_id, PDO::PARAM_INT);
            $stmtDeleteServicos->execute();

            // Remove a piscina
            $queryDeletePiscina = "DELETE FROM piscinas WHERE id = :piscina_id AND cliente_id = :cliente_id";
            $stmtDeletePiscina = $conexao->prepare($queryDeletePiscina);
            $stmtDeletePiscina->bindParam(':piscina_id', $piscina_id, PDO::PARAM_INT);
            $stmtDeletePiscina->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmtDeletePiscina->execute();

            // Confirma a transação
            $conexao->commit();
            $_SESSION['mensagem_sucesso'] = 'Solicitação removida com sucesso!';
        } else {
            $_SESSION['mensagem_erro'] = 'Piscina associada não encontrada.';
        }
    } catch (Exception $e) {
        $conexao->rollBack();
        $_SESSION['mensagem_erro'] = 'Erro ao remover a solicitação: ' . $e->getMessage();
    }
}

header('Location: acompanharServico.php');
exit;
