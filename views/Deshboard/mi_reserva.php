<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Hotel MYQ</title>
    <link rel="stylesheet" href="css/stile.css">
    <link rel="stylesheet" href="css/mi_reserva.css">
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
            <a href="index.php">Inicio</a>
            <a href="index.php#habitaciones">Habitaciones</a>
            <a href="index.php#reservas">Reservas</a>
            <a href="index.php?action=mis_reservas" class="active">Mis Reservas</a>
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

<div class="mis-reservas-page">

    <div class="mis-reservas-header">
        <h2>📋 Mis Reservas</h2>
        <div class="header-btns">
            <a href="index.php#reservas" class="btn-volver">+ Nueva Reserva</a>
            <button class="btn-reporte-excel" onclick="exportarExcel()">&#128196; Descargar Excel</button>
        </div>
    </div>

    <?php if (!empty($_SESSION['reserva_success'])): ?>
        <div class="alert-success">✅ <?php echo htmlspecialchars($_SESSION['reserva_success']); unset($_SESSION['reserva_success']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['reserva_error'])): ?>
        <div class="alert-error">❌ <?php echo htmlspecialchars($_SESSION['reserva_error']); unset($_SESSION['reserva_error']); ?></div>
    <?php endif; ?>

    <div id="mis-reservas-container">
        <p class="no-reservas">Cargando reservas...</p>
    </div>

</div>

<!-- MODAL EDITAR -->
<div id="editReservaModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalEdit()">&times;</span>
        <h2>✏️ Editar Reserva</h2>
        <form id="editReservaForm" method="post">
            <input type="hidden" name="reserva_id" id="edit_reserva_id">

            <label>Tipo de habitación</label>
            <select name="tipo_habitacion" id="edit_tipo_habitacion" required>
                <option value="">-- Selecciona --</option>
                <?php
                $categorias = $_SESSION['categorias'] ?? [];
                foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['descripcion']); ?>">
                        <?php echo htmlspecialchars($cat['descripcion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="edit-habitacion-container" style="display:none;">
                <label>Habitación</label>
                <select name="habitacion_id" id="edit_habitacion_id" required disabled>
                    <option value="">-- Selecciona --</option>
                </select>
            </div>

            <label>Fecha de llegada</label>
            <input type="date" name="checkin" id="edit_checkin" required min="<?php echo date('Y-m-d'); ?>">

            <label>Fecha de salida</label>
            <input type="date" name="checkout" id="edit_checkout" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">

            <label>Huéspedes</label>
            <select name="huespedes" id="edit_huespedes" required>
                <option value="1">1 huésped</option>
                <option value="2">2 huéspedes</option>
                <option value="3">3 huéspedes</option>
                <option value="4">4 huéspedes</option>
                <option value="5">5 huéspedes</option>
            </select>

            <div class="modal-buttons">
                <button type="submit" class="btn-guardar">Guardar cambios</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModalEdit()">Cancelar</button>
            </div>
        </form>
    </div>
</div>



<script src="js/reservas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script>
    async function exportarExcel() {
    try {
        const res = await fetch('index.php?action=get_mis_reservas');
        const reservas = await res.json();
 
        if (!reservas || reservas.length === 0) {
            alert('No tienes reservas para exportar.');
            return;
        }
 
        const meses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        function fmt(f) {
            if (!f) return '-';
            const d = new Date(f + 'T00:00:00');
            return d.getDate() + ' ' + meses[d.getMonth()] + ' ' + d.getFullYear();
        }
        function noches(a, b) {
            return Math.round((new Date(b+'T00:00:00') - new Date(a+'T00:00:00')) / 86400000);
        }
 
        const workbook  = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Mis Reservas');
 
        // ── Ancho de columnas ──
        worksheet.columns = [
            { header: 'HABITACIÓN',   key: 'hab',    width: 20 },
            { header: 'CHECK-IN',     key: 'cin',    width: 16 },
            { header: 'CHECK-OUT',    key: 'cout',   width: 16 },
            { header: 'NOCHES',       key: 'noch',   width: 10 },
            { header: 'HUÉSPEDES',    key: 'hues',   width: 12 },
            { header: 'PRECIO TOTAL', key: 'precio', width: 18 },
            { header: 'ESTADO',       key: 'estado', width: 14 },
        ];
 
        // ── Estilo encabezado ──
        const headerRow = worksheet.getRow(1);
        headerRow.eachCell(cell => {
            cell.fill = {
                type: 'pattern', pattern: 'solid',
                fgColor: { argb: 'FF042C53' }   // azul oscuro
            };
            cell.font        = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
            cell.alignment   = { vertical: 'middle', horizontal: 'center' };
            cell.border      = {
                bottom: { style: 'thin', color: { argb: 'FFFFFFFF' } }
            };
        });
        headerRow.height = 22;
 
        // ── Filas de datos ──
        reservas.forEach((r, i) => {
            const row = worksheet.addRow({
                hab:    'Habitación ' + (r.numero || r.habitacion_id),
                cin:    fmt(r.fecha_inicio),
                cout:   fmt(r.fecha_final),
                noch:   noches(r.fecha_inicio, r.fecha_final),
                hues:   r.n_personas,
                precio: '$' + Number(r.precio).toLocaleString('es-CO'),
                estado: r.estado_id == 1 ? 'Confirmada' : 'Cancelada',
            });
 
            // Alternar color de filas
            const bg = i % 2 === 0 ? 'FFF5F5F5' : 'FFFFFFFF';
            row.eachCell(cell => {
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: bg } };
                cell.alignment = { vertical: 'middle', horizontal: 'center' };
                cell.border    = {
                    top:    { style: 'thin', color: { argb: 'FFD0D0D0' } },
                    bottom: { style: 'thin', color: { argb: 'FFD0D0D0' } },
                    left:   { style: 'thin', color: { argb: 'FFD0D0D0' } },
                    right:  { style: 'thin', color: { argb: 'FFD0D0D0' } },
                };
            });
 
            // Color celda ESTADO
            const estadoCell = row.getCell('estado');
            if (r.estado_id == 1) {
                estadoCell.font = { bold: true, color: { argb: 'FF27500A' } };
                estadoCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEAF3DE' } };
            } else {
                estadoCell.font = { bold: true, color: { argb: 'FF7B1111' } };
                estadoCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFCEBEB' } };
            }
 
            row.height = 20;
        });
 
        // ── Descargar ──
        const buffer = await workbook.xlsx.writeBuffer();
        const blob   = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url    = URL.createObjectURL(blob);
        const a      = document.createElement('a');
        a.href       = url;
        a.download   = 'mis_reservas.xlsx';
        a.click();
        URL.revokeObjectURL(url);
 
    } catch (e) {
        alert('Error al exportar las reservas.');
        console.error(e);
    }
}
</script>



</body>
</html>