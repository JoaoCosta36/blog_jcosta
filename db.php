<?php
// Tenta ler o ficheiro .env
$env = parse_ini_file('.env');

if ($env === false) {
    die("Erro: Não foi possível ler o ficheiro .env.");
}

// Os nomes aqui devem ser IGUAIS ao que tens no ficheiro .env
$servername = $env['DB_HOST'];
$username   = $env['DB_USER'];
$password   = $env['DB_PASS'];
$dbname     = $env['DB_NAME'];

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    // Regista o erro para ti, mas mostra algo simples ao user
    error_log("Falha na conexão: " . $conn->connect_error);
    die("Erro: Falha na ligação à base de dados. Verifica as credenciais.");
}

// Definir o charset correto
$conn->set_charset("utf8mb4");
?>