<?php

class SubscriptionController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $view = '../app/Views/user/subscription.php';
        require '../app/Views/layouts/layout.php';
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Login required';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];
        $plan = $_POST['plan'] ?? '';
        $price = $_POST['price'] ?? 0;

        if (!$plan) {
            $_SESSION['error'] = 'Invalid plan';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        $stmt = $pdo->prepare("
            INSERT INTO subscriptions (user_email, plan, price)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$user, $plan, $price]);

        $_SESSION['success'] = 'Subscription activated 🎉';
        header('Location: /mealbox/public/?url=dashboard');
        exit();
    }
}
