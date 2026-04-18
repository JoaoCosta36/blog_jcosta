<?php
/** * LISTAGEM DE POSTS - João Costa
 * Solução Final: Novo Vetor Clássico (SVG) e Headers Fixos
 */

// 1. CONFIGURAÇÃO (Ocupa a linha 1 do ficheiro, sem espaços antes)
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; 

/**
 * Função para gerar o preview (Imagem, YouTube ou o Novo Vetor Clássico)
 */
function getMediaPreview($url) {
    if (empty($url)) {
        return renderClassicManuscriptSVG();
    }

    // Se for imagem
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
 * Desenha o Vetor de Folha Antiga com Pena em SVG (Clássico e Manuscrito)
 * Desenhado à mão para ter um ar clássico e limpo.
 */
function renderClassicManuscriptSVG() {
    return '
    <div class="post-preview-img default-bg">
        <svg viewBox="0 0 100 100" class="vector-svg">
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>João Costa | Blog Clássico</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --accent: #d4b26a;
            --bg-card: rgba(30, 25, 20, 0.98);
            --card-height: 200px;
        }

        body {
            font-family: 'EB Garamond', serif;
            background-color: #1a1612;
            color: #f4f4f4;
            margin: 0; padding-top: 90px;
        }

        .main-content { max-width: 950px; margin: 0 auto; padding: 0 20px; }

        .post-resumo {
            background: var(--bg-card);
            border: 1px solid rgba(212, 178, 106, 0.2);
            display: flex; 
            height: var(--card-height);
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: 0.3s;
        }

        .post-resumo:hover { border-color: var(--accent); transform: translateY(-3px); }

        .post-preview-img {
            width: 32%; min-width: 32%;
            height: 100%;
            background-size: cover;
            background-position: center;
            display: flex; align-items: center; justify-content: center;
            border-right: 1px solid rgba(212, 178, 106, 0.1);
        }

        /* Estilo para quando é o Vetor Clássico (Folha Antiga) */
        .default-bg {
            background: linear-gradient(45deg, #1a1612, #25201b);
        }

        .vector-svg {
            width: 80px; /* Tamanho do ícone dentro do card */
            height: 100px;
            opacity: 0.8; /* Leve transparência para integrar com o blog */
        }

        .post-info { padding: 20px 25px; flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; justify-content: center; }
        
        .post-info h2 { color: var(--accent); margin: 0 0 5px 0; font-size: 1.6em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .post-info p { color: #c2b8a6; margin: 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; }

        .video-thumb::after {
            content: "\f144"; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; font-size: 2.5em; color: rgba(255,255,255,0.6);
        }

        @media (max-width: 700px) {
            .post-resumo { height: auto; flex-direction: column; }
            .post-preview-img { width: 100%; height: 180px; border-right: none; border-bottom: 1px solid rgba(212, 178, 106, 0.1); }
            .post-info h2 { white-space: normal; }
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="main-content">
    <h1 style="text-align:center; color:var(--accent); letter-spacing:2px;">PUBLICAÇÕES</h1>
    <div id="posts">
        <?php
        $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
        while($post = $result->fetch_assoc()):
            $preview = getMediaPreview($post['media']);
        ?>
        <div class="post-resumo" onclick="window.location.href='post.php?id=<?php echo $post['id']; ?>'">
            <?php echo $preview; // Aqui aparece a imagem, thumb do vídeo ou o novo vetor clássico ?>
            <div class="post-info">
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <small style="color:#888; margin-bottom:10px;"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                <p><?php echo strip_tags($post['content']); ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>