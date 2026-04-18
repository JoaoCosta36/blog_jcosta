<?php
/**
 * LOGOUT - João Costa
 * Finalidade: Encerra a sessão e limpa os dados do utilizador.
 */

header('Content-Type: text/html; charset=UTF-8');

// Inicia a sessão para poder destruí-la
session_start();

// Remove todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão no servidor
session_destroy();

// Redireciona para a página inicial
header("Location: index.php");
exit;
?>