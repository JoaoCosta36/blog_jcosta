<?php
include "db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM podcasts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res->fetch_assoc();

if (!$p) {
    die("<h1 style='color:white; text-align:center; margin-top:100px;'>Episódio não encontrado!</h1>");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['title']); ?> | João Costa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #1a1612; color: #e8e0d2; font-family: 'EB Garamond', serif; }
        .podcast-wrap { 
            max-width: 850px; 
            margin: 120px auto; 
            padding: 40px; 
            background: rgba(35, 30, 25, 0.98); 
            border: 1px solid rgba(212, 178, 106, 0.3); 
            border-radius: 20px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
        }
        h1 { color: #d4b26a; font-size: 2.4em; margin-bottom: 20px; text-align: center; }
        
        .player-area { 
            background: #111; 
            padding: 25px; 
            border-radius: 15px; 
            margin: 30px 0; 
            border-bottom: 3px solid #d4b26a;
        }
        audio { width: 100%; height: 50px; }
        
        .desc-text { 
            text-align: left; 
            line-height: 1.8; 
            font-size: 1.1em; 
            margin-top: 20px; 
            color: #ccc;
            white-space: pre-line;
        }

        .extra-links { 
            margin-top: 40px; 
            display: flex; 
            gap: 15px; 
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-external { 
            background: transparent; 
            color: #d4b26a; 
            padding: 12px 25px; 
            border: 1px solid #d4b26a; 
            border-radius: 50px; 
            text-decoration: none; 
            transition: 0.3s;
            font-size: 0.9em;
        }
        .btn-external:hover { background: #d4b26a; color: #000; }
    </style>
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="podcast-wrap">
        <h1><?php echo htmlspecialchars($p['title']); ?></h1>
        
        <?php if(!empty($p['audio_url'])): ?>
            <div class="player-area">
                <p style="text-align:center; color:#d4b26a; margin-bottom:15px; font-size:0.8em; letter-spacing:2px;">PLAYER DE ÁUDIO</p>
                <audio controls>
                    <source src="<?php echo $p['audio_url']; ?>" type="audio/mpeg">
                    O teu navegador não suporta este áudio.
                </audio>
            </div>
        <?php endif; ?>

        <div class="desc-text">
            <?php echo nl2br(htmlspecialchars($p['description'])); ?>
        </div>

        <?php if(!empty($p['video_url'])): ?>
            <div class="extra-links">
                <?php 
                $links = explode(',', $p['video_url']);
                foreach($links as $link): 
                    if(empty(trim($link))) continue;
                ?>
                    <a href="<?php echo trim($link); ?>" target="_blank" class="btn-external">
                        <i class="fa-solid fa-link"></i> Ver Recurso Externo
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-bottom: 50px;">
        <a href="podcasts.php" style="color: #666; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Voltar à lista</a>
    </div>
</body>
</html>