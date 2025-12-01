<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Correos Aleatorios</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, button { padding: 8px; margin: 5px; }
        .correo-list { background: #f5f5f5; padding: 15px; margin: 10px 0; }
        .correo-item { padding: 5px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generador de Correos Aleatorios</h1>

        <?php
        require_once 'config.php';
        require_once 'GeneradorCorreos.php';

        $generador = new GeneradorCorreos(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($_POST) {
            $cantidad = intval($_POST['cantidad'] ?? 1);
            $correos = $generador->generarCorreo($cantidad);

            echo "<div class='correo-list'>";
            echo "<h3>Correos Generados:</h3>";
            foreach ($correos as $correo) {
                echo "<div class='correo-item'>" . htmlspecialchars($correo) . "</div>";
            }
            echo "</div>";
        }

        // Buscar correos
        if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
            $resultados = $generador->buscarCorreo($_GET['buscar']);
            if ($resultados) {
                echo "<div class='correo-list'>";
                echo "<h3>Resultados de búsqueda:</h3>";
                foreach ($resultados as $resultado) {
                    echo "<div class='correo-item'>" .
                         htmlspecialchars($resultado['correo']) .
                         " <small>(" . $resultado['fecha_creacion'] . ")</small></div>";
                }
                echo "</div>";
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="cantidad">Cantidad de correos a generar:</label>
                <select name="cantidad" id="cantidad">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>
            <button type="submit">Generar Correos</button>
        </form>

        <hr>

        <form method="GET">
            <div class="form-group">
                <label for="buscar">Buscar correo:</label>
                <input type="text" name="buscar" id="buscar" placeholder="Ej: juan, gmail...">
            </div>
            <button type="submit">Buscar</button>
        </form>

        <hr>

        <h3>Últimos correos generados:</h3>
        <?php
        $ultimos = $generador->obtenerCorreosGenerados(5);
        echo "<div class='correo-list'>";
        foreach ($ultimos as $correo) {
            echo "<div class='correo-item'>" .
                 htmlspecialchars($correo['correo']) .
                 " <small>(" . $correo['fecha_creacion'] . ")</small></div>";
        }
        echo "</div>";
        ?>
    </div>
</body>
</html>
