<?php
header('Content-Type: text/html; charset=UTF-8');
$host = "localhost";
$dbname = "blog_creativo";
$user = "root";
$pass = ""; // coloque sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Inicia sessão
session_start();
?>