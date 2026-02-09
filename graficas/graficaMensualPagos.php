<?php 
include "db.php"; 

// =================================================================
// 1. BLOQUE PHP PARA LA LÓGICA DEL FORMULARIO DE PAGO
// =================================================================

if (isset($_POST["guardar_pago"])) {
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
        echo "❌ Error: El pedido no existe.";
        // No usar exit() si se quiere que el resto del HTML/Gráfico se muestre
    } else {
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
}


// =================================================================
// 2. BLOQUE PHP PARA OBTENER DATOS DEL GRÁFICO (MONTO MENSUAL)
// =================================================================

// Consulta para obtener el total de montos (SUM(total)) de pedidos por mes y año
$query_grafico = "SELECT
                      YEAR(fecha_pedido) AS anio,
                      MONTH(fecha_pedido) AS mes_numero,
                      DATE_FORMAT(fecha_pedido, '%Y-%m') AS anio_mes,
                      SUM(total) AS total_monto_mensual
                  FROM Pedido
                  GROUP BY anio_mes
                  ORDER BY anio_mes ASC";

$result_grafico = $conn->query($query_grafico);

$labels = [];          // Nombres de los meses/años para el eje X
$datasets_data = [];   // Montos totales para el gráfico

// Mapeo de números a nombres de meses
$nombres_meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

if ($result_grafico && $result_grafico->num_rows > 0) {
    while ($row = $result_grafico->fetch_assoc()) {
        $mes_nombre = $nombres_meses[(int)$row['mes_numero']];
        $etiqueta = $mes_nombre . " " . $row['anio'];

        $labels[] = $etiqueta;
        // Usamos floatval() para asegurar el tipo de dato numérico
        $datasets_data[] = floatval($row['total_monto_mensual']); 
    }
}

// Convertir los arrays de PHP a cadenas JSON para pasarlos a JavaScript
$labels_json = json_encode($labels);
$data_json = json_encode($datasets_data);

// Cierra la conexión a la base de datos si ya no se usará
$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro y Gráfico</title>
</head>
<body>

<h2>Registrar Pago</h2>
<form method="POST">
    Pedido:
    <select name="id_pedido" required>
        <option value="">-- Selecciona Pedido --</option>
        <?php
        // Nota: Si cerraste la conexión arriba, este bloque fallará. 
        // Para que funcione, db.php debe estar incluido al inicio y la conexión abierta.
        // Si necesitas que esto funcione, retira la línea $conn->close() del bloque PHP anterior.
        /*
        $conn_form = new mysqli("tu_host", "tu_usuario", "tu_password", "tu_bd");
        $result = $conn_form->query("SELECT id_pedido, total, estado FROM Pedido WHERE estado <> 'Cancelado'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row["id_pedido"]}'>
                    Pedido #{$row["id_pedido"]} - Total: $ {$row["total"]} - Estado: {$row["estado"]}
                  </option>";
        }
        $conn_form->close(); 
        */
        // Dejamos el código original asumiendo que la conexión sigue activa:
        // * SE RECOMIENDA NO CERRAR LA CONEXIÓN HASTA EL FINAL DEL ARCHIVO *
        ?>
    </select><br><br>

    Método de Pago:
    <select name="metodo_pago" required>
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option>
    </select><br><br>

    Monto: <input type="number" step="0.01" name="monto" required><br><br>

    <button type="submit" name="guardar_pago">Registrar Pago</button>
</form>

<hr>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div style="width: 80%; margin: 40px auto;">
    <h3>Monto Total de Pedidos por Mes</h3>
    <canvas id="montoPedidosChart"></canvas>
</div>

<script>
    // 1. Variables de PHP pasadas a JavaScript
    const labelsMonto = <?php echo $labels_json; ?>;
    const dataMontos = <?php echo $data_json; ?>;

    const dataConfig = {
        labels: labelsMonto,
        datasets: [{
            label: 'Monto Total de Pedidos',
            data: dataMontos,
            backgroundColor: 'rgba(75, 192, 192, 0.6)', 
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    const chartOptions = {
        type: 'bar', // Tipo de gráfico de barras
        data: dataConfig,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto Total'
                    },
                    // Formatear los valores del eje Y como moneda
                    ticks: {
                        callback: function(value, index, values) {
                            // Asumiendo moneda local (ejemplo: pesos)
                            return '$' + value.toLocaleString('es-MX', { minimumFractionDigits: 2 });
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mes y Año'
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Monto Total de Pedidos Mensuales'
                },
                // Formatear el tooltip (etiqueta al pasar el mouse) como moneda
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                // Usa el objeto Intl para un formato de moneda correcto
                                label += new Intl.NumberFormat('es-MX', { 
                                    style: 'currency', 
                                    currency: 'MXN' // Cambia 'MXN' por tu código de moneda (USD, EUR, etc.)
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    };
    // Crear la instancia del gráfico
    const montoPedidosChart = new Chart(
        document.getElementById('montoPedidosChart'),
        chartOptions
    );
</script>

</body>
</html>