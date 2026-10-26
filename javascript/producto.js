/* ============================================================
   REGISTRAR PRODUCTO - Lógica del formulario
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    const form       = document.getElementById('productoForm');
    const mensajeDiv = document.getElementById('mensaje');
    const btnSubmit  = document.getElementById('btnSubmit');

    /**
     * Muestra un mensaje de feedback al usuario.
     * @param {string} texto - Texto a mostrar
     * @param {string} tipo  - 'success' | 'error'
     */
    function mostrarMensaje(texto, tipo) {
        mensajeDiv.className = 'show ' + tipo;
        mensajeDiv.innerText = texto;

        if (tipo === 'success') {
            setTimeout(() => { mensajeDiv.className = ''; }, 4000);
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        btnSubmit.disabled = true;
        btnSubmit.textContent = '⏳ Guardando...';

        const formData = new FormData(this);

        fetch('', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
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
            btnSubmit.textContent = '💾 Guardar Producto';
        });
    });
});
