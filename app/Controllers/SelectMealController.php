<?php

class SelectMealController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit();
        }

        require '../config/database.php';

        $meal_id = $_POST['meal_id'] ?? null;
        $meal_type = $_POST['meal_type'] ?? null;
        $userId = $_SESSION['user_id'];
        $email = $_SESSION['user']; // only for subscription

        if (!$meal_id || !$meal_type) {
            echo json_encode(['status' => 'invalid']);
            exit();
        }

        // 🔥 SUBSCRIPTION (still email-based)
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            echo json_encode(['status' => 'nosubscription']);
            exit();
        }

        // 🔥 DAILY LIMIT
        $plan = $subscription['plan'];
        $price = $subscription['price'];

        $dailyLimit =
            $plan === 'daily'
                ? $price
                : ($plan === 'weekly'
                    ? $price / 7
                    : ($plan === 'monthly'
                        ? $price / 30
                        : 999999));

        // 🔥 TODAY TOTAL (FIXED)
        $stmt = $pdo->prepare("
            SELECT SUM(price) as total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // 🔥 MEAL PRICE
        $stmt = $pdo->prepare('SELECT price FROM meals WHERE id = ?');
        $stmt->execute([$meal_id]);
        $mealPrice = $stmt->fetch()['price'] ?? 0;

        if ($todayTotal + $mealPrice > $dailyLimit) {
            echo json_encode(['status' => 'limit_reached']);
            exit();
        }

        // ✅ INSERT (FIXED)
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, meal_id, meal_type, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $meal_id, $meal_type, $mealPrice]);

        echo json_encode(['status' => 'success']);
        exit();
    }
}
