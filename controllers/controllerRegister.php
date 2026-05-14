<?php

class controllerRegister {
    public function index() {
        include 'views/Auth/registrar.php';
    }

    public function validation($data){
        
        $errors = [];
        $documentType = $data['fav_language'] ?? '';
        $cedula = trim($data['Cedula'] ?? '');
        $nombre = trim($data['Nombre'] ?? '');
        $apellido = trim($data['Apellido'] ?? '');
        $email = trim($data['Email'] ?? '');
        $contraseña = $data['Contraseña1'] ?? '';
        $contraseña2 = $data['Contraseña2'] ?? '';

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
            $userModel = new User();
            $passwordHash = password_hash($contraseña, PASSWORD_DEFAULT);

            if ($userModel->createUser($documentType, $cedula, $nombre, $apellido, $email, $passwordHash)) {
                $_SESSION['success'] = 'Usuario registrado correctamente.';
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
