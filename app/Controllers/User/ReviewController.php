<?php

class ReviewController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login first';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $orderId = $_POST['order_id'] ?? null;
        $mealId = $_POST['meal_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $review = trim($_POST['review'] ?? '');

        if (!$orderId || !$mealId || !$rating || empty($review)) {
            $_SESSION['error'] = 'All review fields are required.';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Rating must be between 1 and 5.';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        $stmt = $pdo->prepare("
            SELECT *
            FROM orders
            WHERE id = ?
            AND user_id = ?
            AND meal_id = ?
            AND delivery_status = 'delivered'
        ");
        $stmt->execute([$orderId, $userId, $mealId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['error'] = 'You can only review delivered meals.';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM meal_reviews
            WHERE order_id = ?
            AND user_id = ?
        ");
        $stmt->execute([$orderId, $userId]);

        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'You already reviewed this order.';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        }

        $stmt = $pdo->prepare("
            INSERT INTO meal_reviews (user_id, meal_id, order_id, rating, review)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$userId, $mealId, $orderId, $rating, $review]);

        $_SESSION['success'] = 'Review submitted successfully.';
        header('Location: /mealbox/public/?url=dashboard');
        exit();
    }
}
