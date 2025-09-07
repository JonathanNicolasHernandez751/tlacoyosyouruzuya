<?php include("db.php"); ?>

<h2>🔎 Buscar Clientes</h2>

<form method="GET">
    Nombre: <input type="text" name="nombre" value="<?= isset($_GET['nombre']) ? $_GET['nombre'] : '' ?>">
    Ciudad:
    <select name="id_ciudad">
        <option value="">-- Todas --</option>
        <?php
        $result = $conn->query("SELECT id_ciudad, nombre_ciudad FROM ciudad ORDER BY nombre_ciudad");
        while ($row = $result->fetch_assoc()) {
            $selected = (isset($_GET['id_ciudad']) && $_GET['id_ciudad'] == $row['id_ciudad']) ? 'selected' : '';
            echo "<option value='{$row['id_ciudad']}' $selected>{$row['nombre_ciudad']}</option>";
        }
        ?>
    </select>
    Mz: <input type="text" name="mz" value="<?= isset($_GET['manzana']) ? $_GET['manzana'] : '' ?>" size="4">
    Lt: <input type="text" name="lt" value="<?= isset($_GET['lote']) ? $_GET['lote'] : '' ?>" size="4">
    <button type="submit">Buscar</button>
</form>

<hr>

<?php
// Construir consulta dinámica según filtros
$query = "SELECT c.nombre, c.telefono, c.direccion, c.manzana, c.lote, ci.nombre_ciudad, c.email
          FROM Cliente c
          LEFT JOIN Ciudad ci ON c.id_ciudad = ci.id_ciudad
          WHERE 1=1";

$params = [];
$types = "";

// Filtros dinámicos
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
    $params[] = $_GET['mz'];
    $types .= "s";
}

if (!empty($_GET['lote'])) {
    $query .= " AND c.lote LIKE ?";
    $params[] = $_GET['lt'];
    $types .= "s";
}

$stmt = $conn->prepare($query);

// Si hay filtros, los vinculamos
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>
<tr><th>Nombre</th><th>Teléfono</th><th>Dirección</th><th>Mz</th><th>Lt</th><th>Ciudad</th><th>Email</th></tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['nombre']}</td>
                <td>{$row['telefono']}</td>
                <td>{$row['direccion']}</td>
                <td>{$row['manzana']}</td>
                <td>{$row['lote']}</td>
                <td>{$row['nombre_ciudad']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>⚠️ No se encontraron clientes.</td></tr>";
}

echo "</table>";
$stmt->close();
?>
