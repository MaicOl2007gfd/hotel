<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Gestión de Reservas - Hotel MYQ</title>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Reservas</h1>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="index.php?action=logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert-success">✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['errors']['Database'])): ?>
                <div class="alert-error">❌ <?php echo htmlspecialchars($_SESSION['errors']['Database']); unset($_SESSION['errors']['Database']); ?></div>
            <?php endif; ?>

            <!-- Formulario para crear reserva -->
            <section class="crear-reserva">
                <h2>Crear Nueva Reserva</h2>
                <form action="index.php?action=crear_reserva" method="post">
                    <div class="form-group">
                        <label for="tipo_habitacion">Tipo de Habitación:</label>
                        <select id="tipo_habitacion" name="tipo_habitacion" required>
                            <option value="">Seleccione un tipo</option>
                            <?php
                            $categorias = $_SESSION['categorias'] ?? [];
                            foreach ($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['descripcion']); ?>" <?php echo ($_SESSION['old']['tipo_habitacion'] ?? '') == $cat['descripcion'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['descripcion']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['tipo_habitacion'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['tipo_habitacion']); unset($_SESSION['errors']['tipo_habitacion']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" id="habitacion-container" style="display: none;">
                        <label for="habitacion_id">Habitación:</label>
                        <select id="habitacion_id" name="habitacion_id" disabled required>
                            <option value="">Seleccione una habitación</option>
                        </select>
                        <?php if (isset($_SESSION['errors']['habitacion_id'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['habitacion_id']); unset($_SESSION['errors']['habitacion_id']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="fecha_checkin">Fecha de Check-in:</label>
                        <input type="date" id="fecha_checkin" name="fecha_checkin" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['fecha_checkin'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['fecha_checkin'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['fecha_checkin']); unset($_SESSION['errors']['fecha_checkin']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="fecha_checkout">Fecha de Check-out:</label>
                        <input type="date" id="fecha_checkout" name="fecha_checkout" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['fecha_checkout'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['fecha_checkout'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['fecha_checkout']); unset($_SESSION['errors']['fecha_checkout']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="huespedes">Número de Huéspedes:</label>
                        <input type="number" id="huespedes" name="huespedes" min="1" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['huespedes'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['huespedes'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['huespedes']); unset($_SESSION['errors']['huespedes']); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-primary">Crear Reserva</button>
                </form>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var oldHabitacionId = '<?php echo htmlspecialchars($_SESSION['old']['habitacion_id'] ?? ''); ?>';

            function loadHabitaciones(tipo) {
                $('#habitacion_id').empty();
                $('#habitacion_id').append('<option value="">Seleccione una habitación</option>');

                if (!tipo) {
                    $('#habitacion-container').hide();
                    $('#habitacion_id').prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: 'index.php?action=getRoomsByType&tipo=' + encodeURIComponent(tipo),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#habitacion-container').show();
                        $('#habitacion_id').prop('disabled', false);
                        if (data.length === 0) {
                            $('#habitacion_id').append('<option value="">No hay habitaciones disponibles</option>');
                            return;
                        }
                        $.each(data, function(index, habitacion) {
                            var option = $('<option>').val(habitacion.id).text(habitacion.numero);
                            if (habitacion.id == oldHabitacionId) {
                                option.prop('selected', true);
                            }
                            $('#habitacion_id').append(option);
                        });
                    },
                    error: function() {
                        alert('Error al cargar habitaciones.');
                    }
                });
            }

            $('#tipo_habitacion').change(function() {
                var tipo = $(this).val();
                loadHabitaciones(tipo);
            });

            if ($('#tipo_habitacion').val()) {
                loadHabitaciones($('#tipo_habitacion').val());
            } else {
                $('#habitacion-container').hide();
                $('#habitacion_id').prop('disabled', true);
            }
        });
    </script>
</body>
</html>
