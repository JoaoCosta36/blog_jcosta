<?php
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($m['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <h1><?php echo htmlspecialchars($m['title']); ?></h1>
    
    <div class="content-block">
        <?php if(!empty($m['audio_url'])): ?>
            <div style="margin-bottom: 25px; text-align:center;">
                <audio controls style="width:100%;">
                    <source src="<?php echo $m['audio_url']; ?>" type="audio/mpeg">
                </audio>
            </div>
        <?php endif; ?>

        <p><?php echo nl2br(htmlspecialchars($m['description'])); ?></p>
    </div>

    <a href="musics.php" style="display:inline-block; margin-top:30px; color:#d4b26a;">← Voltar às músicas</a>
</div>

</body>
</html>