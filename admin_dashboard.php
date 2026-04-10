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
    die("<h1 style='color:red; text-align:center; margin-top:100px; font-family:sans-serif;'>🚫 Acesso Negado: Token Inválido</h1>");
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
    <style>
        :root { --gold: #d4b26a; --bg: #1a1612; --card: rgba(35, 30, 25, 0.98); }
        body { padding-top: 90px; background: var(--bg); font-family: 'EB Garamond', serif; color: #e8e0d2; }
        
        .admin-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; max-width: 1400px; margin: 0 auto; padding: 0 20px; }
        
        .sidebar-nav { background: var(--card); border: 1px solid rgba(212,178,106,0.2); border-radius: 15px; padding: 20px; position: sticky; top: 100px; height: fit-content; }
        .sidebar-nav button { width: 100%; padding: 15px; margin-bottom: 10px; border: none; background: none; color: #ccc; text-align: left; cursor: pointer; border-radius: 8px; transition: 0.3s; font-size: 1.1em; }
        .sidebar-nav button i { margin-right: 12px; width: 20px; color: var(--gold); }
        .sidebar-nav button:hover, .sidebar-nav button.active { background: rgba(212,178,106,0.1); color: var(--gold); }
        
        .main-panel { background: var(--card); border: 1px solid rgba(212,178,106,0.2); border-radius: 15px; padding: 30px; min-height: 75vh; }
        
        /* Tabelas Modernas */
        .modern-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .modern-table th { color: var(--gold); text-align: left; padding: 15px; border-bottom: 2px solid #333; background: rgba(0,0,0,0.2); }
        .modern-table td { padding: 15px; border-bottom: 1px solid #222; vertical-align: middle; }
        
        /* Edição e Ações */
        .edit-field { cursor: text; padding: 5px; border-radius: 4px; transition: 0.3s; min-width: 30px; display: inline-block; }
        .edit-field:hover { background: rgba(212, 178, 106, 0.15); outline: 1px dashed var(--gold); }
        .edit-field:focus { background: #000; outline: 2px solid var(--gold); color: #fff; }
        
        .action-icon { cursor: pointer; font-size: 1.2em; transition: 0.2s; padding: 8px; }
        .delete-icon { color: #ff6b6b; }
        .delete-icon:hover { color: #ff0000; transform: scale(1.2); }

        /* Player de Áudio na Tabela */
        audio { height: 30px; width: 200px; filter: sepia(100%) transition: 0.3s; opacity: 0.7; }
        audio:hover { opacity: 1; }

        /* Modal */
        #modalOverlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9); z-index:9999; overflow-y:auto; padding: 40px 0; align-items: center; justify-content: center; }
        .modal-content { background: #25201b; max-width: 700px; width: 95%; padding: 30px; border-radius: 15px; border: 1px solid var(--gold); margin: auto; }
        
        input, textarea, select { width: 100%; padding: 12px; margin: 10px 0; background: #111; border: 1px solid #444; color: #fff; border-radius: 6px; box-sizing: border-box; }
        
        .btn-plus { background: var(--gold); border: none; padding: 10px; border-radius: 5px; cursor: pointer; color: #000; }
        .btn-remove { background: #ff4d4d; border: none; padding: 10px; border-radius: 5px; color: white; cursor: pointer; }
        .dynamic-link { display: flex; gap: 10px; align-items: center; margin-bottom: 5px; }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="admin-layout">
    <aside class="sidebar-nav">
        <h3 style="color:var(--gold); margin-bottom:20px; text-align:center;"><i class="fa-solid fa-shield-halved"></i> ADMIN</h3>
        <button class="nav-link active" data-tab="list_posts"><i class="fa-solid fa-pen-nib"></i> Posts</button>
        <button class="nav-link" data-tab="list_podcasts"><i class="fa-solid fa-microphone"></i> Podcasts</button>
        <button class="nav-link" data-tab="list_suggestions"><i class="fa-solid fa-lightbulb"></i> Sugestões</button>
        <button class="nav-link" data-tab="list_users"><i class="fa-solid fa-users"></i> Utilizadores</button>
        <button class="nav-link" data-tab="list_comments"><i class="fa-solid fa-comments"></i> Comentários</button>
        <button onclick="openModal()" style="margin-top:20px; background:var(--gold); color:black; font-weight:bold;"><i class="fa-solid fa-plus"></i> CRIAR NOVO</button>
    </aside>

    <section class="main-panel" id="ajaxContent">
        <div style="text-align:center; padding:50px;"><i class="fa-solid fa-cog fa-spin fa-3x"></i></div>
    </section>
</div>

<div id="modalOverlay">
    <div class="modal-content">
        <h2 id="modalTitle" style="color:var(--gold);"><i class="fa-solid fa-plus-circle"></i> Novo Conteúdo</h2>
        <form id="adminForm" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?php echo $tokenUrl; ?>">
            <input type="hidden" name="action" value="save_item">
            
            <label>Tipo</label>
            <select name="item_type" id="itemTypeSelector" onchange="toggleFields(this.value)">
                <option value="post">Publicação (Blog)</option>
                <option value="podcast">Podcast (Áudio)</option>
            </select>

            <label>Título</label>
            <input type="text" name="titulo" required placeholder="Título...">

            <label>Descrição / Conteúdo</label>
            <textarea name="conteudo" style="height:120px;" required placeholder="Texto..."></textarea>

            <div id="postFields">
                <label>Link de Mídia (YouTube/Imagem)</label>
                <input type="text" name="media" placeholder="URL...">
            </div>

            <div id="podcastFields" style="display:none;">
                <label>Ficheiro Áudio (MP3)</label>
                <input type="file" name="audio_file" accept="audio/*">
                
                <label style="margin-top:15px; display:block;">Links Externos</label>
                <div id="linksContainer">
                    <div class="dynamic-link">
                        <input type="text" name="external_links[]" placeholder="URL da plataforma...">
                        <button type="button" class="btn-plus" onclick="addLinkField()"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <div style="margin-top:30px; display:flex; gap:10px;">
                <button type="submit" style="flex:1; background:var(--gold); color:#000; border:none; padding:15px; border-radius:8px; cursor:pointer; font-weight:bold;">GUARDAR</button>
                <button type="button" onclick="$('#modalOverlay').fadeOut()" style="flex:1; background:#444; color:#fff; border:none; border-radius:8px; cursor:pointer;">CANCELAR</button>
            </div>
        </form>
    </div>
</div>

<script>
const dashboardToken = "<?php echo $tokenUrl; ?>";

// Carregar conteúdo das abas
function loadTab(tabName) {
    $("#ajaxContent").html('<div style="text-align:center; padding:50px;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>');
    $.get("admin_api.php", { action: tabName, token: dashboardToken }, function(data) {
        $("#ajaxContent").html(data);
    }).fail(function(err) {
        alert("Erro ao carregar: " + err.responseText);
    });
}

function toggleFields(type) {
    if(type === 'podcast') { $("#podcastFields").show(); $("#postFields").hide(); }
    else { $("#podcastFields").hide(); $("#postFields").show(); }
}

function addLinkField() {
    $("#linksContainer").append('<div class="dynamic-link"><input type="text" name="external_links[]" placeholder="Link..."><button type="button" class="btn-remove" onclick="$(this).parent().remove()"><i class="fa-solid fa-trash"></i></button></div>');
}

function openModal() {
    $("#adminForm")[0].reset();
    $("#modalOverlay").css('display','flex').hide().fadeIn();
    toggleFields('post');
}

$(document).ready(function() {
    // Inicialização
    loadTab('list_posts');

    // Cliques na Sidebar
    $(".nav-link").click(function() {
        $(".nav-link").removeClass('active');
        $(this).addClass('active');
        loadTab($(this).data('tab'));
    });

    // Submissão do Formulário (Criar Novo)
    $("#adminForm").on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.set('token', dashboardToken);

        $.ajax({
            url: 'admin_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.trim().toLowerCase().includes("sucesso")) {
                    alert("Gravado com Sucesso!");
                    $("#modalOverlay").fadeOut();
                    loadTab($(".nav-link.active").data('tab'));
                } else {
                    alert("Resposta: " + res);
                }
            }
        });
    });

    // Edição Direta na Tabela (Blur)
    $(document).on('blur', '.edit-field', function() {
        let cell = $(this);
        let data = {
            action: 'update_field',
            id: cell.data('id'),
            type: cell.data('type'),
            column: cell.data('column'),
            value: cell.text().trim(),
            token: dashboardToken
        };

        $.post("admin_api.php", data, function(res) {
            if(res.trim() === "OK") {
                cell.css('background', 'rgba(144, 238, 144, 0.2)');
                setTimeout(() => cell.css('background', 'transparent'), 500);
            } else {
                alert("Erro ao atualizar: " + res);
            }
        });
    });
});

// Eliminar Item
function deleteItem(id, type) {
    if(confirm("Tens a certeza que desejas eliminar este item permanentemente?")) {
        $.post("admin_api.php", { 
            action: 'delete_item', 
            id: id, 
            type: type, 
            token: dashboardToken 
        }, function(res) {
            if(res.trim() === "OK") {
                loadTab($(".nav-link.active").data('tab'));
            } else {
                alert("Erro ao eliminar: " + res);
            }
        });
    }
}
</script>
</body>
</html>