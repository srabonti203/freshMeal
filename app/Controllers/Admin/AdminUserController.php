<?php

class AdminUserController
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
        $statusFilter = $_GET['status'] ?? '';
        $planFilter = $_GET['plan'] ?? '';

        $query = "
            SELECT 
                users.id,
                users.name,
                users.email,
                users.phone,
                users.profile_image,
                users.created_at,
                users.status,
                users.role,

                (
                    SELECT subscriptions.plan
                    FROM subscriptions
                    WHERE subscriptions.user_id = users.id
                       OR subscriptions.user_email = users.email
                    ORDER BY subscriptions.created_at DESC
                    LIMIT 1
                ) AS latest_plan,

                (
                    SELECT subscriptions.status
                    FROM subscriptions
                    WHERE subscriptions.user_id = users.id
                       OR subscriptions.user_email = users.email
                    ORDER BY subscriptions.created_at DESC
                    LIMIT 1
                ) AS subscription_status,

                COUNT(orders.id) AS total_orders

            FROM users
            LEFT JOIN orders ON orders.user_id = users.id
            WHERE users.role = 'user'
        ";

        $params = [];

        if (!empty($search)) {
            $query .=
                ' AND (users.name LIKE ? OR users.email LIKE ? OR users.phone LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (in_array($statusFilter, ['active', 'suspended'])) {
            $query .= ' AND users.status = ?';
            $params[] = $statusFilter;
        }

        if (in_array($planFilter, ['daily', 'weekly', 'monthly'])) {
            $query .= "
                AND (
                    SELECT subscriptions.plan
                    FROM subscriptions
                    WHERE subscriptions.user_id = users.id
                       OR subscriptions.user_email = users.email
                    ORDER BY subscriptions.created_at DESC
                    LIMIT 1
                ) = ?
            ";

            $params[] = $planFilter;
        }

        $query .= "
            GROUP BY users.id
            ORDER BY users.id DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/admin/admin-users.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function suspend()
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
            header('Location: /mealbox/public/?url=admin-users');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: /mealbox/public/?url=admin-users');
            exit();
        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET status = 'suspended'
            WHERE id = ? AND role = 'user'
        ");

        $stmt->execute([$id]);

        $_SESSION['success'] = 'User suspended successfully.';
        header('Location: /mealbox/public/?url=admin-users');
        exit();
    }

    public function activate()
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
            header('Location: /mealbox/public/?url=admin-users');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: /mealbox/public/?url=admin-users');
            exit();
        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET status = 'active'
            WHERE id = ? AND role = 'user'
        ");

        $stmt->execute([$id]);

        $_SESSION['success'] = 'User activated successfully.';
        header('Location: /mealbox/public/?url=admin-users');
        exit();
    }
}
