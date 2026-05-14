<?php

class Conexion {
    private $mySQLi; // objeto de la conexion a la base de datos
    private $sql; // query a la base de datos
    private $result; // resultado de la query
    private $filasAfectadas; // numero de filas afectadas por la query

    public function conectar() {
        $host = 'localhost';
        $db = 'hotel';
        $user = 'root';
        $password = '';

        $this->mySQLi = new mysqli($host, $user, $password, $db);

        if ($this->mySQLi->connect_error) {
            throw new Exception('Error de conexión a la base de datos: ' . $this->mySQLi->connect_error);
        }
    }

    public function desconectar() {
        if ($this->mySQLi) {
            $this->mySQLi->close();
        }
    }

    public function query($sql) {
        $this->sql = $sql;
        $this->result = $this->mySQLi->query($sql);
        if ($this->result === false) {
            throw new Exception('Query error: ' . $this->mySQLi->error);
        }
        $this->filasAfectadas = $this->mySQLi->affected_rows;
        return $this->result;
    }

    public function escape($value) {
        return $this->mySQLi->real_escape_string($value);
    }

    public function getResult() {
        return $this->result;
    }

    public function getFilasAfectadas() {
        return $this->filasAfectadas;
    }
}

?>