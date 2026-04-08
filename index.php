<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "db.php"; // conexão $conn
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
        }

        .main-content {
            max-width: 900px;
            margin: 100px auto 40px auto;
            padding: 0 20px;
        }

        h1.page-title {
            color: var(--accent);
            text-align: center;
            font-size: 3em;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 1px solid rgba(212, 178, 106, 0.2);
            padding-bottom: 20px;
        }

        /* Barra de Pesquisa Moderna */
        .search-container {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }

        #searchInput {
            width: 100%;
            max-width: 500px;
            padding: 15px 25px;
            border-radius: 30px;
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

        /* Grid de Posts */
        #posts {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .post-resumo {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(212, 178, 106, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .post-resumo:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }

        .post-resumo h2 {
            margin: 0 0 10px 0;
            color: var(--accent);
            font-size: 1.8em;
        }

        .post-resumo small {
            display: block;
            color: #888;
            margin-bottom: 15px;
            font-style: italic;
        }

        .post-resumo p {
            color: var(--text-dim);
            font-size: 1.1em;
            margin: 0;
        }

        /* Botão Flutuante para Admin */
        .admin-add-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--accent);
            color: #2b241a;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            font-size: 30px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            z-index: 1000;
            transition: 0.3s;
        }

        .admin-add-btn:hover {
            transform: scale(1.1);
            background: #fff;
        }

        @media (max-width: 600px) {
            h1.page-title { font-size: 2em; }
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="main-content">
    <h1 class="page-title">Cronologia de Ideias</h1>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="O que procuras hoje?">
    </div>

    <div id="posts">
    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    if($result && $result->num_rows > 0):
        while($post = $result->fetch_assoc()):
            $data = date('d M, Y', strtotime($post['created_at']));
    ?>
        <div class="post-resumo" onclick="window.location.href='post.php?id=<?php echo $post['id']; ?>'">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <small>Publicado a <?php echo $data; ?></small>
            <p><?php echo strlen($post['content'])>160 ? substr(htmlspecialchars($post['content']),0,160).'...' : htmlspecialchars($post['content']); ?></p>
        </div>
    <?php
        endwhile;
    else:
        echo "<p style='text-align:center;'>Ainda não existem publicações.</p>";
    endif;
    ?>
    </div>
</div>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
    <a href="create_post.php" class="admin-add-btn" title="Novo Post">+</a>
<?php endif; ?>

<script>
$(document).ready(function(){
    // Função para renderizar os posts (usada no AJAX)
    function renderPosts(data) {
        var html = '';
        if(data.length > 0){
            data.forEach(function(post){
                html += '<div class="post-resumo" onclick="window.location.href=\'post.php?id='+post.id+'\'">';
                html += '<h2>'+post.title+'</h2>';
                html += '<p>'+post.content+'</p>';
                html += '</div>';
            });
        } else {
            html = '<p style="text-align:center;">Sem resultados para a tua pesquisa.</p>';
        }
        $('#posts').hide().html(html).fadeIn(300);
    }

    $('#searchInput').on('input', function(){
        var query = $(this).val();
        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(data){
                renderPosts(data);
            }
        });
    });
});
</script>

</body>
</html>