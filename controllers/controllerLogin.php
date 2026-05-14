<?php

class controllerLogin {
    public function index() {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        $success = $_SESSION['success'] ?? '';

        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

        $userModel = new User();
        $_SESSION['documentTypes'] = $userModel->getDocumentTypes();

        include 'views/Auth/login.php';
    }

    public function login_validation($data) {
        $errors = [];
        $email = trim($data['Email'] ?? '');
        $contraseña = $data['Contraseña'] ?? '';

        if ($email === '') {
            $errors['Email'] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = 'El email no es válido.';
        }

        if ($contraseña === '') {
            $errors['Contraseña'] = 'La contraseña es obligatoria.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['Email' => $email];
            header('Location: index.php?action=login');
            exit;
        }

        try {
            $userModel = new User();
            $user = $userModel->getByEmail($email);

            if ($user && password_verify($contraseña, $user['contraseña'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['nombre'] . ' ' . $user['apellido'];
                $_SESSION['user_email'] = $user['email']; // guardar email en sesión
                header('Location: index.php');
                exit;
            } else {
                $errors['Database'] = 'Email o contraseña incorrectos.';
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['Email' => $email];
                header('Location: index.php?action=login');
                exit;
            }
        } catch (Exception $e) {
            $errors['Database'] = 'Error de base de datos: ' . $e->getMessage();
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['Email' => $email];
            header('Location: index.php?action=login');
            exit;
        }
    }
}
?>
