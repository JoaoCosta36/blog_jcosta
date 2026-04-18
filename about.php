<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <title>Sobre mim | João Costa</title>
    
    <link rel="stylesheet" href="style.css">

    <style>
        /* Removemos as definições de body que conflituavam com o style.css */
        .container-about {
            max-width: 850px;
            margin: 0 auto;
            padding: 120px 20px 60px; /* Padding extra no topo para a navbar */
            text-align: center;
        }

        .profile-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 50%;
            margin: 30px auto;
            display: block;
            border: 3px solid #d4b26a; /* Borda dourada para combinar com o tema */
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }

        .about-text {
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 20px;
            text-align: justify; /* Melhora a leitura em blocos de texto */
            color: #e8e0d2;
        }

        .quote-container {
            margin-top: 50px;
            padding: 20px;
            border-top: 1px solid rgba(212,178,106,0.3);
        }

        .quote {
            font-style: italic;
            color: #d4b26a;
            font-size: 1.4rem;
            display: block;
        }

        .quote-author {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #c2b8a6;
        }
    </style>
</head>
<body>

<?php include "nav_bar.php"; ?>

<div class="container-about">
    <h1>Sobre mim</h1>

    <img src="img.jpeg" alt="João Costa" class="profile-img">

    <div class="about-text">
        <p>Olá, o meu nome é João. Nasci em 1996 e este espaço digital nasceu da minha necessidade de traduzir em palavras a complexidade da existência.</p>
        
        <p>Sou um "impaciente apaixonado pela vida", como costumo dizer. Inquieto por dentro, numa constante busca por respostas filosóficas, mas que tenta manter a paz e o equilíbrio no exterior. A música é o meu grande refúgio e a ferramenta que me permite tocar na profundidade da vida de uma forma que o silêncio não consegue.</p>

        <p>Atualmente, estou dedicado a aprender violino, um instrumento que exige tanta disciplina quanto sensibilidade. Mas o meu horizonte musical não se fica por aqui: pretendo explorar o piano, o saxofone e o vasto mundo dos sintetizadores e keyboards. Quando não estou rodeado de notas musicais, encontro o meu foco no ténis de mesa, um desporto que equilibra a minha mente.</p>

        <p>Criei este blog para exprimir livremente as minhas visões, filosofias e as questões que me acompanham. É um convite para quem, tal como eu, não se contenta com o superficial.</p>
    </div>

    <div class="quote-container">
        <span class="quote">"Music is an everlasting deep sense of life"</span>
        <span class="quote-author">— João Costa</span>
    </div>
</div>

</body>
</html>