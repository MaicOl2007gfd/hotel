<?php

class Habitacion {
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

    public function getAll() {
        $this->conectar();
        $sql = "SELECT * FROM habitaciones WHERE estado_id = 1 ORDER BY id";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $habitaciones = $result->fetch_all(MYSQLI_ASSOC);
        $this->desconectar();
        return $habitaciones;
    }

    public function getCategories() {
        $this->conectar();
        $sql = "SELECT DISTINCT descripcion FROM habitaciones WHERE estado_id = 1 ORDER BY descripcion";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $categorias = $result->fetch_all(MYSQLI_ASSOC);
        $this->desconectar();
        return $categorias;
    }

    public function getRoomsByType($tipo) {
        $this->conectar();
        $sql = "SELECT id, numero FROM habitaciones WHERE descripcion = '{$this->conexion->escape($tipo)}' AND estado_id = 1";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $habitaciones = $result->fetch_all(MYSQLI_ASSOC);
        $this->desconectar();
        return $habitaciones;
    }

    public function crear($tipo, $nombre, $capacidad, $precio, $descripcion) {
        $this->conectar();
        $sql = "INSERT INTO habitaciones (tipo, nombre, capacidad, precio, descripcion, disponible, numero)
                VALUES ('{$this->conexion->escape($tipo)}', '{$this->conexion->escape($nombre)}', '{$this->conexion->escape($capacidad)}', '{$this->conexion->escape($precio)}', '{$this->conexion->escape($descripcion)}', 1)";
        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }

    public function editar($id, $nombre, $capacidad, $precio, $descripcion, $numero) {
        $this->conectar();
        $sql = "UPDATE habitaciones SET
                nombre = '{$this->conexion->escape($nombre)}',
                capacidad = '{$this->conexion->escape($capacidad)}',
                precio = '{$this->conexion->escape($precio)}',
                descripcion = '{$this->conexion->escape($descripcion)}',
                numero = '{$this->conexion->escape($numero)}'
                WHERE id = '{$this->conexion->escape($id)}'";
        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() >= 0;
        $this->desconectar();
        return $resultado;
    }

    public function eliminar($id) {
        $this->conectar();
        $sql = "UPDATE habitaciones SET disponible = 0 WHERE id = '{$this->conexion->escape($id)}'";
        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;
        $this->desconectar();
        return $resultado;
    }

    public function obtenerHabitacion($id) {
        $this->conectar();
        $sql = "SELECT * FROM habitaciones WHERE id = '{$this->conexion->escape($id)}' AND estado_id = 1";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $habitacion = $result->fetch_assoc();
        $this->desconectar();
        return $habitacion;
    }
}