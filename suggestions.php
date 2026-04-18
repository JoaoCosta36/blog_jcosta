<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

// --- A tua lógica de processamento de formulário e PHPMailer deve ficar aqui ---
// Exemplo simples de inserção (ajusta conforme a tua tabela):
if (isset($_POST['enviar'])) {
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $user_id = $_SESSION['user_id'] ?? 0; // 0 se for anónimo, ou podes obrigar a login
    
    $stmt = $conn->prepare("INSERT INTO suggestions (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $titulo, $texto);
    $stmt->execute();
    $stmt->close();
}

$sugestoes_result = $conn->query("SELECT * FROM suggestions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugestões | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <div style="text-align: center; margin-bottom: 50px;">
            <h1 style="color: #d4b26a; text-transform: uppercase; letter-spacing: 3px;">Sugestões</h1>
            <p style="color: #c2b8a6; font-style: italic;">Tens uma ideia para um tema, música ou conversa? Partilha comigo.</p>
        </div>
        
        <div class="content-block" style="max-width: 600px; margin: 0 auto 60px auto;">
            <h3 style="color: #d4b26a; margin-bottom: 20px; text-align: center;">
                <i class="fa-regular fa-lightbulb"></i> Enviar Nova Ideia
            </h3>
            <form method="post" action="">
                <input type="text" name="titulo" placeholder="Assunto (ex: Sugestão de Podcast)" required>
                <textarea name="texto" placeholder="Descreve a tua ideia em detalhe..." rows="5" required></textarea>
                <button type="submit" name="enviar" class="btn-auth" style="width: 100%;">
                    Enviar Sugestão
                </button>
            </form>
        </div>

        <hr style="border: 0; border-top: 1px solid rgba(212,178,106,0.2); margin-bottom: 50px;">

        <h2 style="color: #d4b26a; text-align: center; margin-bottom: 30px;">
            <i class="fa-solid fa-comments"></i> Ideias da Comunidade
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php if($sugestoes_result && $sugestoes_result->num_rows > 0): ?>
                <?php while($sug = $sugestoes_result->fetch_assoc()): ?>
                    <div class="content-block" style="margin: 0; padding: 25px; background: rgba(35, 30, 25, 0.6); position: relative; transition: 0.3s;">
                        <h4 style="color: #d4b26a; margin-bottom: 10px; font-size: 1.2rem;">
                            <?php echo htmlspecialchars($sug['title']); ?>
                        </h4>
                        <p style="font-size: 0.95rem; color: #f2e8d5; margin-bottom: 15px; line-height: 1.5;">
                            <?php echo nl2br(htmlspecialchars($sug['content'])); ?>
                        </p>
                        <div style="border-top: 1px solid rgba(212,178,106,0.1); padding-top: 10px;">
                            <small style="color: #888; font-style: italic;">
                                <i class="fa-regular fa-calendar-check"></i> 
                                <?php echo date('d/m/Y', strtotime($sug['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; color: #888;">Ainda não há sugestões. Sê o primeiro a contribuir!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>