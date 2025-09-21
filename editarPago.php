

<?php include("db.php"); ?>

<h2>💰 Buscar, Agregar, Editar y Eliminar Pagos</h2>

<!-- FORMULARIO PARA AGREGAR NUEVO PAGO -->
<form method="POST">
    <h3>➕ Agregar Pago</h3>
    Pedido:
    <select name="id_pedido" required>
        <option value="">-- Selecciona Pedido --</option>
        <?php
        $pedidos = $conn->query("SELECT id_pedido FROM Pedido ORDER BY id_pedido DESC");
        while ($p = $pedidos->fetch_assoc()) {
            echo "<option value='{$p['id_pedido']}'>{$p['id_pedido']}</option>";
        }
        ?>
    </select>
    Método: <input type="text" name="metodo_pago" required>
    Monto: <input type="number" step="0.01" name="monto" required>
    Fecha: <input type="date" name="fecha_pago" value="<?= date('Y-m-d') ?>" required>
    <button type="submit" name="agregar_pago">💾 Guardar</button>
</form>

<hr>

<!-- FORMULARIO DE BÚSQUEDA -->
<form method="GET">
    Pedido: <input type="text" name="id_pedido" value="<?= $_GET['id_pedido'] ?? '' ?>" size="4">
    Método: <input type="text" name="metodo_pago" value="<?= $_GET['metodo_pago'] ?? '' ?>">
    Mostrar eliminados:
    <input type="checkbox" name="ver_eliminados" value="1" <?= isset($_GET['ver_eliminados']) ? "checked" : "" ?>>
    <button type="submit">🔎 Buscar</button>
</form>

<hr>

<?php
// --- AGREGAR NUEVO PAGO ---
if (isset($_POST['agregar_pago'])) {
    $stmt = $conn->prepare("INSERT INTO Pago (id_pedido, metodo_pago, monto, fecha_pago, activo)
                            VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("isds", $_POST['id_pedido'], $_POST['metodo_pago'], $_POST['monto'], $_POST['fecha_pago']);
    if ($stmt->execute()) {
        echo "✅ Pago agregado correctamente.<br><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br><br>";
    }
    $stmt->close();
}

// --- EDITAR PAGO ---
if (isset($_POST['actualizar_pago'])) {
    $stmt = $conn->prepare("UPDATE Pago SET id_pedido=?, metodo_pago=?, monto=?, fecha_pago=? WHERE id_pago=?");
    $stmt->bind_param("isdsi", $_POST['id_pedido'], $_POST['metodo_pago'], $_POST['monto'], $_POST['fecha_pago'], $_POST['id_pago']);
    if ($stmt->execute()) {
        echo "✅ Pago actualizado.<br><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br><br>";
    }
    $stmt->close();
}

// --- ELIMINAR (SOFT DELETE) ---
if (isset($_GET['eliminar_pago'])) {
    $id = $_GET['eliminar_pago'];
    $stmt = $conn->prepare("UPDATE Pago SET activo=0 WHERE id_pago=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "🗑️ Pago eliminado.<br><br>";
    $stmt->close();
}

// --- REACTIVAR ---
if (isset($_GET['activar_pago'])) {
    $id = $_GET['activar_pago'];
    $stmt = $conn->prepare("UPDATE Pago SET activo=1 WHERE id_pago=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "♻️ Pago reactivado.<br><br>";
    $stmt->close();
}

// --- CONSULTA DE PAGOS ---
$query = "SELECT * FROM Pago WHERE 1=1";
$params = [];
$types = "";

if (!empty($_GET['id_pedido'])) {
    $query .= " AND id_pedido = ?";
    $params[] = $_GET['id_pedido'];
    $types .= "i";
}

if (!empty($_GET['metodo_pago'])) {
    $query .= " AND metodo_pago LIKE ?";
    $params[] = "%" . $_GET['metodo_pago'] . "%";
    $types .= "s";
}

if (!isset($_GET['ver_eliminados'])) {
    $query .= " AND activo=1";
}

$query .= " ORDER BY fecha_pago DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// --- MOSTRAR TABLA ---
echo "<table border='1' cellpadding='5'>
<tr>
<th>ID</th><th>Pedido</th><th>Método</th><th>Monto</th><th>Fecha</th><th>Estado</th><th>Acciones</th>
</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $estado = $row['activo'] ? "✅ Activo" : "🚫 Eliminado";


echo "<tr style='" . ($row['activo'] ? "" : "background:#f8d7da;") . "'>
        <form method='POST'>
            <td>{$row['id_pago']}</td>
            <td><input type='number' name='id_pedido' value='{$row['id_pedido']}' required></td>
            <td><input type='text' name='metodo_pago' value='{$row['metodo_pago']}' required></td>
            <td><input type='number' step='0.01' name='monto' value='{$row['monto']}' required></td>
            <td><input type='date' name='fecha_pago' value='{$row['fecha_pago']}' required></td>
            <td>$estado</td>
            <td>
                <input type='hidden' name='id_pago' value='{$row['id_pago']}'>
                <button type='submit' name='actualizar_pago'>💾 Guardar</button>
                " . ($row['activo']
                    ? "<a href='panel_pagos.php?eliminar_pago={$row['id_pago']}' onclick=\"return confirm('¿Eliminar este pago?');\">🗑️ Eliminar</a>"
                    : "<a href='panel_pagos.php?activar_pago={$row['id_pago']}'>♻️ Reactivar</a>"
                ) . "
            </td>
        </form></tr>";
    }
} else {
    echo "<tr><td colspan='7'>⚠️ No se encontraron pagos.</td></tr>";
}

echo "</table>";
$stmt->close();
?>
