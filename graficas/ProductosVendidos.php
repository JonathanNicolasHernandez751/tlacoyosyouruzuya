

<?php
// Llamamos al archivo de conexión
require_once 'conexion2.php';

try {
    // CONSULTA PARA EL GRÁFICO DE PRODUCTOS
    $sqlProd = "SELECT p.nombre_producto, SUM(d.cantidad) AS total_unidades
                FROM detallepedido d
                INNER JOIN producto p ON d.id_producto = p.id_producto
                GROUP BY p.id_producto
                ORDER BY total_unidades DESC
                LIMIT 5";

    $stmtProd = $pdo->query($sqlProd);
    $datosProd = $stmtProd->fetchAll();

    // Preparar arreglos para JS
    $nombresProd = [];
    $cantidadesProd = [];
    foreach($datosProd as $p) {
        $nombresProd[] = $p['nombre_producto'];
        $cantidadesProd[] = $p['total_unidades'];
    }

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Youruzuya</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { width: 400px; margin: auto; text-align: center; font-family: sans-serif; }
    </style>
</head>
<body>

    <div class="chart-container">
        <h2>🌮 Top 5 Productos</h2>
        <canvas id="graficoProductos"></canvas>
    </div>

    <script>
        const ctxProd = document.getElementById('graficoProductos').getContext('2d');
        new Chart(ctxProd, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($nombresProd); ?>,
                datasets: [{
                    data: <?php echo json_encode($cantidadesProd); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            }
        });
    </script>
</body>
</html>

