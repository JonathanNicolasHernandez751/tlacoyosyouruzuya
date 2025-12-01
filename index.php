<?php include "db.php"; ?>
< html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Ventas - Tlacoyos</title>
    <link rel="stylesheet" href="css/menu_despegable.css">
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
   <ul class="nav">
   <li>  <a href="Cliente.php" target="contenido">Clientes</a></li>
    <li>
    <a href="registrarPedido.php" target="contenido">Pedidos</a>
    </li>
     <li>
    <a href="agregarProductoApedido.php" target="contenido">Detalles de Pedido</a></li>
     <li>
    <a href="registrarPago.php" target="contenido">Pagos</a></li>
     <li>
    <a>otros</a>
<ul>
  <li> <a href="panelDePedidos.php" target="contenido">Panel</a></li>
  <li>  <a href="editarPago.php" target="contenido">Panel</a></li>
<li><a href="editarPedido.php" target="contenido">Panel</a></li>
    <li> <a href="producto.php" target="contenido">Productos</a></li>

                 <li> <a href="corrreelectronico/generadorCorreos2.php" target="contenido">correo electronico2</a></li>
         </ul>
     </ul>
</nav>

<div class="content">
    <iframe title="panelPedidos" name="contenido" src="panelDePedidos.php"></iframe>
</div>

</body>
</html>
