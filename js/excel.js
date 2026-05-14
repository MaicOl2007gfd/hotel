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