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
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-VGYVZ37XK1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-VGYVZ37XK1');
</script>
<link rel="icon" href="icon.jpg" type="image/png">
<meta charset="UTF-8">
<link rel="icon" href="icon.jpg" type="image/png">
<meta charset="UTF-8">
<title>João Costa Blog</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="padding-top:60px;">

<?php include "nav_bar.php"; ?>

<h1>Posts</h1>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
<div class="formulario">
    <h2>Novo Post</h2>
    <form method="post" enctype="multipart/form-data" action="create_post.php">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="texto" placeholder="Escreva sua poesia, reflexão ou texto..." required></textarea>
        <input type="text" name="media_link" placeholder="Link de mídia (opcional)">
        <p style="color:#bbb;font-style:italic;">Ou carregue do computador:</p>
        <input type="file" name="fileInput" accept="image/*,video/*,audio/*,.gif">
        <button type="submit" name="criar">Publicar</button>
    </form>
</div>
<?php endif; ?>

<!-- Barra de pesquisa -->
<div class="search-container">
    <input type="text" id="searchInput" placeholder="Pesquisar posts...">
    <div id="searchResults"></div>
</div>

<!-- Lista de posts -->
<div id="posts">
<?php
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
if($result && $result->num_rows > 0):
    while($post = $result->fetch_assoc()):
?>
    <div class="post-resumo" onclick="window.location.href='post.php?id=<?php echo $post['id']; ?>'">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        <small><?php echo $post['created_at']; ?></small>
        <p><?php echo strlen($post['content'])>100 ? substr(htmlspecialchars($post['content']),0,100).'...' : htmlspecialchars($post['content']); ?></p>
    </div>
<?php
    endwhile;
else:
    echo "<p>Nenhum post disponível.</p>";
endif;
?>
</div>

<!-- Script AJAX para busca dinâmica -->
<script>
$(document).ready(function(){
    $('#searchInput').on('input', function(){
        var query = $(this).val();
        if(query.length > 0){
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
                        html = '<p>Nenhum resultado encontrado.</p>';
                    }
                    $('#posts').html(html);
                }
            });
        } else {
            // Se campo de busca vazio, recarrega todos os posts
            $.ajax({
                url: 'search.php',
                type: 'GET',
                data: { q: '' },
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
                        html = '<p>Nenhum post disponível.</p>';
                    }
                    $('#posts').html(html);
                }
            });
        }
    });
});
</script>

</body>
</html>