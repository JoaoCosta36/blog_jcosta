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
        // ✅ CORREÇÃO: Coluna 'content' conforme a tua BD
        $stmt = $conn->prepare("INSERT INTO suggestions (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
        if(!$stmt){
            die("Erro no prepare: " . $conn->error);
        }
        $stmt->bind_param("iss", $user_id, $titulo, $texto);

        if($stmt->execute()){
            $sucesso = "Sugestão enviada com sucesso!";

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

                    $mail->setFrom($env['GMAIL_USER'], $env['FROM_NAME'] ?? 'joaocostArt');
                    $mail->addAddress('jpscosta.music@gmail.com', 'João Costa');
                    $mail->isHTML(true);
                    $mail->Subject = 'Nova sugestão no Blog';
                    $mail->Body = "<strong>Nova Sugestão Recebida</strong><br><br>Titulo: $titulo <br> Mensagem: $texto";
                    $mail->send();
                } catch (Exception $e) {
                    // Erro silencioso no email para não confundir o user
                }
            }
            $titulo = $texto = '';
        } else {
            $erro = "Erro ao inserir: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ✅ REMOVIDO O JOIN: Agora não identifica quem fez a sugestão
$sugestoes_result = $conn->query("SELECT title, content, created_at FROM suggestions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugestões</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
            font-family: 'EB Garamond', serif;
        }

        .form-wrapper {
            background: rgba(45, 35, 25, 0.9);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(212, 178, 106, 0.3);
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            margin-bottom: 40px;
        }

        .form-wrapper h1 { color: #d4b26a; text-align: center; margin-bottom: 20px; }

        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        .form-group label { color: #d4b26a; font-size: 0.9em; }
        
        .form-group input, .form-group textarea {
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #5a4c3c;
            border-radius: 6px;
            color: #fff;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #d4b26a;
            color: #2b241a;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover { background: #b6924d; }

        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; }
        .alert-error { background: rgba(255,0,0,0.2); color: #ff8888; border: 1px solid #ff0000; }
        .alert-success { background: rgba(0,255,0,0.1); color: #88ff88; border: 1px solid #00ff00; }

        /* Lista de Sugestões Anónimas */
        .sugestao-card {
            background: rgba(255, 255, 255, 0.03);
            border-left: 3px solid #d4b26a;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            width: 100%;
            max-width: 600px;
        }

        .sugestao-card h3 { color: #d4b26a; margin: 0 0 10px 0; font-size: 1.2em; }
        .sugestao-card p { color: #c2b8a6; margin: 0; line-height: 1.5; }
        .sugestao-card small { color: #5a4c3c; display: block; margin-top: 10px; }
    </style>
</head>
<body style="padding-top:60px;">

    <?php if(file_exists("nav_bar.php")) include "nav_bar.php"; ?>

    <div class="container">
        <div class="form-wrapper">
            <h1>Sugestões</h1>

            <?php if($erro): ?><div class="alert alert-error"><?php echo $erro; ?></div><?php endif; ?>
            <?php if($sucesso): ?><div class="alert alert-success"><?php echo $sucesso; ?></div><?php endif; ?>

            <?php if($user_id): ?>
                <form method="post">
                    <div class="form-group">
                        <label>Assunto</label>
                        <input type="text" name="titulo" placeholder="Título da sugestão" required>
                    </div>
                    <div class="form-group">
                        <label>A tua ideia</label>
                        <textarea name="texto" placeholder="O que gostarias de ver no blog?" required rows="4"></textarea>
                    </div>
                    <button type="submit" name="enviar" class="btn-submit">Enviar Sugestão</button>
                </form>
            <?php else: ?>
                <p style="text-align:center;">
                    <a href="login.php" style="color:#d4b26a;">Faz login</a> para enviares uma sugestão.
                </p>
            <?php endif; ?>
        </div>

        <div style="width: 100%; max-width: 600px;">
            <h2 style="color: #d4b26a; border-bottom: 1px solid rgba(212,178,106,0.2); padding-bottom: 10px;">Ideias da Comunidade</h2>
            
            <?php while($sug = $sugestoes_result->fetch_assoc()): ?>
                <div class="sugestao-card">
                    <h3><?php echo htmlspecialchars($sug['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($sug['content'])); ?></p>
                    <small>Enviada em: <?php echo date('d/m/Y H:i', strtotime($sug['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>