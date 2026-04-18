<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
   
    <title>Sobre mim | João Costa</title>
    
    <link rel="stylesheet" href="style.css?v=2.3">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <h1 style="color: #d4b26a; text-align: center; margin-bottom: 30px; text-transform: uppercase;">Sobre mim</h1>
        
        <img src="img.jpeg" alt="João Costa" class="img-profile-circle">
        
        <div class="content-block" style="text-align: center;">
            <p style="margin-bottom: 15px;">Olá, o meu nome é <strong>João</strong>. Nasci em 1996 e este blog é o meu espaço de expressão pessoal e artística.</p>
            <p style="margin-bottom: 15px;">Sou um apaixonado pela música e o violino é a minha bússola.</p>
            
            <div style="margin-top: 40px; border-top: 1px solid rgba(212, 178, 106, 0.2); padding-top: 20px; color: #d4b26a; font-style: italic;">
                "Music is an everlasting deep sense of life"
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?></body>
</html>