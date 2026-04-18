<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

// Pega o ID do post da URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Post inválido.");
}
$post_id = (int)$_GET['id'];

// Busca o post com o nome do autor
$stmt = $conn->prepare("SELECT p.id, p.title, p.content, p.media, p.created_at, u.username as nome 
                        FROM posts p 
                        JOIN users u ON p.user_id = u.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post não encontrado.");
}

// Busca comentários
$comentarios_stmt = $conn->prepare("SELECT c.mensagem, c.created_at, u.username as nome 
                                    FROM comments c 
                                    JOIN users u ON c.user_id = u.id 
                                    WHERE c.post_id = ? 
                                    ORDER BY c.created_at ASC");
$comentarios_stmt->bind_param("i", $post_id);
$comentarios_stmt->execute();
$comentarios_result = $comentarios_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <article class="content-block">
        <h1 style="color: #d4b26a; font-size: 2.5rem; margin-bottom: 10px; line-height: 1.2;">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        
        <div style="margin-bottom: 30px; border-bottom: 1px solid rgba(212,178,106,0.2); padding-bottom: 15px;">
            <small style="color: #c2b8a6; font-style: italic;">
                <i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['nome']); ?> &nbsp; | &nbsp;
                <i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
            </small>
        </div>

        <?php if ($post['media']): ?>
            <div class="post-media-container" style="margin-bottom: 30px;">
                <?php 
                    $media = $post['media'];
                    $is_url = filter_var($media, FILTER_VALIDATE_URL);

                    if ($is_url) {
                        // YouTube
                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $media, $match)) {
                            echo '<div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:8px;">
                                    <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" src="https://www.youtube.com/embed/'.$match[1].'" frameborder="0" allowfullscreen></iframe>
                                  </div>';
                        }
                        // Imagem Direta
                        elseif (preg_match("/\.(jpg|jpeg|png|gif|webp)$/i", $media)) {
                            echo '<img src="'.$media.'" style="width:100%; border-radius:8px; border:1px solid rgba(212,178,106,0.3);">';
                        }
                        // Áudio
                        elseif (preg_match("/\.(mp3|wav|ogg)$/i", $media)) {
                            echo '<audio controls style="width:100%; margin-top:10px;"><source src="'.$media.'"></audio>';
                        }
                        else {
                            echo '<div class="btn-auth" style="display:inline-block;"><a href="'.$media.'" target="_blank" style="color:inherit; text-decoration:none;">Ver Conteúdo Externo</a></div>';
                        }
                    } else {
                        // Arquivos Locais
                        $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                            echo '<img src="'.$media.'" style="width:100%; border-radius:8px;">';
                        } elseif (in_array($ext, ['mp4','webm'])) {
                            echo '<video src="'.$media.'" controls style="width:100%; border-radius:8px;"></video>';
                        } elseif (in_array($ext, ['mp3','wav'])) {
                            echo '<audio src="'.$media.'" controls style="width:100%;"></audio>';
                        }
                    }
                ?>
            </div>
        <?php endif; ?>

        <div style="font-size: 1.2rem; color: #f2e8d5; text-align: justify; line-height: 1.8;">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    </article>

    <section class="content-block" style="background: rgba(20, 15, 10, 0.7);">
        <h3 style="color: #d4b26a; margin-bottom: 25px; border-bottom: 1px solid rgba(212,178,106,0.1); padding-bottom: 10px;">
            <i class="fa-regular fa-comments"></i> Comentários
        </h3>

        <?php if($comentarios_result->num_rows > 0): ?>
            <?php while($comentario = $comentarios_result->fetch_assoc()): ?>
                <div style="margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.03); border-left: 3px solid #d4b26a; border-radius: 4px;">
                    <small style="color: #d4b26a; font-weight: bold;">
                        <?php echo htmlspecialchars($comentario['nome']); ?>
                    </small>
                    <small style="color: #888; margin-left: 10px;">
                        <?php echo date('d/m/Y H:i', strtotime($comentario['created_at'])); ?>
                    </small>
                    <p style="margin-top: 8px; color: #e8e0d2; font-size: 1rem;">
                        <?php echo nl2br(htmlspecialchars($comentario['mensagem'])); ?>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888; font-style: italic; margin-bottom: 20px;">Ainda não há reflexões sobre este tema. Seja o primeiro a comentar.</p>
        <?php endif; ?>

        <div style="margin-top: 40px;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="add_comment.php">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <textarea name="mensagem" placeholder="Deixe a sua opinião..." required style="min-height: 120px;"></textarea>
                    <button type="submit" class="btn-auth">Publicar Comentário</button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; border: 1px dashed rgba(212,178,106,0.3);">
                    <p style="color: #c2b8a6;">Deseja participar na conversa?</p>
                    <a href="login.php" style="color: #d4b26a; font-weight: bold; text-decoration: none;">Faça login aqui</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

</body>
</html>