
<?php include "db.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrar Pedido</title>
 <link rel="stylesheet" href="css/registrarpedido.css">
</head>
<body>

<div class="container">
    <h2>📦 Registrar Pedido</h2>

    <?php if (isset($_POST["guardar_pedido"])) {
        $id_cliente = intval($_POST["id_cliente"]);
        $estado = $_POST["estado"];

        $check = $conn->prepare("SELECT COUNT(*) FROM cliente WHERE id_cliente = ?");
        $check->bind_param("i", $id_cliente);
        $check->execute();
        $check->bind_result($existe);
        $check->fetch();
        $check->close();

        if ($existe == 0) {
            echo '<div class="mensaje advertencia">⚠️ El cliente seleccionado no existe.</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO Pedido (id_cliente, estado, fecha_pedido) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $id_cliente, $estado);

            if ($stmt->execute()) {
                echo '<div class="mensaje exito">✅ Pedido registrado correctamente. ID: ' . $stmt->insert_id . '</div>';
            } else {
                echo '<div class="mensaje error">❌ Error al registrar pedido: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
    } ?>

    <form method="POST">
        <div class="form-group">
            <label for="buscar_cliente">🔍 Buscar cliente</label>
            <input type="text" id="buscar_cliente" placeholder="Escribe un nombre..." autocomplete="off">
            <div class="spinner" id="spinner"></div>
            <ul id="resultados"></ul>
            <input type="hidden" name="id_cliente" id="id_cliente" required>
            <p id="cliente_seleccionado"></p>
        </div>

        <div class="form-group">
            <label for="estado">Estado del pedido</label>
            <select name="estado" id="estado" required>
                <option value="Pendiente">🕒 Pendiente</option>
                <option value="Entregado">✅ Entregado</option>
                <option value="Cancelado">❌ Cancelado</option>
            </select>
        </div>

        <button type="submit" name="guardar_pedido">Guardar Pedido</button>
    </form>
</div>

<script>
const inputBuscar = document.getElementById("buscar_cliente");
const resultados   = document.getElementById("resultados");
const spinner      = document.getElementById("spinner");
let timerDebounce  = null;
let abortController = null;

/* Escapa caracteres especiales para usar en regex */
function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/* Resalta la coincidencia dentro del nombre usando <mark> */
function resaltar(nombre, termino) {
    const safe = escapeRegex(termino);
    const regex = new RegExp(`(${safe})`, "gi");
    return nombre.replace(regex, "<mark>$1</mark>");
}

/* Debounce + fetch */
inputBuscar.addEventListener("keyup", function() {
    const query = this.value.trim();

    clearTimeout(timerDebounce);

    // Menos de 2 letras → oculta resultados
    if (query.length < 2) {
        resultados.style.display = "none";
        spinner.classList.remove("visible");
        if (abortController) abortController.abort();
        return;
    }

    // Espera 250ms antes de disparar la petición
    timerDebounce = setTimeout(() => {
        // Cancela la petición anterior si aún está en curso
        if (abortController) abortController.abort();
        abortController = new AbortController();

        spinner.classList.add("visible");

        fetch("buscar_clientes.php?nombre=" + encodeURIComponent(query), {
            signal: abortController.signal
        })
            .then(res => res.json())
            .then(data => {
                spinner.classList.remove("visible");
                resultados.innerHTML = "";

                if (data.length > 0) {
                    resultados.style.display = "block";
                    data.forEach(cliente => {
                        const li = document.createElement("li");
                        // innerHTML porque ya está resaltado con <mark>
                        li.innerHTML = resaltar(cliente.nombre, query);
                        li.style.cursor = "pointer";
                        li.onclick = function() {
                            document.getElementById("id_cliente").value = cliente.id_cliente;
                            const span = document.getElementById("cliente_seleccionado");
                            span.innerText = "👤 " + cliente.nombre;
                            span.classList.add("visible");
                            inputBuscar.value = cliente.nombre;
                            resultados.style.display = "none";
                        };
                        resultados.appendChild(li);
                    });
                } else {
                    resultados.style.display = "none";
                }
            })
            .catch(err => {
                spinner.classList.remove("visible");
                if (err.name !== "AbortError") {
                    resultados.style.display = "none";
                    console.error("Error en búsqueda:", err);
                }
            });
    }, 250);
});

/* Cierra el dropdown al hacer clic fuera */
document.addEventListener("click", function(e) {
    if (!e.target.closest(".form-group")) {
        resultados.style.display = "none";
    }
});

/* Validación antes de enviar el formulario */
document.querySelector("form").addEventListener("submit", function(e) {
    if (!document.getElementById("id_cliente").value) {
        e.preventDefault();
        alert("Debes seleccionar un cliente de la lista.");
        inputBuscar.focus();
    }
});
</script>

</body>
</html>
