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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($m['title']); ?> | joaocostArt</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper" style="max-width: 800px;">
    
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="musics.php" style="text-decoration: none; color: #888; font-size: 0.9rem; transition: 0.3s;" onmouseover="this.style.color='#d4b26a'" onmouseout="this.style.color='#888'">
            <i class="fa-solid fa-arrow-left"></i> Voltar à Galeria Musical
        </a>
    </div>

    <div class="content-block" style="text-align: center; padding: 50px 30px;">
        
        <div style="margin-bottom: 40px;">
            <div style="width: 150px; height: 150px; background: rgba(212, 178, 106, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 2px solid #d4b26a; box-shadow: 0 0 30px rgba(212, 178, 106, 0.2);">
                <i class="fa-solid fa-compact-disc fa-spin" style="font-size: 5rem; color: #d4b26a; --fa-animation-duration: 4s;"></i>
            </div>
        </div>

        <h1 style="color: #d4b26a; font-size: 2.2rem; margin-bottom: 10px; letter-spacing: 1px;">
            <?php echo htmlspecialchars($m['title']); ?>
        </h1>
        
        <p style="color: #888; font-style: italic; margin-bottom: 30px;">Obra Musical de João Costa</p>

        <?php if(!empty($m['audio_url'])): ?>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 50px; margin-bottom: 40px; border: 1px solid rgba(212,178,106,0.2);">
                <audio controls style="width:100%; filter: sepia(20%) saturate(70%) grayscale(100%) contrast(150%) invert(100%);">
                    <source src="<?php echo $m['audio_url']; ?>" type="audio/mpeg">
                    O teu navegador não suporta este elemento de áudio.
                </audio>
            </div>
        <?php endif; ?>

        <div style="text-align: justify; line-height: 1.8; color: #f2e8d5; font-size: 1.1rem; border-top: 1px solid rgba(212,178,106,0.1); padding-top: 30px;">
            <i class="fa-solid fa-quote-left" style="color: #d4b26a; opacity: 0.3; font-size: 1.5rem;"></i>
            <p style="padding: 0 20px; display: inline;">
                <?php echo nl2br(htmlspecialchars($m['description'])); ?>
            </p>
        </div>

    </div>

    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="edit_music.php?id=<?php echo $m['id']; ?>" class="btn-auth" style="text-decoration: none; font-size: 0.9rem;">
                <i class="fa-solid fa-pen-to-square"></i> Editar Música
            </a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>