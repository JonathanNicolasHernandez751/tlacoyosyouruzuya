<?php include("db.php"); ?>

<h2>✏️ Editar Fecha de Pago</h2>

<form method="POST">
    ID Pago: <input type="number" name="id_pago" required><br><br>
    Nueva Fecha: <input type="datetime-local" name="nueva_fecha" required><br><br>
    <button type="submit" name="editar_pago">Actualizar Fecha</button>
</form>

<?php
if (isset($_POST['editar_pago'])) {
    $id_pago = $_POST['id_pago'];
    $nueva_fecha = $_POST['nueva_fecha'];

    $stmt = $conn->prepare("UPDATE Pago SET fecha_pago = ? WHERE id_pago = ?");
    $stmt->bind_param("si", $nueva_fecha, $id_pago);

    if ($stmt->execute()) {
        echo "✅ Fecha de pago actualizada correctamente.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
