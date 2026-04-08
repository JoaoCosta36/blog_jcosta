<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

// Pega o ID do post da URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Post inválido.");
}
$post_id = (int)$_GET['id'];

// Busca o post
$stmt = $conn->prepare("SELECT p.id, p.title, p.content, p.media, p.created_at, u.nome 
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
$comentarios_stmt = $conn->prepare("SELECT c.mensagem, c.created_at, u.nome 
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
<link rel="icon" href="icon.jpg" type="image/png">
<meta charset="UTF-8">
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($post['title']); ?> 🎵</title>
<link rel="icon" href="icon.png" type="image/png">
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.post-container {
    max-width: 850px;
    margin: 120px auto 40px auto;
    padding: 25px;
    background: rgba(45,35,25,0.85);
    border: 1px solid rgba(212,178,106,0.3);
    border-radius: 12px;
    color: #f2e8d5;
    font-family: 'EB Garamond', serif;
    line-height: 1.6;
}

.post-container h1 {
    color: #d4b26a;
    margin-bottom: 5px;
}

.post-container small {
    color: #c2b8a6;
    font-size: 0.9em;
}

.post-container p {
    margin-top: 15px;
    white-space: pre-line;
}

/* Estilo de mídia */
.post-container img,
.post-container video,
.post-container audio,
.post-container iframe {
    display: block;
    margin: 15px auto;
    border-radius: 8px;
    width: 100%;
    max-width: 100%;
}

.post-container audio {
    outline: none;
}

/* Comentários */
.comentarios {
    max-width: 800px;
    margin: 30px auto;
    padding: 20px;
    background: rgba(45,35,25,0.8);
    border-radius: 10px;
    border: 1px solid rgba(212,178,106,0.3);
}

.comentarios h3 {
    color: #d4b26a;
    margin-bottom: 10px;
}

.comentario {
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 10px;
}

.comentario small {
    color: #b9a889;
    display: block;
    margin-bottom: 5px;
}

.comentario p {
    margin: 0;
    color: #f2e8d5;
}

.comentarios textarea {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid rgba(212,178,106,0.3);
    background: rgba(255,255,255,0.08);
    color: #fff;
    resize: vertical;
    min-height: 80px;
}

.comentarios button {
    margin-top: 8px;
    padding: 8px 16px;
    background: #d4b26a;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-family: 'EB Garamond', serif;
    transition: 0.3s;
}

.comentarios button:hover {
    background: #b6924d;
    color: #fff;
}

@media (max-width: 768px) {
    .post-container, .comentarios {
        margin: 90px 15px 30px 15px;
        padding: 15px;
    }
}
</style>
</head>
<body style="padding-top:60px;">

<?php include "nav_bar.php"; ?>

<div class="post-container">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <small>Por <?php echo htmlspecialchars($post['nome']); ?> em <?php echo $post['created_at']; ?></small>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <?php if ($post['media']): ?>
        <?php 
            $media = $post['media'];
            $is_url = filter_var($media, FILTER_VALIDATE_URL);

            if ($is_url) {
                // YouTube embed
                if (preg_match("/youtube\.com\/watch\?v=([^\&\?\/]+)/", $media, $id)) {
                    $youtube_id = $id[1];
                    echo '<iframe width="100%" height="400" src="https://www.youtube.com/embed/'.$youtube_id.'" frameborder="0" allowfullscreen></iframe>';
                }
                // Vimeo embed
                elseif (preg_match("/vimeo\.com\/(\d+)/", $media, $id)) {
                    echo '<iframe src="https://player.vimeo.com/video/'.$id[1].'" width="100%" height="400" frameborder="0" allowfullscreen></iframe>';
                }
                // Link direto de imagem
                elseif (preg_match("/\.(jpg|jpeg|png|gif)$/i", $media)) {
                    echo '<img src="'.$media.'" alt="imagem">';
                }
                // Link direto de áudio
                elseif (preg_match("/\.(mp3|wav|ogg)$/i", $media)) {
                    echo '<audio controls><source src="'.$media.'" type="audio/'.pathinfo($media, PATHINFO_EXTENSION).'"></audio>';
                }
                // Outros links → apenas link clicável
                else {
                    echo '<a href="'.$media.'" target="_blank" style="color:#d4b26a;">'.$media.'</a>';
                }
            } else {
                // Arquivos locais
                $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    echo '<img src="'.$media.'" alt="imagem">';
                } elseif (in_array($ext, ['mp4','webm','ogg'])) {
                    echo '<video src="'.$media.'" controls></video>';
                } elseif (in_array($ext, ['mp3','wav','ogg'])) {
                    echo '<audio src="'.$media.'" controls></audio>';
                }
            }
        ?>
    <?php endif; ?>
</div>

<div class="comentarios">
    <h3>Comentários</h3>
    <?php while($comentario = $comentarios_result->fetch_assoc()): ?>
        <div class="comentario">
            <small><?php echo htmlspecialchars($comentario['nome']); ?> em <?php echo $comentario['created_at']; ?></small>
            <p><?php echo htmlspecialchars($comentario['mensagem']); ?></p>
        </div>
    <?php endwhile; ?>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <form method="post" action="add_comment.php">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <textarea name="mensagem" placeholder="Escreva um comentário..." required></textarea>
        <button type="submit">Comentar</button>
    </form>
    <?php else: ?>
        <p>Faça login para comentar.</p>
    <?php endif; ?>
</div>

</body>
</html>