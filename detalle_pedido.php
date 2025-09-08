<?php include("db.php"); ?>

<?php
$id_pedido = $_GET['id'];
$pedido = $conn->query("SELECT p.id_pedido, p.fecha_pedido, c.nombre
                        FROM pedido p
                        JOIN Ccliente c ON p.id_cliente=c.id_cliente
                        WHERE p.id_pedido=$id_pedido")->fetch_assoc();

echo "<h2>📄 Detalle del Pedido #{$pedido['id_pedido']}</h2>";
echo "<p><strong>Cliente:</strong> {$pedido['nombre']}<br>";
echo "<strong>Fecha:</strong> {$pedido['fecha_pedido']}</p>";

$result = $conn->query("SELECT d.cantidad, d.precio_unitario, d.subtotal, pr.nombre_producto
                        FROM DetallePedido d
                        JOIN Producto pr ON d.id_producto=pr.id_producto
                        WHERE d.id_pedido=$id_pedido");

echo "<table border='1' cellpadding='5'>
<tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>";
$total = 0;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['nombre_producto']}</td>
        <td>{$row['cantidad']}</td>
        <td>{$row['precio_unitario']}</td>
        <td>{$row['subtotal']}</td>
    </tr>";
    $total += $row['subtotal'];
}
echo "<tr><td colspan='3'><strong>Total</strong></td><td><strong>$total</strong></td></tr>";
echo "</table>";
?>
