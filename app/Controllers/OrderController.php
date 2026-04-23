<?php

class OrderController
{
    public function store()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'You must be logged in to place an order ❌';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];
        $meal_id = $_POST['meal_id'];

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_email, meal_id) VALUES (?, ?)',
            );
            $stmt->execute([$user, $meal_id]);

            // ✅ Success toast
            $_SESSION['success'] = 'Order placed successfully ✅';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        } catch (Exception $e) {
            // ❌ Error toast
            $_SESSION['error'] = 'Failed to place order. Please try again.';
            header('Location: /mealbox/public/?url=menu');
            exit();
        }
    }
}
