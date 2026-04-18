<?php
header('Content-Type: text/html; charset=UTF-8');

// --- INÍCIO DA CONFIGURAÇÃO DO AMBIENTE (.env) ---
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}
loadEnv(__DIR__ . '/.env');
define('POST_TOKEN', $_ENV['POST_TOKEN'] ?? 'token_nao_configurado');

session_start();
include "db.php";

// Verifica token
if (!isset($_GET['token']) || $_GET['token'] !== POST_TOKEN) {
    die("<h1 style='color:#ff6b6b; text-align:center; margin-top:100px; font-family:serif;'>🚫 Acesso negado: token inválido.</h1>");
}

$mensagem_sucesso = "";
$mensagem_erro = "";

// Processa formulário
if (isset($_POST['criar'])) {
    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $media_link = trim($_POST['media_link']);
    $media = '';

    if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = time() . '_' . basename($_FILES["fileInput"]["name"]);
        $target_file = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $target_file)) {
            $media = $target_file;
        } else {
            $mensagem_erro = "Falha ao fazer upload do arquivo.";
        }
    } else {
        $media = $media_link;
    }

    $user_id = 1; // Ajustar conforme a tua lógica de sessão se necessário

    $stmt = $conn->prepare("INSERT INTO posts (title, content, media, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $titulo, $texto, $media, $user_id);

    if ($stmt->execute()) {
        $mensagem_sucesso = "✅ Post criado com sucesso!";
    } else {
        $mensagem_erro = "Erro ao criar post: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Post | João Costa</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilo específico apenas para a Dropzone que não está no global */
        .dropzone {
            border: 2px dashed #d4b26a;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #c2b8a6;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
            margin-bottom: 20px;
        }
        .dropzone.dragover { background: rgba(212, 178, 106, 0.2); }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="page-wrapper">
    <div class="content-block" style="max-width: 700px; margin: 0 auto;">
        <h1 style="color: #d4b26a; text-align: center; margin-bottom: 20px;">Novo Post</h1>

        <?php if($mensagem_sucesso) echo "<p style='color:lightgreen; text-align:center; margin-bottom:15px;'>$mensagem_sucesso</p>"; ?>
        <?php if($mensagem_erro) echo "<p style='color:#ff6b6b; text-align:center; margin-bottom:15px;'>$mensagem_erro</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Título" required>
            
            <textarea name="texto" placeholder="Escreva sua poesia, texto ou reflexão..." required style="height: 150px;"></textarea>
            
            <input type="text" name="media_link" placeholder="Link de mídia (opcional)">
            
            <p style="font-size: 0.9em; color: #c2b8a6; margin-bottom: 10px;">Ou arraste um ficheiro (imagem, vídeo ou áudio):</p>
            <div class="dropzone" id="dropzone">Clique ou arraste o ficheiro aqui 🎵</div>
            
            <input type="file" id="fileInput" name="fileInput" accept="image/*,video/*,audio/*" style="display:none;">
            
            <div style="text-align: right;">
                <button type="submit" name="criar" class="btn-auth">Publicar Post</button>
            </div>
        </form>
    </div>
</div>

<script>
    const dropzone = document.getElementById("dropzone");
    const fileInput = document.getElementById("fileInput");

    dropzone.addEventListener("click", () => fileInput.click());

    dropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropzone.classList.add("dragover");
    });

    dropzone.addEventListener("dragleave", () => {
        dropzone.classList.remove("dragover");
    });

    dropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropzone.classList.remove("dragover");
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            dropzone.textContent = "📂 " + e.dataTransfer.files[0].name;
        }
    });

    fileInput.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            dropzone.textContent = "📂 " + fileInput.files[0].name;
        }
    });
</script>

</body>
</html>