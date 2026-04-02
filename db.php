<?php
$servername = "localhost";
$username = "jcosta_user";
$password = "senha123"; 
$dbname = "blog_jcosta";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8mb4");
?>