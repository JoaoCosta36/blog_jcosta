<?php
/** * LISTAGEM DE POSTS - João Costa
 * Solução Final Corrigida 100%
 */

header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; 

/**
 * Função para gerar o preview (Imagem, YouTube ou Vetor Clássico)
 */
function getMediaPreview($url) {
    if (empty($url)) {
        return renderClassicManuscriptSVG();
    }

    // Se for imagem (Upload ou Link)
    if (preg_match('/\.(jpeg|jpg|gif|png|webp)$/i', $url)) {
        return '<div class="post-preview-img" style="background-image: url(\''.$url.'\');"></div>';
    }

    // Se for YouTube
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        $videoID = "";
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $videoID = $match[1];
        }
        if ($videoID) {
            $thumb = "https://img.youtube.com/vi/$videoID/mqdefault.jpg";
            return '<div class="post-preview-img video-thumb" style="background-image: url(\''.$thumb.'\');"></div>';
        }
    }

    return renderClassicManuscriptSVG();
}

/**
 * Vetor SVG de Folha Antiga com Pena
 */
function renderClassicManuscriptSVG() {
    return '
    <div class="post-preview-img default-bg">
        <svg viewBox="0 0 100 100" class="vector-svg" style="width:80px; height:100px; opacity:0.7;">
            <path d="M15,10 C15,5 25,5 25,10 L25,90 C25,95 15,95 15,90 Z" fill="#f8e7b9"/>
            <path d="M25,10 C25,5 75,5 75,10 L75,90 C75,95 25,95 25,90 Z" fill="#f4d193"/>
            <path d="M25,10 C25,5 15,5 15,10" fill="none" stroke="#2b1a13" stroke-width="1.5"/>
            <path d="M25,90 C25,95 15,95 15,90" fill="none" stroke="#2b1a13" stroke-width="1.5"/>
            <line x1="32" y1="20" x2="68" y2="20" stroke="#8b6543" stroke-width="0.75" opacity="0.6"/>
            <line x1="32" y1="35" x2="68" y2="35" stroke="#8b6543" stroke-width="0.75" opacity="0.6"/>
            <line x1="32" y1="50" x2="68" y2="50" stroke="#8b6543" stroke-width="0.75" opacity="0.6"/>
            <line x1="32" y1="65" x2="68" y2="65" stroke="#8b6543" stroke-width="0.75" opacity="0.6"/>
            <path d="M85,30 C75,30 70,50 85,80 Z" fill="#2b1a13"/>
            <path d="M85,30 C85,25 75,30 75,40" fill="none" stroke="#2b1a13" stroke-width="1.5"/>
            <path d="M85,80 C88,85 82,88 85,92 C88,88 82,85 85,80" fill="#2b1a13"/>
        </svg>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
   
    <title>João Costa | Blog Clássico</title>
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <h1 style="text-align:center; color:#d4b26a; letter-spacing:3px; margin-bottom:40px; text-transform:uppercase;">
        Publicações
    </h1>

    <div class="main-content">
        <?php
        $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
        while($post = $result->fetch_assoc()):
            $preview = getMediaPreview($post['media']);
        ?>
        <div class="post-resumo" onclick="window.location.href='post.php?id=<?php echo $post['id']; ?>'">
            
            <?php echo $preview; ?>

            <div class="post-info">
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <small style="color:#d4b26a; opacity:0.7; margin-bottom:10px; display:block;">
                    <i class="fa-regular fa-calendar-days"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                </small>
                <p><?php echo mb_strimwidth(strip_tags($post['content']), 0, 180, "..."); ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?></body>
</html>