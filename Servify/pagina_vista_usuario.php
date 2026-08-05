<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Servify - Menú Digital</title>
</head>
<body>
    <div class="app-shell">
        <header class="hero card">
            <div>
                <h1>Servify</h1>
                <p>Menú interactivo con secciones, carrito y estado de pedidos. Agrega lo que quieras y personaliza los ingredientes no deseados.</p>
            </div>
            <div class="status-chip" id="orderStatusOverview">
                <span>En preparación: 0</span>
                <span>Terminado: 0</span>
            </div>
        </header>

        <main class="app-grid">
            <section class="menu-panel card">
                <h2>Menú Digital</h2>
                <div id="menuSections" class="menu-sections"></div>
            </section>

            <aside class="cart-panel card">
                <h2>Carrito de pedidos</h2>
                <div id="cartContent" class="cart-list"></div>
                <div class="cart-summary" id="cartSummary"></div>
                <div class="order-status card" id="orderStatusPanel">
                    <h3>Estado del pedido</h3>
                    <p id="statusText">Aquí verás el estado de tus pedidos: En preparación y Terminado.</p>
                </div>
            </aside>
        </main>

        <footer class="footer-card card">
            <h2>Cómo usar</h2>
            <p>Presiona "Agregar" en tus productos favoritos, indica los ingredientes no deseados y visualiza los pedidos en el carrito. El total se actualiza y puedes eliminar cualquier pedido.</p>
            <p><strong>Conexión MySQL:</strong> <?= htmlspecialchars($dbStatus) ?></p>
            <p><?= htmlspecialchars($dbMessage) ?></p>
        </footer>
    </div>

    <script src="backend_vista_usuario.js"></script>
</body>
</html>
