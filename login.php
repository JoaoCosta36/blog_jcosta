<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

$erro = '';

// Processamento do Login
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $senha = $_POST['senha'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_role'] = $user['role']; // Caso tenhas roles de admin
            
            header("Location: index.php");
            exit;
        } else {
            $erro = "Palavra-passe incorreta.";
        }
    } else {
        $erro = "Utilizador não encontrado.";
    }
}
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
            <h1 style="color: #d4b26a; margin-bottom: 20px;">Login</h1>
            
            <?php if($erro != ''): ?>
                <p style="color: #ff6b6b; margin-bottom: 15px; font-weight: bold;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?>
                </p>
            <?php endif; ?>

            <form method="post">
                <label style="display: block; text-align: left; color: #c2b8a6; font-size: 0.9rem;">Email</label>
                <input type="email" name="email" placeholder="exemplo@email.com" required>
                
                <label style="display: block; text-align: left; color: #c2b8a6; font-size: 0.9rem;">Palavra-passe</label>
                <input type="password" name="senha" placeholder="A tua senha" required>
                
                <button type="submit" name="login" class="btn-auth" style="width: 100%; margin-top: 10px;">
                    Entrar na Conta
                </button>
            </form>
            
            <p style="margin-top: 25px; color: #e8e0d2;">
                Não tens conta? 
                <a href="register.php" style="color:#d4b26a; text-decoration: none; font-weight: bold;">Regista-te aqui</a>
            </p>
        </div>
    </div>

</body>
</html>