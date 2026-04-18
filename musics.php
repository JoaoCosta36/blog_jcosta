<?php
/** * 1. LÓGICA E CONFIGURAÇÃO - MUSICS
 */
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; 
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include 'adsense.php'; ?>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VGYVZ37XK1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-VGYVZ37XK1');
    </script>
    
    <meta charset="UTF-8">
   
    <title>Músicas | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <h1 style="text-align:center; color:#d4b26a; letter-spacing:3px; margin-bottom:30px; text-transform:uppercase;">
        Músicas
    </h1>

    <div class="main-content" style="max-width: 800px; margin: 0 auto;">
        
        <div style="margin-bottom: 40px; text-align: center;">
            <input type="text" id="searchInput" placeholder="Filtrar por título ou descrição..." 
                   style="width: 100%; max-width: 400px; padding: 12px; border-radius: 25px; border: 1px solid #d4b26a; background: rgba(255,255,255,0.05); color: #fff;">
        </div>

        <div id="musics-list">
        <?php
        // Consulta à base de dados
        $result = $conn->query("SELECT * FROM musics ORDER BY created_at DESC");
        
        if($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $data = date('d/m/Y', strtotime($row['created_at']));
        ?>
            <div class="music-item content-block" 
                 onclick="window.location.href='view_music.php?id=<?php echo $row['id']; ?>'" 
                 style="cursor: pointer; display: flex; align-items: center; gap: 20px; transition: 0.3s; margin-bottom: 20px;">
                
                <div style="font-size: 2rem; color: #d4b26a;">
                    <i class="fa-solid fa-compact-disc fa-spin-hover"></i>
                </div>

                <div style="flex: 1;">
                    <h2 style="color: #d4b26a; margin: 0; font-size: 1.4rem;"><?php echo htmlspecialchars($row['title']); ?></h2>
                    <small style="color: #888;">Publicado em <?php echo $data; ?></small>
                    <p style="margin-top: 8px; color: #c2b8a6; font-size: 0.95rem;">
                        <?php 
                        $desc = strip_tags($row['description']);
                        echo mb_strimwidth($desc, 0, 100, "..."); 
                        ?>
                    </p>
                </div>
                
                <div style="color: #d4b26a; opacity: 0.5;">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        <?php
            endwhile;
        else:
            echo "<p style='text-align:center; color: #c2b8a6;'>Novas composições em breve.</p>";
        endif;
        ?>
        </div>
    </div>
</div>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
    <a href="admin_dashboard.php" class="admin-add-btn" title="Painel Admin" 
       style="position: fixed; bottom: 30px; right: 30px; background: #d4b26a; color: #1a1612; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.5); text-decoration: none;">+</a>
<?php endif; ?>

<script>
$(document).ready(function(){
    // Lógica de pesquisa em tempo real
    $('#searchInput').on('keyup', function(){
        var value = $(this).val().toLowerCase();
        $(".music-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<?php include 'footer.php'; ?></body>
</html>