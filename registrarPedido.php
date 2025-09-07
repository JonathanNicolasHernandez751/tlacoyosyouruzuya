<?php include("db.php"); ?>

<h2>Registrar Pedido</h2>
<form method="POST">

    <select name="id_cliente"  required>
        <option value="">-- Selecciona cliente --</option>
        <?php
        $result = $conn->query("SELECT id_cliente, nombre FROM cliente ORDER BY nombre");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_cliente']}'>{$row['nombre']}</option>";
        }
        ?>
    </select><br>


    Estado:
    <select name="estado">
        <option value="Pendiente">Pendiente</option>
        <option value="Entregado">Entregado</option>
        <option value="Cancelado">Cancelado</option>
    </select><br>
    <button type="submit" name="guardar_pedido">Guardar</button>
</form>

<?php
if (isset($_POST['guardar_pedido'])) {
    $stmt = $conn->prepare("INSERT INTO Pedido (id_cliente, estado) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST['id_cliente'], $_POST['estado']);

    if ($stmt->execute()) {
        echo "Pedido registrado correctamente ✅. ID: " . $stmt->insert_id;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
