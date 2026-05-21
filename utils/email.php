<?php
// utils/email.php
// Reutiliza PHPMailer para enviar notificaciones desde registro y reservas.
// Ajusta las credenciales SMTP si cambia el proveedor de correo.

require_once __DIR__ . '/../libs/email/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email notification.
 *
 * @param string      $toEmail Recipient email address.
 * @param string      $toName  Recipient full name.
 * @param string      $subject Email subject.
 * @param string|null $body    HTML body content. If null, a default template is used.
 */
function enviarCorreo(string $toEmail, string $toName, string $subject = 'Confirmación de reservación', string $body = null): void
{
    // Crear nuevo objeto PHPMailer para cada envío.
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
        $mail->Subject = $subject;

        // Usar cuerpo personalizado si se recibe, de lo contrario usar plantilla de reserva.
        $mail->Body = $body ?? "
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;background-color:#eef2ff;color:#1f2937;font-family:Arial,sans-serif;'>
          <table width='100%' cellpadding='0' cellspacing='0' style='max-width:640px;margin:0 auto;padding:24px;'>
            <tr>
              <td style='text-align:center;padding-bottom:20px;'>
                <div style='display:inline-block;padding:16px 24px;background:#2563eb;border-radius:20px;color:#ffffff;font-size:22px;font-weight:700;'>Hotel MyQ</div>
              </td>
            </tr>
            <tr>
              <td style='background:#ffffff;border-radius:28px;padding:32px;box-shadow:0 24px 80px rgba(15,23,42,0.08);'>
                <h1 style='font-size:24px;color:#111827;margin:0 0 16px;'>¡Hola {$toName}!</h1>
                <p style='font-size:16px;line-height:1.8;color:#475569;margin:0 0 24px;'>Tu reservación se ha confirmado correctamente. Gracias por elegir Hotel MyQ. Estamos preparando todo para que tu estadía sea especial y muy cómoda.</p>
                <div style='background:#eff6ff;border:1px solid #dbeafe;padding:20px;border-radius:20px;margin-bottom:24px;'>
                  <p style='margin:0;font-size:15px;font-weight:700;color:#1e3a8a;'>Resumen de tu reserva</p>
                  <p style='margin:12px 0 0;color:#475569;font-size:14px;'>Revisa tu panel para ver los detalles completos y el estado de tu reserva.</p>
                </div>
                <a href='#' style='display:inline-block;padding:12px 24px;background:#2563eb;color:#ffffff;border-radius:999px;text-decoration:none;font-weight:700;font-size:14px;'>Ver mi reserva</a>
                <p style='font-size:15px;line-height:1.8;color:#475569;margin:24px 0 0;'>Si necesitas ayuda, contáctanos respondiendo a este correo o usando los datos que encuentras al final.</p>
                <p style='font-size:13px;line-height:1.8;color:#94a3b8;margin:28px 0 0;'>Este es un mensaje automático, por favor no responder.</p>
              </td>
            </tr>
            <tr>
              <td style='text-align:center;padding:24px 0 0;color:#64748b;font-size:13px;'>
                Hotel MyQ · Tel: +57 000 000 0000 · contacto@hotelmyq.com
              </td>
            </tr>
          </table>
        </body>
        </html>";
        $mail->AltBody = strip_tags(preg_replace('/\s+/', ' ', $mail->Body));

        $mail->send();
        // Optional: log success
        // error_log('Correo enviado a ' . $toEmail);
    } catch (Exception $e) {
        // Log the error for debugging; do not expose to user
        error_log('Error enviando correo: ' . $mail->ErrorInfo);
    }
}
