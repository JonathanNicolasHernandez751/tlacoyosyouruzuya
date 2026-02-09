<?php
// conexion.php
$host = 'localhost';
$db   = 'tlacoyosyouruzuya';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Opcional: Configurar meses en español para los reportes
    $pdo->exec("SET lc_time_names = 'es_ES'");

} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>