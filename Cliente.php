<?php include("db.php"); ?>
<?php
require_once 'corrreelectronico/vendor/autoload.php';
$faker = Faker\Factory::create('es_ES');
$emailInicial = $faker->email();
?>

<h2>Registrar Cliente</h2>
<div id="mensaje" style="margin-bottom: 20px; font-weight: bold;"></div>

<form id="registroForm">
    Nombre: <input type="text" name="nombre" required><br>
    Teléfono: <input type="text" name="telefono"><br>
    Dirección: <input type="text" name="direccion"><br>
    Mz: <input type="text" name="manzana"><br>
    Lt: <input type="text" name="lote"><br>
    Ciudad:
    <select name="id_ciudad" required>
        <option value="">-- Selecciona Ciudad --</option>
        <?php
        $result = $conn->query("SELECT id_ciudad, nombre_ciudad FROM ciudad ORDER BY nombre_ciudad");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id_ciudad']}'>{$row['nombre_ciudad']}</option>";
        }
        ?>
    </select><br>
    
    Email: 
    <input type="email" name="email" id="emailInput" value="<?php echo $emailInicial; ?>">
    <button type="button" id="btnRegenerar">Regenerar Email</button>
    <br><br>

    <button type="submit">Guardar Cliente</button>
</form>

<script>
// 1. Lógica para Regenerar Email
document.getElementById('btnRegenerar').addEventListener('click', function() {
    fetch('get_email.php')
        .then(response => response.text())
        .then(nuevoEmail => {
            document.getElementById('emailInput').value = nuevoEmail;
        });
});

// 2. Lógica para Guardar el Formulario (AJAX)
document.getElementById('registroForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Evita que la página se recargue
    
    const mensajeDiv = document.getElementById('mensaje');
    const formData = new FormData(this); // Captura todos los datos del formulario automáticamente

    fetch('GuardarCliente.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            mensajeDiv.style.color = 'green';
            mensajeDiv.innerText = "✅ " + data.message;
            this.reset(); // Limpia el formulario tras el éxito
        } else {
            mensajeDiv.style.color = 'red';
            mensajeDiv.innerText = "❌ Error: " + data.message;
        }
    })
    .catch(error => {
        mensajeDiv.style.color = 'red';
        mensajeDiv.innerText = "❌ Error en la conexión.";
    });
});
</script>