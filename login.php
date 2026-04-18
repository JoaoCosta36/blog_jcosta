<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";
$erro = '';
// ... (mantem a tua lógica de POST aqui igual)
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | João Costa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <div class="form-container">
            <h1>Login</h1>
            <?php if($erro != ''): ?>
                <p style="color: #ff6b6b;"><?php echo htmlspecialchars($erro); ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Palavra-passe" required>
                <button type="submit" name="login">Entrar</button>
            </form>
            <p style="margin-top: 15px;">Não tens conta? <a href="register.php" style="color:#d4b26a;">Regista-te aqui</a></p>
        </div>
    </div>
</body>
</html>