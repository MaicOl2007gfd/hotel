// ========== HELPERS ==========
function getBasePath() {
    return window.location.pathname
        .replace(/\/views\/Deshboard\/[\w\-\.]+$/, '')
        .replace(/\/index\.php$/, '')
        .replace(/\/$/, '');
}

function getUrl(action, id) {
    const base = `${window.location.origin}${getBasePath()}/index.php?action=${action}`;
    return id !== undefined ? `${base}&id=${id}` : base;
}

// ========== MODAL RESERVA (home) ==========
document.addEventListener('DOMContentLoaded', () => {

    const reserveBtn = document.getElementById('reserveBtn');
    if (reserveBtn) {
        reserveBtn.addEventListener('click', () => {
            document.getElementById('reservationModal').style.display = 'block';
        });
    }

    const closeBtn = document.querySelector('.close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            document.getElementById('reservationModal').style.display = 'none';
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });

    const modalTipo = document.getElementById('modal_tipo');
    if (modalTipo) {
        modalTipo.addEventListener('change', function () {
            cargarHabitaciones(this.value, 'modal_habitacion_id', 'modal-habitacion-container');
        });
    }

    const homeTipo = document.getElementById('tipo_habitacion_reserva');
    if (homeTipo) {
        homeTipo.addEventListener('change', function () {
            cargarHabitaciones(this.value, 'habitacion_id_home', 'habitacion-field-home');
        });
    }

    const formsReserva = document.querySelectorAll('form[action*="crear_reserva"]');
    formsReserva.forEach(form => {
        const checkinInput  = form.querySelector('input[name="checkin"]');
        const checkoutInput = form.querySelector('input[name="checkout"]');

        if (checkinInput && checkoutInput) {
            checkinInput.addEventListener('change', function () {
                const min = new Date(this.value);
                min.setDate(min.getDate() + 1);
                checkoutInput.min = min.toISOString().split('T')[0];
            });
        }

        form.addEventListener('submit', (e) => {
            const checkin    = checkinInput?.value;
            const checkout   = checkoutInput?.value;
            const tipo       = form.querySelector('select[name*="tipo_habitacion"]')?.value;
            const habitacion = form.querySelector('select[name="habitacion_id"]')?.value;
            const huespedes  = form.querySelector('input[name="huespedes"]')?.value
                            || form.querySelector('select[name="huespedes"]')?.value;

            if (!tipo)                                   { e.preventDefault(); alert('Por favor selecciona un tipo de habitación'); return; }
            if (habitacion !== undefined && !habitacion) { e.preventDefault(); alert('Por favor selecciona una habitación'); return; }
            if (!checkin || !checkout)                   { e.preventDefault(); alert('Por favor completa las fechas'); return; }

            const fechaCheckin  = new Date(checkin);
            const fechaCheckout = new Date(checkout);
            const fechaMin      = new Date(fechaCheckin);
            fechaMin.setDate(fechaMin.getDate() + 1);

            if (fechaCheckout < fechaMin) { e.preventDefault(); alert('La fecha de salida debe ser al menos un día después de la llegada'); return; }
            if (huespedes && (parseInt(huespedes) < 1 || parseInt(huespedes) > 5)) { e.preventDefault(); alert('El número de huéspedes debe estar entre 1 y 5'); return; }
        });
    });

    // MIS RESERVAS
    cargarMisReservas();

    const editTipo = document.getElementById('edit_tipo_habitacion');
    if (editTipo) {
        editTipo.addEventListener('change', function () {
            cargarHabitacionesParaEditar(this.value, null);
        });
    }

    const editCheckin  = document.getElementById('edit_checkin');
    const editCheckout = document.getElementById('edit_checkout');
    if (editCheckin && editCheckout) {
        editCheckin.addEventListener('change', function () {
            const min = new Date(this.value);
            min.setDate(min.getDate() + 1);
            editCheckout.min = min.toISOString().split('T')[0];
        });
    }

    const editForm = document.getElementById('editReservaForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('edit_reserva_id').value;
            this.action = getUrl('actualizar_reserva', id);
            this.submit();
        });
    }
});

// ========== CARGAR HABITACIONES ==========
function cargarHabitaciones(tipo, selectHabId, containerId) {
    const container = document.getElementById(containerId);
    const selectHab = document.getElementById(selectHabId);

    selectHab.innerHTML = '<option value="">-- Selecciona una habitación --</option>';
    selectHab.disabled  = true;

    if (!tipo) { container.style.display = 'none'; return; }

    fetch(getUrl('getRoomsByType') + `&tipo=${encodeURIComponent(tipo)}`)
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                selectHab.innerHTML = '<option value="">No hay habitaciones disponibles</option>';
            } else {
                data.forEach(hab => {
                    selectHab.innerHTML += `<option value="${hab.id}">${hab.numero || hab.nombre || 'Habitación'}</option>`;
                });
                selectHab.disabled = false;
            }
            container.style.display = 'block';
        })
        .catch(() => {
            selectHab.innerHTML = '<option value="">Error al cargar habitaciones</option>';
            container.style.display = 'block';
        });
}

function abrirModal(tipo) {
    document.getElementById('reservationModal').style.display = 'block';
    const select = document.getElementById('modal_tipo');
    if (select) { select.value = tipo; select.dispatchEvent(new Event('change')); }
}

// ========== MIS RESERVAS ==========
function cargarMisReservas() {
    const container = document.getElementById('mis-reservas-container');
    if (!container) return;

    fetch(getUrl('get_mis_reservas'))
        .then(res => res.json())
        .then(reservas => {
            if (!reservas || reservas.length === 0) {
                container.innerHTML = '<p class="no-reservas">No tienes reservas realizadas.</p>';
                return;
            }

            function formatFecha(f) {
                if (!f) return '-';
                const meses = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                const d = new Date(f + 'T00:00:00');
                return d.getDate() + ' ' + meses[d.getMonth()] + ' ' + d.getFullYear();
            }
            function formatPrecio(p) {
                return '$' + Number(p).toLocaleString('es-CO');
            }
            function calcNoches(inicio, fin) {
                const d1 = new Date(inicio + 'T00:00:00'), d2 = new Date(fin + 'T00:00:00');
                return Math.round((d2 - d1) / 86400000);
            }

            let html = `
            <table class="reservas-tabla">
                <thead>
                    <tr>
                        <th>HABITACIÓN</th>
                        <th>FECHAS</th>
                        <th>PERSONAS</th>
                        <th>TOTAL</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
            `;

            reservas.forEach(reserva => {
                const noches = calcNoches(reserva.fecha_inicio, reserva.fecha_final);
                const estado = reserva.estado_id == 1 ? 'ACTIVA' : 'CANCELADA';
                const estadoClass = reserva.estado_id == 1 ? 'estado-activa' : 'estado-cancelada';
                html += `
                <tr class="reserva-fila">
                    <td class="col-habitacion">
                        <div class="hab-info">
                            <div class="hab-img-placeholder">🛏️</div>
                            <strong>Habitación ${reserva.numero || reserva.habitacion_id}</strong>
                        </div>
                    </td>
                    <td class="col-fechas">
                        <div class="fecha-bloque">
                            <span class="fecha-label">ENTRADA</span>
                            <span class="fecha-valor">${formatFecha(reserva.fecha_inicio)}</span>
                            <span class="fecha-arrow">→</span>
                            <span class="fecha-label">SALIDA</span>
                            <span class="fecha-valor">${formatFecha(reserva.fecha_final)}</span>
                            <span class="noches-badge">${noches} noches</span>
                        </div>
                    </td>
                    <td class="col-personas">
                        <span class="personas-txt">${'👤'.repeat(Math.min(reserva.n_personas,4))} ${reserva.n_personas}</span>
                    </td>
                    <td class="col-total">
                        <strong>${formatPrecio(reserva.precio)}</strong>
                    </td>
                    <td class="col-estado">
                        <span class="estado-badge ${estadoClass}">${estado}</span>
                    </td>
                    <td class="col-acciones">
                        <div class="acciones-btns">
                            <button class="accion-btn btn-ac-editar" title="Editar" onclick="abrirModalEditar(${reserva.id})">✏️</button>
                            <button class="accion-btn btn-ac-pdf" title="Descargar PDF" onclick="descargarPDF(${reserva.id})">📄</button>
                            <button class="accion-btn btn-ac-cancelar" title="Cancelar" onclick="desactivarReserva(${reserva.id})">🚫</button>
                        </div>
                    </td>
                </tr>
                `;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error cargando reservas:', error);
            container.innerHTML = '<p class="no-reservas">Error al cargar las reservas.</p>';
        });
}

// ========== MODAL EDITAR ==========
function abrirModalEditar(id) {
    fetch(getUrl('obtener_reserva', id))
        .then(res => res.json())
        .then(reserva => {
            if (!reserva || Object.keys(reserva).length === 0) { alert('No se encontró la reserva'); return; }

            document.getElementById('edit_reserva_id').value = reserva.id;
            document.getElementById('edit_checkin').value    = reserva.fecha_inicio;
            document.getElementById('edit_checkout').value   = reserva.fecha_final;
            document.getElementById('edit_huespedes').value  = reserva.n_personas;

            const tipoSelect = document.getElementById('edit_tipo_habitacion');
            tipoSelect.value = reserva.descripcion || reserva.tipo_habitacion || '';
            if (tipoSelect.value) cargarHabitacionesParaEditar(tipoSelect.value, reserva.habitacion_id);

            document.getElementById('editReservaModal').style.display = 'block';
        })
        .catch(() => alert('Error al cargar la reserva'));
}

function cargarHabitacionesParaEditar(tipo, habitacionId) {
    const container = document.getElementById('edit-habitacion-container');
    const selectHab = document.getElementById('edit_habitacion_id');

    selectHab.innerHTML = '<option value="">-- Selecciona --</option>';

    fetch(getUrl('getRoomsByType') + `&tipo=${encodeURIComponent(tipo)}`)
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                data.forEach(hab => {
                    const selected = hab.id == habitacionId ? 'selected' : '';
                    selectHab.innerHTML += `<option value="${hab.id}" ${selected}>${hab.numero || hab.nombre || 'Habitación'}</option>`;
                });
            }
            selectHab.disabled = false;
            container.style.display = 'block';
        })
        .catch(() => {});
}

function cerrarModalEdit() {
    document.getElementById('editReservaModal').style.display = 'none';
}

// ========== MODAL ELIMINAR ==========
function desactivarReserva(id) {
    if (!id) return;
    fetch(getUrl('desactivar_reserva', id))
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.success) {
                alert('Reserva desactivada correctamente.');
                cargarMisReservas();
            } else {
                alert(data.message || 'Error al desactivar la reserva.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al desactivar la reserva.');
        });
}



// ========== DESCARGAR PDF ==========
function descargarPDF(id) {
    window.location.href = 'index.php?action=descargar_reserva_pdf&id=' + id;
}

function verPDF(id) {
    window.open(getUrl('descargar_reserva_pdf', id), '_blank');
}

// exportar a Excel
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