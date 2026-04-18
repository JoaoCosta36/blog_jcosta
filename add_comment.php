<?php
header('Content-Type: text/html; charset=UTF-8');

include "db.php";
session_start();

// Verifica se o utilizador está logado
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

// Verifica se a mensagem existe e não está vazia
if(!isset($_POST['mensagem']) || empty(trim($_POST['mensagem']))) { 
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    header("Location: post.php?id=$post_id"); 
    exit; 
}

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['user_id'];

// Escapar a mensagem para evitar SQL Injection
$mensagem = $conn->real_escape_string($_POST['mensagem']);

// Inserir o comentário na base de dados
$query = "INSERT INTO comments (post_id, user_id, mensagem) VALUES ($post_id, $user_id, '$mensagem')";

if($conn->query($query)) {
    // Redireciona de volta para o post após sucesso
    header("Location: post.php?id=$post_id");
} else {
    // Em caso de erro técnico, volta ao post para não deixar página em branco
    header("Location: post.php?id=$post_id&error=1");
}
exit;
?>