<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM musics WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

if (!$m) { die("Música não encontrada."); }

// --- BUSCAR COMENTÁRIOS (Filtrando por Música) ---
$comentarios_result = false;
$sql_comentarios = "SELECT c.mensagem, c.created_at, u.nome 
                    FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.content_id = ? AND c.content_type = 'music' 
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
   
    <title><?php echo htmlspecialchars($m['title']); ?> | joaocostArt</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper" style="max-width: 800px;">
    
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="musics.php" style="text-decoration: none; color: #888; transition: 0.3s;" onmouseover="this.style.color='#d4b26a'" onmouseout="this.style.color='#888'">
            <i class="fa-solid fa-arrow-left"></i> Voltar à Galeria
        </a>
    </div>

    <div class="content-block" style="text-align: center; padding: 40px; border-radius: 10px;">
        <h1 style="color: #d4b26a; font-size: 2rem;"><?php echo htmlspecialchars($m['title']); ?></h1>
        
        <?php if(!empty($m['audio_url'])): ?>
            <div style="margin: 30px 0; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 50px;">
                <audio controls style="width:100%;">
                    <source src="<?php echo $m['audio_url']; ?>" type="audio/mpeg">
                </audio>
            </div>
        <?php endif; ?>

        <div style="text-align: justify; color: #f2e8d5; line-height: 1.6;">
            <?php echo nl2br(htmlspecialchars($m['description'])); ?>
        </div>
    </div>

    <section class="content-block" style="margin-top: 30px; background: rgba(20, 15, 10, 0.6); padding: 25px;">
        <h3 style="color: #d4b26a; margin-bottom: 20px;">
            <i class="fa-regular fa-comments"></i> Comentários
        </h3>

        <?php if($comentarios_result && $comentarios_result->num_rows > 0): ?>
            <?php while($comentario = $comentarios_result->fetch_assoc()): ?>
                <div style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid rgba(212,178,106,0.1);">
                    <strong style="color: #d4b26a;"><?php echo htmlspecialchars($comentario['nome']); ?></strong>
                    <small style="color: #666; margin-left: 10px;"><?php echo date('d/m/Y', strtotime($comentario['created_at'])); ?></small>
                    <p style="margin-top: 5px; color: #e8e0d2;"><?php echo nl2br(htmlspecialchars($comentario['mensagem'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888; font-style: italic;">Ainda não existem comentários para esta música.</p>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(212,178,106,0.2);">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="add_comment.php">
                    <input type="hidden" name="content_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="tipo" value="music">
                    
                    <textarea name="mensagem" placeholder="Deixa a tua opinião sobre esta obra..." required 
                              style="width: 100%; min-height: 100px; background: #111; color: #fff; border: 1px solid #444; padding: 12px; border-radius: 5px;"></textarea>
                    <button type="submit" class="btn-auth" style="margin-top: 10px; width: 100%;">Publicar Comentário</button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; background: rgba(212,178,106,0.05); border: 1px dashed #d4b26a; border-radius: 8px;">
                    <p style="color: #c2b8a6; margin-bottom: 10px;">
                        Queres partilhar o que sentiste ao ouvir esta música?
                    </p>
                    <a href="login.php" class="btn-auth" style="display: inline-block; text-decoration: none; padding: 8px 25px;">
                        <i class="fa-solid fa-user"></i> Faz Login para comentar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include 'footer.php'; ?></body>
</html>