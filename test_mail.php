<?php
require __DIR__ . '/vendor/autoload.php';
$smtp = require __DIR__ . '/config/email.php';

use PHPMailer\PHPMailer\PHPMailer;

echo "<h2>Prueba SMTP Gmail</h2>";
echo "<pre>";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars("[$level] $str") . "\n";
    };
    $mail->Host = $smtp['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp['username'];
    $mail->Password = str_replace(' ', '', $smtp['password']);
    $mail->SMTPSecure = $smtp['encryption'];
    $mail->Port = $smtp['port'];
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($smtp['username'], $smtp['from_name']);
    $mail->addAddress($smtp['username'], $smtp['from_name']);
    $mail->Subject = 'TEST - ' . date('Y-m-d H:i:s');
    $mail->Body = 'Correo de prueba generado el ' . date('Y-m-d H:i:s');
    $mail->send();
    echo "\n<strong style='color:green'>CORREO ENVIADO EXITOSAMENTE</strong>\n";
} catch (Exception $e) {
    echo "\n<strong style='color:red'>ERROR: " . htmlspecialchars($e->getMessage()) . "</strong>\n";
}
echo "</pre>";
