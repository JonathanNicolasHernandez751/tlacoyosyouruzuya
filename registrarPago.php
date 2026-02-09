Waifus, [11/01/2026 06:47 p. m.]
<?php include "db.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pago</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        form { background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        button:hover { background: #45a049; }
        #mensaje-monto { margin-top: -10px; margin-bottom: 15px; font-size: 0.9em; }
        .success { color: #2ecc71; }
        .warning { color: #e67e22; }
        .error { color: #e74c3c; }
    </style>
</head>
<body>

<h2>Registrar Pago</h2>

<form method="POST" id="form-pago">
    <label for="id_pedido">Pedido:</label>
    <select name="id_pedido" id="id_pedido" required>
        <option value="">-- Selecciona Pedido --</option>
        <?php
        $result = $conn->query("SELECT id_pedido, total, estado FROM Pedido WHERE estado <> 'Cancelado'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row["id_pedido"]}' data-total='{$row["total"]}'>
                    Pedido #{$row["id_pedido"]} - Total: $ {$row["total"]} - Estado: {$row["estado"]}
                  </option>";
        }
        ?>
    </select>

    <div style="margin-bottom: 15px;">Total del pedido: <strong id="total_pedido">—</strong></div>
    
    <label for="metodo_pago">Método de Pago:</label>
    <select name="metodo_pago" id="metodo_pago" required>
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option>
    </select>

    <label for="monto">Monto a pagar:</label>
    <input type="number" step="0.01" name="monto" id="monto" required min="0.01">
    <div id="mensaje-monto"></div>

    <button type="submit" name="guardar_pago">Registrar Pago</button>
</form>

<?php if (isset($_POST["guardar_pago"])) {
    $id_pedido = $_POST["id_pedido"];
    $metodo_pago = $_POST["metodo_pago"];
    $monto = $_POST["monto"];

    // Verificar que el pedido existe y obtener el total
    $stmt = $conn->prepare("SELECT total FROM Pedido WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $stmt->bind_result($total_pedido);
    $stmt->fetch();
    $stmt->close();

    if (!$total_pedido) {
        echo "<p class='error'>❌ Error: El pedido no existe.</p>";
        exit();
    }

    // Insertar en Pago
    $stmt = $conn->prepare("INSERT INTO Pago (id_pedido, metodo_pago, monto) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_pedido, $metodo_pago, $monto);

    if ($stmt->execute()) {
        echo "<p class='success'>✅ Pago registrado correctamente.</p>";

        // Actualizar estado del pedido a "Entregado" si está pagado completamente
        if ($monto >= $total_pedido) {
            $conn->query("UPDATE Pedido SET estado = 'Entregado' WHERE id_pedido = $id_pedido");
        }
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<script>
document.getElementById('id_pedido').addEventListener('change', function() {
    const select = this;
    const option = select.options[select.selectedIndex];
    const total = option.getAttribute('data-total') || 0;
    
    document.getElementById('total_pedido').textContent = '$' + parseFloat(total).toFixed(2);
    document.getElementById('monto').value = total; // Sugerir monto completo
    validarMonto(); // Validar inmediatamente
});

document.getElementById('monto').addEventListener('input', validarMonto);

Waifus, [11/01/2026 06:47 p. m.]
function validarMonto() {
    const montoInput = document.getElementById('monto');
    const monto = parseFloat(montoInput.value) || 0;
    const total = parseFloat(document.getElementById('total_pedido').textContent.replace(/[$\s]/g, '')) || 0;
    
    const msg = document.getElementById('mensaje-monto');
    
    if (monto === 0) {
        msg.textContent = '';
        msg.className = '';
    } else if (monto < total) {
        msg.textContent = ⚠️ Falta \[ {(total - monto).toFixed(2)} para completar el pago;
        msg.className = 'warning';
    } else if (monto > total) {
        msg.textContent = ⚠️ Sobrepago de \]{(monto - total).toFixed(2)};
        msg.className = 'warning';
    } else {
        msg.textContent = '✓ Pago exacto';
        msg.className = 'success';
    }
}

document.getElementById('form-pago').addEventListener('submit', function(e) {
    const monto = parseFloat(document.getElementById('monto').value);
    const total = parseFloat(document.getElementById('total_pedido').textContent.replace(/[$\s]/g, ''));
    
    if (monto !== total && !confirm(El monto \[ {monto.toFixed(2)} ${monto < total ? 'NO COMPLETA' : 'EXCEDE'} el total del pedido ( \]{total.toFixed(2)}).\n\n¿Deseas continuar de todos modos?)) {
        e.preventDefault();
    }
});
</script>

</body>
</html>