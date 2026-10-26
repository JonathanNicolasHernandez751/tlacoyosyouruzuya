<?php include "db.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pago</title>
  <link rel="stylesheet" href="css/registrarPago.css">
</head>
<body>

<div class="container">
    <div class="header">
        <h2>💳 Registrar Pago</h2>
        <p>Completa el formulario para registrar un pago</p>
    </div>

    <form method="POST" id="form-pago">

        <?php
        // Mostrar mensajes de resultado al inicio
        if (isset($_POST["guardar_pago"])) {
            $id_pedido   = $_POST["id_pedido"];
            $metodo_pago = $_POST["metodo_pago"];
            $monto       = $_POST["monto"];

            $stmt = $conn->prepare("SELECT total FROM Pedido WHERE id_pedido = ?");
            $stmt->bind_param("i", $id_pedido);
            $stmt->execute();
            $stmt->bind_result($total_pedido);
            $stmt->fetch();
            $stmt->close();

            if (!$total_pedido) {
                echo "<div class='alert error'>❌ El pedido no existe.</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO Pago (id_pedido, metodo_pago, monto) VALUES (?, ?, ?)");
                $stmt->bind_param("isd", $id_pedido, $metodo_pago, $monto);

                if ($stmt->execute()) {
                    echo "<div class='alert success'>✅ Pago registrado correctamente.</div>";

                    if ($monto >= $total_pedido) {
                        $conn->query("UPDATE Pedido SET estado = 'Entregado' WHERE id_pedido = $id_pedido");
                    } elseif ($monto < $total_pedido) {
                        echo "<div class='alert warning'>⚠️ El pago no cubre el total. El pedido sigue pendiente.</div>";
                    }
                } else {
                    echo "<div class='alert error'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            }
        }
        ?>

        <div class="form-group">
            <label for="id_pedido">Pedido</label>
            <select name="id_pedido" id="id_pedido" required>
                <option value="">-- Selecciona Pedido --</option>
                <?php
                $result = $conn->query("SELECT id_pedido, total, estado FROM Pedido WHERE estado <> 'Cancelado' ORDER BY id_pedido DESC");
                while ($row = $result->fetch_assoc()) {
                    $total_fmt = number_format($row["total"], 2);
                    echo "<option value='{$row["id_pedido"]}' data-total='{$row["total"]}'>
                            Pedido #{$row["id_pedido"]} — $ {$total_fmt} — {$row["estado"]}
                          </option>";
                }
                ?>
            </select>
        </div>

        <div class="total-info">
            <span>Total del pedido</span>
            <strong id="total_pedido">—</strong>
        </div>

        <div class="form-group">
            <label for="metodo_pago">Método de Pago</label>
            <select name="metodo_pago" id="metodo_pago" required>
                <option value="Efectivo">💵 Efectivo</option>
                <option value="Transferencia">🏦 Transferencia</option>
                <option value="Tarjeta">💳 Tarjeta</option>
            </select>
        </div>

        <div class="form-group">
            <label for="monto">Monto a pagar</label>
            <input type="number" step="0.01" name="monto" id="monto" required min="0.01" placeholder="0.00">
        </div>

        <div id="mensaje-monto"></div>

        <button type="submit" name="guardar_pago">Registrar Pago</button>
    </form>
</div>

<script>
    const pedidoSelect = document.getElementById('id_pedido');
    const montoInput   = document.getElementById('monto');
    const totalLabel   = document.getElementById('total_pedido');
    const msg          = document.getElementById('mensaje-monto');

    // Obtiene el total actual como número
    function getTotal() {
        return parseFloat(totalLabel.textContent.replace(/[^0-9.]/g, '')) || 0;
    }

    function validarMonto() {
        const monto = parseFloat(montoInput.value) || 0;
        const total = getTotal();

        if (!total || monto === 0) {
            msg.textContent = '';
            msg.className = '';
            return;
        }

        if (monto < total) {
            const falta = (total - monto).toFixed(2);
            msg.textContent = `⚠️ Faltan $${falta} para completar el pago`;
            msg.className = 'warning';
        } else if (monto > total) {
            const exceso = (monto - total).toFixed(2);
            msg.textContent = `⚠️ Sobrepago de $${exceso}`;
            msg.className = 'warning';
        } else {
            msg.textContent = '✓ Pago exacto';
            msg.className = 'success';
        }
    }

    pedidoSelect.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        const total  = parseFloat(option.getAttribute('data-total')) || 0;

        totalLabel.textContent = total
            ? '$' + total.toFixed(2)
            : '—';

        montoInput.value = total ? total.toFixed(2) : '';
        validarMonto();
    });

    montoInput.addEventListener('input', validarMonto);

    document.getElementById('form-pago').addEventListener('submit', function (e) {
        const monto = parseFloat(montoInput.value) || 0;
        const total = getTotal();

        if (monto !== total && total > 0) {
            const accion = monto < total ? 'NO COMPLETA' : 'EXCEDE';
            const ok = confirm(
                `El monto $${monto.toFixed(2)} ${accion} el total del pedido ($${total.toFixed(2)}).\n\n¿Deseas continuar de todos modos?`
            );
            if (!ok) e.preventDefault();
        }
    });
</script>

</body>
</html>
