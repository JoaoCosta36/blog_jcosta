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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['title']); ?> | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
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

    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
        <div style="text-align: center; margin-top: 25px;">
            <a href="edit_podcast.php?id=<?php echo $p['id']; ?>" class="btn-auth" style="text-decoration: none; font-size: 0.85rem; padding: 10px 20px;">
                <i class="fa-solid fa-gear"></i> Gerir Episódio
            </a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>