<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Tlacoyos & Antojitos</title>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #004d40;
            --primary-main: #00695c;
            --primary-light: #00897b;
            --primary-subtle: #e0f2f1;
            --accent-warm: #e65100;
            --accent-gold: #ff9800;
            --accent-hover: #f57c00;
            --bg-body: #f4f7f6;
            --surface-card: #ffffff;
            --text-title: #1a2e2b;
            --text-body: #4a5d5a;
            --text-muted: #809793;
            --border-color: #e2ece9;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 6px 16px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 12px 28px rgba(0, 0, 0, 0.12);
            --radius-md: 10px;
            --radius-lg: 16px;
            --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* --- HEADER STYLES --- */
        .app-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-main) 100%);
            color: #ffffff;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 100;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-icon {
            background: rgba(255, 255, 255, 0.15);
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent-gold);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .header-widgets {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .system-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 0, 0, 0.18);
            padding: 0.4rem 0.85rem;
            border-radius: 30px;
            font-size: 0.78rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #00e676;
            border-radius: 50%;
            box-shadow: 0 0 8px #00e676;
        }

        .time-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            background: rgba(255, 255, 255, 0.12);
            padding: 0.4rem 0.85rem;
            border-radius: 30px;
            font-weight: 500;
        }

        /* --- NAVBAR STYLES --- */
        .navbar {
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1rem 1.1rem;
            color: var(--text-title);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition-fast);
            border-bottom: 3px solid transparent;
            cursor: pointer;
        }

        .nav-link i {
            font-size: 1rem;
            color: var(--primary-main);
            transition: var(--transition-fast);
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-main);
            background-color: var(--primary-subtle);
            border-bottom-color: var(--primary-main);
        }

        .nav-link:hover i, .nav-item.active .nav-link i {
            color: var(--accent-warm);
            transform: translateY(-1px);
        }

        /* Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 240px;
            background: #ffffff;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition-smooth);
            list-style: none;
            z-index: 1000;
        }

        .nav-item.has-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            color: var(--text-body);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 8px;
            transition: var(--transition-fast);
        }

        .dropdown-item a i {
            font-size: 0.95rem;
            color: var(--primary-light);
            width: 18px;
            text-align: center;
        }

        .dropdown-item a:hover {
            background-color: var(--primary-subtle);
            color: var(--primary-dark);
        }

        .dropdown-item a:hover i {
            color: var(--accent-warm);
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-title);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0.8rem;
            border-radius: 8px;
        }

        .mobile-toggle:hover {
            background-color: var(--primary-subtle);
        }

        /* --- CONTENT & IFRAME CONTAINER --- */
        .main-wrapper {
            flex: 1;
            padding: 1.25rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
        }

        .iframe-card {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
            min-height: 680px;
        }

        .iframe-toolbar {
            background: #fafcfc;
            padding: 0.6rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .iframe-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .iframe-actions {
            display: flex;
            gap: 0.4rem;
        }

        .action-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-body);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.85rem;
            transition: var(--transition-fast);
        }

        .action-btn:hover {
            background-color: var(--primary-subtle);
            color: var(--primary-dark);
            border-color: var(--primary-light);
        }

        .iframe-viewport {
            position: relative;
            flex: 1;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
        }

        /* Loading Skeleton / Spinner Overlay */
        .iframe-loader {
            position: absolute;
            inset: 0;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            z-index: 10;
            transition: opacity 0.3s ease;
        }

        .iframe-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 42px;
            height: 42px;
            border: 4px solid var(--primary-subtle);
            border-top-color: var(--primary-main);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .iframe-loader p {
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        iframe {
            width: 100%;
            height: 100%;
            min-height: 640px;
            border: none;
            display: block;
        }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 992px) {
            .header-widgets .time-badge {
                display: none;
            }

            .mobile-toggle {
                display: block;
            }

            .nav-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #ffffff;
                flex-direction: column;
                align-items: stretch;
                box-shadow: var(--shadow-lg);
                border-bottom: 1px solid var(--border-color);
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease-in-out;
            }

            .nav-menu.open {
                max-height: 600px;
                overflow-y: auto;
            }

            .nav-link {
                padding: 0.9rem 1.25rem;
                border-bottom: none;
                border-left: 4px solid transparent;
            }

            .nav-link:hover, .nav-item.active .nav-link {
                border-bottom-color: transparent;
                border-left-color: var(--primary-main);
            }

            .dropdown-menu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                border: none;
                background-color: var(--bg-body);
                margin: 0 1rem 0.5rem 2.2rem;
                display: none;
            }

            .nav-item.has-dropdown.open-mobile .dropdown-menu {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .app-header {
                padding: 0.8rem 1rem;
            }

            .brand-text h1 {
                font-size: 1.15rem;
            }

            .brand-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .main-wrapper {
                padding: 0.75rem;
            }

            .iframe-card {
                min-height: 520px;
            }

            iframe {
                min-height: 480px;
            }
        }
    </style>
</head>
<body>

<header class="app-header">
    <div class="brand-container">
        <div class="brand-icon">
            <i class="fa-solid fa-utensils"></i>
        </div>
        <div class="brand-text">
            <h1>Tlacoyos y Más</h1>
            <p>Sistema de Ventas & Gestión</p>
        </div>
    </div>

    <div class="header-widgets">
        <div class="system-status">
            <span class="status-dot"></span>
            <span>Sistema En Línea</span>
        </div>
        <div class="time-badge">
            <i class="fa-regular fa-clock"></i>
            <span id="live-clock">--:--:--</span>
        </div>
    </div>
</header>

<nav class="navbar">
    <div class="nav-container">
        <button class="mobile-toggle" id="mobile-toggle-btn" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li class="nav-item">
                <a href="Cliente.php" target="contenido" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="registrarPedido.php" target="contenido" class="nav-link">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Pedidos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="agregarProductoApedido.php" target="contenido" class="nav-link">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Detalles de Pedido</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="registrarPago.php" target="contenido" class="nav-link">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Pagos</span>
                </a>
            </li>

            <!-- Submenu Dropdown -->
            <li class="nav-item has-dropdown" id="dropdown-parent">
                <a class="nav-link" id="dropdown-toggle">
                    <i class="fa-solid fa-cubes"></i>
                    <span>Otros</span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; margin-left: 0.2rem;"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-item">
                        <a href="panelDePedidos.php" target="contenido">
                            <i class="fa-solid fa-gauge-high"></i> Panel de Pedidos
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="editarPago.php" target="contenido">
                            <i class="fa-solid fa-pen-to-square"></i> Editar Pago
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="editarPedido.php" target="contenido">
                            <i class="fa-solid fa-file-pen"></i> Editar Pedido
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="producto.php" target="contenido">
                            <i class="fa-solid fa-bowl-rice"></i> Productos
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="corrreelectronico/generadorCorreos2.php" target="contenido">
                            <i class="fa-solid fa-envelope"></i> Correo Electrónico
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="graficas/graficaMensualPedidos.php" target="contenido">
                            <i class="fa-solid fa-chart-line"></i> Gráfica de Pedidos
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="graficas/graficaMensualPagos.php" target="contenido">
                            <i class="fa-solid fa-chart-pie"></i> Gráfica de Pagos
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<main class="main-wrapper">
    <div class="iframe-card">
        <div class="iframe-toolbar">
            <div class="iframe-title">
                <i class="fa-solid fa-window-maximize"></i>
                <span id="active-view-name">Panel de Pedidos</span>
            </div>
            <div class="iframe-actions">
                <button class="action-btn" id="reload-iframe" title="Recargar vista">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button class="action-btn" id="open-new-tab" title="Abrir en ventana nueva">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </button>
                <button class="action-btn" id="fullscreen-iframe" title="Pantalla completa">
                    <i class="fa-solid fa-expand"></i>
                </button>
            </div>
        </div>

        <div class="iframe-viewport" id="viewport-container">
            <!-- Spinner / Loader -->
            <div class="iframe-loader" id="iframe-loader">
                <div class="spinner"></div>
                <p>Cargando módulo...</p>
            </div>

            <iframe
                id="main-iframe"
                title="contenido"
                name="contenido"
                src="panelDePedidos.php"
                allowfullscreen>
            </iframe>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const iframe = document.getElementById('main-iframe');
        const loader = document.getElementById('iframe-loader');
        const activeViewName = document.getElementById('active-view-name');
        const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
        const navMenu = document.getElementById('nav-menu');
        const dropdownParent = document.getElementById('dropdown-parent');
        const dropdownToggle = document.getElementById('dropdown-toggle');
        const reloadBtn = document.getElementById('reload-iframe');
        const openNewTabBtn = document.getElementById('open-new-tab');
        const fullscreenBtn = document.getElementById('fullscreen-iframe');
        const viewportContainer = document.getElementById('viewport-container');

        // --- Live Clock ---
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-MX', { hour12: false });
            document.getElementById('live-clock').textContent = timeString;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // --- Iframe Loader Controls ---
        function showLoader() {
            loader.classList.remove('hidden');
        }

        function hideLoader() {
            loader.classList.add('hidden');
        }

        iframe.addEventListener('load', hideLoader);

        // --- Active Link and Dynamic Title Update ---
        const allNavLinks = document.querySelectorAll('a[target="contenido"]');

        allNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                showLoader();

                // Clear active states
                document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

                // Add active to current parent item
                const parentItem = this.closest('.nav-item');
                if (parentItem) parentItem.classList.add('active');

                // Update Header Title from Link text
                const text = this.innerText.trim();
                activeViewName.textContent = text;

                // Close mobile menu if open
                if (window.innerWidth <= 992) {
                    navMenu.classList.remove('open');
                    dropdownParent.classList.remove('open-mobile');
                }
            });
        });

        // --- Mobile Menu Toggle ---
        mobileToggleBtn.addEventListener('click', () => {
            navMenu.classList.toggle('open');
        });

        // --- Mobile Dropdown Submenu Toggle ---
        dropdownToggle.addEventListener('click', (e) => {
            if (window.innerWidth <= 992) {
                e.preventDefault();
                dropdownParent.classList.toggle('open-mobile');
            }
        });

        // --- Toolbar Actions ---
        reloadBtn.addEventListener('click', () => {
            showLoader();
            iframe.src = iframe.src;
        });

        openNewTabBtn.addEventListener('click', () => {
            window.open(iframe.src, '_blank');
        });

        fullscreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                viewportContainer.requestFullscreen().catch(err => {
                    console.error(`Error al intentar pantalla completa: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        });
    });
</script>
</body>
</html>
