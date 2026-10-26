<?php include("db.php"); ?>
<?php
// Procesar formulario
$respuesta = null;
if (isset($_POST['guardar_producto'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO Producto (nombre_producto, descripcion, precio_unitario, tipo, unidad_medida) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss",
            $_POST['nombre_producto'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['tipo'],
            $_POST['unidad']
        );
        if ($stmt->execute()) {
            $respuesta = ['status' => 'success', 'message' => 'Producto registrado correctamente'];
        } else {
            $respuesta = ['status' => 'error', 'message' => $stmt->error];
        }
        $stmt->close();
    } catch (Exception $e) {
        $respuesta = ['status' => 'error', 'message' => $e->getMessage()];
    }

    // Si es AJAX → devolver JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="css/producto.css">
</head>
<body>

<div class="container">
    <h2>Registrar Producto</h2>
    <div id="mensaje"></div>

    <form id="productoForm">
        <div class="form-grid">

            <div class="form-group full">
                <label for="nombre_producto">Nombre del producto *</label>
                <input type="text" id="nombre_producto" name="nombre_producto"
                       placeholder="Ej: Tlacoyo de frijol" required autocomplete="off">
            </div>

            <div class="form-group full">
                <label for="descripcion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion"
                       placeholder="Ingredientes, tamaño, notas...">
            </div>

            <div class="form-group">
                <label for="precio">Precio unitario *</label>
                <div class="price-wrapper">
                    <input type="number" id="precio" name="precio"
                           step="0.01" min="0" placeholder="0.00" required>
                </div>
            </div>

            <div class="form-group">
                <label for="unidad">Unidad de medida *</label>
                <select name="unidad" id="unidad" required>
                    <option value="pieza">🍽️ Pieza</option>
                    <option value="docena">🥡 Docena</option>
                    <option value="kilo">⚖️ Kilo</option>
                </select>
            </div>

            <div class="form-group full">
                <label for="tipo">Tipo de producto *</label>
                <select name="tipo" id="tipo" required>
                    <option value="Tlacoyo">🌮 Tlacoyo</option>
                    <option value="Gordita">🫓 Gordita</option>
                    <option value="Tortilla">🟡 Tortilla</option>
                    <option value="Ensalada">🥗 Ensalada</option>
                </select>
                <span class="hint">Selecciona la categoría a la que pertenece</span>
            </div>

            <button type="submit" class="submit-btn" id="btnSubmit" name="guardar_producto">
                💾 Guardar Producto
            </button>

        </div>
    </form>
</div>

    <script src="javascript/producto.js"></script>
