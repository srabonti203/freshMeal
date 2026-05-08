<?php

class AdminLoginController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../config/database.php';

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: /mealbox/public/?url=admin-login');
                exit();
            }

            $stmt = $pdo->prepare(
                "SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1",
            );
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['role'] = 'admin';

                header('Location: /mealbox/public/?url=admin-dashboard');
                exit();
            }

            $_SESSION['error'] = 'Invalid admin email or password';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $view = '../app/Views/admin/admin-login.php';
        require '../app/Views/layouts/layout.php';
    }
}
