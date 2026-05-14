<!DOCTYPE html>
<html lang="es">

<?php
$documents = $_SESSION['documentTypes'] ?? [];    
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <title>Registro de Usuario</title>
</head>

<body>
    <div class="login-container">
        <?php if (!empty($errors['Database'])): ?>
            <div class="message-error"><strong><?php echo htmlspecialchars($errors['Database']); ?></strong></div>
        <?php endif; ?>

        <div class="login-card">
            <div class="card-header">
                <h1>Registrar Usuario</h1>
            </div>
            <form id="registerForm" action="index.php?action=validation" method="post" novalidate class="form-content">                  
                <label class="form-label">tipos de documento</label>
                    <select class="form-control" name="fav_language" id="documentType">
                    <?php foreach ($documents as $document): ?>
                        <option value="<?php echo htmlspecialchars($document['id']); ?>"><?php echo htmlspecialchars($document['nombre']); ?></option>
                    <?php endforeach; ?>
                    </select>
                <br>
                <br>

                <div class="form-group">
                    <label for="Cedula" class="form-label">Cedula</label>
                    <input type="int" name="Cedula" id="Cedula" class="form-control"
                        placeholder="Ingrese su documento"
                        value="<?php echo htmlspecialchars($old['Cedula'] ?? ''); ?>">
                    <?php if (isset($errors['Cedula'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Cedula']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Nombre" class="form-label">Nombre de usuario</label>
                    <input type="text" name="Nombre" id="Nombre" class="form-control"
                        placeholder="Ingrese su nombre de usuario"
                        value="<?php echo htmlspecialchars($old['Nombre'] ?? ''); ?>">
                    <?php if (isset($errors['Nombre'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Nombre']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Apellido" class="form-label">Apellido de usuario</label>
                    <input type="text" name="Apellido" id="Apellido" class="form-control"
                        placeholder="Ingrese su apellido de usuario"
                        value="<?php echo htmlspecialchars($old['Apellido'] ?? ''); ?>">
                    <?php if (isset($errors['Apellido'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Apellido']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Email" class="form-label">Correo electrónico</label>
                    <input type="email" name="Email" id="Email" class="form-control" placeholder="Ingrese su correo"
                        value="<?php echo htmlspecialchars($old['Email'] ?? ''); ?>">
                    <?php if (isset($errors['Email'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Contraseña1" class="form-label">Contraseña</label>
                    <input type="password" name="Contraseña1" id="Contraseña1" class="form-control"
                        placeholder="Ingrese su contraseña">
                    <?php if (isset($errors['Contraseña1'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Contraseña1']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Contraseña2" class="form-label">Confirmar contraseña</label>
                    <input type="password" name="Contraseña2" id="Contraseña2" class="form-control"
                        placeholder="Confirme su contraseña">
                    <?php if (isset($errors['Contraseña2'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Contraseña2']); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" id="btnEnviar" class="btn-primary">Registrarse</button>
            </form>
        </div>
    </div>
    <script src="js/validaciones.js"></script>
</body>

</html>
