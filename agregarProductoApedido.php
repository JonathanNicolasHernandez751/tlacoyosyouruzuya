<?php include("db.php"); ?>

<h2>Agregar Producto a un Pedido</h2>
<form method="POST">
    <!-- Selección de Pedido -->
    Pedido:
    <select name="id_pedido" required>
        <option value="">-- Selecciona Pedido --</option>
        <?php
        $result = $conn->query("SELECT id_pedido, id_cliente, fecha_pedido FROM Pedido  ORDER BY id_pedido DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_pedido']}'>Pedido #{$row['id_pedido']} - Cliente {$row['id_cliente']}  ({$row['fecha_pedido']})</option>";
        }
        ?>
    </select><br><br>

    <!-- Selección de Producto -->
    Producto:
    <select name="id_producto" required>
        <option value="">-- Selecciona Producto --</option>
        <?php
        $result = $conn->query("SELECT id_producto, nombre_producto, precio_unitario FROM Producto");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_producto']}' data-precio='{$row['precio_unitario']}'>
                    {$row['nombre_producto']} - $ {$row['precio_unitario']}
                  </option>";
        }
        ?>
    </select><br><br>

    Cantidad: <input type="number" name="cantidad" min="1" required><br><br>

    <button type="submit" name="guardar_detalle">Agregar al Pedido</button>
</form>

<?php
if (isset($_POST['guardar_detalle'])) {
    $id_pedido = $_POST['id_pedido'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];

    // Obtener el precio del producto
    $stmt = $conn->prepare("SELECT precio_unitario FROM Producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($precio_unitario);
    $stmt->fetch();
    $stmt->close();

    $subtotal = $precio_unitario * $cantidad;

    // Insertar en DetallePedido
    $stmt = $conn->prepare("INSERT INTO DetallePedido (id_pedido, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $subtotal);

    if ($stmt->execute()) {
        echo "Producto agregado correctamente ✅";

        // Actualizar total del pedido
        $conn->query("UPDATE Pedido
                      SET total = (SELECT SUM(subtotal) FROM DetallePedido WHERE id_pedido = $id_pedido)
                      WHERE id_pedido = $id_pedido");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
