<?php
include("db.php");

if (isset($_GET['nombre'])) {
    $nombre = "%" . $_GET['nombre'] . "%";
    $stmt = $conn->prepare("SELECT id_cliente, nombre FROM cliente WHERE nombre LIKE ? ORDER BY nombre LIMIT 10");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $res = $stmt->get_result();

    $clientes = [];
    while ($row = $res->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}
?>
