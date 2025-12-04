<?php

class Database {
    private $hostname;
    private $database;
    private $username;
    private $password;
    private $charset;

    public function __construct()
    {
        // Carga variables de entorno si aún no están disponibles
        if (!isset($_ENV['DB_HOST'])) {
            $autoload = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($autoload)) {
                require_once $autoload;
                if (class_exists('Dotenv\\Dotenv')) {
                    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), 'data.env');
                    $dotenv->safeLoad();
                }
            }
        }

        // Asignación con valores por defecto
        $this->hostname = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'dulce_osadia';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->charset  = $_ENV['DB_CHARSET'] ?? 'utf8';
    }

function conectar()
{
  try {
    $con = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . "; charset=" . $this->charset;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($con, $this->username, $this->password, $options);

    return $pdo;
 } catch (PDOException $e) {
echo 'Error conexion: ' . $e->getMessage();
        exit;

    }

}   
}
?>
