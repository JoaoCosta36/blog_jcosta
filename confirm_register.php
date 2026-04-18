<?php
header('Content-Type: text/html; charset=UTF-8');
include "db.php";
// ... (tua lógica de confirmação de token aqui)
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação | João Costa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <div class="content-block" style="text-align: center;">
            <h2>Registo</h2>
            <p><?php echo $mensagem; ?></p>
            <br>
            <a href="login.php" style="color: #d4b26a; font-weight: bold;">Ir para o Login</a>
        </div>
    </div>
</body>
</html>