<?php
/** * 1. LÓGICA E CONFIGURAÇÃO - MUSICS
 */
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VGYVZ37XK1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-VGYVZ37XK1');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Músicas - João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        :root {
            --accent: #d4b26a;
            --bg-card: rgba(45, 35, 25, 0.85);
            --text-main: #f4f4f4;
            --text-dim: #c2b8a6;
        }

        .main-content {
            max-width: 900px;
            margin: 0 auto 40px auto;
            padding: 20px;
        }

        .music-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .music-item {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid rgba(212, 178, 106, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .music-item:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .music-icon {
            font-size: 2.5em;
            color: var(--accent);
        }

        .music-info h2 {
            margin: 0 0 5px 0;
            color: var(--accent);
            font-size: 1.6em;
        }

        .music-info small {
            color: var(--text-dim);
            font-style: italic;
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="main-content">
    <h1 class="page-title">Músicas</h1>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Pesquisar músicas...">
    </div>

    <div id="musics" class="music-grid">
    <?php
    // Aqui assumimos que a tabela se chama 'musics'. Se for outro nome, basta alterar abaixo.
    $result = $conn->query("SELECT * FROM musics ORDER BY created_at DESC");
    
    if($result && $result->num_rows > 0):
        while($row = $result->fetch_assoc()):
            $data = date('d/m/Y', strtotime($row['created_at']));
    ?>
        <div class="music-item" onclick="window.location.href='view_music.php?id=<?php echo $row['id']; ?>'">
            <div class="music-icon">🎵</div>
            <div class="music-info">
                <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                <small>Lançada em <?php echo $data; ?></small>
                <p><?php 
                    $desc = strip_tags($row['description']);
                    echo strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc; 
                ?></p>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p style='text-align:center; color: var(--text-dim);'>Brevemente novas músicas.</p>";
    endif;
    ?>
    </div>
</div>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
    <a href="add_music.php" class="admin-add-btn" title="Adicionar Música">+</a>
<?php endif; ?>

<script>
$(document).ready(function(){
    $('#searchInput').on('input', function(){
        var query = $(this).val().toLowerCase();
        $(".music-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1)
        });
    });
});
</script>

</body>
</html>