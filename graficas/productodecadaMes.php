<?php
require_once 'conexion2.php';

try {
    // Consulta para obtener el top de productos por cada mes
    $sql = "SELECT 
                DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS periodo,
                DATE_FORMAT(p.fecha_pedido, '%M %Y') AS mes_nombre,
                pr.nombre_producto,
                SUM(dp.cantidad) AS total_vendido
            FROM pedido p
            INNER JOIN detallepedido dp ON p.id_pedido = dp.id_pedido
            INNER JOIN producto pr ON dp.id_producto = pr.id_producto
            GROUP BY periodo, pr.id_producto
            ORDER BY periodo DESC, total_vendido DESC";

    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll();

    // Agrupamos los resultados por mes en un array asociativo
    $meses = [];
    foreach ($resultados as $fila) {
        $meses[$fila['mes_nombre']][] = $fila;
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tops Mensuales - Youruzuya</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 20px; }
        .mes-contenedor { background: white; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        .mes-titulo { background: #2c3e50; color: white; padding: 15px; margin: 0; text-transform: capitalize; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .top-1 { background: #fff9c4; font-weight: bold; color: #f57f17; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.9em; }
        .badge-oro { background: #ffd700; color: #000; }
    </style>
</head>
<body>

    <h1>🏆 Top de Productos por Mes</h1>

    <?php foreach ($meses as $nombreMes => $productos): ?>
        <div class="mes-contenedor">
            <h2 class="mes-titulo"><?php echo $nombreMes; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Producto</th>
                        <th>Cantidad Vendida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $indice => $item): ?>
                        <tr class="<?php echo ($indice == 0) ? 'top-1' : ''; ?>">
                            <td>
                                <?php if($indice == 0): ?>
                                    <span class="badge badge-oro">#1 más vendido</span>
                                <?php else: ?>
                                    #<?php echo ($indice + 1); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                            <td><?php echo $item['total_vendido']; ?> unidades</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

</body>
</html>
