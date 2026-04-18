<?php
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['title']); ?> | João Costa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <h1><?php echo htmlspecialchars($p['title']); ?></h1>
        
        <div class="content-block">
            <?php if(!empty($p['audio_url'])): ?>
                <div style="margin-bottom: 20px; text-align: center;">
                    <audio controls style="width: 100%;">
                        <source src="<?php echo $p['audio_url']; ?>" type="audio/mpeg">
                    </audio>
                </div>
            <?php endif; ?>

            <p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
        </div>

        <a href="podcasts.php" style="color: #d4b26a; display: block; margin-top: 20px;">← Voltar às conversas</a>
    </div>
</body>
</html>