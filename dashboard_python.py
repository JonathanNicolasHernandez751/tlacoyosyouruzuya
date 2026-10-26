# ============================================================
#  Dashboard Youruzuya — Top 5 Productos
#  Equivalente Python del dashboard PHP
#  Librerías: pandas, plotly, sqlalchemy, python-dotenv
#
#  Instalar dependencias:
#  pip install pandas plotly sqlalchemy pymysql python-dotenv
# ============================================================

import os
import logging
import pandas as pd
import plotly.express as px
from sqlalchemy import create_engine, text
from dotenv import load_dotenv

# ── Logging (equivalente a error_log() en PHP) ───────────────
logging.basicConfig(
    filename="dashboard.log",
    level=logging.ERROR,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

# ── Credenciales desde archivo .env (NO hardcodear contraseñas) ──
# Crea un archivo .env con este contenido:
#   DB_HOST=localhost
#   DB_PORT=3306
#   DB_USER=tu_usuario
#   DB_PASSWORD=tu_contraseña
#   DB_NAME=tu_base_de_datos
load_dotenv()

DB_HOST     = os.getenv('DB_HOST','localhost')


DB_PORT     = os.getenv('DB_PORT','3306')

DB_USER     = os.getenv('root')
DB_PASSWORD = os.getenv('')
DB_NAME     = os.getenv('tlacoyosyouruzuya')

# ── Conexión a MySQL (equivalente a conexion2.php + PDO) ─────
def get_engine():
    url = f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}"
    return create_engine(url)

# ── Consulta SQL (idéntica a la de tu PHP) ───────────────────
SQL_TOP_PRODUCTOS = text("""
    SELECT p.nombre_producto, SUM(d.cantidad) AS total_unidades
    FROM detallepedido d
    INNER JOIN producto p ON d.id_producto = p.id_producto
    GROUP BY p.id_producto
    ORDER BY total_unidades DESC
    LIMIT 5
""")

# ── Obtener datos como DataFrame (equivalente a fetchAll) ────
def get_top_productos() -> pd.DataFrame:
    try:
        engine = get_engine()
        with engine.connect() as conn:
            df = pd.read_sql(SQL_TOP_PRODUCTOS, conn)
        return df
    except Exception as e:
        logging.error(f"Error al consultar top productos: {e}")
        return pd.DataFrame()  # DataFrame vacío si falla

# ── Generar gráfico (equivalente a Chart.js doughnut) ────────
def generar_grafico(df: pd.DataFrame):
    if df.empty:
        print("⚠️  No hay datos disponibles para graficar.")
        return

    fig = px.pie(
        df,
        names="nombre_producto",
        values="total_unidades",
        title="🌮 Top 5 Productos más vendidos",
        hole=0.4,               # Hace el pie tipo "doughnut"
        color_discrete_sequence=["#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF"]
    )

    fig.update_traces(
        textposition="inside",
        textinfo="percent+label",
        hovertemplate="<b>%{label}</b><br>Unidades: %{value}<extra></extra>"
    )

    fig.update_layout(
        legend=dict(orientation="h", yanchor="bottom", y=-0.2),
        title_font_size=20,
        margin=dict(t=60, b=60)
    )

    # ── Opciones de salida ────────────────────────────────────
    # 1. Abrir en el navegador (interactivo)
    fig.show()

    # 2. Guardar como HTML (para subir a un servidor)
    fig.write_html("top5_productos.html")
    print("✅ Gráfico guardado como top5_productos.html")

    # 3. Guardar como imagen PNG (para reportes/PDF)
    # Requiere: pip install kaleido
    # fig.write_image("top5_productos.png")

# ── Bonus: Reporte en consola con pandas ─────────────────────
def imprimir_reporte(df: pd.DataFrame):
    if df.empty:
        print("No hay datos.")
        return

    total = df["total_unidades"].sum()
    df["porcentaje"] = (df["total_unidades"] / total * 100).round(1)

    print("\n📊 Reporte — Top 5 Productos")
    print("=" * 40)
    print(df.to_string(index=False))
    print("=" * 40)
    print(f"Total unidades vendidas: {total}")

# ── Main ──────────────────────────────────────────────────────
if __name__ == "__main__":
    df = get_top_productos()
    imprimir_reporte(df)
    generar_grafico(df)
