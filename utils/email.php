<?php
// utils/email.php
// Simple wrapper to send reservation confirmation using PHPMailer.
// Adjust the require paths if PHPMailer is installed via Composer.

require_once 'libs/email/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

/**
 * Send a reservation confirmation email.
 *
 * @param string $toEmail Recipient email address.
 * @param string $toName  Recipient full name.
 */
function enviarCorreo(string $toEmail, string $toName): void
{
    $mail = new PHPMailer(true); // Enable exceptions

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maicolquevedo29@gmail.com';
        $mail->Password   = 'evaw fyju snkp xmij'; // TODO: move to env var
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        // Sender & recipient
        $mail->setFrom('maicolquevedo29@gmail.com', 'Hotel MyQ');
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'Confirmación de reservación';

        $mail->isHTML(true);
        // Simple HTML body
        $mail->Body = "
        <div style='font-family:Arial,sans-serif; background:#f9f9f9; padding:20px;'>
            <h2 style='color:#2c3e50;'>¡Hola {$toName}!</h2>
            <p>Tu reservación se ha creado correctamente.</p>
            <p>Si tienes alguna duda, puedes responder a este correo.</p>
            <hr style='border:none; border-top:1px solid #ccc;'/>
            <p style='font-size:12px; color:#777;'>Este es un mensaje automático, por favor no responder.</p>
        </div>";

        $mail->send();
        // Optional: log success
        // error_log('Correo enviado a ' . $toEmail);
    } catch (Exception $e) {
        // Log the error for debugging; do not expose to user
        error_log('Error enviando correo: ' . $mail->ErrorInfo);
    }
}
