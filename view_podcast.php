<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM podcasts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res->fetch_assoc();

if (!$p) { die("Episódio não encontrado."); }

// --- BUSCAR COMENTÁRIOS (Filtrando por Podcast/Conversas) ---
$comentarios_result = false;
$sql_comentarios = "SELECT c.mensagem, c.created_at, u.nome 
                    FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.content_id = ? AND c.content_type = 'podcast' 
                    ORDER BY c.created_at ASC";

if ($com_stmt = $conn->prepare($sql_comentarios)) {
    $com_stmt->bind_param("i", $id);
    $com_stmt->execute();
    $comentarios_result = $com_stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php if(file_exists('adsense.php')) include 'adsense.php'; ?>
    <meta charset="UTF-8">
   
    <title><?php echo htmlspecialchars($p['title']); ?> | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper" style="max-width: 850px;">
    
    <div style="text-align: left; margin-bottom: 25px;">
        <a href="podcasts.php" style="text-decoration: none; color: #c2b8a6; font-size: 0.9rem; transition: 0.3s;" onmouseover="this.style.color='#d4b26a'" onmouseout="this.style.color='#c2b8a6'">
            <i class="fa-solid fa-chevron-left"></i> Voltar às Conversas
        </a>
    </div>

    <div class="content-block" style="padding: 40px;">
        
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="font-size: 3.5rem; color: #d4b26a; margin-bottom: 15px; opacity: 0.8;">
                <i class="fa-solid fa-microphone-lines"></i>
            </div>
            <h1 style="color: #d4b26a; font-size: 2.2rem; margin-bottom: 10px; line-height: 1.2;">
                <?php echo htmlspecialchars($p['title']); ?>
            </h1>
            <div style="color: #888; font-style: italic; font-size: 0.95rem;">
                <i class="fa-regular fa-calendar-days"></i> Publicado em <?php echo date('d/m/Y', strtotime($p['created_at'])); ?>
            </div>
        </div>

        <?php if(!empty($p['audio_url'])): ?>
            <div style="background: rgba(212, 178, 106, 0.05); padding: 25px; border-radius: 12px; border: 1px solid rgba(212, 178, 106, 0.15); margin-bottom: 35px;">
                <p style="color: #d4b26a; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; text-align: center;">Ouvir Episódio</p>
                <audio controls style="width: 100%; filter: sepia(10%) contrast(110%);">
                    <source src="<?php echo $p['audio_url']; ?>" type="audio/mpeg">
                    O teu navegador não suporta a reprodução deste áudio.
                </audio>
            </div>
        <?php endif; ?>

        <div style="border-top: 1px solid rgba(212, 178, 106, 0.1); padding-top: 30px;">
            <h3 style="color: #d4b26a; font-size: 1.1rem; margin-bottom: 15px; text-transform: uppercase;">Sobre esta conversa</h3>
            <div style="color: #f2e8d5; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                <?php echo nl2br(htmlspecialchars($p['description'])); ?>
            </div>
        </div>
    </div>

    <section class="content-block" style="margin-top: 30px; background: rgba(20, 15, 10, 0.6); padding: 30px;">
        <h3 style="color: #d4b26a; margin-bottom: 25px;">
            <i class="fa-regular fa-comments"></i> Reflexões e Comentários
        </h3>

        <?php if($comentarios_result && $comentarios_result->num_rows > 0): ?>
            <?php while($comentario = $comentarios_result->fetch_assoc()): ?>
                <div style="margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-left: 2px solid #d4b26a; border-radius: 0 4px 4px 0;">
                    <strong style="color: #d4b26a;"><?php echo htmlspecialchars($comentario['nome']); ?></strong>
                    <small style="color: #666; margin-left: 10px; font-size: 0.8rem;"><?php echo date('d/m/Y H:i', strtotime($comentario['created_at'])); ?></small>
                    <p style="margin-top: 8px; color: #e8e0d2; font-size: 1rem;"><?php echo nl2br(htmlspecialchars($comentario['mensagem'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888; font-style: italic; margin-bottom: 20px;">Esta conversa ainda não tem comentários. Sê o primeiro a partilhar.</p>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(212,178,106,0.1);">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="add_comment.php">
                    <input type="hidden" name="content_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="tipo" value="podcast">
                    
                    <textarea name="mensagem" placeholder="O que achaste desta conversa? Partilha a tua opinião..." required 
                              style="width: 100%; min-height: 100px; background: #1a1612; color: #fff; border: 1px solid #d4b26a; padding: 12px; border-radius: 5px; font-family: inherit;"></textarea>
                    <button type="submit" class="btn-auth" style="margin-top: 12px; width: 100%; font-weight: bold;">Publicar Comentário</button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 25px; border: 1px dashed rgba(212,178,106,0.3); border-radius: 8px;">
                    <p style="color: #c2b8a6; margin-bottom: 15px;">Queres participar nesta conversa?</p>
                    <a href="login.php" class="btn-auth" style="display: inline-block; text-decoration: none; padding: 10px 30px;">
                        <i class="fa-solid fa-right-to-bracket"></i> Entra para comentar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
        <div style="text-align: center; margin-top: 25px; margin-bottom: 50px;">
            <a href="edit_podcast.php?id=<?php echo $p['id']; ?>" class="btn-auth" style="text-decoration: none; font-size: 0.85rem; padding: 10px 20px;">
                <i class="fa-solid fa-gear"></i> Gerir Episódio
            </a>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?></body>
</html>