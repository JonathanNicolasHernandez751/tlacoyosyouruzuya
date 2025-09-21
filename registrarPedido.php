<?php include "db.php"; ?>

<h2>Registrar Pedido</h2>

<!-- Campo de búsqueda -->
<label>Buscar cliente:</label>
<input type="text" id="buscar_cliente" placeholder="Escribe un nombre...">
<ul id="resultados" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; display:none;"></ul>
<br>

<form method="POST">
    <!-- Cliente seleccionado -->
    <input type="hidden" name="id_cliente" id="id_cliente" required>
    <p id="cliente_seleccionado" style="font-weight:bold; color:green;"></p>

    Estado:
    <select name="estado" required>
        <option value="Pendiente">Pendiente</option>
        <option value="Entregado">Entregado</option>
        <option value="Cancelado">Cancelado</option>
    </select><br><br>

    <button type="submit" name="guardar_pedido">Guardar</button>
</form>

<?php if (isset($_POST["guardar_pedido"])) {
    $id_cliente = intval($_POST["id_cliente"]);
    $estado = $_POST["estado"];

    // Validar si el cliente existe
    $check = $conn->prepare("SELECT COUNT(*) FROM cliente WHERE id_cliente = ?");
    $check->bind_param("i", $id_cliente);
    $check->execute();
    $check->bind_result($existe);
    $check->fetch();
    $check->close();

    if ($existe == 0) {
        echo "⚠️ Error: El cliente no existe.";
    } else {
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO Pedido (id_cliente, estado, fecha_pedido) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $id_cliente, $estado);

        if ($stmt->execute()) {
            echo "✅ Pedido registrado correctamente. ID: " . $stmt->insert_id;
        } else {
            echo "❌ Error al registrar pedido: " . $stmt->error;
        }
        $stmt->close();
    }
} ?>

<script>
// --- AJAX buscador ---
document.getElementById("buscar_cliente").addEventListener("keyup", function() {
    let query = this.value;

    if (query.length < 2) { // solo buscar si hay al menos 2 letras
        document.getElementById("resultados").style.display = "none";
        return;
    }

    fetch("buscar_clientes.php?nombre=" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            let resultados = document.getElementById("resultados");
            resultados.innerHTML = "";
            if (data.length > 0) {
                resultados.style.display = "block";
                data.forEach(cliente => {
                    let li = document.createElement("li");
                    li.textContent = cliente.nombre;
                    li.style.cursor = "pointer";
                    li.onclick = function() {
                        document.getElementById("id_cliente").value = cliente.id_cliente;
                        document.getElementById("cliente_seleccionado").innerText =
                            "Cliente seleccionado: " + cliente.nombre;
                        resultados.style.display = "none";
                    };
                    resultados.appendChild(li);
                });
            } else {
                resultados.style.display = "none";
            }
        });
});
</script>
