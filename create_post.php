<?php
header('Content-Type: text/html; charset=UTF-8');

// --- INÍCIO DA CONFIGURAÇÃO DO AMBIENTE (.env) ---
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        // Divide a linha apenas no primeiro '=' encontrado
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}

// Carregar o ficheiro .env que criaste na mesma pasta
loadEnv(__DIR__ . '/.env');

// Define o token vindo do .env (ou um fallback caso o ficheiro falhe)
define('POST_TOKEN', $_ENV['POST_TOKEN'] ?? 'token_nao_configurado');
// --- FIM DA CONFIGURAÇÃO ---

// Mostrar todos os erros (para debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "db.php";

// Verifica token (A lógica continua exatamente a mesma que tinhas)
if (!isset($_GET['token']) || $_GET['token'] !== POST_TOKEN) {
    die("<p style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>🚫 Acesso negado: token inválido.</p>");
}

// Processa formulário
if (isset($_POST['criar'])) {
    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $media_link = trim($_POST['media_link']);
    $media = '';

    // Upload de arquivo (imagem, vídeo, áudio)
    if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = time() . '_' . basename($_FILES["fileInput"]["name"]);
        $target_file = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $target_file)) {
            $media = $target_file;
        } else {
            echo "<p style='color:red;text-align:center;'>Falha ao fazer upload do arquivo.</p>";
        }
    } else {
        $media = $media_link;
    }

    $user_id = 1;

    $stmt = $conn->prepare("INSERT INTO posts (title, content, media, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("<p style='color:red;'>Erro na preparação da query: " . $conn->error . "</p>");
    }
    $stmt->bind_param("sssi", $titulo, $texto, $media, $user_id);

    if ($stmt->execute()) {
        echo "<p style='color:lightgreen;text-align:center;'>✅ Post criado com sucesso!</p>";
    } else {
        echo "<p style='color:red;text-align:center;'>Erro ao criar post: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <title>Criar Post</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Manti o teu CSS original */
        .formulario {
            max-width: 700px;
            margin: 120px auto 40px auto;
            padding: 20px 25px;
            background: rgba(45, 35, 25, 0.85);
            border: 1px solid rgba(212,178,106,0.3);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            font-family: 'EB Garamond', serif;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }

        .formulario h1 {
            text-align: center;
            color: #d4b26a;
            margin-bottom: 5px;
        }

        .formulario input[type="text"],
        .formulario textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 1em;
            border-radius: 6px;
            border: 1px solid #5a4c3c;
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            max-height: 250px;
        }

        .dropzone {
            border: 2px dashed #d4b26a;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #c2b8a6;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }
        .dropzone.dragover {
            background: rgba(212, 178, 106, 0.2);
        }

        .formulario button {
            width: fit-content;
            align-self: flex-end;
            padding: 10px 20px;
            background: #d4b26a;
            color: #2b241a;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .formulario button:hover {
            background: #b6924d;
            color: #fff;
        }

        .formulario p {
            font-size: 0.9em;
            color: #c2b8a6;
            margin: 0;
        }

        @media (max-width: 768px) {
            .formulario {
                margin: 100px 15px 30px 15px;
                padding: 15px;
            }
            .formulario button {
                width: 100%;
                align-self: center;
            }
        }
    </style>
</head>
<body style="padding-top:60px;">

<?php include "nav_bar.php"; ?>

<div class="formulario">
    <h1>Novo Post</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="texto" placeholder="Escreva sua poesia, texto ou reflexão..." required></textarea>
        <input type="text" name="media_link" placeholder="Link de mídia (opcional)">
        
        <p>Ou arraste e largue um ficheiro (imagem, vídeo ou música):</p>
        <div class="dropzone" id="dropzone">Arraste o ficheiro aqui 🎵</div>
        
        <input type="file" id="fileInput" name="fileInput" accept="image/*,video/*,audio/*" style="display:none;">
        
        <button type="submit" name="criar">Publicar</button>
    </form>
</div>

<script>
    // Manti o teu script original
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
</script>

</body>
</html>