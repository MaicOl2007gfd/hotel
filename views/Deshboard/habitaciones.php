<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Gestión de Habitaciones - Hotel MYQ</title>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Habitaciones</h1>
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

            <!-- Formulario para crear habitación -->
            <section class="crear-habitacion">
                <h2>Crear Nueva Habitación</h2>
                <form action="index.php?action=crear_habitacion" method="post">
                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <input type="text" id="tipo" name="tipo" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['tipo'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['tipo'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['tipo']); unset($_SESSION['errors']['tipo']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['nombre'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['nombre'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['nombre']); unset($_SESSION['errors']['nombre']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="capacidad">Capacidad:</label>
                        <input type="number" id="capacidad" name="capacidad" min="1" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['capacidad'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['capacidad'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['capacidad']); unset($_SESSION['errors']['capacidad']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio por noche:</label>
                        <input type="number" id="precio" name="precio" min="0" step="0.01" required
                               value="<?php echo htmlspecialchars($_SESSION['old']['precio'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['precio'])): ?>
                            <div class="message-error"><?php echo htmlspecialchars($_SESSION['errors']['precio']); unset($_SESSION['errors']['precio']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($_SESSION['old']['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Crear Habitación</button>
                </form>
            </section>

            <!-- Lista de habitaciones -->
            <section class="lista-habitaciones">
                <h2>Habitaciones Disponibles</h2>
                <?php
                $habitaciones = $_SESSION['habitaciones'] ?? [];
                if (empty($habitaciones)):
                ?>
                    <p>No hay habitaciones disponibles.</p>
                <?php else: ?>
                    <div class="habitaciones-grid">
                        <?php foreach ($habitaciones as $habitacion): ?>
                            <div class="habitacion-card">
                                <h3><?php echo htmlspecialchars($habitacion['nombre']); ?> (<?php echo htmlspecialchars($habitacion['tipo']); ?>)</h3>
                                <p><strong>Capacidad:</strong> <?php echo htmlspecialchars($habitacion['capacidad']); ?> personas</p>
                                <p><strong>Precio:</strong> $<?php echo htmlspecialchars($habitacion['precio']); ?> por noche</p>
                                <p><?php echo htmlspecialchars($habitacion['descripcion']); ?></p>
                                <div class="acciones">
                                    <a href="index.php?action=editar_habitacion&id=<?php echo $habitacion['id']; ?>" class="btn-secondary">Editar</a>
                                    <a href="index.php?action=eliminar_habitacion&id=<?php echo $habitacion['id']; ?>" class="btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta habitación?')">Eliminar</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>