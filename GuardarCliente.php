<?php
// guardar_cliente.php
include("db.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("INSERT INTO cliente (nombre, telefono, direccion, manzana, lote, email, id_ciudad) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", 
        $_POST['nombre'], 
        $_POST['telefono'], 
        $_POST['direccion'], 
        $_POST['manzana'], 
        $_POST['lote'], 
        $email, 
        $_POST['id_ciudad']
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente registrado: ' . $email]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}
?>