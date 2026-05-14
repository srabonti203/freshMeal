<?php

class AdminOrderController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        $search = trim($_GET['search'] ?? '');
        $mealType = $_GET['meal_type'] ?? '';

        $query = "
            SELECT 
                orders.id,
                orders.user_id,
                orders.meal_id,
                orders.meal_type,
                orders.price,
                orders.created_at,
                orders.delivery_status,
                orders.delivered_at,

                users.name AS user_name,
                users.email AS user_email,

                meals.name AS meal_name,
                meals.image AS meal_image

            FROM orders
            LEFT JOIN users ON users.id = orders.user_id
            LEFT JOIN meals ON meals.id = orders.meal_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($search)) {
            $query .= "
                AND (
                    users.name LIKE ?
                    OR users.email LIKE ?
                    OR meals.name LIKE ?
                    OR orders.id LIKE ?
                )
            ";

            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (in_array($mealType, ['breakfast', 'lunch', 'dinner'])) {
            $query .= ' AND orders.meal_type = ?';
            $params[] = $mealType;
        }

        $query .= ' ORDER BY orders.id DESC';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/admin/admin-orders.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function deliver()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=admin-orders');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'Invalid order ID';
            header('Location: /mealbox/public/?url=admin-orders');
            exit();
        }

        $stmt = $pdo->prepare("
        UPDATE orders
        SET delivery_status = 'delivered',
            delivered_at = NOW()
        WHERE id = ?
    ");

        $stmt->execute([$id]);

        $_SESSION['success'] = 'Order marked as delivered.';
        header('Location: /mealbox/public/?url=admin-orders');
        exit();
    }
}
