<?php
include "db.php";
session_start();

// Proteção básica por Token (como já usas)
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) $_ENV[trim($parts[0])] = trim($parts[1]);
    }
}
loadEnv(__DIR__ . '/.env');
define('POST_TOKEN', $_ENV['POST_TOKEN'] ?? 'erro');

if (!isset($_GET['token']) || $_GET['token'] !== POST_TOKEN) {
    die("Acesso negado.");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Controlo - João Costa</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --gold: #d4b26a;
            --dark-card: rgba(30, 25, 20, 0.95);
        }
        body { padding-top: 80px; background: #1a1612; }
        
        .dashboard-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            gap: 20px;
        }

        /* Sidebar de Navegação */
        .sidebar {
            width: 250px;
            background: var(--dark-card);
            border: 1px solid rgba(212,178,106,0.3);
            border-radius: 12px;
            padding: 20px;
            height: fit-content;
        }

        .sidebar button {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            background: transparent;
            color: #eee;
            border: 1px solid transparent;
            text-align: left;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s;
        }

        .sidebar button.active {
            border-color: var(--gold);
            color: var(--gold);
            background: rgba(212,178,106,0.1);
        }

        /* Área de Conteúdo */
        .content-area {
            flex: 1;
            background: var(--dark-card);
            border-radius: 12px;
            padding: 30px;
            border: 1px solid rgba(212,178,106,0.3);
            min-height: 600px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th { color: var(--gold); text-align: left; padding: 10px; border-bottom: 2px solid #333; }
        .data-table td { padding: 12px; border-bottom: 1px solid #333; font-size: 0.9em; }

        .btn-delete { color: #ff6b6b; cursor: pointer; background: none; border: none; }
        .status-pill { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; background: #222; }
        
        /* Modal Simples */
        #actionModal {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:10001;
            align-items:center; justify-content:center;
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="dashboard-container">
    <aside class="sidebar">
        <h2 style="color:var(--gold); font-size: 1.2em;">Gestão</h2>
        <button class="tab-btn active" data-target="list_posts">📝 Posts</button>
        <button class="tab-btn" data-target="list_suggestions">💡 Sugestões</button>
        <button class="tab-btn" data-target="list_podcasts">🎙️ Podcasts</button>
        <button class="tab-btn" data-target="list_users">👥 Utilizadores</button>
        <hr style="border:0; border-top:1px solid #333; margin: 20px 0;">
        <button id="openCreatePost" style="background:var(--gold); color:#000; font-weight:bold;">+ Novo Item</button>
    </aside>

    <main class="content-area" id="mainDisplay">
        <div id="loader">A carregar dados...</div>
    </main>
</div>

<div id="actionModal">
    <div class="post-container" style="width:90%; max-width:600px; position:relative;">
        <button onclick="$('#actionModal').fadeOut()" style="position:absolute; right:15px; top:15px; background:none; border:none; color:white; font-size:20px; cursor:pointer;">&times;</button>
        <h2 id="modalTitle" style="color:var(--gold);">Inserir Novo</h2>
        <form id="mainForm" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="create_post">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="texto" placeholder="Conteúdo..." required></textarea>
            <input type="text" name="extra_field" id="extraField" placeholder="Media Link / URL">
            <select name="type" id="itemType" style="margin: 10px 0;">
                <option value="post">Post Normal</option>
                <option value="podcast">Podcast</option>
            </select>
            <button type="submit" id="submitBtn">Guardar Alterações</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const token = "<?php echo POST_TOKEN; ?>";

    // Função para carregar as listas
    function loadList(target) {
        $('#mainDisplay').html('<p>A sincronizar...</p>');
        $.ajax({
            url: 'admin_api.php',
            type: 'GET',
            data: { action: target, token: token },
            success: function(response) {
                $('#mainDisplay').html(response);
            }
        });
    }

    // Inicial
    loadList('list_posts');

    // Troca de Tabs
    $('.tab-btn').click(function() {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        loadList($(this).data('target'));
    });

    // Abrir Modal
    $('#openCreatePost').click(function() {
        $('#mainForm')[0].reset();
        $('#modalTitle').text('Criar Novo Post/Podcast');
        $('#actionModal').css('display','flex').hide().fadeIn();
    });

    // Submeter Formulário via AJAX (Tempo Real)
    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('token', token);

        $.ajax({
            url: 'admin_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                alert('Operação concluída!');
                $('#actionModal').fadeOut();
                loadList($('.tab-btn.active').data('target'));
            }
        });
    });

    // Eliminar Itens (Evento Delegado)
    $(document).on('click', '.btn-delete', function() {
        if(confirm('Tem a certeza?')){
            let id = $(this).data('id');
            let type = $(this).data('type');
            $.post('admin_api.php', { action: 'delete', id: id, type: type, token: token }, function() {
                loadList($('.tab-btn.active').data('target'));
            });
        }
    });
});
</script>
</body>
</html>