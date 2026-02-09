<?php include("db.php"); ?>

<h2>Agregar Producto a un Pedido</h2>
<?php
// ... (Tu código de conexión a db.php)

// Consulta para obtener la cantidad de pedidos por mes y año
$query_grafico = "SELECT
                      YEAR(fecha_pedido) AS anio,
                      MONTH(fecha_pedido) AS mes_numero,
                      DATE_FORMAT(fecha_pedido, '%Y-%m') AS anio_mes,
                      COUNT(id_pedido) AS total_pedidos
                  FROM Pedido
                  GROUP BY anio_mes
                  ORDER BY anio_mes ASC";

$result_grafico = $conn->query($query_grafico);

$data_pedidos = [];
$labels = []; // Nombres de los meses/años para el eje X
$datasets_data = []; // Cantidad de pedidos para el gráfico

// Mapeo simple de número de mes a nombre (puedes usar una librería de localización si lo necesitas)
$nombres_meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

if ($result_grafico->num_rows > 0) {
    while ($row = $result_grafico->fetch_assoc()) {
        $mes_nombre = $nombres_meses[(int)$row['mes_numero']];
        $etiqueta = $mes_nombre . " " . $row['anio']; // Ejemplo: Enero 2024

        $labels[] = $etiqueta;
        $datasets_data[] = (int)$row['total_pedidos'];
    }
}

// Convertir los arrays de PHP a cadenas JSON para pasarlos a JavaScript
$labels_json = json_encode($labels);
$data_json = json_encode($datasets_data);

// ... (El resto de tu código PHP y HTML)
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div style="width: 80%; margin: 0 auto;">
    <canvas id="pedidosChart"></canvas>
</div>


<script>
    // Variables de PHP pasadas a JavaScript
    const labels = <?php echo $labels_json; ?>;
    const dataPedidos = <?php echo $data_json; ?>;

    const data = {
        labels: labels,
        datasets: [{
            label: 'Total de Pedidos por Mes',
            data: dataPedidos,
            backgroundColor: [
                'rgba(54, 162, 235, 0.5)', // Azul
                'rgba(255, 99, 132, 0.5)', // Rojo
                'rgba(75, 192, 192, 0.5)', // Verde
                'rgba(255, 205, 86, 0.5)', // Amarillo
                'rgba(153, 102, 255, 0.5)', // Púrpura
                'rgba(255, 159, 64, 0.5)'  // Naranja
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar', // Puedes cambiar a 'line' para un gráfico de líneas
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Pedidos'
                    },
                    // Asegura que los ticks sean números enteros
                    ticks: {
                        callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
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
                    text: 'Pedidos Registrados por Mes'
                }
            }
        }
    };

    // Crear la instancia del gráfico
    const pedidosChart = new Chart(
        document.getElementById('pedidosChart'),
        config
    );
</script>