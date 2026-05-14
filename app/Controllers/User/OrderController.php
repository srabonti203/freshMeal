<?php

class OrderController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'You must be logged in to place an order';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];
        $mealId = $_POST['meal_id'] ?? null;

        if (!$mealId || !filter_var($mealId, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = 'Invalid meal selection';
            header('Location: /mealbox/public/?url=menu');
            exit();
        }

        // GET ACTIVE SUBSCRIPTION
        $stmt = $pdo->prepare("
            SELECT *
            FROM subscriptions
            WHERE user_id = ?
            AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $subscription = $stmt->fetch();

        // AUTO EXPIRE SUBSCRIPTION
        if (
            $subscription &&
            $subscription['status'] === 'active' &&
            !empty($subscription['expiry_date']) &&
            strtotime($subscription['expiry_date']) < strtotime(date('Y-m-d'))
        ) {
            $expireStmt = $pdo->prepare("
                UPDATE subscriptions
                SET status = 'expired'
                WHERE id = ?
            ");

            $expireStmt->execute([$subscription['id']]);

            $_SESSION['error'] = 'Your subscription has expired';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        if (!$subscription || $subscription['status'] !== 'active') {
            $_SESSION['error'] = 'Please subscribe before placing an order';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        // CHECK MEAL IS ALLOWED IN THIS PLAN
        if (!empty($subscription['plan_id'])) {
            $stmt = $pdo->prepare("
                SELECT meals.*
                FROM meals
                INNER JOIN plan_meals 
                    ON meals.id = plan_meals.meal_id
                WHERE meals.id = ?
                AND plan_meals.plan_id = ?
                AND meals.status = 'active'
                LIMIT 1
            ");

            $stmt->execute([$mealId, $subscription['plan_id']]);
        } else {
            $stmt = $pdo->prepare("
                SELECT *
                FROM meals
                WHERE id = ?
                AND status = 'active'
                LIMIT 1
            ");

            $stmt->execute([$mealId]);
        }

        $meal = $stmt->fetch();

        if (!$meal) {
            $_SESSION['error'] =
                'This meal is not available in your current plan';
            header('Location: /mealbox/public/?url=menu');
            exit();
        }

        // DAILY LIMIT CHECK
        $planDays = 1;

        if (!empty($subscription['plan_id'])) {
            $stmt = $pdo->prepare("
                SELECT duration_days
                FROM subscription_plans
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$subscription['plan_id']]);
            $planData = $stmt->fetch();

            if ($planData && (int) $planData['duration_days'] > 0) {
                $planDays = (int) $planData['duration_days'];
            }
        } else {
            $planDays =
                $subscription['plan'] === 'weekly'
                    ? 7
                    : ($subscription['plan'] === 'monthly'
                        ? 30
                        : 1);
        }

        $baseDaily = (float) $subscription['price'] / $planDays;
        $carry = (float) ($subscription['carry_over'] ?? 0);
        $dailyLimit = $baseDaily + $carry;

        $stmt = $pdo->prepare("
            SELECT SUM(price) AS total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");

        $stmt->execute([$userId]);
        $todayTotal = (float) ($stmt->fetch()['total'] ?? 0);

        $newTotal = $todayTotal + (float) $meal['price'];

        if ($newTotal > $dailyLimit) {
            $_SESSION['error'] =
                'Daily limit exceeded. Please choose a cheaper meal or order tomorrow.';
            header('Location: /mealbox/public/?url=menu');
            exit();
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (user_id, meal_id, meal_type, price)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([$userId, $mealId, $meal['type'], $meal['price']]);

            $_SESSION['success'] = 'Order placed successfully ✅';
            header('Location: /mealbox/public/?url=dashboard');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to place order. Please try again.';
            header('Location: /mealbox/public/?url=menu');
            exit();
        }
    }
}
