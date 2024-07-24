<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->SMTPDebug = 2; // Habilitar depuración detallada
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'thejokerharuno@gmail.com'; // Tu correo de Gmail
    $mail->Password = 'ruppapuegaultnuf'; // Tu contraseña de Gmail o la contraseña de aplicación generada
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O ENCRYPTION_SMTPS para SSL
    $mail->Port = 587; // O 465 para SSL

    // Remitente y destinatario
    $mail->setFrom('thejokerharuno@gmail.com', 'Alma');
    $mail->addAddress('oficial.sr.hidden@gmail.com', 'Moises');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Prueba de Correo';
    $mail->Body    = '<p>Este es un mensaje de prueba enviado desde PHPMailer con Gmail.</p>';
    $mail->AltBody = 'Este es un mensaje de prueba enviado desde PHPMailer con Gmail.';

    $mail->send();
    echo 'Correo enviado correctamente';
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}
?>
