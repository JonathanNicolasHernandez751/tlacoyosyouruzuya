<?php
// insertar_datos_faker.php
require_once 'vendor/autoload.php';
require_once 'config.php';

use Faker\Factory;

class InsertarDatosFaker {
    private $db;
    private $faker;

    public function __construct($host, $usuario, $password, $base_datos) {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8", $usuario, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Inicializar Faker en español
            $this->faker = Factory::create('es_ES');
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Insertar nombres españoles
    public function insertarNombres($cantidad = 50) {
        $stmt = $this->db->prepare("INSERT INTO nombres (nombre, tipo) VALUES (?, 'nombre')");
        $nombresInsertados = 0;

        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->faker->firstName();
            // Convertir a minúsculas y sin espacios
            $nombre = strtolower(trim($nombre));

            try {
                $stmt->execute([$nombre]);
                $nombresInsertados++;
            } catch(PDOException $e) {
                // Ignorar duplicados
                if ($e->getCode() != 23000) { // Código de duplicado
                    echo "Error: " . $e->getMessage() . "\n";
                }
            }
        }

        return $nombresInsertados;
    }

    // Insertar apellidos españoles
    public function insertarApellidos($cantidad = 30) {
        $stmt = $this->db->prepare("INSERT INTO nombres (nombre, tipo) VALUES (?, 'apellido')");
        $apellidosInsertados = 0;

        for ($i = 0; $i < $cantidad; $i++) {
            $apellido = $this->faker->lastName();
            // Convertir a minúsculas y sin espacios
            $apellido = strtolower(trim($apellido));

            try {
                $stmt->execute([$apellido]);
                $apellidosInsertados++;
            } catch(PDOException $e) {
                // Ignorar duplicados
                if ($e->getCode() != 23000) {
                    echo "Error: " . $e->getMessage() . "\n";
                }
            }
        }

        return $apellidosInsertados;
    }

    // Insertar dominios populares
    public function insertarDominios($dominios = []) {
        if (empty($dominios)) {
            $dominios = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
                'protonmail.com', 'icloud.com', 'aol.com', 'zoho.com',
                'yandex.com', 'mail.com', 'gmx.com', 'fastmail.com',
                'hushmail.com', 'tutanota.com', 'live.com'
            ];
        }

        $stmt = $this->db->prepare("INSERT INTO dominios (dominio) VALUES (?)");
        $dominiosInsertados = 0;

        foreach ($dominios as $dominio) {
            try {
                $stmt->execute([$dominio]);
                $dominiosInsertados++;
            } catch(PDOException $e) {
                if ($e->getCode() != 23000) {
                    echo "Error: " . $e->getMessage() . "\n";
                }
            }
        }

        return $dominiosInsertados;
    }

    // Método completo para poblar toda la base de datos
    public function poblarBaseDatos($numNombres = 50, $numApellidos = 30) {
        echo "Iniciando población de base de datos...\n";

        echo "Insertando nombres...\n";
        $nombres = $this->insertarNombres($numNombres);
        echo "Nombres insertados: $nombres\n";

        echo "Insertando apellidos...\n";
        $apellidos = $this->insertarApellidos($numApellidos);
        echo "Apellidos insertados: $apellidos\n";
        echo "Insertando dominios...\n";
        $dominios = $this->insertarDominios();
        echo "Dominios insertados: $dominios\n";

        echo "¡Base de datos poblada exitosamente!\n";
    }

    // Mostrar estadísticas
    public function mostrarEstadisticas() {
        $stmt = $this->db->prepare("SELECT tipo, COUNT(*) as cantidad FROM nombres GROUP BY tipo");
        $stmt->execute();
        $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT COUNT(*) as cantidad FROM dominios");
        $stmt->execute();
        $dominios = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "=== ESTADÍSTICAS ===\n";
        foreach ($nombres as $fila) {
            echo $fila['tipo'] . ": " . $fila['cantidad'] . "\n";
        }
        echo "Dominios: " . $dominios['cantidad'] . "\n";
    }
}

// Uso del script
try {
    $insertador = new InsertarDatosFaker(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Poblar la base de datos
    $insertador->poblarBaseDatos(100, 50);

    // Mostrar estadísticas
    $insertador->mostrarEstadisticas();

} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
