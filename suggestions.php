<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";
// ... (mantem a tua lógica de PHPMailer e POST aqui)
$sugestoes_result = $conn->query("SELECT * FROM suggestions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugestões | João Costa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <h1>Sugestões</h1>
        
        <div class="form-container">
            <h3>Enviar Ideia</h3>
            <form method="post">
                <input type="text" name="titulo" placeholder="Assunto" required>
                <textarea name="texto" placeholder="A tua ideia..." rows="4" required></textarea>
                <button type="submit" name="enviar">Enviar</button>
            </form>
        </div>

        <h2 style="margin-top: 40px; color: #d4b26a;">Ideias da Comunidade</h2>
        <div style="margin-top: 20px;">
            <?php while($sug = $sugestoes_result->fetch_assoc()): ?>
                <div class="custom-card">
                    <h4><?php echo htmlspecialchars($sug['title']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($sug['content'])); ?></p>
                    <small>Enviada em: <?php echo date('d/m/Y', strtotime($sug['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>