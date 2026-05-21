<?php

require_once 'libs/fpdf.php';

class controllerPDF {

    public function descargarReservaPDF($reservas_id) {

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $reservas_id = intval($reservas_id);

        if ($reservas_id <= 0) {
            $_SESSION['reserva_error'] = 'ID de reserva invalido.';
            header('Location: index.php?action=mis_reservas');
            exit;
        }

        $reservaModel = new Reserva();
        $reservas = $reservaModel->obtenerPorId($reservas_id);

        if (empty($reservas)) {
            $_SESSION['reserva_error'] = 'Reserva no encontrada (ID: ' . $reservas_id . ').';
            header('Location: index.php?action=mis_reservas');
            exit;
        }

        if (!empty($_SESSION['user_id']) && intval($reservas['user_id']) !== intval($_SESSION['user_id'])) {
            $_SESSION['reserva_error'] = 'No tienes permiso para descargar esta reserva';
            header('Location: index.php?action=mis_reservas');
            exit;
        }

        $noches = (strtotime($reservas['fecha_final']) - strtotime($reservas['fecha_inicio'])) / 86400;

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        // Encabezado
        $pdf->SetFillColor(30, 60, 100);
        $pdf->Rect(0, 0, 210, 40, 'F');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 22);
        $pdf->SetXY(20, 10);
        $pdf->Cell(0, 10, 'Hotel MYQ', 0, 1, 'L');

        $pdf->SetFont('Helvetica', '', 11);
        $pdf->SetXY(20, 22);
        $pdf->Cell(0, 8, 'Comodidad y descanso garantizados', 0, 1, 'L');

        $pdf->SetTextColor(200, 225, 255);
        $pdf->SetFont('Helvetica', 'I', 9);
        $pdf->SetXY(20, 31);
        $pdf->Cell(0, 6, 'www.hotelmyq.com', 0, 1, 'L');

        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(0, 14);
        $pdf->Cell(190, 8, 'Reserva #' . str_pad($reservas['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');

        // Título
        $pdf->SetY(50);
        $pdf->SetTextColor(30, 60, 100);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'COMPROBANTE DE RESERVA', 0, 1, 'C');

        $pdf->SetDrawColor(30, 60, 100);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(5);

        $pdf->SetFont('Helvetica', 'I', 9);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 6, 'Emitido el: ' . date('d/m/Y H:i'), 0, 1, 'R');
        $pdf->Ln(3);

        // Secciones
        self::seccionTitulo($pdf, 'Datos del Huésped');
        self::fila($pdf, 'Nombre', htmlspecialchars_decode($_SESSION['user_name']));
        if (!empty($_SESSION['user_email'])) {
            self::fila($pdf, 'Email', htmlspecialchars_decode($_SESSION['user_email']));
        }
        $pdf->Ln(4);

        self::seccionTitulo($pdf, 'Detalles de la Habitacion');
        self::fila($pdf, 'Habitacion',   'Nro. ' . htmlspecialchars_decode($reservas['numero']));
        self::fila($pdf, 'Tipo',         htmlspecialchars_decode($reservas['descripcion']));
        self::fila($pdf, 'Precio/noche', '$' . number_format($reservas['precio_noche'], 2, '.', ','));
        $pdf->Ln(4);

        self::seccionTitulo($pdf, 'Detalles de la Estadia');
        self::fila($pdf, 'Check-in',  date('d/m/Y', strtotime($reservas['fecha_inicio'])));
        self::fila($pdf, 'Check-out', date('d/m/Y', strtotime($reservas['fecha_final'])));
        self::fila($pdf, 'Noches',    (int)$noches . ' noche(s)');
        self::fila($pdf, 'Huespedes', $reservas['n_personas'] . ' persona(s)');
        $pdf->Ln(4);

        // Total
        $pdf->SetFillColor(30, 60, 100);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->SetX(20);
        $pdf->Cell(170, 12, 'TOTAL A PAGAR:   $' . number_format($reservas['precio'], 2, '.', ','), 0, 1, 'R', true);
        $pdf->Ln(8);

        // Notas
        $pdf->SetFillColor(235, 245, 255);
        $pdf->SetDrawColor(100, 150, 210);
        $pdf->SetLineWidth(0.3);
        $pdf->SetX(20);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor(30, 60, 100);
        $pdf->Cell(170, 7, '  Informacion importante', 1, 1, 'L', true);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(60, 60, 60);
        $notas = [
            'Presente este comprobante al momento del check-in.',
            'El check-in es a partir de las 15:00 h y el check-out hasta las 12:00 h.',
            'Para cancelaciones o modificaciones contáctenos con 48 h de anticipación.',
            'Se requiere documento de identidad válido al momento del registro.',
        ];
        foreach ($notas as $nota) {
            $pdf->SetX(20);
            $pdf->MultiCell(170, 5, chr(149) . ' ' . $nota, 'LR', 'L', true);
        }
        $pdf->SetX(20);
        $pdf->Cell(170, 0, '', 'B');
        $pdf->Ln(10);

        // Pie de página
        $pdf->SetFillColor(30, 60, 100);
        $pdf->SetY(270);
        $pdf->Rect(0, 270, 210, 30, 'F');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetXY(20, 274);
        $pdf->Cell(0, 5, 'Hotel MYQ  |  Tel: +57 000 000 0000  |  contacto@hotelmyq.com', 0, 1, 'C');
        $pdf->SetXY(20, 281);
        $pdf->SetTextColor(180, 210, 255);
        $pdf->Cell(0, 5, 'Gracias por elegir Hotel MYQ. Esperamos su visita.', 0, 1, 'C');

        $nombreArchivo = 'reserva_' . str_pad($reservas['id'], 6, '0', STR_PAD_LEFT) . '.pdf';
        // Limpiar cualquier salida previa para evitar páginas en blanco o PDF corruptos.
        if (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output('D', $nombreArchivo);
        exit;
    }

    private static function seccionTitulo(FPDF $pdf, string $titulo) {
        $pdf->SetFillColor(220, 235, 255);
        $pdf->SetTextColor(30, 60, 100);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(170, 7, '  ' . $titulo, 0, 1, 'L', true);
        $pdf->Ln(1);
    }

    private static function fila(FPDF $pdf, string $etiqueta, string $valor) {
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetX(20);
        $pdf->Cell(55, 7, $etiqueta . ':', 0, 0, 'L');

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(115, 7, $valor, 0, 1, 'L');
    }
}
