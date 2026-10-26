<?php
// ── Cabeceras de seguridad ───────────────────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

// ── Conexión ─────────────────────────────────────────────────────────────────
require_once 'conexion2.php';

// ── Inicializar arreglos ANTES del try (evita errores si la consulta falla) ──
$nombresProd    = [];
$cantidadesProd = [];

// ── Consulta ─────────────────────────────────────────────────────────────────
try {
    $sqlProd = "SELECT p.nombre_producto, SUM(d.cantidad) AS total_unidades
                FROM detallepedido d
                INNER JOIN producto p ON d.id_producto = p.id_producto
                GROUP BY p.id_producto
                ORDER BY total_unidades DESC
                LIMIT 5";

    $stmtProd = $pdo->query($sqlProd);
    $datosProd = $stmtProd->fetchAll(PDO::FETCH_ASSOC); // ✅ Solo índices por nombre

    foreach ($datosProd as $p) {
        $nombresProd[]    = $p['nombre_producto'];
        $cantidadesProd[] = (int) $p['total_unidades']; // ✅ Forzar tipo entero
    }

} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage()); // ✅ Log interno, no expuesto
    $errorMsg = "No se pudo cargar la información. Inténtalo más tarde.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Youruzuya</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .chart-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 420px;
            text-align: center;
        }
        h2 { margin-bottom: 1.2rem; color: #333; }
        .error-msg {
            color: #e53e3e;
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 1rem;
        }
        .empty-msg {
            color: #718096;
            padding: 2rem 0;
        }
    </style>
</head>
<body>

<div class="chart-container">
    <h2>🌮 Top 5 Productos</h2>

    <?php if (isset($errorMsg)): ?>
        <!-- ✅ Mensaje amigable, sin exponer detalles técnicos -->
        <p class="error-msg"><?php echo htmlspecialchars($errorMsg); ?></p>

    <?php elseif (empty($nombresProd)): ?>
        <!-- ✅ Caso de datos vacíos manejado -->
        <p class="empty-msg">📭 No hay datos de ventas disponibles aún.</p>

    <?php else: ?>
        <canvas id="graficoProductos"></canvas>
        <script>
            const ctxProd = document.getElementById('graficoProductos').getContext('2d');
            new Chart(ctxProd, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($nombresProd, JSON_UNESCAPED_UNICODE); ?>,
                    datasets: [{
                        data: <?php echo json_encode($cantidadesProd); ?>,
                        backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',  // ✅ Leyenda visible
                            labels: { padding: 16, font: { size: 13 } }
                        },
                        tooltip: {
                            enabled: true,       // ✅ Tooltips activados
                            callbacks: {
                                label: (ctx) => ` ${ctx.label}: ${ctx.parsed} uds.`
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>

</div>

</body>
</html>
