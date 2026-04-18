<?php
// Ativar exibição de erros para caso a tabela ainda não esteja pronta
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "db.php";
session_start();

// 1. Verificação de Segurança
if(!isset($_SESSION['user_id'])) { 
    die("Erro: Precisas de estar logado para comentar."); 
}

// 2. Captura e Limpeza de Dados
// Usamos content_id que serve para ID de Post, Música ou Podcast
$c_id     = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
$u_id     = $_SESSION['user_id'];
$tipo     = isset($_POST['tipo']) ? $_POST['tipo'] : 'post'; 

// 3. Mapeamento de Retorno (Para onde o utilizador volta)
$urls = [
    'post'    => 'post.php',
    'music'   => 'view_music.php',
    'podcast' => 'view_podcast.php'
];

$return_page = isset($urls[$tipo]) ? $urls[$tipo] : 'index.php';

// 4. Inserção na Base de Dados
if($c_id > 0 && !empty($mensagem)) {
    // IMPORTANTE: Esta query assume que já fizeste o ALTER TABLE na tabela comments
    $sql = "INSERT INTO comments (user_id, content_type, content_id, mensagem) VALUES (?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // "isis" -> Integer (user_id), String (tipo), Integer (content_id), String (mensagem)
        $stmt->bind_param("isis", $u_id, $tipo, $c_id, $mensagem);
        
        if(!$stmt->execute()) {
            // Se der erro aqui, provavelmente as colunas content_type/content_id não existem
            die("Erro ao salvar comentário: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Erro na preparação da base de dados: " . $conn->error);
    }
}

// 5. Redirecionamento Final
header("Location: " . $return_page . "?id=" . $c_id);
exit;