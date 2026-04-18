<?php
include "db.php";
session_start();

function loadEnvLocal($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if(strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim(str_replace(['"', "'"], '', $parts[1]));
            $_ENV[$key] = $val;
        }
    }
}

$envPath = __DIR__ . '/.env';
if(!file_exists($envPath)) $envPath = __DIR__ . '/../.env';
loadEnvLocal($envPath);

$tokenUrl = trim($_GET['token'] ?? '');
$tokenEnv = trim($_ENV['POST_TOKEN'] ?? '');

if ($tokenUrl === '' || $tokenUrl !== $tokenEnv) {
    die("<h1 style='color:#ff6b6b; text-align:center; margin-top:100px; font-family:serif;'>🚫 Acesso Negado</h1>");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Dashboard | João Costa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="admin-layout">
    <aside class="sidebar-nav">
        <h3 style="color:#d4b26a; text-align:center; margin-bottom: 20px;">ADMIN</h3>
        <button class="nav-link active" data-tab="list_posts"><i class="fa-solid fa-pen-nib"></i> Posts</button>
        <button class="nav-link" data-tab="list_musics"><i class="fa-solid fa-music"></i> Músicas</button>
        <button class="nav-link" data-tab="list_podcasts"><i class="fa-solid fa-microphone"></i> Podcasts</button>
        <button class="nav-link" data-tab="list_suggestions"><i class="fa-solid fa-lightbulb"></i> Sugestões</button>
        <button class="nav-link" data-tab="list_users"><i class="fa-solid fa-users"></i> Users</button>
        <button class="nav-link" data-tab="list_comments"><i class="fa-solid fa-comments"></i> Comentários</button>
        
        <button onclick="openModal()" style="background:#d4b26a; color:#1a1612; font-weight:bold; margin-top:20px; width:100%;">
            <i class="fa-solid fa-plus"></i> CRIAR NOVO
        </button>
    </aside>

    <section class="main-panel" id="ajaxContent">
        <p>A carregar conteúdo...</p>
    </section>
</div>

<div id="modalOverlay">
    <div class="modal-content">
        <h2 style="color:#d4b26a; margin-bottom: 20px;">Adicionar Conteúdo</h2>
        <form id="adminForm" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenUrl); ?>">
            <input type="hidden" name="action" value="save_item">
            
            <label>Tipo de Conteúdo</label>
            <select name="item_type" id="itemTypeSelector" onchange="toggleFields(this.value)">
                <option value="post">Publicação (Blog)</option>
                <option value="music">Música</option>
                <option value="podcast">Podcast</option>
            </select>

            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="conteudo" placeholder="Descrição / Conteúdo" required style="height:120px;"></textarea>

            <div id="postFields">
                <input type="text" name="media" placeholder="URL da Imagem ou YouTube">
            </div>

            <div id="mediaFields" style="display:none;">
                <label>Ficheiro de Áudio (MP3)</label>
                <input type="file" name="audio_file" accept="audio/*">
                <input type="text" name="media" placeholder="Link do Vídeo (Opcional)">
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" style="flex:2;">GUARDAR</button>
                <button type="button" onclick="$('#modalOverlay').hide()" style="flex:1; background:#444; color:white;">FECHAR</button>
            </div>
        </form>
    </div>
</div>

<script>
const dashboardToken = "<?php echo $tokenUrl; ?>";

function loadTab(tab) {
    $("#ajaxContent").html("<p style='color:#d4b26a;'>A carregar...</p>");
    $.get("admin_api.php", { action: tab, token: dashboardToken }, function(data) {
        $("#ajaxContent").html(data);
    });
}

function toggleFields(val) {
    if(val === 'post') { $("#postFields").show(); $("#mediaFields").hide(); }
    else { $("#postFields").hide(); $("#mediaFields").show(); }
}

function openModal() {
    $("#adminForm")[0].reset();
    toggleFields('post');
    $("#modalOverlay").css('display','flex');
}

$(document).ready(function() {
    loadTab('list_posts');

    $(".nav-link").click(function() {
        $(".nav-link").removeClass('active');
        $(this).addClass('active');
        loadTab($(this).data('tab'));
    });

    $("#adminForm").on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: 'admin_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.trim().includes("sucesso")) {
                    alert("Gravado com sucesso!");
                    $("#modalOverlay").hide();
                    loadTab($(".nav-link.active").data('tab'));
                } else { alert("Erro: " + res); }
            }
        });
    });

    $(document).on('blur', '.edit-field', function() {
        let el = $(this);
        $.post("admin_api.php", {
            action: 'update_field',
            id: el.data('id'),
            type: el.data('type'),
            column: el.data('column'),
            value: el.text().trim(),
            token: dashboardToken
        }, function(res) {
            if(res.trim() === "OK") {
                el.css('background', 'rgba(212, 178, 106, 0.2)');
                setTimeout(() => el.css('background', 'transparent'), 400);
            }
        });
    });
});

function deleteItem(id, type) {
    if(confirm("Tem a certeza que deseja apagar este item?")) {
        $.post("admin_api.php", { action: 'delete_item', id: id, type: type, token: dashboardToken }, function() {
            loadTab($(".nav-link.active").data('tab'));
        });
    }
}
</script>
</body>
</html>