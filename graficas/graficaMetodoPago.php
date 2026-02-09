<?php include("db.php"); ?>
<?php// =================================================================
// 3. BLOQUE PHP PARA OBTENER DATOS DEL GRÁFICO (MÉTODO DE PAGO)
// =================================================================

// Consulta para obtener la suma total de montos por método de pago
$query_metodos_pago = "SELECT metodo_pago, SUM(monto) AS total_por_metodo 
                       FROM pago 
                       GROUP BY metodo_pago 
                       ORDER BY total_por_metodo DESC";
                       
// Nota: Usamos 'Pago' ya que la información del monto pagado está allí, no en 'Pedido'.
$result_metodos = $conn->query($query_metodos_pago); 

$metodos_labels = [];
$metodos_data = [];

if ($result_metodos && $result_metodos->num_rows > 0) {
    while ($row = $result_metodos->fetch_assoc()) {
        $metodos_labels[] = $row['metodo_pago'];
        $metodos_data[] = floatval($row['total_por_metodo']);
    }
}

// Convertir los arrays de PHP a cadenas JSON
$metodos_labels_json = json_encode($metodos_labels);
$metodos_data_json = json_encode($metodos_data);
?>
<html>
<head>
    
</head>
<body>
<hr>
<div style="width: 50%; margin: 40px auto;">
    <h3>Distribución de Ingresos por Método de Pago</h3>
    <canvas id="metodosPagoChart"></canvas>
</div>


<script>
    // =================================================================
    // GRÁFICO DE MÉTODO DE PAGO (DOUGHNUT/DONA)
    // =================================================================
    
    // 1. Variables de PHP pasadas a JavaScript
    const metodosLabels = <?php echo $metodos_labels_json; ?>;
    const metodosData = <?php echo $metodos_data_json; ?>;

    // Colores para cada sección (puedes ajustar estos colores)
    const colores = [
        'rgba(255, 99, 132, 0.8)', // Rojo
        'rgba(54, 162, 235, 0.8)', // Azul
        'rgba(255, 206, 86, 0.8)', // Amarillo
        'rgba(75, 192, 192, 0.8)'  // Verde
    ];

    const metodosDataConfig = {
        labels: metodosLabels,
        datasets: [{
            label: 'Total Pagado',
            data: metodosData,
            backgroundColor: colores.slice(0, metodosLabels.length),
            hoverOffset: 4
        }]
    };

    const metodosChartOptions = {
        type: 'doughnut', // Gráfico de Dona o 'pie' (circular)
        data: metodosDataConfig,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Porcentaje de Ingreso por Vía de Pago'
                },
                // Formatear el tooltip para mostrar el monto y el porcentaje
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const currentValue = context.raw;
                            const percentage = ((currentValue / total) * 100).toFixed(1);
                            
                            const monedaFormato = new Intl.NumberFormat('es-MX', { 
                                style: 'currency', 
                                currency: 'MXN' 
                            }).format(currentValue);

                            return ${context.label}: ${monedaFormato} (${percentage}%);
                        }
                    }
                }
            }
        }
    };

    // Crear la instancia del gráfico
    const metodosPagoChart = new Chart(
        document.getElementById('metodosPagoChart'),
        metodosChartOptions
    );
</script>
</body>s
</html>
