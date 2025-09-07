<?php include("db.php"); ?>

<h2>Registrar Producto</h2>
<form method="POST">
    Nombre: <input type="text" name="nombre_producto" required><br>
    Descripción: <input type="text" name="descripcion"><br>
    Precio: <input type="number" step="0.01" name="precio" required><br>
    Tipo:
    <select name="tipo" required>
        <option value="Tlacoyo">Tlacoyo</option>
        <option value="Gordita">Gordita</option>
        <option value="Tortilla">Tortilla</option>
        <option value="Ensalada">Ensalada</option>
    </select><br>
    Unidad:
    <select name="unidad" required>
        <option value="pieza">Pieza</option>
        <option value="docena">Docena</option>
        <option value="kilo">Kilo</option>
    </select><br>
    <button type="submit" name="guardar_producto">Guardar</button>
</form>

<?php
if (isset($_POST['guardar_producto'])) {
    $stmt = $conn->prepare("INSERT INTO Producto (nombre_producto, descripcion, precio_unitario, tipo, unidad_medida) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $_POST['nombre_producto'], $_POST['descripcion'], $_POST['precio'], $_POST['tipo'], $_POST['unidad']);

    if ($stmt->execute()) {
        echo "Producto registrado correctamente ✅";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
