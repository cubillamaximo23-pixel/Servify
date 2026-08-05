<?php
require_once __DIR__ . '/db_admin.php';

$adminUser = 'admin';
$adminPass = 'servify123';
$action = $_POST['action'] ?? '';
$loginError = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === $adminUser && $password === $adminPass) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: pagina_vista_admin.php');
            exit;
        }

        $loginError = 'Usuario o contraseña incorrectos.';
    }

    if ($action === 'logout') {
        session_destroy();
        header('Location: pagina_vista_admin.php');
        exit;
    }
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}

$categories = [];
$products = [];
$orders = [];

if ($connection instanceof mysqli && $isLoggedIn && $dbStatus === 'conectado') {
    $categoriesResult = $connection->query('SELECT id, name FROM categories ORDER BY name');
    if ($categoriesResult) {
        $categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
    }

    $productsResult = $connection->query('SELECT p.id, p.name, p.description, p.price, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC');
    if ($productsResult) {
        $products = $productsResult->fetch_all(MYSQLI_ASSOC);
    }

    $ordersResult = $connection->query('SELECT id, customer_name, total, status, created_at FROM orders ORDER BY id DESC');
    if ($ordersResult) {
        $orders = $ordersResult->fetch_all(MYSQLI_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_product' && $isLoggedIn) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $categoryName = trim($_POST['section'] ?? '');

    if ($name !== '' && $description !== '' && $price > 0 && $categoryName !== '') {
        $stmt = $connection->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->bind_param('s', $categoryName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $insertCategory = $connection->prepare('INSERT INTO categories (name) VALUES (?)');
            $insertCategory->bind_param('s', $categoryName);
            $insertCategory->execute();
            $categoryId = $insertCategory->insert_id;
        } else {
            $categoryId = $result->fetch_assoc()['id'];
        }

        $insertProduct = $connection->prepare('INSERT INTO products (category_id, name, description, price) VALUES (?, ?, ?, ?)');
        $insertProduct->bind_param('issd', $categoryId, $name, $description, $price);
        $insertProduct->execute();

        header('Location: pagina_vista_admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_product' && $isLoggedIn) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId > 0) {
        $deleteStmt = $connection->prepare('DELETE FROM products WHERE id = ?');
        $deleteStmt->bind_param('i', $productId);
        $deleteStmt->execute();
        header('Location: pagina_vista_admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle_order' && $isLoggedIn) {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    if ($orderId > 0) {
        $connection->query("UPDATE orders SET status = CASE WHEN status = 'En preparación' THEN 'Terminado' ELSE 'En preparación' END WHERE id = {$orderId}");
        header('Location: pagina_vista_admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_order' && $isLoggedIn) {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    if ($orderId > 0) {
        $connection->query("DELETE FROM orders WHERE id = {$orderId}");
        header('Location: pagina_vista_admin.php');
        exit;
    }
}
?>
