
<?php include("db.php"); ?>

<h2>🔎 Buscar, Editar y Desactivar/Activar pagos</h2>

<form method="GET">
    Nombre: <input type="text" name="id_pago" value="<?= isset($_GET['id_pago']) ? $_GET['id_pago'] : '' ?>">


    Mz: <input type="text" name="id_cliente" value="<?= isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '' ?>" size="4">
    Lt: <input type="text" name="fecha" value="<?= isset($_GET['fecha_pago']) ? $_GET['fecha_pago'] : '' ?>" size="4">

    <button type="submit">Buscar</button>
</form>

<hr>

<?php
// --- Desactivar cliente (Soft Delete) ---
if (isset($_GET['desactivar_cliente'])) {
    $id = $_GET['desactivar_cliente'];
    $stmt = $conn->prepare("UPDATE cliente SET activo=0 WHERE id_cliente=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "🟡 Cliente desactivado.<br><br>";
    $stmt->close();
}

// --- Reactivar cliente ---
if (isset($_GET['activar_cliente'])) {
    $id = $_GET['activar_cliente'];
    $stmt = $conn->prepare("UPDATE cliente SET activo=1 WHERE id_cliente=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "🟢 Cliente reactivado.<br><br>";
    $stmt->close();
}

// --- Actualizar cliente ---
if (isset($_POST['ac'])) {
    $stmt = $conn->prepare("UPDATE  SET nombre=?, telefono=?, direccion=?, manzana=?, lote=?, email=?, id_ciudad=? WHERE id_cliente=?");
    $stmt->bind_param("ssssssii",
        $_POST['nombre'],
        $_POST['telefono'],
        $_POST['direccion'],
        $_POST['manzana'],
        $_POST['lote'],
        $_POST['email'],
        $_POST['id_ciudad'],
        $_POST['id_cliente']
    );

    if ($stmt->execute()) {
        echo "✅ Cliente actualizado.<br><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br><br>";
    }
    $stmt->close();
}

// --- Construir consulta ---
$query = "SELECT c.id_cliente, c.nombre, c.telefono, c.direccion, c.manzana, c.lote, c.email, c.id_ciudad, c.activo, ci.nombre_ciudad
          FROM cliente c
          LEFT JOIN ciudad ci ON c.id_ciudad = ci.id_ciudad
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($_GET['nombre'])) {
    $query .= " AND c.nombre LIKE ?";
    $params[] = "%" . $_GET['nombre'] . "%";
    $types .= "s";
}
if (!empty($_GET['id_ciudad'])) {
    $query .= " AND c.id_ciudad = ?";
    $params[] = $_GET['id_ciudad'];
    $types .= "i";
}
if (!empty($_GET['manzana'])) {
    $query .= " AND c.manzana LIKE ?";
    $params[] = $_GET['manzana'];
    $types .= "s";
}
if (!empty($_GET['lote'])) {
    $query .= " AND c.lote LIKE ?";
    $params[] = $_GET['lote'];
    $types .= "s";
}
if (!isset($_GET['ver_inactivos'])) {
    $query .= " AND c.activo = 1";
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
<th>Nombre</th><th>Teléfono</th><th>Dirección</th><th>Mz</th><th>Lt</th><th>Ciudad</th><th>Email</th><th>Estado</th><th>Acciones</th>
</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $estado = $row['activo'] ? "✅ Activo" : "🚫 Inactivo";

echo "<tr style='" . ($row['activo'] ? "" : "background:#f8d7da;") . "'>
        <form method='POST'>
            <td><input type='text' name='nombre' value='{$row['nombre']}' required></td>
            <td><input type='text' name='telefono' value='{$row['telefono']}'></td>
            <td><input type='text' name='direccion' value='{$row['direccion']}'></td>
            <td><input type='text' name='mz' value='{$row['manzana']}' size='3'></td>
            <td><input type='text' name='lt' value='{$row['lote']}' size='3'></td>
            <td>
                <select name='id_ciudad'>";
                    $ciudades = $conn->query("SELECT id_ciudad, nombre_ciudad FROM ciudad ORDER BY nombre_ciudad");
                    while ($c = $ciudades->fetch_assoc()) {
                        $selected = ($c['id_ciudad'] == $row['id_ciudad']) ? "selected" : "";
                        echo "<option value='{$c['id_ciudad']}' $selected>{$c['nombre_ciudad']}</option>";
                    }
        echo "</select>
            </td>
            <td><input type='email' name='email' value='{$row['email']}'></td>
            <td>$estado</td>
            <td>
                <input type='hidden' name='id_cliente' value='{$row['id_cliente']}'>
                <button type='submit' name='actualizar_cliente'>💾 Guardar</button>
                " . ($row['activo']
                    ? "<a href='editarPago.php?desactivar_cliente={$row['id_cliente']}' onclick=\"return confirm('⚠️ ¿Desactivar a {$row['nombre']}?');\">🚫 Desactivar</a>"
                    : "<a href='editarPago.php?activar_cliente={$row['id_cliente']}'>🔄 Activar</a>"
                ) . "
            </td>
        </form></tr>";
    }
} else {
    echo "<tr><td colspan='9'>⚠️ No se encontraron clientes.</td></tr>";
}

echo "</table>";
$stmt->close();
?>



?>
