<?php
/** * 1. LÓGICA E CONFIGURAÇÃO (Sempre no topo absoluto)
 */
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão aqui. Garante que na nav_bar.php NÃO tenhas outro session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php"; // Conexão com a base de dados
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
    <title>João Costa Blog</title>
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

        body {
            font-family: 'EB Garamond', serif;
            background-color: #1a1612;
            color: var(--text-main);
            margin: 0;
            line-height: 1.6;
            padding-top: 80px; /* Espaço para a nav_bar */
        }

        .main-content {
            max-width: 900px;
            margin: 0 auto 40px auto;
            padding: 0 20px;
        }

        h1.page-title {
            color: var(--accent);
            text-align: center;
            font-size: 2.8em;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(212, 178, 106, 0.2);
            padding-bottom: 15px;
        }

        .search-container {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }

        #searchInput {
            width: 100%;
            max-width: 500px;
            padding: 12px 25px;
            border-radius: 25px;
            border: 1px solid var(--accent);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 1.1em;
            outline: none;
            transition: 0.3s;
        }

        #searchInput:focus {
            box-shadow: 0 0 15px rgba(212, 178, 106, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        #posts {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .post-resumo {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid rgba(212, 178, 106, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .post-resumo:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .post-resumo h2 {
            margin: 0 0 8px 0;
            color: var(--accent);
            font-size: 1.8em;
        }

        .post-resumo small {
            display: block;
            color: #888;
            margin-bottom: 12px;
            font-size: 0.9em;
        }

        .post-resumo p {
            color: var(--text-dim);
            font-size: 1.05em;
            margin: 0;
        }

        .admin-add-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--accent);
            color: #2b241a;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            font-size: 28px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.6);
            z-index: 1000;
            transition: 0.3s;
        }

        .admin-add-btn:hover {
            transform: rotate(90deg) scale(1.1);
            background: #fff;
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="main-content">
    <h1 class="page-title">Publicações</h1>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Pesquisar publicações...">
    </div>

    <div id="posts">
    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    if($result && $result->num_rows > 0):
        while($post = $result->fetch_assoc()):
            $dataFormatted = date('d/m/Y', strtotime($post['created_at']));
    ?>
        <div class="post-resumo" onclick="window.location.href='post.php?id=<?php echo $post['id']; ?>'">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <small>Publicado em <?php echo $dataFormatted; ?></small>
            <p><?php 
                $cleanContent = strip_tags($post['content']);
                echo strlen($cleanContent) > 160 ? substr(htmlspecialchars($cleanContent), 0, 160) . '...' : htmlspecialchars($cleanContent); 
            ?></p>
        </div>
    <?php
        endwhile;
    else:
        echo "<p style='text-align:center; color: var(--text-dim);'>Ainda não existem posts disponíveis.</p>";
    endif;
    ?>
    </div>
</div>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
    <a href="create_post.php" class="admin-add-btn" title="Novo Post">+</a>
<?php endif; ?>

<script>
$(document).ready(function(){
    $('#searchInput').on('input', function(){
        var query = $(this).val();
        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(data){
                var html = '';
                if(data.length > 0){
                    data.forEach(function(post){
                        html += '<div class="post-resumo" onclick="window.location.href=\'post.php?id='+post.id+'\'">';
                        html += '<h2>'+post.title+'</h2>';
                        html += '<p>'+post.content+'</p>';
                        html += '</div>';
                    });
                } else {
                    html = '<p style="text-align:center; color: #888;">Nenhum resultado encontrado.</p>';
                }
                $('#posts').html(html);
            }
        });
    });
});
</script>

</body>
</html>