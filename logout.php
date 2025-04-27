<?php
session_start();
session_destroy(); // Destrói todas as sessões
header('Location: LoginCadastro.php'); // Redireciona para a página de login
exit;
?>

