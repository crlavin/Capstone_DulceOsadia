<?php

class Database {
    private $hostname;
    private $database;
    private $username;
    private $password;
    private $charset = "utf8mb4";
    private $port;

    function conectar()
    {
        // 1. Leer variables de entorno (Nube)
        $this->hostname = getenv('DB_HOST');
        $this->port     = getenv('DB_PORT');
        $this->database = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');

        // 2. Fallback local (XAMPP)
        if (!$this->hostname) {
            $this->hostname = "localhost";
            $this->port     = "3306";
            $this->database = "dulce_osadia";
            $this->username = "root";
            $this->password = "";
        }

        try {
            $con = "mysql:host=" . $this->hostname . ";port=" . $this->port . ";dbname=" . $this->database . ";charset=" . $this->charset;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_CA => true,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];

            $pdo = new PDO($con, $this->username, $this->password, $options);

            // --- CAMBIO DE HORA AQUÍ ---
            // Intentamos usar la zona horaria nominal 'America/Santiago'.
            // Si la base de datos no tiene cargadas las tablas de zona horaria (común en servicios gratis),
            // fallará silenciosamente. Por seguridad, podrías usar el offset fijo '-03:00' si ves que no cambia.
            
            try {
                // Opción A: Nombre (Ideal, maneja cambio de hora automático)
                $pdo->exec("SET time_zone = 'America/Santiago'");
            } catch (Exception $e) {
                // Opción B: Offset Fijo (Plan de respaldo para Verano actual)
                $pdo->exec("SET time_zone = '-03:00'");
            }
            // ---------------------------

            return $pdo;

        } catch (PDOException $e) {
            echo 'Error conexion: ' . $e->getMessage();
            exit;
        }
    }
}
?>