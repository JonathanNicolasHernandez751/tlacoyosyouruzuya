<?php include("db.php"); ?>

<h2>🔎 Buscar / Editar / Eliminar Pedidos</h2>

<form method="GET">
    Cliente:
    <select name="id_cliente">
        <option value="">-- Todos --</option>
        <?php
        $clientes = $conn->query("SELECT id_cliente, nombre FROM Cliente ORDER BY nombre");
        while ($c = $clientes->fetch_assoc()) {
            $sel = (isset($_GET['id_cliente']) && $_GET['id_cliente'] == $c['id_cliente']) ? "selected" : "";
            echo "<option value='{$c['id_cliente']}' $sel>{$c['nombre']}</option>";
        }
        ?>
    </select>
    Fecha: <input type="date" name="fecha" value="<?= $_GET['fecha'] ?? '' ?>">
    <button type="submit">Buscar</button>
</form>

<hr>

<?php
// Eliminar pedido
if (isset($_GET['eliminar_pedido'])) {
    $id = $_GET['eliminar_pedido'];
    $conn->query("DELETE FROM DetallePedido WHERE id_pedido=$id");
    $conn->query("DELETE FROM Pedido WHERE id_pedido=$id");
    echo "🗑️ Pedido eliminado.<br><br>";
}

// Construir consulta
$query = "SELECT p.id_pedido, p.fecha_pedido, c.nombre
          FROM Pedido p
          JOIN Cliente c ON p.id_cliente=c.id_cliente
          WHERE 1=1";

if (!empty($_GET['id_cliente'])) {
    $query .= " AND p.id_cliente=" . intval($_GET['id_cliente']);
}
if (!empty($_GET['fecha'])) {
    $query .= " AND DATE(p.fecha_pedido)='" . $_GET['fecha'] . "'";
}

$query .= " ORDER BY p.fecha_pedido DESC";

$result = $conn->query($query);

echo "<table border='1' cellpadding='5'>
<tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Acciones</th></tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id_pedido']}</td>
            <td>{$row['nombre']}</td>
            <td>{$row['fecha_pedido']}</td>
            <td>
                <a href='detalle_pedido.php?id={$row['id_pedido']}'>📄 Ver</a> |
                <a href='panel_pedidos.php?eliminar_pedido={$row['id_pedido']}' onclick=\"return confirm('⚠️ ¿Eliminar pedido {$row['id_pedido']}?');\">🗑️ Eliminar</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>⚠️ No se encontraron pedidos.</td></tr>";
}
echo "</table>";
?>
