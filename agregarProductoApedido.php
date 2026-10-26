<?php include("db.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto a un Pedido</title>
    <link rel="stylesheet" href="css/agregarProductoApedido.css">
</head>
<script>
const selectProducto = document.getElementById('id_producto');
const inputCantidad  = document.getElementById('cantidad');

function mostrarSubtotal() {
    const precio = parseFloat(selectProducto.selectedOptions[0]?.dataset.precio || 0);
    const cant   = parseFloat(inputCantidad.value || 0);
    const total  = precio * cant;
    // Puedes insertar un <div id="subtotal"></div> en el HTML y actualizarlo
}

selectProducto.addEventListener('change', mostrarSubtotal);
inputCantidad.addEventListener('input', mostrarSubtotal);
</script>
<body>

<div class="form-card">
    <h2>Agregar Producto a un Pedido</h2>

    <?php
    if (isset($_POST['guardar_detalle'])) {
        $id_pedido   = intval($_POST['id_pedido']);
        $id_producto = intval($_POST['id_producto']);
        $cantidad    = floatval($_POST['cantidad']);

        if ($id_pedido <= 0 || $id_producto <= 0 || $cantidad <= 0) {
            echo '<div class="mensaje error">Datos inválidos. Revisa los campos.</div>';
        } else {
            // Obtener precio
            $stmt = $conn->prepare("SELECT precio_unitario FROM Producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $stmt->bind_result($precio_unitario);
            $stmt->fetch();
            $stmt->close();

            if (isset($precio_unitario)) {
                $subtotal = $precio_unitario * $cantidad;

                // Insertar detalle
                $stmt = $conn->prepare(
                    "INSERT INTO DetallePedido (id_pedido, id_producto, cantidad, subtotal)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $subtotal);

                if ($stmt->execute()) {
                    echo '<div class="mensaje exito">Producto agregado correctamente ✅</div>';

                    // Actualizar total
                    $stmt2 = $conn->prepare(
                        "UPDATE Pedido
                         SET total = (SELECT SUM(subtotal) FROM DetallePedido WHERE id_pedido = ?)
                         WHERE id_pedido = ?"
                    );
                    $stmt2->bind_param("ii", $id_pedido, $id_pedido);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    echo '<div class="mensaje error">Error al guardar: ' . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            } else {
                echo '<div class="mensaje error">Producto no encontrado.</div>';
            }
        }
    }
    ?>

    <form method="POST">
        <div class="form-group">
            <label for="id_pedido">Pedido</label>
            <select name="id_pedido" id="id_pedido" required>
                <option value="">-- Selecciona Pedido --</option>
                <?php
                $result = $conn->query(
                    "SELECT id_pedido, id_cliente, fecha_pedido
                     FROM Pedido ORDER BY id_pedido DESC"
                );
                while ($row = $result->fetch_assoc()) {
                    $idp  = htmlspecialchars($row['id_pedido']);
                    $idc  = htmlspecialchars($row['id_cliente']);
                    $fec  = htmlspecialchars($row['fecha_pedido']);
                    echo "<option value='{$idp}'>Pedido #{$idp} - Cliente {$idc} ({$fec})</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_producto">Producto</label>
            <select name="id_producto" id="id_producto" required>
                <option value="">-- Selecciona Producto --</option>
                <?php
                $result = $conn->query(
                    "SELECT id_producto, nombre_producto, precio_unitario FROM Producto"
                );
                while ($row = $result->fetch_assoc()) {
                    $idp    = htmlspecialchars($row['id_producto']);
                    $nom    = htmlspecialchars($row['nombre_producto']);
                    $precio = htmlspecialchars($row['precio_unitario']);
                    echo "<option value='{$idp}' data-precio='{$precio}'>
                            {$nom} - $ {$precio}
                          </option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" min="1" step="any" required>
        </div>

        <button type="submit" name="guardar_detalle" class="btn-submit">
            Agregar al Pedido
        </button>
    </form>
</div>

</body>
</html>
