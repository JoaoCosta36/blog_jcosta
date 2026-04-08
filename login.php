<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php";

$erro = '';

if(isset($_POST['login'])){
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if(empty($email) || empty($senha)){
        $erro = "Preencha todos os campos!";
    } else {
        // Buscar id, password e estado de verificação
        $stmt = $conn->prepare("SELECT id, password, confirmed FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows === 1){
            $stmt->bind_result($user_id, $hash, $confirmed);
            $stmt->fetch();

            if($confirmed == 0){
                $erro = "A tua conta ainda não foi verificada. Verifica o teu e-mail para confirmar o registo.";
            } elseif(password_verify($senha, $hash)){
                $_SESSION['user_id'] = $user_id;
                header("Location: index.php");
                exit;
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Email não cadastrado!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<?php include 'adsense.php'; ?>
<meta charset="UTF-8">
<title>Login</title>
<link rel="icon" href="icon.png" type="image/png">
<link rel="stylesheet" href="style.css">
</head>
<body style="padding-top:60px;">
<?php include "nav_bar.php"; ?>
<h1>Login</h1>

<?php if($erro != ''): ?>
    <p class="erro" style="color:red;"><?php echo htmlspecialchars($erro); ?></p>
<?php endif; ?>

<div class="input-container">
<form method="post">
    <input type="email" name="email" placeholder="Email" required maxlength="50" value="<?php echo htmlspecialchars($email ?? ''); ?>">
    <input type="password" name="senha" placeholder="Senha" required maxlength="50">
    <button type="submit" name="login">Login</button>
</form>
<p>Não tem conta? <a href="register.php">Registar-se</a></p>
</div>

</body>
</html>