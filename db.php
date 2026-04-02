<?php
// Carrega as definições do ficheiro externo
$config = parse_ini_file('config.ini');

if ($config === false) {
    // Erro caso o ficheiro .ini não exista ou esteja mal formatado
    die("Erro: Não foi possível ler o ficheiro de configuração.");
}

$servername = $config['host'];
$username   = $config['user'];
$password   = $config['pass'];
$dbname     = $config['name'];

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    // Em produção, não mostres o erro detalhado ao utilizador
    error_log("Falha na conexão: " . $conn->connect_error);
    die("Erro interno de base de dados.");
}

// Definir o charset para suportar acentos e emojis
$conn->set_charset("utf8mb4");
?>