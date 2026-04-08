<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
session_start();

// ✅ Conexão com a BD
require_once __DIR__ . "/db.php";

// ✅ Caminho direto para o PHPMailer
$phpmailer_dir = __DIR__ . '/PHPMailer/';

if (file_exists($phpmailer_dir . 'PHPMailer.php')) {
    require_once $phpmailer_dir . 'Exception.php';
    require_once $phpmailer_dir . 'PHPMailer.php';
    require_once $phpmailer_dir . 'SMTP.php';
} else {
    die("Erro Crítico: Pasta PHPMailer não encontrada em: " . $phpmailer_dir);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Carregar configurações do .env
$env_file = __DIR__ . '/.env';
$env = file_exists($env_file) ? parse_ini_file($env_file) : [];

$erro = '';
$sucesso = '';
$nome = $paixao_por = $email = '';

// ✅ Lógica de Registo
if (isset($_POST['register'])) {
    $nome = trim($_POST['nome'] ?? '');
    $paixao_por = trim($_POST['paixao_por'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "O email introduzido não é válido.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Este email já está registado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            $stmt_insert = $conn->prepare("INSERT INTO users (nome, paixao_por, email, password, verify_token) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssss", $nome, $paixao_por, $email, $hash, $token);

            if ($stmt_insert->execute()) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $env['GMAIL_USER'] ?? '';
                    $mail->Password   = $env['GMAIL_APP_PASS'] ?? ''; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom($env['GMAIL_USER'] ?? 'noreply@joaocostart.com', $env['FROM_NAME'] ?? 'joaocostArt');
                    $mail->addAddress($email, $nome);

                    $link = "https://joaocostart.com/confirm_register.php?token=" . $token;
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Confirma o teu Registo - joaocostArt';
                    $mail->Body    = "<h2>Bem-vindo, $nome!</h2><p>Clique no botão abaixo para ativar a sua conta:</p>
                                     <a href='$link' style='background:#d4b26a; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Confirmar Conta</a>";

                    $mail->send();
                    $sucesso = "Registo efetuado! Verifica a tua caixa de entrada.";
                    $nome = $paixao_por = $email = ''; 
                } catch (Exception $e) {
                    $sucesso = "Conta criada, mas houve um erro ao enviar o email de confirmação.";
                }
            } else {
                $erro = "Erro ao processar o registo. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - joaocostArt</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Correção de Alinhamento */
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
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .form-wrapper h1 {
            color: #d4b26a;
            text-align: center;
            margin-bottom: 10px;
        }

        .form-wrapper p {
            text-align: center;
            color: #c2b8a6;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column; /* Alinha label em cima do input */
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-group label {
            color: #d4b26a;
            font-size: 0.9em;
            font-weight: bold;
        }

        .form-group input {
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #5a4c3c;
            border-radius: 6px;
            color: #fff;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box; /* Garante que o padding não quebre a largura */
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

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-danger { background: rgba(255, 0, 0, 0.2); color: #ff8888; border: 1px solid #ff0000; }
        .alert-success { background: rgba(0, 255, 0, 0.1); color: #88ff88; border: 1px solid #00ff00; }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }
        .form-footer a { color: #d4b26a; text-decoration: none; }
    </style>
</head>
<body>

    <?php if(file_exists('nav_bar.php')) include 'nav_bar.php'; ?>

    <div class="container">
        <div class="form-wrapper">
            <h1>Criar Conta</h1>
            <p>Junta-te à nossa comunidade</p>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                </div>

                <div class="form-group">
                    <label>Paixão por...</label>
                    <input type="text" name="paixao_por" value="<?php echo htmlspecialchars($paixao_por); ?>" placeholder="Ex: Música dos anos 80">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label>Palavra-passe</label>
                    <input type="password" name="senha" required>
                </div>

                <button type="submit" name="register" class="btn-submit">Registar</button>
            </form>
            
            <div class="form-footer">
                <p>Já tens conta? <a href="login.php">Faz login aqui</a></p>
            </div>
        </div>
    </div>

</body>
</html>