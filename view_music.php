<?php
include "db.php";

// 1. Obter o ID da música da URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Procurar na tabela de músicas
$stmt = $conn->prepare("SELECT * FROM musics WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$m = $res->fetch_assoc();

if (!$m) {
    die("<h1 style='color:white; text-align:center; margin-top:100px; font-family:sans-serif;'>Música não encontrada!</h1>");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($m['title']); ?> | João Costa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #1a1612; color: #e8e0d2; font-family: 'EB Garamond', serif; }
        
        .music-wrap { 
            max-width: 850px; 
            margin: 120px auto 40px auto; 
            padding: 40px; 
            background: rgba(35, 30, 25, 0.98); 
            border: 1px solid rgba(212, 178, 106, 0.3); 
            border-radius: 20px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            text-align: center;
        }

        .music-icon { font-size: 3em; color: #d4b26a; margin-bottom: 20px; }
        h1 { color: #d4b26a; font-size: 2.8em; margin-bottom: 10px; }
        
        .player-area { 
            background: #111; 
            padding: 30px; 
            border-radius: 15px; 
            margin: 30px 0; 
            border-left: 4px solid #d4b26a;
            border-right: 4px solid #d4b26a;
        }
        audio { width: 100%; height: 50px; filter: sepia(20%) contrast(90%); }
        
        .desc-text { 
            text-align: left; 
            line-height: 1.8; 
            font-size: 1.15em; 
            margin: 30px 0; 
            color: #ccc;
            white-space: pre-line;
            padding: 0 20px;
        }

        .extra-links { 
            margin-top: 40px; 
            display: flex; 
            gap: 15px; 
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-music { 
            background: rgba(212, 178, 106, 0.1); 
            color: #d4b26a; 
            padding: 15px 30px; 
            border: 1px solid #d4b26a; 
            border-radius: 50px; 
            text-decoration: none; 
            transition: 0.3s;
            font-size: 1em;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-music:hover { background: #d4b26a; color: #000; transform: translateY(-3px); }

        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 100px;
            color: #666;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-link:hover { color: #d4b26a; }
    </style>
</head>
<body>

    <?php include "nav_bar.php"; ?>

    <div class="music-wrap">
        <div class="music-icon"><i class="fa-solid fa-compact-disc fa-spin-hover"></i></div>
        <h1><?php echo htmlspecialchars($m['title']); ?></h1>
        <p style="color: #666; font-style: italic; letter-spacing: 1px;">LANÇAMENTO ORIGINAL</p>
        
        <?php if(!empty($m['audio_url'])): ?>
            <div class="player-area">
                <p style="color:#d4b26a; margin-bottom:15px; font-size:0.8em; letter-spacing:3px; font-weight:bold;">OUVIR AGORA</p>
                <audio controls>
                    <source src="<?php echo $m['audio_url']; ?>" type="audio/mpeg">
                    O teu navegador não suporta este player.
                </audio>
            </div>
        <?php endif; ?>

        <div class="desc-text">
            <?php echo nl2br(htmlspecialchars($m['description'])); ?>
        </div>

        <?php if(!empty($m['video_url'])): ?>
            <div class="extra-links">
                <?php 
                $links = explode(',', $m['video_url']);
                foreach($links as $link): 
                    $link = trim($link);
                    if(empty($link)) continue;
                    
                    // Lógica para mudar ícone se for YouTube
                    $icon = "fa-link";
                    $text = "Ver Link Externo";
                    if(strpos($link, 'youtube.com') !== false || strpos($link, 'youtu.be') !== false) {
                        $icon = "fa-brands fa-youtube";
                        $text = "Assistir no YouTube";
                    }
                ?>
                    <a href="<?php echo $link; ?>" target="_blank" class="btn-music">
                        <i class="<?php echo $icon; ?>"></i> <?php echo $text; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <a href="musicas.php" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Voltar à discografia
    </a>

</body>
</html>