<?php include("db.php"); ?>

<h2>🛒 Nuevo Pedido</h2>

<form method="POST">
    <label>Cliente:</label>
    <select name="id_cliente" required>
        <option value="">-- Selecciona cliente --</option>
        <?php
        $clientes = $conn->query("SELECT id_cliente, nombre FROM cliente WHERE activo=1 ORDER BY nombre");
        while ($c = $clientes->fetch_assoc()) {
            echo "<option value='{$c['id_cliente']}'>{$c['nombre']}</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Fecha:</label>
    <input type="datetime-local" name="fecha_pedido" value="<?= date('Y-m-d\TH:i') ?>" required>
    <br><br>

    <h3>Productos:</h3>
    <div id="productos">
        <div>
            <select name="productos[]">
                <?php
                $productos = $conn->query("SELECT id_producto, nombre_producto, precio_unitario FROM producto");
                while ($p = $productos->fetch_assoc()) {
                    echo "<option value='{$p['id_producto']}' data-precio_unitario='{$p['precio_unitario']}'>{$p['nombre_producto']} - $ {$p['precio_unitario']}</option>";
                }
                ?>
            </select>
            <input type="number" name="cantidades[]" step="0.01" placeholder="Cantidad" required>
        </div>
    </div>
    <button type="button" onclick="agregarProducto()">➕ Agregar producto</button>

    <br><br>
    <button type="submit" name="guardar_pedido">💾 Guardar Pedido</button>
</form>

<script>
function agregarProducto() {
    let div = document.querySelector("#productos div").cloneNode(true);
    div.querySelector("input").value = "";
    document.querySelector("#productos").appendChild(div);
}
</script>

<?php
if (isset($_POST['guardar_pedido'])) {
    $conn->begin_transaction();

    try {
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO pedido (id_cliente, fecha_pedido) VALUES (?, ?)");
        $stmt->bind_param("is", $_POST['id_cliente'], $_POST['fecha_pedido']);
        $stmt->execute();
        $id_pedido = $stmt->insert_id;
        $stmt->close();

        // Insertar detalles
        $productos = $_POST['productos'];
        $cantidades = $_POST['cantidades'];

        foreach ($productos as $i => $id_producto) {
            if (!empty($cantidades[$i])) {
                // Obtener precio del producto
                $precio = $conn->query("SELECT precio_unitario FROM producto WHERE id_producto=$id_producto")->fetch_assoc()['precio_unitario'];
                $subtotal = $precio * $cantidades[$i];

                $stmt = $conn->prepare("INSERT INTO detallepedido (id_pedido, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iidd d", $id_pedido, $id_producto, $cantidades[$i], $cantidad, $subtotal);
                $stmt->execute();
                $stmt->close();
            }
        }

        $conn->commit();
        echo "✅ Pedido guardado correctamente.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "❌ Error al guardar pedido: " . $e->getMessage();
    }
}
?>
