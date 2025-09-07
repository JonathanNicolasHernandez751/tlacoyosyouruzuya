<?php include("db.php"); ?>
< html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Ventas - Tlacoyos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f5f5f5;
        }
        header {
            background: #00897b;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background: #00695c;
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }
        nav a:hover {
            background: #004d40;
        }
        .content {
            padding: 20px;
        }
        iframe {
            width: 100%;
            height: 600px;
            border: none;
            background: white;
        }
    </style>
</head>
<body>

<header>
    <h1>🌮 Sistema de Ventas - Tlacoyos y Más</h1>
</header>

<nav>
    <a href="Cliente.php" target="contenido">Clientes</a>
    <a href="producto.php" target="contenido">Productos</a>
    <a href="registrarPedido.php" target="contenido">Pedidos</a>
    <a href="agregarProductoApedido.php" target="contenido">Detalles de Pedido</a>
    <a href="registrarPago.php" target="contenido">Pagos</a>
    <a href="panelDePedidos.php" target="contenido">Panel</a>
    <a href="editarPago.php" target="contenido">Panel</a>
    <a href="editarPedido.php" target="contenido">Panel</a>
</nav>

<div class="content">
    <iframe title="panelPedidos" name="contenido" src="panelDePedidos.php"></iframe>
</div>

</body>
</html>
