<?php include("db.php"); ?>

<h2>🔎 Buscar, Editar y Desactivar/Activar Pedidos</h2>

<form method="GET">
    Nombre del cliente:
    <input type="text" name="nombre_cliente" placeholder="Ej: Juan Pérez"
           value="<?= isset($_GET['nombre_cliente']) ? htmlspecialchars($_GET['nombre_cliente']) : '' ?>">
    <ul id="resultados" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; display:none;"></ul>
    <br>
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

if (!empty($_GET['nombre_cliente'])) {
    $query .= " AND c.nombre LIKE ?";
    $params[] = "%" . $_GET['nombre_cliente'] . "%";
    $types .= "s";
}

if (!empty($_GET['estado'])) {
    $query .= " AND p.estado = ?";
    $params[] = $_GET['estado'];
    $types .= "s";
}

if (!isset($_GET['ver_inactivos'])) {
    $query .= " AND p.activo = 1";
}

// Ordenar por fecha más reciente primero
$query .= " ORDER BY p.id_pedido DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// --- Mostrar tabla ---
echo "<table border='1' cellpadding='5'>
<tr>
    <th>ID Pedido</th>
    <th>Cliente</th>
    <th>Estado</th>
    <th>Fecha</th>
    <th>Total</th>
    <th>Activo</th>
    <th>Acciones</th>
</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $estado_activo = $row['activo'] ? "✅ Activo" : "🚫 Inactivo";
        $bg_style = $row['activo'] ? "" : "background:#f8d7da;";

        echo "<tr style='$bg_style'>
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
            <td>$estado_activo</td>
            <td>
                <input type='hidden' name='id_pedido' value='{$row['id_pedido']}'>
                <button type='submit' name='actualizar_pedido'>💾 Guardar</button>
                " . ($row['activo']
                    ? "<a href='?desactivar_pedido={$row['id_pedido']}' onclick=\"return confirm('⚠️ ¿Desactivar pedido #{$row['id_pedido']}?');\">🚫 Desactivar</a>"
                    : "<a href='?activar_pedido={$row['id_pedido']}'>🔄 Activar</a>"
                ) . "
            </td>
        </form></tr>";
    }
} else {
    echo "<tr><td colspan='7'>⚠️ No se encontraron pedidos.</td></tr>";
}

echo "</table>";
$stmt->close();
$conn->close(); // opcional, pero buena práctica
?>
<script>
// --- AJAX buscador ---
document.getElementById("nombre_cliente").addEventListener("keyup", function() {
    let query = this.value;

    if (query.length < 2) { // solo buscar si hay al menos 2 letras
        document.getElementById("resultados").style.display = "none";
        return;
    }

    fetch("buscar_clientes.php?nombre=" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            let resultados = document.getElementById("resultados");
            resultados.innerHTML = "";
            if (data.length > 0) {
                resultados.style.display = "block";
                data.forEach(cliente => {
                    let li = document.createElement("li");
                    li.textContent = cliente.nombre;
                    li.style.cursor = "pointer";
                    li.onclick = function() {
                        document.getElementById("id_cliente").value = cliente.id_cliente;
                        document.getElementById("cliente_seleccionado").innerText =
                            "Cliente seleccionado: " + cliente.nombre;
                        resultados.style.display = "none";
                    };
                    resultados.appendChild(li);
                });
            } else {
                resultados.style.display = "none";
            }
        });
});
</script>
