<?php

class DeleteOrderController
{
    public function delete()
    {
        session_start();

        // 🔒 Check login
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'You must be logged in to cancel an order ❌';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $order_id = $_POST['order_id'] ?? null;

        //Invalid request
        if (!$order_id) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        $user = $_SESSION['user'];

        //Delete order
        $stmt = $pdo->prepare(
            'DELETE FROM orders WHERE id = ? AND user_email = ?',
        );
        $stmt->execute([$order_id, $user]);

        //Success toast
        $_SESSION['success'] = 'Order cancelled';
        header('Location: /mealbox/public/?url=dashboard');
        exit();
    }
}
