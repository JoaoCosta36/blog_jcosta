<?php
include "db.php";

$token = $_REQUEST['token'] ?? '';
// Validação de segurança básica (deve coincidir com o .env)
loadEnv(__DIR__ . '/.env');
if ($token !== ($_ENV['POST_TOKEN'] ?? '')) {
    die("Acesso Proibido");
}

$action = $_REQUEST['action'] ?? '';

// --- LISTAGENS ---

if ($action == 'list_posts') {
    $res = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    echo "<h2>Gerir Publicações</h2><table class='data-table'><tr><th>Título</th><th>Data</th><th>Ações</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['title']}</td><td>{$row['created_at']}</td>
              <td><button class='btn-delete' data-id='{$row['id']}' data-type='post'>🗑️</button></td></tr>";
    }
    echo "</table>";
}

if ($action == 'list_suggestions') {
    $res = $conn->query("SELECT s.*, u.nome FROM suggestions s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC");
    echo "<h2>Sugestões dos Leitores</h2><table class='data-table'><tr><th>Utilizador</th><th>Sugestão</th><th>Data</th><th>Ação</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr><td><b>" . ($row['nome'] ?? 'Anónimo') . "</b></td>
              <td><i>{$row['title']}</i>: {$row['content']}</td>
              <td>{$row['created_at']}</td>
              <td><button class='btn-delete' data-id='{$row['id']}' data-type='suggestion'>Apagar</button></td></tr>";
    }
    echo "</table>";
}

if ($action == 'list_podcasts') {
    $res = $conn->query("SELECT * FROM podcasts ORDER BY created_at DESC");
    echo "<h2>Gerir Podcasts</h2><table class='data-table'><tr><th>Episódio</th><th>Status</th><th>Ações</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['title']}</td><td><span class='status-pill'>{$row['status']}</span></td>
              <td><button class='btn-delete' data-id='{$row['id']}' data-type='podcast'>🗑️</button></td></tr>";
    }
    echo "</table>";
}

// --- AÇÕES DE ESCRITA ---

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'create_post') {
    $title = $_POST['titulo'];
    $text = $_POST['texto'];
    $type = $_POST['type']; // 'post' ou 'podcast'
    $extra = $_POST['extra_field'];

    if ($type == 'post') {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, media) VALUES (1, ?, ?, ?)");
        $stmt->bind_param("sss", $title, $text, $extra);
    } else {
        $stmt = $conn->prepare("INSERT INTO podcasts (title, description, video_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $text, $extra);
    }
    
    $stmt->execute();
    echo "sucesso";
}

if ($action == 'delete') {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $table = ($type == 'post') ? 'posts' : (($type == 'podcast') ? 'podcasts' : 'suggestions');
    
    $conn->query("DELETE FROM $table WHERE id = $id");
    echo "apagado";
}

// Função auxiliar repetida por segurança no escopo
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) $_ENV[trim($parts[0])] = trim($parts[1]);
    }
}