<?php
// ATIVAR DEBUG para ver erros se algo falhar
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Post inválido.");
}
$post_id = (int)$_GET['id'];

// 1. Busca o post
$stmt = $conn->prepare("SELECT p.id, p.title, p.content, p.media, p.created_at, u.nome 
                        FROM posts p 
                        JOIN users u ON p.user_id = u.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post não encontrado.");
}

// 2. Busca comentários (Nova estrutura universal)
$com_stmt = $conn->prepare("SELECT c.mensagem, c.created_at, u.nome 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.content_id = ? AND c.content_type = 'post' 
                            ORDER BY c.created_at ASC");
$com_stmt->bind_param("i", $post_id);
$com_stmt->execute();
$comentarios_result = $com_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> | João Costa</title>
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <article class="content-block">
        <h1 style="color: #d4b26a; margin-bottom: 10px;"><?php echo htmlspecialchars($post['title']); ?></h1>
        
        <div style="margin-bottom: 25px; border-bottom: 1px solid rgba(212,178,106,0.2); padding-bottom: 10px;">
            <small style="color: #c2b8a6;">
                <i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['nome']); ?> | 
                <i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
            </small>
        </div>

        <?php if (!empty($post['media'])): ?>
            <div class="media-container" style="margin-bottom: 30px; text-align: center;">
                <?php 
                $media = $post['media'];
                $is_url = filter_var($media, FILTER_VALIDATE_URL);

                if ($is_url) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $media, $match)) {
                        $video_id = $match[1];
                        echo '<div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:8px; background:#000;">
                                <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" 
                                        src="https://www.youtube.com/embed/'.$video_id.'" 
                                        frameborder="0" allowfullscreen></iframe>
                              </div>';
                    } 
                    elseif (preg_match("/\.(jpg|jpeg|png|gif|webp)$/i", $media)) {
                        echo '<img src="'.$media.'" style="max-width:100%; border-radius:8px; border:1px solid rgba(212,178,106,0.3);">';
                    }
                    else {
                        echo '<a href="'.$media.'" target="_blank" class="btn-auth" style="display:inline-block; text-decoration:none;">Ver Conteúdo Externo</a>';
                    }
                } 
                else {
                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                        echo '<img src="'.$media.'" style="max-width:100%; border-radius:8px;">';
                    } elseif (in_array($ext, ['mp4','webm','ogg'])) {
                        echo '<video src="'.$media.'" controls style="max-width:100%; border-radius:8px;"></video>';
                    }
                }
                ?>
            </div>
        <?php endif; ?>

        <div style="font-size: 1.2rem; color: #f2e8d5; line-height: 1.8; text-align: justify;">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    </article>

    <section class="content-block" style="margin-top: 40px; background: rgba(0,0,0,0.2); padding: 25px; border-radius: 8px;">
        <h3 style="color: #d4b26a; border-bottom: 1px solid rgba(212,178,106,0.2); padding-bottom: 10px; margin-bottom: 20px;">
            <i class="fa-regular fa-comments"></i> Comentários
        </h3>
        
        <?php if ($comentarios_result->num_rows > 0): ?>
            <?php while($comentario = $comentarios_result->fetch_assoc()): ?>
                <div style="margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.05); border-left: 3px solid #d4b26a; border-radius: 4px;">
                    <strong style="color: #d4b26a;"><?php echo htmlspecialchars($comentario['nome']); ?></strong> 
                    <small style="color: #888; float: right;"><?php echo date('d/m/Y H:i', strtotime($comentario['created_at'])); ?></small>
                    <p style="margin-top: 10px; color: #e8e0d2;"><?php echo nl2br(htmlspecialchars($comentario['mensagem'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888; font-style: italic;">Ainda não há comentários. Seja o primeiro a comentar!</p>
        <?php endif; ?>

        <div style="margin-top: 30px; border-top: 1px solid rgba(212,178,106,0.1); padding-top: 20px;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="add_comment.php">
                    <input type="hidden" name="content_id" value="<?php echo $post_id; ?>">
                    <input type="hidden" name="tipo" value="post">
                    <textarea name="mensagem" placeholder="Partilha a tua opinião..." required 
                              style="width: 100%; min-height: 100px; background: #111; color: #fff; border: 1px solid #444; padding: 12px; border-radius: 5px;"></textarea>
                    <button type="submit" class="btn-auth" style="margin-top: 10px; width: 100%;">Publicar Comentário</button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 15px; background: rgba(212,178,106,0.05); border: 1px dashed #d4b26a; border-radius: 8px;">
                    <p style="color: #c2b8a6;">Queres comentar? <a href="login.php" style="color: #d4b26a; font-weight: bold;">Entra na tua conta</a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?></body>
</html>