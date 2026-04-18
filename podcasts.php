<?php
/** * 1. LÓGICA E CONFIGURAÇÃO - PODCASTS (CONVERSAS)
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
    <title>Conversas | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <h1 style="text-align:center; color:#d4b26a; letter-spacing:3px; margin-bottom:30px; text-transform:uppercase;">
        Conversas
    </h1>

    <div class="main-content" style="max-width: 800px; margin: 0 auto;">
        
        <div style="margin-bottom: 40px; text-align: center;">
            <input type="text" id="searchInput" placeholder="Procurar episódios ou temas..." 
                   style="width: 100%; max-width: 400px; padding: 12px; border-radius: 25px; border: 1px solid #d4b26a; background: rgba(255,255,255,0.05); color: #fff;">
        </div>

        <div id="podcasts-list">
        <?php
        $result = $conn->query("SELECT * FROM podcasts ORDER BY created_at DESC");
        
        if($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $data = date('d/m/Y', strtotime($row['created_at']));
        ?>
            <div class="podcast-item content-block" 
                 onclick="window.location.href='view_podcast.php?id=<?php echo $row['id']; ?>'" 
                 style="cursor: pointer; display: flex; align-items: center; gap: 20px; transition: 0.3s; margin-bottom: 20px; text-align: left;">
                
                <div style="font-size: 2rem; color: #d4b26a; min-width: 50px; text-align: center;">
                    <i class="fa-solid fa-microphone-lines"></i>
                </div>

                <div style="flex: 1;">
                    <h2 style="color: #d4b26a; margin: 0; font-size: 1.4rem;"><?php echo htmlspecialchars($row['title']); ?></h2>
                    <small style="color: #888; font-style: italic;">Publicado em <?php echo $data; ?></small>
                    <p style="margin-top: 8px; color: #c2b8a6; font-size: 0.95rem; line-height: 1.4;">
                        <?php 
                        $desc = strip_tags($row['description']);
                        echo mb_strimwidth($desc, 0, 130, "..."); 
                        ?>
                    </p>
                </div>
                
                <div style="color: #d4b26a; opacity: 0.4;">
                    <i class="fa-solid fa-play"></i>
                </div>
            </div>
        <?php
            endwhile;
        else:
            echo "<p style='text-align:center; color: #c2b8a6;'>Novas conversas a caminho.</p>";
        endif;
        ?>
        </div>
    </div>
</div>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
    <a href="add_podcast.php" class="btn-auth" 
       style="position: fixed; bottom: 30px; right: 30px; border-radius: 50%; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; z-index: 1000;">
       <i class="fa-solid fa-plus"></i>
    </a>
<?php endif; ?>

<script>
$(document).ready(function(){
    $('#searchInput').on('keyup', function(){
        var value = $(this).val().toLowerCase();
        $(".podcast-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

</body>
</html>