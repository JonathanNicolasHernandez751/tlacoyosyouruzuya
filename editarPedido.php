<?php include("db.php"); ?>

<h2>✏️ Editar Fecha de Pedido</h2>

<form method="POST">
    ID Pedido: <input type="number" name="id_pedido" required><br><br>
    Nueva Fecha: <input type="datetime-local" name="nueva_fecha" required><br><br>
    <button type="submit" name="editar_pedido">Actualizar Fecha</button>
</form>

<?php
if (isset($_POST['editar_pedido'])) {
    $id_pedido = $_POST['id_pedido'];
    $nueva_fecha = $_POST['nueva_fecha'];

    $stmt = $conn->prepare("UPDATE Pedido SET fecha_pedido = ? WHERE id_pedido = ?");
    $stmt->bind_param("si", $nueva_fecha, $id_pedido);

    if ($stmt->execute()) {
        echo "✅ Fecha de pedido actualizada correctamente.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
