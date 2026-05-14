<?php
session_start();
include 'config/config.php';

// Modelos
include 'models/conexion.php';
include 'models/user.php';
include 'models/reserva.php';
include 'models/habitacion.php';

// Controladores
include 'controllers/controllerLogin.php';
include 'controllers/controllerRegister.php';
include 'controllers/controllerHabitacion.php';
include 'controllers/controllerReserva.php';
require_once 'controllers/controllerPDF.php';

if (isset($_GET['action'])) {

    $action = $_GET['action'];

    // Autenticación - Login
    if ($action == 'login') {
        $controller = new controllerLogin();
        $controller->index();
    }
    elseif ($action == 'login_validation') {
        $controller = new controllerLogin();
        $controller->login_validation($_POST);
    }
    elseif ($action == 'logout') {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // Autenticación - Registro
    elseif ($action == 'register') {
        $controller = new controllerRegister();
        $controller->index();
    }
    elseif ($action == 'validation') {
        $controller = new controllerRegister();
        $controller->validation($_POST);
    }

    // Habitaciones
    elseif ($action == 'habitaciones') {
        $controller = new controllerHabitacion();
        $controller->index();
    }
    elseif ($action == 'crear_habitacion') {
        $controller = new controllerHabitacion();
        $controller->crear($_POST);
    }
    elseif ($action == 'getRoomsByType') {
        $controller = new controllerHabitacion();
        $controller->getRoomsByType();
    }
    // NOTA: controllerHabitacion no tiene editar_habitacion o eliminar_habitacion en el index.php original, 
    // pero si las requiere, deberían llamarse acá.

    // Reservas
    elseif ($action == 'reserva') {
        $controller = new controllerReserva();
        $controller->reserva();
    }
    elseif ($action == 'crear_reserva') {
        $controller = new controllerReserva();
        $controller->crear_reserva($_POST);
    }
    elseif ($action == 'mis_reservas') {
        $controller = new controllerReserva();
        $controller->vista_mis_reservas();
    }
    elseif ($action == 'get_mis_reservas') {
        $controller = new controllerReserva();
        $controller->mis_reservas();
    }
    elseif ($action == 'obtener_reserva') {
        $controller = new controllerReserva();
        $controller->obtener_reserva($_GET['id']);
    }
    elseif ($action == 'editar_reserva') {
        // En el archivo original, la ruta editar_reserva llama a `editar` que parece de habitacion
        // Pero `actualizar_reserva` es la que actualiza reservas. Lo redirigiremos si es necesario, 
        // o llamaremos al metodo correspondiente.
        $controller = new controllerReserva();
        $controller->actualizar_reserva($_GET['id'], $_POST);
    }
    elseif ($action == 'actualizar_reserva') {
        $controller = new controllerReserva();
        $controller->actualizar_reserva($_GET['id'], $_POST);
    }
    elseif ($action == 'eliminar_reserva') {
        $controller = new controllerReserva();
        $controller->eliminar_reserva($_GET['id']);
    }
    elseif ($action == 'desactivar_reserva') {
        $controller = new controllerReserva();
        $controller->desactivar_reserva($_GET['id'] ?? '');
    }

    // PDF
    elseif ($action == 'descargar_reserva_pdf') {
        $controller = new controllerPDF();
        $controller->descargarReservaPDF($_GET['id'] ?? '');
    }

    exit;
}

// Cargar categorías de habitaciones para home.php
if (!isset($_SESSION['categorias'])) {
    $habitacionModel = new Habitacion();
    $_SESSION['categorias'] = $habitacionModel->getCategories();
}

include 'views/Deshboard/home.php';
?>