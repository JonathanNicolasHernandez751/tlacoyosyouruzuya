<?php
class GeneradorCorreos {
    private $db;

    public function __construct($host, $usuario, $password, $base_datos) {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8", $usuario, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Generar un correo aleatorio
    public function generarCorreo($cantidad = 1) {
        $nombres = $this->obtenerNombres();
        $apellidos = $this->obtenerApellidos();
        $dominios = $this->obtenerDominios();

        $correos = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)];
            $dominio = $dominios[array_rand($dominios)];

            // Diferentes formatos de correo
            $formatos = [
                $nombre . '.' . $apellido,
                $nombre . $apellido,
                $nombre[0] . $apellido,
                $nombre . rand(100, 999),
                $apellido . $nombre,
                $nombre . '_' . $apellido
            ];

            $usuario = $formatos[array_rand($formatos)];
            $correo = strtolower($usuario . '@' . $dominio);

            // Guardar en base de datos
            $this->guardarCorreo($correo);
            $correos[] = $correo;
        }

        return $correos;
    }

    private function obtenerNombres() {
        $stmt = $this->db->prepare("SELECT nombre FROM nombres WHERE tipo = 'nombre'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function obtenerApellidos() {
        $stmt = $this->db->prepare("SELECT nombre FROM nombres WHERE tipo = 'apellido'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function obtenerDominios() {
        $stmt = $this->db->prepare("SELECT dominio FROM dominios WHERE activo = TRUE");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function guardarCorreo($correo) {
        $stmt = $this->db->prepare("INSERT INTO correos_generados (correo) VALUES (?)");
        $stmt->execute([$correo]);
    }

    // Obtener correos generados
    public function obtenerCorreosGenerados($limite = 10) {
        $stmt = $this->db->prepare("SELECT correo, fecha_creacion FROM correos_generados ORDER BY fecha_creacion DESC LIMIT ?");
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar correo por patrón
    public function buscarCorreo($patron) {
        $stmt = $this->db->prepare("SELECT correo, fecha_creacion FROM correos_generados WHERE correo LIKE ? ORDER BY fecha_creacion DESC");
        $stmt->execute(["%$patron%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
