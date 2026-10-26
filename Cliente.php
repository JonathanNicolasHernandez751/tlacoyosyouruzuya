
   <?php include("db.php"); ?>
<?php
require_once 'corrreelectronico/vendor/autoload.php';
$faker = Faker\Factory::create('es_ES');
$emailInicial = $faker->email();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrar Cliente</title>
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --success: #10b981;
        --danger: #ef4444;
        --bg: #f3f4f6;
        --card: #ffffff;
        --text: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --radius: 10px;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        background: var(--bg);
        color: var(--text);
        margin: 0;
        padding: 40px 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .container {
        width: 100%;
        max-width: 640px;
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        padding: 32px 36px;
    }

    h2 {
        margin: 0 0 24px;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text);
        border-bottom: 2px solid var(--border);
        padding-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    h2::before {
        content: "👤";
        font-size: 1.4rem;
    }

    #mensaje {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
        display: none;
        animation: fadeIn 0.3s ease;
    }
    #mensaje.show { display: block; }
    #mensaje.success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid var(--success);
    }
    #mensaje.error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid var(--danger);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group.full { grid-column: 1 / -1; }

    label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    input, select {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 0.95rem;
        color: var(--text);
        background: #fafafa;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    input:focus, select:focus {
        outline: none;
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    input:valid:not(:placeholder-shown) {
        border-color: #a7f3d0;
    }

    .email-group {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }
    .email-group input { flex: 1; }

    button {
        font-family: inherit;
        cursor: pointer;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    #btnRegenerar {
        padding: 0 16px;
        background: #e0e7ff;
        color: var(--primary);
        font-size: 0.85rem;
        white-space: nowrap;
        border: 1.5px solid transparent;
    }
    #btnRegenerar:hover {
        background: #c7d2fe;
        transform: translateY(-1px);
    }
    #btnRegenerar:active { transform: translateY(0); }
.submit-btn {
        grid-column: 1 / -1;
        margin-top: 12px;
        padding: 14px;
        background: var(--primary);
        color: #fff;
        font-size: 1rem;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .submit-btn:hover {
        background: var(--primary-hover);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        transform: translateY(-1px);
    }
    .submit-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
    }
    .submit-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    @media (max-width: 560px) {
        .container { padding: 24px 20px; }
        .form-grid { grid-template-columns: 1fr; }
        .email-group { flex-direction: column; }
        #btnRegenerar { padding: 10px; }
    }
</style>
</head>
<body>

<div class="container">
    <h2>Registrar Cliente</h2>
    <div id="mensaje"></div>

    <form id="registroForm">
        <div class="form-grid">
            <div class="form-group full">
                <label for="nombre">Nombre completo *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej: 555-1234">
            </div>

            <div class="form-group">
                <label for="id_ciudad">Ciudad *</label>
                <select name="id_ciudad" id="id_ciudad" required>
                    <option value="">-- Selecciona Ciudad --</option>
                    <?php
                    $result = $conn->query("SELECT id_ciudad, nombre_ciudad FROM ciudad ORDER BY nombre_ciudad");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_ciudad']}'>" . htmlspecialchars($row['nombre_ciudad']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group full">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" placeholder="Calle, número, referencias">
            </div>

            <div class="form-group">
                <label for="manzana">Manzana</label>
                <input type="text" id="manzana" name="manzana" placeholder="Mz">
            </div>

            <div class="form-group">
                <label for="lote">Lote</label>
                <input type="text" id="lote" name="lote" placeholder="Lt">
            </div>

            <div class="form-group full">
                <label for="emailInput">Correo electrónico</label>
                <div class="email-group">
                    <input type="email" id="emailInput" name="email" value="<?php echo htmlspecialchars($emailInicial); ?>">
                    <button type="button" id="btnRegenerar">🔄 Regenerar</button>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="btnSubmit">💾 Guardar Cliente</button>
        </div>
    </form>
</div>

<script>
const mensajeDiv = document.getElementById('mensaje');
const form = document.getElementById('registroForm');
const btnSubmit = document.getElementById('btnSubmit');

function mostrarMensaje(texto, tipo) {
    mensajeDiv.className = 'show ' + tipo;
    mensajeDiv.innerText = texto;
    if (tipo === 'success') {
        setTimeout(() => { mensajeDiv.className = ''; }, 4000);
    }
}

// Regenerar email
document.getElementById('btnRegenerar').addEventListener('click', function() {
    const btn = this;
   btn.disabled = true;
    btn.textContent = '⏳...';
    fetch('get_email.php')
        .then(r => r.text())
        .then(nuevoEmail => {
            document.getElementById('emailInput').value = nuevoEmail.trim();
            btn.disabled = false;
            btn.textContent = '🔄 Regenerar';
        })
        .catch(() => {
            mostrarMensaje('❌ No se pudo generar el correo.', 'error');
            btn.disabled = false;
            btn.textContent = '🔄 Regenerar';
        });
});

// Enviar formulario
form.addEventListener('submit', function(e) {
    e.preventDefault();
    btnSubmit.disabled = true;
    btnSubmit.textContent = '⏳ Guardando...';

    const formData = new FormData(this);

    fetch('GuardarCliente.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            mostrarMensaje('✅ ' + data.message, 'success');
            form.reset();
        } else {
            mostrarMensaje('❌ Error: ' + data.message, 'error');
        }
    })
    .catch(() => {
        mostrarMensaje('❌ Error en la conexión con el servidor.', 'error');
    })
    .finally(() => {
        btnSubmit.disabled = false;
        btnSubmit.textContent = '💾 Guardar Cliente';
    });
});
</script>

</body>
</html>
