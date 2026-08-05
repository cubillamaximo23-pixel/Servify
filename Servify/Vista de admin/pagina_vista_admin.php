<?php
session_start();
require_once __DIR__ . '/backend_vista_admin.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_admin.css">
    <title>Servify - Panel Administrador</title>
</head>
<body>
    <div class="app-shell">
        <header class="hero card">
            <div>
                <h1>Servify Admin</h1>
            </div>
        </header>

        <main class="app-grid">
            <?php if (!$isLoggedIn): ?>
                <section class="admin-login card" id="loginSection">
                    <h2>Ingreso administrador</h2>
                    <form class="admin-form" method="post" action="pagina_vista_admin.php">
                        <input type="hidden" name="action" value="login">
                        <label>
                            Usuario
                            <input type="text" name="username" placeholder="admin" autocomplete="username" required>
                        </label>
                        <label>
                            Contraseña
                            <input type="password" name="password" placeholder="Contraseña" autocomplete="current-password" required>
                        </label>
                        <button class="button-primary" type="submit">Ingresar</button>
                        <p>Usuario: <strong>admin</strong> / Contraseña: <strong>servify123</strong></p>
                        <?php if (!empty($loginError)): ?>
                            <p style="color:#ff7373; margin:0;"><?= htmlspecialchars($loginError) ?></p>
                        <?php endif; ?>
                    </form>
                </section>
            <?php else: ?>
                <section class="admin-dashboard card" id="dashboardSection">
                    <div class="admin-actions">
                        <form method="post" action="pagina_vista_admin.php">
                            <input type="hidden" name="action" value="logout">
                            <button class="button-secondary" type="submit">Cerrar sesión</button>
                        </form>
                    </div>

                    <div class="admin-summary admin-card">
                        <h2>Resumen rápido</h2>
                        <div class="summary-item">Pedidos totales: <?= count($orders) ?></div>
                        <div class="summary-item">En preparación: <?= count(array_filter($orders, static fn($order) => $order['status'] === 'En preparación')) ?></div>
                        <div class="summary-item">Terminados: <?= count(array_filter($orders, static fn($order) => $order['status'] === 'Terminado')) ?></div>
                    </div>

                    <div class="admin-grid">
                        <div class="admin-card">
                            <h2>Pedidos activos</h2>
                            <table class="order-table" id="ordersTable">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                            <td>1</td>
                                            <td><?= htmlspecialchars($order['status']) ?></td>
                                            <td>
                                                <form method="post" action="pagina_vista_admin.php" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="toggle_order">
                                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                                    <button class="button-secondary" type="submit">Cambiar estado</button>
                                                </form>
                                                <form method="post" action="pagina_vista_admin.php" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="delete_order">
                                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                                    <button class="button-primary" type="submit">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="admin-card">
                            <h2>Agregar producto al menú</h2>
                            <form class="admin-form" method="post" action="pagina_vista_admin.php">
                                <input type="hidden" name="action" value="add_product">
                                <label>
                                    Nombre
                                    <input type="text" name="name" placeholder="Nombre del producto" required>
                                </label>
                                <label>
                                    Descripción
                                    <textarea name="description" rows="3" placeholder="Descripción del producto" required></textarea>
                                </label>
                                <label>
                                    Precio
                                    <input type="number" name="price" min="1" placeholder="Precio en pesos" required>
                                </label>
                                <label>
                                    Sección
                                    <select name="section">
                                        <option value="Pizzas">Pizzas</option>
                                        <option value="Hamburguesas">Hamburguesas</option>
                                        <option value="Ensaladas">Ensaladas</option>
                                        <option value="Bebidas">Bebidas</option>
                                    </select>
                                </label>
                                <button class="button-primary" type="submit">Guardar producto</button>
                            </form>
                        </div>
                    </div>

                    <div class="admin-card">
                        <h2>Menú actual</h2>
                        <table class="menu-table" id="menuItemsTable">
                            <thead>
                                <tr>
                                    <th>Sección</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td>$<?= number_format((float) $product['price'], 0, ',', '.') ?></td>
                                        <td>
                                            <form method="post" action="pagina_vista_admin.php">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                                <button class="button-secondary" type="submit">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <div class="admin-card">
            <h2>Conexión a MySQL</h2>
            <p><strong>Estado:</strong> <?= htmlspecialchars($dbStatus) ?></p>
            <?php if ($dbStatus === 'error'): ?>
                <p style="color:#ff7373;"><?= htmlspecialchars($dbError) ?></p>
            <?php else: ?>
                <p><?= htmlspecialchars($dbMessage) ?></p>
            <?php endif; ?>
            <p>Se recomienda usar variables de entorno para host, usuario, contraseña y nombre de la base de datos.</p>
        </div>

        <footer class="footer-card card">
            <h2>Panel administrador</h2>
            <p>La administración está conectada al backend PHP y lista para crecer con tablas reales de productos, pedidos y usuarios.</p>
        </footer>
    </div>
</body>
</html>
