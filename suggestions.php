<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

include "db.php"; // conexão $conn

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$env_path = __DIR__ . '/.env';
$env = file_exists($env_path) ? parse_ini_file($env_path) : [];

ini_set('display_errors', 1);
error_reporting(E_ALL);

$erro = '';
$sucesso = '';
$titulo = '';
$texto = '';

$user_id = $_SESSION['user_id'] ?? null;

if($user_id && isset($_POST['enviar'])){
    $titulo = trim($_POST['titulo'] ?? '');
    $texto  = trim($_POST['texto'] ?? '');

    if(empty($titulo) || empty($texto)){
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // ✅ CORREÇÃO 1: Coluna 'content' em vez de 'text'. 
        // Removi 'activated' porque não existe na tua imagem.
        $stmt = $conn->prepare("INSERT INTO suggestions (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
        if(!$stmt){
            die("Erro no prepare: " . $conn->error);
        }
        $stmt->bind_param("iss", $user_id, $titulo, $texto);

        if($stmt->execute()){
            $sucesso = "Sugestão enviada com sucesso!";

            // Envio de email (PHPMailer)
            if(!empty($env['GMAIL_USER']) && !empty($env['GMAIL_APP_PASS'])){
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $env['GMAIL_USER'];
                    $mail->Password = $env['GMAIL_APP_PASS'];
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom($env['GMAIL_USER'], $env['FROM_NAME'] ?? 'Blog');
                    $mail->addAddress('jpscosta.music@gmail.com', 'João Costa');
                    $mail->isHTML(true);
                    $mail->Subject = 'Nova sugestão';
                    $mail->Body = "ID: $user_id <br> Titulo: $titulo <br> Mensagem: $texto";
                    $mail->send();
                } catch (Exception $e) {
                    $erro .= " Erro no email: " . $mail->ErrorInfo;
                }
            }
            $titulo = $texto = '';
        } else {
            $erro = "Erro ao inserir: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ✅ CORREÇÃO 2: SELECT usa 'content' e não 'text'. 
// Removi o filtro 'WHERE activated = 1' porque a coluna não existe na imagem.
$sugestoes_result = $conn->query("SELECT s.title, s.content, u.nome 
                                  FROM suggestions s 
                                  JOIN users u ON s.user_id = u.id 
                                  ORDER BY s.created_at DESC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Sugestões</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding: 60px 20px;">

    <?php if(file_exists("nav_bar.php")) include "nav_bar.php"; ?>

    <div style="max-width: 600px; margin: auto;">
        <h1>Sugestões</h1>

        <?php if($erro) echo "<p style='color:red;'>$erro</p>"; ?>
        <?php if($sucesso) echo "<p style='color:green;'>$sucesso</p>"; ?>

        <?php if($user_id): ?>
            <form method="post">
                <input type="text" name="titulo" placeholder="Título" required style="width:100%; margin-bottom:10px; padding:8px;">
                <textarea name="texto" placeholder="Sua sugestão" required style="width:100%; height:100px; padding:8px;"></textarea>
                <button type="submit" name="enviar" style="padding:10px 20px; cursor:pointer;">Enviar</button>
            </form>
        <?php else: ?>
            <p>Faz login para sugerir algo.</p>
        <?php endif; ?>

        <hr>

        <h2>Lista de Sugestões</h2>
        <?php while($sug = $sugestoes_result->fetch_assoc()): ?>
            <div style="border-bottom: 1px solid #ccc; padding: 10px 0;">
                <strong><?php echo htmlspecialchars($sug['title']); ?></strong> 
                <small>(por <?php echo htmlspecialchars($sug['nome']); ?>)</small>
                <p><?php echo nl2br(htmlspecialchars($sug['content'])); // ✅ USAR content aqui ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>