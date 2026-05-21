<?php

class controllerRegister {
    public function index() {
        include 'views/Auth/registrar.php';
    }

    public function validation($data){
        
        // Preparar datos del formulario y almacenar errores de validación.
        $errors = [];
        $documentType = $data['fav_language'] ?? '';
        $cedula = trim($data['Cedula'] ?? '');
        $nombre = trim($data['Nombre'] ?? '');
        $apellido = trim($data['Apellido'] ?? '');
        $email = trim($data['Email'] ?? '');
        $contraseña = $data['Contraseña1'] ?? '';
        $contraseña2 = $data['Contraseña2'] ?? '';

        // Validar tipo de documento.
        if ($documentType === '') {
            $errors['DocumentType'] = 'El tipo de documento es obligatorio.';
        }

        if ($cedula === '') {
            $errors['Cedula'] = 'La cédula es obligatoria.';
        } elseif (!ctype_digit($cedula)) {
            $errors['Cedula'] = 'La cédula solo puede contener números.';
        }

        if ($apellido === '') {
            $errors['Apellido'] = 'El apellido es obligatorio.';
        } elseif (mb_strlen($apellido) < 3) {
            $errors['Apellido'] = 'El apellido debe tener al menos 3 caracteres.';
        }

        if ($nombre === '') {
            $errors['Nombre'] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombre) < 3) {
            $errors['Nombre'] = 'El nombre debe tener al menos 3 caracteres.';
        }

        if ($email === '') {
            $errors['Email'] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = 'El email no es válido.';
        }

        // Validar contraseña y confirmación.
        if ($contraseña === '') {
            $errors['Contraseña1'] = 'La contraseña es obligatoria.';
        } elseif (strlen($contraseña) < 6) {
            $errors['Contraseña1'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($contraseña2 === '') {
            $errors['Contraseña2'] = 'La confirmación de contraseña es obligatoria.';
        } elseif ($contraseña !== $contraseña2) {
            $errors['Contraseña2'] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            // Si hay errores de validación, volver al formulario con mensajes y valores anteriores.
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'Nombre'   => $nombre,
                'Apellido' => $apellido,
                'Email'    => $email,
                'Cedula'   => $cedula
            ];
            header('Location: index.php?action=login');
            exit;
        }

        try {
            // Crear usuario y encriptar contraseña antes de guardarla.
            $userModel = new User();
            $passwordHash = password_hash($contraseña, PASSWORD_DEFAULT);

            if ($userModel->createUser($documentType, $cedula, $nombre, $apellido, $email, $passwordHash)) {
                $_SESSION['success'] = 'Usuario registrado correctamente.';

                // Enviar correo de confirmación al nuevo usuario.
                require_once __DIR__ . '/../utils/email.php';
                $fullName = trim($nombre . ' ' . $apellido);
                $body = "
                    <html>
                    <head><meta charset='UTF-8'></head>
                    <body style='margin:0;padding:0;background:#f3f6ff;color:#344054;font-family:Arial,sans-serif;'>
                      <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px;margin:0 auto;'>
                        <tr>
                          <td style='padding:24px 24px 0;text-align:center;'>
                            <div style='display:inline-block;padding:18px 24px;background:#1a73e8;border-radius:16px;color:#fff;font-size:24px;font-weight:700;'>Hotel MyQ</div>
                          </td>
                        </tr>
                        <tr>
                          <td style='background:#fff;border-radius:24px;margin-top:16px;padding:32px;box-shadow:0 16px 40px rgba(16,24,40,0.08);'>
                            <h1 style='font-size:22px;color:#101828;margin:0 0 16px;'>¡Hola {$fullName}!</h1>
                            <p style='font-size:16px;line-height:1.75;color:#475467;margin:0 0 24px;'>Tu registro en Hotel MyQ se ha realizado con éxito. Ya puedes iniciar sesión y empezar a reservar tu próxima estadía.</p>
                            <div style='padding:20px;border-radius:18px;background:#eef4ff;border:1px solid #dbeafe;'>
                              <p style='margin:0;font-size:15px;font-weight:600;color:#0f172a;'>Bienvenido a Hotel MyQ</p>
                              <p style='margin:12px 0 0;color:#475467;font-size:14px;'>Disfruta de un servicio exclusivo y atención personalizada en cada reserva.</p>
                            </div>
                            <p style='font-size:15px;line-height:1.75;color:#475467;margin:24px 0 0;'>Si necesitas ayuda para iniciar sesión, responde a este correo.</p>
                            <p style='font-size:13px;line-height:1.8;color:#94a3b8;margin:24px 0 0;'>Este es un mensaje automático, por favor no responder.</p>
                          </td>
                        </tr>
                      </table>
                    </body>
                    </html>";
                enviarCorreo($email, $fullName, 'Confirmación de registro', $body);

                header('Location: index.php?action=login');
                exit;
            }

            $errors['Database'] = 'No se pudo registrar el usuario. Inténtalo nuevamente.';
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'Nombre'   => $nombre,
                'Apellido' => $apellido,
                'Email'    => $email,
                'Cedula'   => $cedula
            ];
            header('Location: index.php?action=login');
            exit;
        } catch (Exception $e) {
            $errors['Database'] = 'Error de base de datos: ' . $e->getMessage();
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'Nombre'   => $nombre,
                'Apellido' => $apellido,
                'Email'    => $email,
                'Cedula'   => $cedula
            ];
            header('Location: index.php?action=login');
            exit;
        }
    }
}
?>
