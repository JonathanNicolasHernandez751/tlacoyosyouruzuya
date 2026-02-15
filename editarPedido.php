<?php include("db.php"); ?>

<h2>🔎 Buscar, Editar y Desactivar/Activar Pedidos</h2>

<form method="GET">
    Cliente (ID): <input type="number" name="id_cliente" value="<?= isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '' ?>">
    Estado:
    <select name="estado">
        <option value="">-- Todos --</option>
        <option value="Pendiente" <?= (isset($_GET['estado']) && $_GET['estado']=="Pendiente") ? "selected" : "" ?>>Pendiente</option>
        <option value="Entregado" <?= (isset($_GET['estado']) && $_GET['estado']=="Entregado") ? "selected" : "" ?>>Entregado</option>
        <option value="Cancelado" <?= (isset($_GET['estado']) && $_GET['estado']=="Cancelado") ? "selected" : "" ?>>Cancelado</option>
    </select>
    Mostrar inactivos:
    <input type="checkbox" name="ver_inactivos" value="1" <?= isset($_GET['ver_inactivos']) ? "checked" : "" ?>>
    <button type="submit">Buscar</button>
</form>

<hr>

<?php
// --- Desactivar pedido (Soft Delete) ---
if (isset($_GET['desactivar_pedido'])) {
    $id = $_GET['desactivar_pedido'];
    $stmt = $conn->prepare("UPDATE Pedido SET activo=0 WHERE id_pedido=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "🟡 Pedido desactivado.<br><br>";
    $stmt->close();
}

// --- Reactivar pedido ---
if (isset($_GET['activar_pedido'])) {
    $id = $_GET['activar_pedido'];
    $stmt = $conn->prepare("UPDATE Pedido SET activo=1 WHERE id_pedido=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "🟢 Pedido reactivado.<br><br>";
    $stmt->close();
}

// --- Actualizar pedido ---
if (isset($_POST['actualizar_pedido'])) {
    $stmt = $conn->prepare("UPDATE Pedido SET id_cliente=?, estado=?, fecha_pedido=?, total=? WHERE id_pedido=?");
    $stmt->bind_param("issdi",
        $_POST['id_cliente'],
        $_POST['estado'],
        $_POST['fecha_pedido'],
        $_POST['total'],
        $_POST['id_pedido']
    );

    if ($stmt->execute()) {
        echo "✅ Pedido actualizado.<br><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br><br>";
    }
    $stmt->close();
}

// --- Construir consulta ---
$query = "SELECT p.id_pedido, p.id_cliente, p.estado, p.fecha_pedido, p.total, p.activo, c.nombre
          FROM Pedido p
          LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente
          WHERE 1=1 ";

$params = [];
$types = "";

if (!empty($_GET['id_cliente'])) {
    $query .= " AND p.id_cliente = ?";
    $params[] = $_GET['id_cliente'];
    $types .= "i";
}
if (!empty($_GET['estado'])) {
    $query .= " AND p.estado = ?";
    $params[] = $_GET['estado'];
    $types .= "s";
}
if (!isset($_GET['ver_inactivos'])) {
    $query .= " AND p.activo = 1";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// --- Mostrar tabla ---
echo "<table border='1' cellpadding='5'>
<tr>
<th>ID Pedido</th><th>Cliente</th><th>Estado</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Acciones</th>
</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $estado = $row['activo'] ? "✅ Activo" : "🚫 Inactivo";
        echo "<tr style='" . ($row['activo'] ? "" : "background:#f8d7da;") . "'>
        <form method='POST'>
            <td>{$row['id_pedido']}</td>
            <td><input type='number' name='id_cliente' value='{$row['id_cliente']}'></td>
            <td>
                <select name='estado'>
                    <option value='Pendiente' " . ($row['estado']=="Pendiente" ? "selected" : "") . ">Pendiente</option>
                    <option value='Entregado' " . ($row['estado']=="Entregado" ? "selected" : "") . ">Entregado</option>
                    <option value='Cancelado' " . ($row['estado']=="Cancelado" ? "selected" : "") . ">Cancelado</option>
                </select>
            </td>
            <td><input type='date' name='fecha_pedido' value='{$row['fecha_pedido']}'></td>
            <td><input type='number' step='0.01' name='total' value='{$row['total']}'></td>
            <td>$estado</td>
            <td>
                <input type='hidden' name='id_pedido' value='{$row['id_pedido']}'>
                <button type='submit' name='actualizar_pedido'>💾 Guardar</button>
                " . ($row['activo']
                    ? "<a href='panel_pedidos.php?desactivar_pedido={$row['id_pedido']}' onclick=\"return confirm('⚠️ ¿Desactivar pedido #{$row['id_pedido']}?');\">🚫 Desactivar</a>"
                    : "<a href='panel_pedidos.php?activar_pedido={$row['id_pedido']}'>🔄 Activar</a>"
                ) . "
            </td>
        </form></tr>";
    }
} else {
    echo "<tr><td colspan='7'>⚠️ No se encontraron pedidos.</td></tr>";
}

echo "</table>";
$stmt->close();
?>
