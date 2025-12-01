<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Poblar Base de Datos con Faker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin: 15px 0; }
        button { padding: 10px 20px; margin: 5px; }
        .resultado { background: #f0f0f0; padding: 15px; margin: 10px 0; }
        .estadisticas { background: #e0e0ff; padding: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Poblar Base de Datos con FakerPHP</h1>

        <?php
        require_once 'vendor/autoload.php';
        require_once 'config.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $insertador = new InsertarDatosFaker(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                if (isset($_POST['poblar_completo'])) {
                    $nombres = intval($_POST['cantidad_nombres'] ?? 100);
                    $apellidos = intval($_POST['cantidad_apellidos'] ?? 50);

                    echo "<div class='resultado'>";
                    $insertador->poblarBaseDatos($nombres, $apellidos);
                    echo "</div>";
                }

                if (isset($_POST['solo_nombres'])) {
                    $cantidad = intval($_POST['cantidad_nombres'] ?? 50);
                    echo "<div class='resultado'>";
                    echo "Nombres insertados: " . $insertador->insertarNombres($cantidad);
                    echo "</div>";
                }

                if (isset($_POST['solo_apellidos'])) {
                    $cantidad = intval($_POST['cantidad_apellidos'] ?? 30);
                    echo "<div class='resultado'>";
                    echo "Apellidos insertados: " . $insertador->insertarApellidos($cantidad);
                    echo "</div>";
                }

            } catch(Exception $e) {
                echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
            }
        }

        // Mostrar estadísticas actuales
        try {
            $insertador = new InsertarDatosFaker(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            echo "<div class='estadisticas'>";
            $insertador->mostrarEstadisticas();
            echo "</div>";
        } catch(Exception $e) {
            echo "<div style='color: red;'>Error al conectar: " . $e->getMessage() . "</div>";
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="cantidad_nombres">Cantidad de nombres:</label>
                <input type="number" name="cantidad_nombres" value="100" min="1" max="1000">
            </div>

            <div class="form-group">
                <label for="cantidad_apellidos">Cantidad de apellidos:</label>
                <input type="number" name="cantidad_apellidos" value="50" min="1" max="500">
            </div>

            <button type="submit" name="poblar_completo">Poblar Base de Datos Completa</button>
            <button type="submit" name="solo_nombres">Insertar Solo Nombres</button>
            <button type="submit" name="solo_apellidos">Insertar Solo Apellidos</button>
        </form>
    </div>
</body>
</html>
