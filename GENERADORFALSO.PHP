<?php include("db.php"); ?>
<?php
require_once 'corrreelectronico/vendor/autoload.php';
$faker = Faker\Factory::create('es_ES');

// Generar un email inicial
$emailInicial = $faker->email();
?>

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
    
    Email: 
    <input type="email" name="email" value="<?php echo $emailInicial; ?>">
    <button type="submit" name="regenerar_email" formnovalidate>Regenerar Email</button>
    <br>

    <button type="submit" name="guardar_cliente">Guardar Cliente</button>
</form>

<?php
// Regenerar email si se hace clic en el botón
if (isset($_POST['regenerar_email'])) {
    $nuevoEmail = $faker->email();
    echo "<script>document.querySelector('input[name=\"email\"]').value = '$nuevoEmail';</script>";
}

if (isset($_POST['guardar_cliente'])) {
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("INSERT INTO Cliente (nombre, telefono, direccion, manzana, lote, email, id_ciudad) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $_POST['nombre'], $_POST['telefono'], $_POST['direccion'], $_POST['manzana'], $_POST['lote'], $email, $_POST['id_ciudad']);

    if ($stmt->execute()) {
        echo "✅ Cliente registrado correctamente";
        echo "<br>📧 Email: " . $email;
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>