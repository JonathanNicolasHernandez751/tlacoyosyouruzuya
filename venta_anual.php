<?php include("db.php"); ?>
<h2>📅 Registro de Ventas Anuales</h2>

<form method="POST">

    Año:
    <input type="number" name="ano" min="2000" max="2100" required><br><br>

    Mes:
    <select name="mes" required>
        <option value="">-- Seleccionar --</option>
        <option>Enero</option><option>Febrero</option><option>Marzo</option>
        <option>Abril</option><option>Mayo</option><option>Junio</option>
        <option>Julio</option><option>Agosto</option><option>Septiembre</option>
        <option>Octubre</option><option>Noviembre</option><option>Diciembre</option>
    </select><br><br>

    Producto:
    <select name="id_producto" required>
        <option value="">-- Seleccionar producto --</option>
        <?php
        $prod = $conn->query("SELECT id_producto, nombre_producto FROM Producto WHERE activo=1 ORDER BY nombre_producto");
        while ($p = $prod->fetch_assoc()) {
            echo "<option value='{$p['id_producto']}'>{$p['nombre_producto']}</option>";
        }
        ?>
    </select><br><br>

    Cantidad:
    <input type="number" name="cantidad" min="1" required><br><br>

    Total vendido:
    <input type="number" step="0.01" name="total" min="0" required><br><br>

    <button type="submit" name="guardar">💾 Guardar Venta</button>
</form>

<hr>

<?php
// Guardar datos
if (isset($_POST['guardar'])) {

    $stmt = $conn->prepare("
        INSERT INTO VentaAnual (ano, mes, id_producto, cantidad, total)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isiii",
        $_POST['ano'],
        $_POST['mes'],
        $_POST['id_producto'],
        $_POST['cantidad'],
        $_POST['total']
    );

    if ($stmt->execute()) {
        echo "✅ Venta anual registrada con éxito.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
