import flet as ft

def main(page: ft.Page):
    # Configuración de la ventana
    page.title = "🔍 Revisión de Código - Youruzuya"
    page.theme_mode = ft.ThemeMode.DARK
    page.bgcolor = "#0f1117"
    page.padding = 30
    page.scroll = "auto"

    # Variables de estado
    base_score = 72
    checked_points = 0
    total_items = 5 # Items con puntos

    # --- FUNCIONES DE LÓGICA ---
    def update_score(e):
        nonlocal checked_points
        # Sumar puntos según el checkbox
        if e.control.value:
            checked_points += int(e.control.data)
        else:
            checked_points -= int(e.control.data)

        current_total = base_score + checked_points
        score_text.value = f"{current_total}"
        progress_ring.value = current_total / 100
        page.update()

    # --- COMPONENTES DE LA INTERFAZ ---

    # Marcador de puntuación (Círculo)
    progress_ring = ft.ProgressRing(
        value=base_score/100, stroke_width=8, color="#22c55e", bgcolor="#1e293b", width=80, height=80
    )
    score_text = ft.Text(f"{base_score}", size=25, weight="bold", color="#22c55e")

    score_header = ft.Row(
        [
            ft.Stack([progress_ring, ft.Container(score_text, alignment=ft.alignment.center, width=80, height=80)]),
            ft.Column([
                ft.Text("Puntuación general", size=18, weight="bold"),
                ft.Text("Marca los ítems corregidos para subir tu score", color="#94a3b8")
            ])
        ],
        spacing=20
    )

    # Función para crear secciones colapsables (Expansión)
    def create_item(title, desc, points=0, is_ok=False):
        content = ft.Column([
            ft.Text(title, weight="bold", size=14),
            ft.Text(desc, color="#94a3b8", size=12),
        ], expand=True)

        if is_ok:
            return ft.Container(
                content=ft.Row([ft.Icon(ft.icons.CHECK_CIRCLE, color="green"), content]),
                padding=10, bgcolor="#1e293b", border_radius=10
            )
        else:
            return ft.Container(
                content=ft.Row([
                    ft.Checkbox(on_change=update_score, data=points),
                    content,
                    ft.Badge(content=ft.Text(f"+{points}"), bgcolor="#7f1d1d" if points > 5 else "#713f12")
                ]),
                padding=10, bgcolor="#1e293b", border_radius=10
            )

    # --- ARMADO DE LA PÁGINA ---
    page.add(
        ft.Text("🔍 Revisión de Código PHP", size=28, weight="bold", text_align="center"),
        ft.Text("Dashboard Youruzuya — Top 5 Productos", color="#94a3b8", text_align="center"),
        ft.Divider(height=40, color="transparent"),
        score_header,
        ft.Divider(height=40),

        ft.Text("✅ Lo que está bien", size=16, weight="bold", color="green"),
        create_item("Uso de PDO", "Captura de errores correcta con try/catch", is_ok=True),

        ft.Text("🔐 Seguridad", size=16, weight="bold", color="red"),
        create_item("No mostrar errores", "Cambiar die($e->getMessage()) por logs internos", points=10),
        create_item("Content Security Policy", "Añadir headers nosniff", points=8),

        ft.Text("📋 Buenas Prácticas", size=16, weight="bold", color="orange"),
        create_item("Inicializar arreglos", "Definir $nombresProd antes del try", points=5),
        create_item("Fetch Assoc", "Usar PDO::FETCH_ASSOC para eficiencia", points=5)
    )

# Ejecución normal de Python
if __name__ == "__main__":
    ft.run("main")

