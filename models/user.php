<?php

class User {
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

    public function getDocumentTypes() {
        $this->conectar();
        $sql = "SELECT * FROM documentos";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $documentTypes = $result->fetch_all(MYSQLI_ASSOC);
        $this->desconectar();
        return $documentTypes;
    }

    public function createUser($tipo_documento_id, $documento, $nombre, $apellido, $email, $passwordHash) {
        $this->conectar();

        $sql = "INSERT INTO usuarios (tipo_documento_id, documento, nombre, apellido, email, contraseña, fecha_registro) VALUES ('{$this->conexion->escape($tipo_documento_id)}', '{$this->conexion->escape($documento)}', '{$this->conexion->escape($nombre)}', '{$this->conexion->escape($apellido)}', '{$this->conexion->escape($email)}', '{$this->conexion->escape($passwordHash)}', NOW())";
        $this->conexion->query($sql);
        $resultado = $this->conexion->getFilasAfectadas() > 0;

        $this->desconectar();
        return $resultado;
    }

    public function getByEmail($email) {
        $this->conectar();

        $sql = "SELECT id, nombre, apellido, email, contraseña FROM usuarios WHERE email = '{$this->conexion->escape($email)}'";
        $this->conexion->query($sql);
        $result = $this->conexion->getResult();
        $user = $result->fetch_assoc();

        $this->desconectar();
        return $user;
    }
}
