<?php include("db.php"); ?>

<h2>Registrar Cliente</h2>
<form method="POST">
    Nombre: <input type="text" name="nombre" required><br>
    Teléfono: <input type="text" name="telefono"><br>
    Dirección: <input type="text" name="direccion"><br>
    Mz: <input type="text" name="manzana"><br>
    Lt: <input type="text" name="lote"><br>
    Ciudad:
    <select name="id_ciudad" required>
        <option value="">-- Selecciona Ciudad --</option>
        <?php
        $result = $conn->query("SELECT id_ciudad, nombre_ciudad FROM ciudad ORDER BY nombre_ciudad");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_ciudad']}'>{$row['nombre_ciudad']}</option>";
        }
        ?>
    </select><br>
    Email: <input type="email" name="email"><br>


    <button type="submit" name="guardar_cliente">Guardar</button>
</form>

<?php

if (isset($_POST['guardar_cliente'])) {
    $stmt = $conn->prepare("INSERT INTO Cliente (nombre, telefono, direccion, manzana, lote, email, id_ciudad) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $_POST['nombre'], $_POST['telefono'], $_POST['direccion'], $_POST['manzana'], $_POST['lote'], $_POST['email'], $_POST['id_ciudad']);

    if ($stmt->execute()) {
        echo "✅ Cliente registrado correctamente";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    }
    $stmt->close();
}
?>
