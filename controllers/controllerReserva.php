<?php

class controllerReserva {
    public function reserva() {
        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
        
        $habitacionModel = new Habitacion();
        $categorias = $habitacionModel->getCategories();
        $_SESSION['categorias'] = $categorias;
        
        include 'views/Deshboard/reserva.php';
    }

    public function crear_reserva($data) {
        // Datos de la sesión y del formulario para crear una reserva.
        $usuario_id    = $_SESSION['user_id'];
        $habitacion_id = $data['habitacion_id'] ?? '';
        $fecha_inicio  = $data['checkin']  ?? $data['fecha_checkin']  ?? '';
        $fecha_final   = $data['checkout'] ?? $data['fecha_checkout'] ?? '';
        $n_personas    = $data['huespedes'] ?? '';

        $errors = [];

        // Validar campos requeridos y tipos de datos.
        if (empty($habitacion_id))           { $errors['habitacion_id'] = 'Seleccione una habitación.'; }
        elseif (!is_numeric($habitacion_id)) { $errors['habitacion_id'] = 'Habitación inválida.'; }
        if (empty($fecha_inicio))            { $errors['fecha_checkin'] = 'Fecha obligatoria.'; }
        if (empty($fecha_final))             { $errors['fecha_checkout'] = 'Fecha obligatoria.'; }

        if (!empty($fecha_inicio) && !empty($fecha_final)) {
            // La fecha de salida debe ser posterior a la fecha de entrada.
            if (strtotime($fecha_final) <= strtotime($fecha_inicio)) {
                $errors['fecha_checkout'] = 'La fecha de salida debe ser al menos un día después de la fecha de llegada.';
            }
        }

        if (empty($n_personas)) {
            $errors['huespedes'] = 'Número de huéspedes obligatorio.';
        } elseif (!is_numeric($n_personas) || $n_personas < 1 || $n_personas > 5) {
            $errors['huespedes'] = 'El número de huéspedes debe estar entre 1 y 5.';
        }

        $habitacionModel = new Habitacion();
        $habitacion = $habitacionModel->obtenerHabitacion($habitacion_id);
        if (!$habitacion) { $errors['habitacion_id'] = 'Habitación no encontrada.'; }

        // Si hubo errores, devolver al formulario de reserva con los valores anteriores.
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?action=reserva');
            exit;
        }

        $noches = (strtotime($fecha_final) - strtotime($fecha_inicio)) / 86400;
        $precio_total = $habitacion['precio'] * $noches;

        try {
            $reservaModel = new Reserva();
            if ($reservaModel->crear($usuario_id, $habitacion_id, $fecha_inicio, $fecha_final, $n_personas, $precio_total)) {
                $_SESSION['reserva_success'] = 'Reserva creada correctamente.';

                // Enviar correo de confirmación de reserva al usuario autenticado.
                if (isset($_SESSION['user_email'], $_SESSION['user_name'])) {
                    require_once __DIR__ . '/../utils/email.php';
                    enviarCorreo($_SESSION['user_email'], $_SESSION['user_name']);
                }
            } else {
                $_SESSION['errors']['Database'] = 'Error al crear la reserva.';
            }
        } catch (Exception $e) {
            $_SESSION['errors']['Database'] = 'Error de base de datos: ' . $e->getMessage();
        }

        header('Location: index.php?action=mis_reservas');
        exit;
    }

    public function vista_mis_reservas() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $habitacionModel = new Habitacion();
        $_SESSION['categorias'] = $habitacionModel->getCategories();
        include 'views/Deshboard/mi_reserva.php';
    }

    public function mis_reservas() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $reservaModel = new Reserva();
        $misReservas = $reservaModel->obtenerPorUsuario($_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode($misReservas);
        exit;
    }

    public function obtener_reserva($id) {
        $reservaModel = new Reserva();
        $reserva = $reservaModel->obtenerPorId($id);

        header('Content-Type: application/json');
        echo json_encode($reserva);
        exit;
    }

    public function actualizar_reserva($id, $data) {
        $habitacion_id = $data['habitacion_id'] ?? '';
        $fecha_inicio  = $data['checkin']  ?? '';
        $fecha_final   = $data['checkout'] ?? '';
        $n_personas    = $data['huespedes'] ?? '';

        $errors = [];

        if (empty($habitacion_id)) { $errors['habitacion_id'] = 'Seleccione una habitación.'; }
        if (empty($fecha_inicio))  { $errors['fecha_checkin'] = 'Fecha obligatoria.'; }
        if (empty($fecha_final))   { $errors['fecha_checkout'] = 'Fecha obligatoria.'; }

        if (!empty($fecha_inicio) && !empty($fecha_final)) {
            if (strtotime($fecha_final) <= strtotime($fecha_inicio)) {
                $errors['fecha_checkout'] = 'La fecha de salida debe ser al menos un día después.';
            }
        }

        if (empty($n_personas) || !is_numeric($n_personas)) {
            $errors['huespedes'] = 'Número de huéspedes inválido.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=mis_reservas');
            exit;
        }

        $habitacionModel = new Habitacion();
        $habitacion = $habitacionModel->obtenerHabitacion($habitacion_id);
        $noches = (strtotime($fecha_final) - strtotime($fecha_inicio)) / 86400;
        $precio_total = $habitacion ? $habitacion['precio'] * $noches : 0;

        try {
            $reservaModel = new Reserva();
            if ($reservaModel->editar($id, $habitacion_id, $fecha_inicio, $fecha_final, $n_personas, $precio_total)) {
                $_SESSION['reserva_success'] = 'Reserva actualizada correctamente.';
            } else {
                $_SESSION['reserva_error'] = 'No se pudo actualizar la reserva.';
            }
        } catch (Exception $e) {
            $_SESSION['reserva_error'] = 'Error de base de datos: ' . $e->getMessage();
        }

        header('Location: index.php?action=mis_reservas');
        exit;
    }

    public function eliminar_reserva($id) {
        try {
            $reservaModel = new Reserva();
            if ($reservaModel->eliminar($id)) {
                $_SESSION['reserva_success'] = 'Reserva eliminada correctamente.';
            } else {
                $_SESSION['reserva_error'] = 'No se pudo eliminar la reserva.';
            }
        } catch (Exception $e) {
            $_SESSION['reserva_error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: index.php?action=mis_reservas');
        exit;
    }

    public function desactivar_reserva($id) {
        header('Content-Type: application/json');

        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit;
        }

        try {
            $reservaModel = new Reserva();
            if ($reservaModel->cancelar($id)) {
                echo json_encode(['success' => true, 'message' => 'Reserva cancelada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo cancelar la reserva.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }

        exit;
    }
}
?>
