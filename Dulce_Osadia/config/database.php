<?php

class Database {
    // Definimos las propiedades, pero los valores se asignarán dinámicamente
    private $hostname;
    private $database;
    private $username;
    private $password;
    private $charset = "utf8mb4"; // Recomendado usar utf8mb4 en vez de utf8
    private $port;

    function conectar()
    {
        // 1. Intentamos leer las variables de entorno (Render / Aiven)
        $this->hostname = getenv('DB_HOST');
        $this->port     = getenv('DB_PORT');
        $this->database = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');

        // 2. Si no existen (significa que estás en tu PC local con XAMPP), usamos valores por defecto
        if (!$this->hostname) {
            $this->hostname = "localhost";
            $this->port     = "3306";
            $this->database = "dulce_osadia";
            $this->username = "root";
            $this->password = "";
        }

        try {
            // 3. Cadena de conexión (DSN) incluyendo el PUERTO (Vital para Aiven)
            $con = "mysql:host=" . $this->hostname . ";port=" . $this->port . ";dbname=" . $this->database . ";charset=" . $this->charset;

            // 4. Opciones: Agregamos soporte SSL obligatorio para la nube
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Estas dos líneas permiten la conexión segura a Aiven
                PDO::MYSQL_ATTR_SSL_CA => true,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];

            $pdo = new PDO($con, $this->username, $this->password, $options);

            return $pdo;

        } catch (PDOException $e) {
            // En producción es mejor no mostrar el error exacto por seguridad, pero para debug está bien
            echo 'Error conexion: ' . $e->getMessage();
            exit;
        }
    }
}
?>