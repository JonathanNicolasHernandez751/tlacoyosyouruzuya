<?php include("db.php"); ?>

<h2>🏙️ Administración de Ciudades</h2>

<!-- Formulario para agregar ciudad -->
<form method="POST">
    <input type="text" name="nombre_cuidad" placeholder="Nombre de ciudad" required>
    <button type="submit" name="agregar">Agregar Ciudad</button>
</form>

<hr>

<?php
// Agregar ciudad
if (isset($_POST['agregar'])) {
    $nombre_ciudad = trim($_POST['nombre_ciudad']);
    $stmt = $conn->prepare("INSERT INTO Ciudad (nombre_ciudad) VALUES (?)");
    $stmt->bind_param("s", $nombre_ciudad);

    if ($stmt->execute()) {
        echo "✅ Ciudad agregada correctamente.<br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Eliminar ciudad
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM ciudad WHERE id_ciudad = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "🗑️ Ciudad eliminada.<br>";
    } else {
        echo "⚠️ No se pudo eliminar. Verifica que no tenga clientes relacionados.<br>";
    }
    $stmt->close();
}

// Editar ciudad
if (isset($_POST['editar'])) {
    $id = $_POST['id_ciudad'];
    $nuevo_nombre = trim($_POST['nuevo_nombre']);
    $stmt = $conn->prepare("UPDATE Ciudad SET nombre_ciudad = ? WHERE id_ciudad = ?");
    $stmt->bind_param("si", $nuevo_nombre, $id);

    if ($stmt->execute()) {
        echo "✏️ Ciudad actualizada.<br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Mostrar todas las ciudades
$result = $conn->query("SELECT * FROM Ciudad ORDER BY nombre_ciudad");
echo "<table border='1' cellpadding='5'>
<tr><th>ID</th><th>Ciudad</th><th>Acciones</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id_ciudad']}</td>
        <td>{$row['nombre_ciudad']}</td>
        <td>
            <!-- Botón de editar -->
            <form style='display:inline;' method='POST'>
                <input type='hidden' name='id_ciudad' value='{$row['id_ciudad']}'>
                <input type='text' name='nuevo_nombre' value='{$row['nombre_ciudad']}' required>
                <button type='submit' name='editar'>Actualizar</button>
            </form>
            |
            <!-- Botón de eliminar -->
            <a href='ciudad.php?eliminar={$row['id_ciudad']}' onclick=\"return confirm('¿Eliminar ciudad?');\">Eliminar</a>
        </td>
    </tr>";
}
echo "</table>";
?>
