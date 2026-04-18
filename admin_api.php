<?php
/**
 * MASTER ADMIN API - João Costa
 * Versão Final: Suporte a Upload de Ficheiros, Edição Total e Coluna de Conteúdo
 */

include "db.php";

// 1. Proteção de Memória e POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $max_p = ini_get('post_max_size');
    die("Erro Crítico: Ficheiro excede os $max_p permitidos.");
}

/**
 * Ler Token do .env
 */
function getAdminToken() {
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $l) {
            if (stripos(trim($l), 'POST_TOKEN=') === 0) {
                $parts = explode('=', $l, 2);
                return str_replace(['"', "'"], '', trim($parts[1]));
            }
        }
    }
    return 'TOKEN_NAO_CONFIGURADO';
}

$tokenEnviado = trim($_REQUEST['token'] ?? '');
$tokenReal = getAdminToken();
$action = $_REQUEST['action'] ?? '';

if (empty($tokenEnviado) || $tokenEnviado !== $tokenReal) {
    header('HTTP/1.0 403 Forbidden');
    die("Erro: Acesso Negado.");
}

/**
 * AÇÃO: ATUALIZAÇÃO RÁPIDA (Universal)
 */
if ($action == 'update_field') {
    $id     = (int)($_POST['id'] ?? 0);
    $type   = $_POST['type'] ?? '';
    $column = $_POST['column'] ?? '';
    $value  = $_POST['value'] ?? '';

    $map = [
        'post'       => 'posts', 
        'user'       => 'users', 
        'podcast'    => 'podcasts', 
        'suggestion' => 'suggestions', 
        'comment'    => 'comments',
        'music'      => 'musics'
    ];

    if (!isset($map[$type])) die("Erro: Tabela inválida.");
    $table = $map[$type];

    $stmt = $conn->prepare("UPDATE $table SET $column = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $id);
    echo $stmt->execute() ? "OK" : "Erro: " . $conn->error;
    exit;
}

/**
 * LISTAGENS
 */

if ($action == 'list_posts') {
    $res = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    echo "<h2><i class='fa-solid fa-newspaper'></i> Publicações</h2>";
    echo "<table class='modern-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Conteúdo</th>
                    <th>Media</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>";
    while($r = $res->fetch_assoc()) {
        echo "<tr>
                <td>#{$r['id']}</td>
                <td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='post' data-column='title'>{$r['title']}</td>
                <td>
                    <div contenteditable='true' class='edit-field col-content' data-id='{$r['id']}' data-type='post' data-column='content' style='max-width:300px; max-height:60px; overflow:hidden;'>
                        {$r['content']}
                    </div>
                </td>
                <td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='post' data-column='media'>{$r['media']}</td>
                <td>".date('d/m/Y', strtotime($r['created_at']))."</td>
                <td><i class='fa-solid fa-trash delete-icon' onclick='deleteItem({$r['id']}, \"post\")'></i></td>
              </tr>";
    }
    echo "</tbody></table>";
}

if ($action == 'list_podcasts' || $action == 'list_musics') {
    $isMusic = ($action == 'list_musics');
    $table = $isMusic ? 'musics' : 'podcasts';
    $type = $isMusic ? 'music' : 'podcast';
    $icon = $isMusic ? 'fa-music' : 'fa-microphone';
    $title = $isMusic ? 'Músicas' : 'Podcasts';

    $res = $conn->query("SELECT * FROM $table ORDER BY created_at DESC");
    echo "<h2><i class='fa-solid $icon'></i> $title</h2>";
    echo "<table class='modern-table'><thead><tr><th>Título / Áudio</th><th>Descrição</th><th>Link Vídeo</th><th>Ação</th></tr></thead><tbody>";
    while($r = $res->fetch_assoc()) {
        $player = !empty($r['audio_url']) ? "<br><audio controls style='height:30px;'><source src='{$r['audio_url']}' type='audio/mpeg'></audio>" : "";
        echo "<tr>
            <td><b contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='$type' data-column='title'>{$r['title']}</b>$player</td>
            <td><div contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='$type' data-column='description' style='max-width:300px; max-height:80px; overflow:auto;'>{$r['description']}</div></td>
            <td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='$type' data-column='video_url'>{$r['video_url']}</td>
            <td><i class='fa-solid fa-trash delete-icon' onclick='deleteItem({$r['id']}, \"$type\")'></i></td>
        </tr>";
    }
    echo "</tbody></table>";
}

if ($action == 'list_users') {
    $res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    echo "<h2><i class='fa-solid fa-users'></i> Utilizadores</h2>";
    echo "<table class='modern-table'><thead><tr><th>Nome</th><th>Email</th><th>Estado</th><th>Ação</th></tr></thead><tbody>";
    while($r = $res->fetch_assoc()) {
        $st = trim(strtolower($r['status'] ?? 'pendente'));
        echo "<tr><td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='user' data-column='nome'>{$r['nome']}</td><td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='user' data-column='email'>{$r['email']}</td><td>$st</td><td><i class='fa-solid fa-trash delete-icon' onclick='deleteItem({$r['id']}, \"user\")'></i></td></tr>";
    }
    echo "</tbody></table>";
}

if ($action == 'list_suggestions') {
    $res = $conn->query("SELECT s.*, u.nome FROM suggestions s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC");
    echo "<h2><i class='fa-solid fa-lightbulb'></i> Sugestões</h2>";
    echo "<table class='modern-table'><thead><tr><th>Autor</th><th>Sugestão</th><th>Ação</th></tr></thead><tbody>";
    while($r = $res->fetch_assoc()) {
        echo "<tr><td>".($r['nome'] ?? 'Anónimo')."</td><td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='suggestion' data-column='title'>{$r['title']}</td><td><i class='fa-solid fa-trash delete-icon' onclick='deleteItem({$r['id']}, \"suggestion\")'></i></td></tr>";
    }
    echo "</tbody></table>";
}

if ($action == 'list_comments') {
    $res = $conn->query("SELECT c.*, u.nome as user_name, p.title as post_name FROM comments c LEFT JOIN users u ON c.user_id = u.id LEFT JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC");
    echo "<h2><i class='fa-solid fa-comments'></i> Comentários</h2>";
    echo "<table class='modern-table'><thead><tr><th>Autor</th><th>Post</th><th>Conteúdo</th><th>Ação</th></tr></thead><tbody>";
    while($r = $res->fetch_assoc()) {
        echo "<tr><td>".($r['user_name'] ?? 'Anónimo')."</td><td>{$r['post_name']}</td><td contenteditable='true' class='edit-field' data-id='{$r['id']}' data-type='comment' data-column='content'>{$r['content']}</td><td><i class='fa-solid fa-trash delete-icon' onclick='deleteItem({$r['id']}, \"comment\")'></i></td></tr>";
    }
    echo "</tbody></table>";
}

/**
 * AÇÃO: GUARDAR COM UPLOAD
 */
if ($action == 'save_item') {
    $type = $_POST['item_type'];
    $tit  = $_POST['titulo'];
    $cont = $_POST['conteudo'];
    $audio_url = "";

    if (!empty($_FILES['audio_file']['name'])) {
        $target_dir = "uploads/audio/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_ext = pathinfo($_FILES["audio_file"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["audio_file"]["tmp_name"], $target_file)) {
            $audio_url = $target_file;
        }
    }

    if ($type == 'post') {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, media, created_at) VALUES (1, ?, ?, ?, NOW())");
        $stmt->bind_param("sss", $tit, $cont, $_POST['media']);
        echo $stmt->execute() ? "sucesso" : $conn->error;
    } else if ($type == 'podcast' || $type == 'music') {
        $table = ($type == 'music') ? 'musics' : 'podcasts';
        $v_url = $_POST['media'] ?? ''; 
        $stmt = $conn->prepare("INSERT INTO $table (title, description, audio_url, video_url, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $tit, $cont, $audio_url, $v_url);
        echo $stmt->execute() ? "sucesso" : $conn->error;
    }
    exit;
}

if ($action == 'delete_item') {
    $id = (int)$_POST['id'];
    $type = $_POST['type'];
    $map = ['post'=>'posts', 'user'=>'users', 'podcast'=>'podcasts', 'suggestion'=>'suggestions', 'comment'=>'comments', 'music'=>'musics'];
    $table = $map[$type];
    $conn->query("DELETE FROM $table WHERE id = $id");
    echo "OK";
    exit;
}