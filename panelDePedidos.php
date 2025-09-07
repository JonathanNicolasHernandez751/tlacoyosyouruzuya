<?php include("db.php"); ?>

<h1>📊 Panel de Pedidos</h1>

<?php
// Obtener todos los pedidos con datos del cliente
$sql = "SELECT p.id_pedido, p.fecha_pedido, p.estado, p.total,
               c.nombre AS cliente, c.telefono, c.direccion
        FROM Pedido p
        INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
        ORDER BY p.id_pedido DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($pedido = $result->fetch_assoc()) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px; border-radius:8px;'>";
        echo "<h2>Pedido #{$pedido['id_pedido']} - {$pedido['estado']}</h2>";
        echo "<p><b>Cliente:</b> {$pedido['cliente']} ({$pedido['telefono']})</p>";
        echo "<p><b>Dirección:</b> {$pedido['direccion']}</p>";
        echo "<p><b>Fecha:</b> {$pedido['fecha_pedido']}</p>";

        // Detalle del pedido
        $sqlDetalle = "SELECT d.cantidad, d.subtotal, pr.nombre_producto, pr.precio_unitario, pr.unidad_medida
                       FROM DetallePedido d
                       INNER JOIN Producto pr ON d.id_producto = pr.id_producto
                       WHERE d.id_pedido = {$pedido['id_pedido']}";
        $resDetalle = $conn->query($sqlDetalle);

        if ($resDetalle->num_rows > 0) {
            echo "<h3>🛒 Productos</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Subtotal</th>
                    </tr>";
            while ($detalle = $resDetalle->fetch_assoc()) {
                echo "<tr>
                        <td>{$detalle['nombre_producto']}</td>
                        <td>$ {$detalle['precio_unitario']}</td>
                        <td>{$detalle['cantidad']}</td>
                        <td>{$detalle['unidad_medida']}</td>
                        <td>$ {$detalle['subtotal']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay productos en este pedido.</p>";
        }

        // Pago del pedido
        $sqlPago = "SELECT metodo_pago, monto, fecha_pago
                    FROM Pago WHERE id_pedido = {$pedido['id_pedido']}";
        $resPago = $conn->query($sqlPago);

        if ($resPago->num_rows > 0) {
            $pago = $resPago->fetch_assoc();
            echo "<h3>💰 Pago</h3>";
            echo "<p><b>Método:</b> {$pago['metodo_pago']}</p>";
            echo "<p><b>Monto:</b> $ {$pago['monto']}</p>";
            echo "<p><b>Fecha:</b> {$pago['fecha_pago']}</p>";
        } else {
            echo "<p>⚠️ Aún no se ha registrado pago para este pedido.</p>";
        }

        echo "<h3>Total del Pedido: $ {$pedido['total']}</h3>";
        echo "</div>";
    }
} else {
    echo "<p>No hay pedidos registrados.</p>";
}
?>
