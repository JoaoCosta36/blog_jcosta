<?php
header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>

    <meta charset="UTF-8">
    <link rel="icon" href="icon.png" type="image/png">
    <title>Sobre mim</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 100px 20px 60px;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .profile-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 50%;
            margin: 20px auto;
            display: block;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            
            margin-bottom: 15px;
        }

        .quote {
            margin-top: 30px;
            font-style: italic;
            color: #555;
        }
    </style>
</head>

<body>

<?php include "nav_bar.php"; ?>

<div class="container">

    <h1>Sobre mim</h1>

    <img src="img.jpeg" alt="Foto de perfil" class="profile-img">

    <p>Olá. Sou o João. Sou de 1996.</p>

    <p>Apaixonado pela profundidade da vida e a música ajuda-me nisso.</p>

    <p>Criei este blog para exprimir pensamentos, visões, filosofias e questões.</p>

    <p>Sou um impaciente apaixonado pela vida, inquieto por dentro mas pacífico por fora.</p>

    <p>Estou a aprender violino e jogo ténis de mesa.</p>

    <p>Quero aprender mais instrumentos como piano, saxofone e trabalhar com keyboards.</p>

    <p class="quote">
        "Music is an everlasting deep sense of life"
        <p class="quote">João Costa
    </p></p>

</div>

</body>
</html>