<?php 
header('Content-Type: text/html; charset=UTF-8');
include "db.php";

// Mantém a tua lógica de PHP aqui (tokens, validações, etc.)
// A variável $mensagem deve ser definida na tua lógica acima.
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
   
    <title>Confirmação | João Costa</title>
    <link rel="stylesheet" href="style.css?v=2.3">
</head>
<body>

    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <div class="content-block" style="text-align: center;">
            <h2 style="color: #d4b26a; margin-bottom: 20px;">Registo</h2>
            
            <p style="font-size: 1.2rem; margin-bottom: 25px;">
                <?php echo isset($mensagem) ? $mensagem : "A processar a sua confirmação..."; ?>
            </p>

            <a href="login.php" class="btn-auth" style="display: inline-block; padding: 12px 25px;">
                Ir para o Login
            </a>
        </div>
    </div>

<?php include 'footer.php'; ?></body>
</html>