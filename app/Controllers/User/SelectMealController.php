<?php

class SelectMealController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'unauthorized']);
                exit();
            }

            require '../config/database.php';

            $meal_id = $_POST['meal_id'] ?? null;
            $meal_type = $_POST['meal_type'] ?? null;
            $userId = $_SESSION['user_id'];

            if (!$meal_id || !$meal_type) {
                echo json_encode(['status' => 'invalid']);
                exit();
            }

            // Get active subscription with plan details
            $stmt = $pdo->prepare("
                SELECT 
                    s.*,
                    sp.name AS plan_name,
                    sp.price AS plan_price,
                    sp.duration_days,
                    sp.meal_limit
                FROM subscriptions s
                JOIN subscription_plans sp ON s.plan_id = sp.id
                WHERE s.user_id = ?
                AND s.status = 'active'
                AND (s.expiry_date IS NULL OR s.expiry_date >= CURDATE())
                ORDER BY s.created_at DESC
                LIMIT 1
            ");

            $stmt->execute([$userId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subscription) {
                echo json_encode(['status' => 'nosubscription']);
                exit();
            }

            // Check expiry
            if (
                !empty($subscription['expiry_date']) &&
                $subscription['expiry_date'] < date('Y-m-d')
            ) {
                $stmt = $pdo->prepare("
                    UPDATE subscriptions 
                    SET status = 'expired' 
                    WHERE id = ?
                ");
                $stmt->execute([$subscription['id']]);

                echo json_encode(['status' => 'expired']);
                exit();
            }

            // Check if meal is allowed in user's plan
            $stmt = $pdo->prepare("
                SELECT id 
                FROM plan_meals 
                WHERE plan_id = ? 
                AND meal_id = ?
                LIMIT 1
            ");
            $stmt->execute([$subscription['plan_id'], $meal_id]);

            if (!$stmt->fetch()) {
                echo json_encode(['status' => 'not_allowed']);
                exit();
            }

            // Get meal price and active status
            $stmt = $pdo->prepare("
                SELECT id, price, status 
                FROM meals 
                WHERE id = ?
                LIMIT 1
            ");
            $stmt->execute([$meal_id]);
            $meal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$meal || $meal['status'] !== 'active') {
                echo json_encode(['status' => 'not_allowed']);
                exit();
            }

            $mealPrice = (float) $meal['price'];

            // Daily budget limit
            $durationDays = (int) ($subscription['duration_days'] ?? 1);

            if ($durationDays <= 0) {
                $durationDays = 1;
            }

            $planPrice =
                (float) ($subscription['price'] ??
                    ($subscription['plan_price'] ?? 0));

            $dailyLimit = $planPrice / $durationDays;

            // Today's used amount
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(price), 0) AS total
                FROM orders
                WHERE user_id = ?
                AND DATE(created_at) = CURDATE()
            ");
            $stmt->execute([$userId]);
            $todayTotal = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            if ($todayTotal + $mealPrice > $dailyLimit) {
                echo json_encode(['status' => 'limit_reached']);
                exit();
            }

            // Insert order
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (user_id, meal_id, meal_type, price, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $meal_id, $meal_type, $mealPrice]);

            echo json_encode([
                'status' => 'success',
            ]);
            exit();
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
            exit();
        }
    }
}
