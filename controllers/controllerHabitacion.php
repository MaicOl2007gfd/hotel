<?php

class controllerHabitacion {

    public function index() {
        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

        $habitacionModel = new Habitacion();
        $habitaciones = $habitacionModel->getAll();

        $_SESSION['habitaciones'] = $habitaciones;
        include 'views/Deshboard/habitaciones.php';
    }

    public function crear($data) {
        $errors = [];

        $numero       = trim($data['numero'] ?? '');
        $descripcion  = trim($data['descripcion'] ?? '');
        $numero_camas = trim($data['numero_camas'] ?? '');
        $precio       = trim($data['precio'] ?? '');
        $max_persona  = trim($data['max_persona'] ?? '');

        if (empty($numero))       { $errors['numero'] = 'El número de habitación es obligatorio.'; }
        if (empty($descripcion))  { $errors['descripcion'] = 'El tipo de habitación es obligatorio.'; }
        if (empty($numero_camas) || !is_numeric($numero_camas)) { $errors['numero_camas'] = 'Número de camas inválido.'; }
        if (empty($precio) || !is_numeric($precio))             { $errors['precio'] = 'Precio inválido.'; }
        if (empty($max_persona) || !is_numeric($max_persona))   { $errors['max_persona'] = 'Capacidad inválida.'; }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?action=habitaciones');
            exit;
        }

        try {
            $habitacionModel = new Habitacion();
            if ($habitacionModel->crear($numero, $descripcion, $numero_camas, $precio, $max_persona)) {
                $_SESSION['success'] = 'Habitación creada correctamente.';
            } else {
                $_SESSION['errors']['Database'] = 'No se pudo crear la habitación.';
            }
        } catch (Exception $e) {
            $_SESSION['errors']['Database'] = 'Error de base de datos: ' . $e->getMessage();
        }

        header('Location: index.php?action=habitaciones');
        exit;
    }

    public function editar($id, $data) {
        $habitacionModel = new Habitacion();

        if ($habitacionModel->editar(
            $id,
            $data['numero'],
            $data['numero_camas'],
            $data['descripcion'],
            $data['precio'],
            $data['max_persona']
        )) {
            $_SESSION['success'] = 'Habitación actualizada.';
        } else {
            $_SESSION['errors']['Database'] = 'No se pudo actualizar la habitación.';
        }

        header('Location: index.php?action=habitaciones');
        exit;
    }

    public function eliminar($id) {
        $habitacionModel = new Habitacion();

        if ($habitacionModel->eliminar($id)) {
            $_SESSION['success'] = 'Habitación eliminada.';
        } else {
            $_SESSION['errors']['Database'] = 'No se pudo eliminar la habitación.';
        }

        header('Location: index.php?action=habitaciones');
        exit;
    }

    public function getCategories() {
        $habitacionModel = new Habitacion();
        return $habitacionModel->getCategories();
    }

    public function getRoomsByType() {
        $tipo = $_GET['tipo'] ?? '';
        header('Content-Type: application/json');

        if (empty($tipo)) { echo json_encode([]); exit; }

        $habitacionModel = new Habitacion();
        $habitaciones = $habitacionModel->getRoomsByType($tipo);
        echo json_encode($habitaciones);
        exit;
    }
}
?>