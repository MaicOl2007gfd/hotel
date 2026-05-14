<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Paraiso</title>
    <link rel="stylesheet" href="css/stile.css">
    <link rel="stylesheet" href="css/huespedes.css">
</head>

<body>

    <header>
        <div class="header-inner">
            <h1>Hotel MYQ</h1>
            <p>Comodidad y descanso garantizados</p>
        </div>
    </header>

    <nav>
        <div class="nav-inner">
            <div class="nav-links">
                <a href="#">Inicio</a>
                <a href="#habitaciones">Habitaciones</a>
                <a href="#servicios">Servicios</a>
                <a href="#galeria">Galeria</a>
                <a href="index.php?action=mis_reservas">Mis Reservas</a>
            </div>
            <div class="nav-auth">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="nav-user">🧑 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="index.php?action=logout" class="btn-nav-logout">Cerrar sesión</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-nav-login">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <h2>Bienvenido a tu lugar de descanso</h2>
            <p>Disfruta de la mejor experiencia con nosotros en el corazón del paraíso</p>
            <div class="hero-btns">
                <button id="reserveBtn" class="btn-hero-primary">Reservar ahora</button>
            </div>
        </div>
    </section>

    <!-- HABITACIONES -->
    <section id="habitaciones" class="section">
        <h2>Nuestras Habitaciones</h2>
        <p class="section-subtitle">Elige la experiencia perfecta para ti</p>
        <div class="cards">
            <?php
            $categorias = $_SESSION['categorias'] ?? [];
            $randomImages = [1, 2, 3, 4, 5, 6];
            $index = 0;
            foreach ($categorias as $cat):
                $featured = ($index === 0) ? ' featured' : '';
                $imageNum = $randomImages[$index % count($randomImages)];
                $index++;
            ?>
            <div class="card<?php echo $featured; ?>">
                <div class="card-img-wrap">
                    <img src="https://picsum.photos/300/200?random=<?php echo $imageNum; ?>" alt="<?php echo htmlspecialchars($cat['descripcion']); ?>">
                    <span class="card-badge<?php echo $featured ? ' popular' : ''; ?>">Desde $80/noche<?php echo $featured ? '? Popular' : ''; ?></span>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($cat['descripcion']); ?></h3>
                    <p>Disfruta de una experiencia inolvidable en nuestras habitaciones.</p>
                    <button class="btn-card" onclick="abrirModal('<?php echo htmlspecialchars($cat['descripcion']); ?>')">Reservar</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="section alt">
        <h2>Nuestros Servicios</h2>
        <p class="section-subtitle">Todo lo que necesitas en un solo lugar</p>
        <div class="servicios">
            <div class="servicio">
                <span>??</span>
                <p>WiFi gratis</p>
            </div>
            <div class="servicio">
                <span>??</span>
                <p>Piscina</p>
            </div>
            <div class="servicio">
                <span>???</span>
                <p>Restaurante</p>
            </div>
            <div class="servicio">
                <span>??</span>
                <p>Parqueadero</p>
            </div>
            <div class="servicio">
                <span>??</span>
                <p>Spa</p>
            </div>
            <div class="servicio">
                <span>???</span>
                <p>Gimnasio</p>
            </div>
        </div>
    </section>

    <!-- GALERIA -->
    <section id="galeria" class="section">
        <h2>Galeria</h2>
        <p class="section-subtitle">Conoce nuestras instalaciones</p>
        <div class="galeria">
            <img src="https://picsum.photos/400/280?random=4" alt="Instalaciones">
            <img src="https://picsum.photos/400/280?random=5" alt="Instalaciones">
            <img src="https://picsum.photos/400/280?random=6" alt="Instalaciones">
            <img src="https://picsum.photos/400/280?random=7" alt="Instalaciones">
            <img src="https://picsum.photos/400/280?random=8" alt="Instalaciones">
            <img src="https://picsum.photos/400/280?random=9" alt="Instalaciones">
        </div>
    </section>

    <!-- RESERVAS -->
    <section id="reservas" class="section reservas-section">
        <h2>Haz tu Reserva</h2>
        <p class="section-subtitle" style="color:rgba(255,255,255,0.65)">Elige tus fechas y disfruta una estadia inolvidable</p>

        <?php if (!empty($_SESSION['reserva_success'])): ?>
            <div class="alert-success">? <?php echo htmlspecialchars($_SESSION['reserva_success']); unset($_SESSION['reserva_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['reserva_error'])): ?>
            <div class="alert-error">? <?php echo htmlspecialchars($_SESSION['reserva_error']); unset($_SESSION['reserva_error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
        <form class="reserva-form" action="index.php?action=crear_reserva" method="post">
            <div class="reserva-fields">
                <div class="reserva-field">
                    <label>Tipo de habitación</label>
                    <select name="tipo_habitacion" id="tipo_habitacion_reserva" required>
                        <option value="">-- Selecciona --</option>
                        <?php
                        $categorias = $_SESSION['categorias'] ?? [];
                        foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['descripcion']); ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="reserva-field" id="habitacion-field-home" style="display: none;">
                    <label>Habitación</label>
                    <select name="habitacion_id" id="habitacion_id_home" disabled required>
                        <option value="">Seleccione una habitación</option>
                    </select>
                </div>
                <div class="reserva-field">
                    <label>Fecha de llegada</label>
                    <input type="date" name="checkin" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="reserva-field">
                    <label>Fecha de salida</label>
                    <input type="date" name="checkout" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>
                <div class="reserva-field">
                    <label>Huéspedes</label>
                    <select name="huespedes" required>
                        <option value="1">1 huésped</option>
                        <option value="2">2 huéspedes</option>
                        <option value="3">3 huéspedes</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-reserva">Confirmar Reserva</button>
        </form>
        <?php else: ?>
            <div class="reserva-login-aviso">
                <p>Para hacer una reserva, primero debes <a href="index.php?action=login">iniciar sesión</a>.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- MODAL DE RESERVA -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reservar habitación</h2>
            <form action="index.php?action=crear_reserva" method="post">
                <label>Tipo de habitación</label>
                <select name="tipo_habitacion" id="modal_tipo" required>
                    <option value=""> Selecciona </option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['descripcion']); ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="modal-habitacion-container" style="display:none;">
                    <label>Número de habitación</label>
                    <select name="habitacion_id" id="modal_habitacion_id" required >
                        <option value="">Seleccione una habitación</option>
                    </select>
                </div>
                <label>Fecha de llegada</label>
                <input type="date" name="checkin" required min="<?php echo date('Y-m-d'); ?>">
                <label>Fecha de salida</label>
                <input type="date" name="checkout" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                <label>Huéspedes</label>
                    <div class="selector-huespedes">

    <!-- Huéspedes -->
    <div class="fila">
        <div>
            <strong>Huéspedes</strong>
            <p>Huéspedes</p>
        </div>

        <div class="controles">
            <button type="button" onclick="cambiar('adultos', -1)">-</button>
            <span id="adultos">1</span>
            <button type="button" onclick="cambiar('adultos', 1)">+</button>
        </div>
    </div>
    <input type="hidden" name="huespedes" id="total_huespedes" value="1">
</div>
                <button type="submit" class="btn-reserva">Confirmar reserva</button>
                
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-inner">
            <p class="footer-logo">Hotel MYQ</p>
            <p class="footer-tagline">Comodidad y descanso garantizados</p>
            <div class="footer-links">
                <a href="#servicios">Servicios</a>
                <a href="#galeria">Galería</a>
                <a href="index.php?action=mis_reservas">Mis Reservas</a>
            </div>
            <p class="footer-copy">&copy; 2026 Hotel Paraíso. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/reservas.js"></script>
    <script src="js/huespedes.js"></script>
</body>
</html>
