<?php
$servername = "localhost";
$username = "root";   // tu usuario de MySQL
$password = "";       // tu contraseña
$dbname = "correos_aletorios";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>
