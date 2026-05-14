<!DOCTYPE html>
<html lang="es">

<?php
$documents = $_SESSION['documentTypes'] ?? [];    
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <title>Iniciar Sesión</title>
</head>

<body>
    <div class="login-container">
        <?php if (!empty($errors['Database'])): ?>
            <div class="message-error"><strong><?php echo htmlspecialchars($errors['Database']); ?></strong></div>
        <?php endif; ?>

        <div class="login-card">
            <div class="card-header">
                <h1>Iniciar Sesión</h1>
            </div>
            <form id="loginForm" action="index.php?action=login_validation" method="post" novalidate class="form-content">                  
                <div class="form-group">
                    <label for="Email" class="form-label">Correo electrónico</label>
                    <input type="email" name="Email" id="Email" class="form-control" placeholder="Ingrese su correo"
                        value="<?php echo htmlspecialchars($old['Email'] ?? ''); ?>">
                    <?php if (isset($errors['Email'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Contraseña" class="form-label">Contraseña</label>
                    <input type="password" name="Contraseña" id="Contraseña" class="form-control"
                        placeholder="Ingrese su contraseña">
                    <?php if (isset($errors['Contraseña'])): ?>
                        <div class="message-error"><?php echo htmlspecialchars($errors['Contraseña']); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" id="btnEnviar" class="btn-primary">Iniciar Sesión</button>
            </form>
            <p>¿No tienes cuenta? <a href="index.php?action=register">Regístrate</a></p>
        </div>
    </div>
    <script src="js/validaciones.js"></script>
</body>

</html>
