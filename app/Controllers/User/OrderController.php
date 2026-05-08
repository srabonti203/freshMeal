<?php

class OrderController
{
    public function store()
    {
        session_start();

        // CHECK LOGIN
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'You must be logged in to place an order ❌';

            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];
        $meal_id = $_POST['meal_id'] ?? null;

        // VALIDATION
        if (!$meal_id) {
            $_SESSION['error'] = 'Invalid meal selection';

            header('Location: /mealbox/public/?url=menu');
            exit();
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_email, meal_id) VALUES (?, ?)',
            );

            $stmt->execute([$user, $meal_id]);

            // SUCCESS
            $_SESSION['success'] = 'Order placed successfully ✅';

            header('Location: /mealbox/public/?url=dashboard');
            exit();
        } catch (Exception $e) {
            // ERROR
            $_SESSION['error'] = 'Failed to place order. Please try again.';

            header('Location: /mealbox/public/?url=menu');
            exit();
        }
    }
}
