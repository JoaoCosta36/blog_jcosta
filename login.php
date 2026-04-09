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
                $erro = "A tua conta ainda não foi verificada. Verifica o teu e-mail!";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estrutura idêntica ao Registo */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }

        .form-wrapper {
            background: rgba(45, 35, 25, 0.9);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(212, 178, 106, 0.3);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            font-family: 'EB Garamond', serif;
        }

        .form-wrapper h1 {
            color: #d4b26a;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.2em;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-group label {
            color: #d4b26a;
            font-size: 0.9em;
        }

        .form-group input {
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #5a4c3c;
            border-radius: 6px;
            color: #fff;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #d4b26a;
            color: #2b241a;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #b6924d;
            transform: translateY(-2px);
        }

        .erro-msg {
            background: rgba(255, 0, 0, 0.15);
            color: #ff8888;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            color: #c2b8a6;
        }

        .form-footer a {
            color: #d4b26a;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body style="padding-top:60px;">

<?php include "nav_bar.php"; ?>

<div class="container">
    <div class="form-wrapper">
        <h1>Login</h1>

        <?php if($erro != ''): ?>
            <div class="erro-msg"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="O teu email" required maxlength="50" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Palavra-passe</label>
                <input type="password" name="senha" placeholder="A tua senha" required maxlength="50">
            </div>

            <button type="submit" name="login" class="btn-submit">Entrar</button>
        </form>

        <div class="form-footer">
            <p>Ainda não tens conta? <br> <a href="register.php">Regista-te aqui</a></p>
        </div>
    </div>
</div>

</body>
</html>