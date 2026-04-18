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
    die("Erro Crítico: Pasta PHPMailer não encontrada.");
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
                    $mail->Subject = 'Confirma o teu Registo | joaocostArt';
                    $mail->Body    = "
                        <div style='background:#1a1612; color:#f2e8d5; padding:40px; font-family:serif; text-align:center;'>
                            <h2 style='color:#d4b26a;'>Bem-vindo, $nome!</h2>
                            <p>Obrigado por te juntares à nossa comunidade. Falta apenas um passo.</p>
                            <br>
                            <a href='$link' style='background:#d4b26a; color:#1a1612; padding:12px 25px; text-decoration:none; border-radius:5px; font-weight:bold;'>Confirmar Conta</a>
                            <br><br>
                            <p style='font-size:0.8em; color:#888;'>Se não criaste esta conta, ignora este email.</p>
                        </div>";

                    $mail->send();
                    $sucesso = "Registo efetuado! Verifica o teu email para ativar a conta.";
                    $nome = $paixao_por = $email = ''; 
                } catch (Exception $e) {
                    $sucesso = "Conta criada, mas houve um erro ao enviar o email. Contacta o suporte.";
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
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include 'adsense.php'; ?>
    <meta charset="UTF-8">
   
    <title>Criar Conta | João Costa</title>
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="icon" href="icon.jpg" type="image/jpeg">
</head>
<body>

    <?php include 'nav_bar.php'; ?>

    <div class="page-wrapper" style="display:flex; justify-content:center; align-items:center; min-height:80vh;">
        <div class="content-block" style="max-width: 450px; width: 100%; padding: 40px;">
            
            <h1 style="color: #d4b26a; text-align: center; margin-bottom: 10px;">Criar Conta</h1>
            <p style="text-align: center; color: #c2b8a6; margin-bottom: 30px;">Junta-te à nossa comunidade</p>

            <?php if ($erro): ?>
                <div style="background: rgba(255, 0, 0, 0.1); color: #ff8888; padding: 10px; border: 1px solid #ff0000; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div style="background: rgba(0, 255, 0, 0.1); color: #88ff88; padding: 10px; border: 1px solid #00ff00; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div style="margin-bottom: 15px;">
                    <label style="color: #d4b26a; display: block; margin-bottom: 5px; font-weight: bold;">Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: #d4b26a; display: block; margin-bottom: 5px; font-weight: bold;">Paixão por...</label>
                    <input type="text" name="paixao_por" value="<?php echo htmlspecialchars($paixao_por); ?>" placeholder="Ex: Música Clássica, Pintura...">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: #d4b26a; display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="color: #d4b26a; display: block; margin-bottom: 5px; font-weight: bold;">Palavra-passe</label>
                    <input type="password" name="senha" required>
                </div>

                <button type="submit" name="register" class="btn-auth" style="width: 100%; font-size: 1.1rem;">
                    Criar Conta
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 25px; border-top: 1px solid rgba(212,178,106,0.1); padding-top: 20px;">
                <p style="color: #c2b8a6;">Já tens conta? <a href="login.php" style="color: #d4b26a; text-decoration: none; font-weight: bold;">Entrar aqui</a></p>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?></body>
</html>