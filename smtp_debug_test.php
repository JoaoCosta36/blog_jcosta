<?php
// Debug script para SMTP (PHPMailer) usando Gmail e .env
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar variáveis de ambiente
$env = parse_ini_file(__DIR__ . '/.env');

$mail = new PHPMailer(true);
$logfile = __DIR__ . '/smtp_debug.log';
@file_put_contents($logfile, "=== Debug started: " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

try {
    // Debug
    $mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client+server
    $mail->Debugoutput = function($str, $level) use ($logfile) {
        file_put_contents($logfile, "[" . date('H:i:s') . "] level $level: $str\n", FILE_APPEND);
    };

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $env['GMAIL_USER'] ?? '';
    $mail->Password = $env['GMAIL_APP_PASS'] ?? '';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $fromEmail = $env['GMAIL_USER'] ?? '';
    $fromName  = $env['FROM_NAME'] ?? 'João Costa Blog';

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($env['GMAIL_TEST'] ?? 'seu-email-teste@gmail.com', 'Destino Teste');

    $mail->Subject = 'Teste SMTP PHPMailer Gmail';
    $mail->Body = 'Corpo do teste SMTP via PHPMailer usando Gmail';

    // Se houver problema com certificado SSL
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->send();
    file_put_contents($logfile, "SEND OK\n", FILE_APPEND);
    echo "Enviado — veja o ficheiro smtp_debug.log\n";

} catch (Exception $e) {
    file_put_contents($logfile, "EXCEPTION: {$e->getMessage()}\n", FILE_APPEND);
    echo "Falha — veja smtp_debug.log\n";
}