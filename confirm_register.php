<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include "db.php"; // Conexão $conn

$mensagem = '';
$tipo = ''; // 'sucesso' ou 'erro'

if(isset($_GET['token'])){
    $token = trim($_GET['token']);

    if(empty($token)){
        $mensagem = "Token inválido.";
        $tipo = 'erro';
    } else {
        // Prepara a consulta
        if($stmt = $conn->prepare("SELECT id, confirmed FROM users WHERE verify_token=?")) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows === 1){
                $stmt->bind_result($id, $confirmed);
                $stmt->fetch();

                if($confirmed){
                    $mensagem = "O seu e-mail já foi confirmado anteriormente.";
                    $tipo = 'erro';
                } else {
                    if($stmt_update = $conn->prepare("UPDATE users SET confirmed=1, verify_token=NULL WHERE id=?")) {
                        $stmt_update->bind_param("i", $id);
                        if($stmt_update->execute()){
                            $mensagem = "O seu e-mail foi confirmado com sucesso! Agora pode fazer login.";
                            $tipo = 'sucesso';
                        } else {
                            $mensagem = "Erro ao confirmar o registo. Tente novamente mais tarde.";
                            $tipo = 'erro';
                        }
                        $stmt_update->close();
                    } else {
                        $mensagem = "Erro interno no servidor.";
                        $tipo = 'erro';
                    }
                }
            } else {
                $mensagem = "Token inválido ou expirado.";
                $tipo = 'erro';
            }

            $stmt->close();
        } else {
            $mensagem = "Erro interno no servidor.";
            $tipo = 'erro';
        }
    }
} else {
    $mensagem = "Token não fornecido.";
    $tipo = 'erro';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Registo</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; text-align: center; }
        h1 { color: #333; }
        p { font-size: 16px; }
        a { text-decoration: none; color: #4CAF50; font-weight: bold; }
        .alert { padding: 15px; margin: 20px auto; width: fit-content; border-radius: 5px; font-size: 16px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php include "nav_bar.php"; ?>
    <h1>Confirmação de Registo</h1>
    <?php if(!empty($mensagem)): ?>
        <div class="alert <?php echo htmlspecialchars($tipo); ?>">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>
    <p><a href="login.php">Ir para Login</a></p>
</body>
</html>