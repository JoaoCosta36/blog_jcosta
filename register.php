<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
session_start();

// ✅ Conexão com a BD
require_once __DIR__ . "/db.php";

// ✅ Caminho direto para o PHPMailer (ajustado para a tua pasta htdocs/PHPMailer/)
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
        // Verificar se email já existe
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
                // ✅ Envio de Email via PHPMailer
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

                    $mail->setFrom($env['GMAIL_USER'] ?? 'noreply@teusite.com', $env['FROM_NAME'] ?? 'Sistema de Registo');
                    $mail->addAddress($email, $nome);

                    $link = "http://localhost/confirm_register.php?token=" . $token;
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Confirma o teu Registo';
                    $mail->Body    = "<h2>Bem-vindo, $nome!</h2><p>Clique no botão abaixo para ativar a sua conta:</p>
                                     <a href='$link' style='background:#28a745; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Confirmar Conta</a>";

                    $mail->send();
                    $sucesso = "Registo efetuado! Verifica a tua caixa de entrada.";
                    $nome = $paixao_por = $email = ''; // Limpar campos
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
    <title>Registo de Utilizador</title>
    <link rel="stylesheet" href="style.css">
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
                    <input type="text" name="paixao_por" value="<?php echo htmlspecialchars($paixao_por); ?>">
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