<?php include("db.php"); ?>

<h2>Registrar Pago</h2>
<form method="POST">
    <!-- Selección de Pedido -->
    Pedido:
    <select name="id_pedido" required>
        <option value="">-- Selecciona Pedido --</option>
        <?php
        $result = $conn->query("SELECT id_pedido, total, estado FROM Pedido WHERE estado <> 'Cancelado'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_pedido']}'>
                    Pedido #{$row['id_pedido']} - Total: $ {$row['total']} - Estado: {$row['estado']}
                  </option>";
        }
        ?>
    </select><br><br>

    <!-- Método de pago -->
    Método de Pago:
    <select name="metodo_pago" required>
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option>
    </select><br><br>

    <!-- Monto -->
    Monto: <input type="number" step="0.01" name="monto" required><br><br>

    <button type="submit" name="guardar_pago">Registrar Pago</button>
</form>

<?php
if (isset($_POST['guardar_pago'])) {
    $id_pedido = $_POST['id_pedido'];
    $metodo_pago = $_POST['metodo_pago'];
    $monto = $_POST['monto'];

    // Verificar que el pedido existe y obtener el total
    $stmt = $conn->prepare("SELECT total FROM Pedido WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $stmt->bind_result($total_pedido);
    $stmt->fetch();
    $stmt->close();

    if (!$total_pedido) {
        echo "❌ Error: El pedido no existe.";
        exit;
    }

    // Validar monto
    if ($monto < $total_pedido) {
        echo "⚠️ Advertencia: El pago es menor al total del pedido. Debe completarse.";
    } elseif ($monto > $total_pedido) {
        echo "⚠️ Advertencia: El pago excede el total del pedido.";
    }

    // Insertar en Pago
    $stmt = $conn->prepare("INSERT INTO Pago (id_pedido, metodo_pago, monto) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_pedido, $metodo_pago, $monto);

    if ($stmt->execute()) {
        echo "✅ Pago registrado correctamente.";

        // Actualizar estado del pedido a "Entregado" si está pagado
        if ($monto >= $total_pedido) {
            $conn->query("UPDATE Pedido SET estado = 'Entregado' WHERE id_pedido = $id_pedido");
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
