<?php

class Reserva {
    private $conexion;

    public function __construct(Conexion $conexion = null) {
        $this->conexion = $conexion ?: new Conexion();
    }

    private function conectar() {
        $this->conexion->conectar();
    }

    private function desconectar() {
        $this->conexion->desconectar();
    }

    public function crear($usuario_id, $habitacion_id, $fecha_inicio, $fecha_final, $n_personas, $precio) {
        $this->conectar();

        $sql = "INSERT INTO reservas 
                (user_id, habitacion_id, fecha_inicio, fecha_final, n_personas, estado_id, precio) 
                VALUES (
                    '{$this->conexion->escape($usuario_id)}',
                    '{$this->conexion->escape($habitacion_id)}',
                    '{$this->conexion->escape($fecha_inicio)}',
                    '{$this->conexion->escape($fecha_final)}',
                    '{$this->conexion->escape($n_personas)}',
                    1,
                    '{$this->conexion->escape($precio)}'
                )";

        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }

    public function obtenerPorUsuario($usuario_id) {
        $this->conectar();

        $sql = "SELECT r.id as id, r.user_id, r.habitacion_id, r.fecha_inicio, r.fecha_final,
                       r.n_personas, r.estado_id, r.precio,
                       h.numero, h.descripcion, h.precio as precio_noche
                FROM reservas r
                INNER JOIN habitaciones h ON r.habitacion_id = h.id
                WHERE r.user_id = '{$this->conexion->escape($usuario_id)}'
                ORDER BY r.id ASC";

        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $reservas = $result->fetch_all(MYSQLI_ASSOC);
        $this->conexion->desconectar();
        return $reservas;
    }

    public function obtenerPorId($id) {
        $this->conectar();

        $sql = "SELECT r.id as id, r.user_id, r.habitacion_id, r.fecha_inicio, r.fecha_final,
                       r.n_personas, r.estado_id, r.precio,
                       h.numero, h.descripcion, h.precio as precio_noche
                FROM reservas r
                INNER JOIN habitaciones h ON r.habitacion_id = h.id
                WHERE r.id = '{$this->conexion->escape($id)}'
                LIMIT 1";

        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $reservas = $result->fetch_assoc();
        $this->desconectar();
        return $reservas;
    }

    public function editar($id, $habitacion_id, $fecha_inicio, $fecha_final, $n_personas, $precio) {
        $this->conectar();

        $sql = "UPDATE reservas SET
                    habitacion_id = '{$this->conexion->escape($habitacion_id)}',
                    fecha_inicio  = '{$this->conexion->escape($fecha_inicio)}',
                    fecha_final   = '{$this->conexion->escape($fecha_final)}',
                    n_personas    = '{$this->conexion->escape($n_personas)}',
                    precio        = '{$this->conexion->escape($precio)}'
                WHERE id = '{$this->conexion->escape($id)}'";

        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }

    public function eliminar($id) {
        $this->conectar();

        $sql = "DELETE FROM reservas WHERE id = '{$this->conexion->escape($id)}'";
        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }

    public function cancelar($id) {
        $this->conectar();

        $sql = "UPDATE reservas SET estado_id = 2
                WHERE id = '{$this->conexion->escape($id)}'";

        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }
}
?>