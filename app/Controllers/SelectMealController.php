<?php

class SelectMealController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit();
        }

        require '../config/database.php';

        $meal_id = $_POST['meal_id'] ?? null;
        $meal_type = $_POST['meal_type'] ?? null;
        $user = $_SESSION['user'];

        if (!$meal_id || !$meal_type) {
            echo json_encode(['status' => 'invalid']);
            exit();
        }

        // 🔥 BEFORE INSERT: check subscription and budget
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            echo json_encode(['status' => 'nosubscription']);
            exit();
        }

        // Daily limit logic
        $plan = $subscription['plan'];
        $price = $subscription['price'];

        if ($plan === 'daily') {
            $dailyLimit = $price;
        } elseif ($plan === 'weekly') {
            $dailyLimit = $price / 7;
        } elseif ($plan === 'monthly') {
            $dailyLimit = $price / 30;
        } else {
            $dailyLimit = 999999; // premium unlimited
        }

        // Current total
        $stmt = $pdo->prepare("
            SELECT SUM(meals.price) as total
            FROM meal_selections
            JOIN meals ON meal_selections.meal_id = meals.id
            WHERE meal_selections.user_email = ?
            AND DATE(meal_selections.created_at) = CURDATE()
        ");
        $stmt->execute([$user]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // Price of selected meal
        $stmt = $pdo->prepare('SELECT price FROM meals WHERE id = ?');
        $stmt->execute([$meal_id]);
        $mealPrice = $stmt->fetch()['price'] ?? 0;

        if ($todayTotal + $mealPrice > $dailyLimit) {
            echo json_encode(['status' => 'limit_reached']);
            exit();
        }

        // ✅ insert
        $stmt = $pdo->prepare("
            INSERT INTO meal_selections (user_email, meal_id, meal_type)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user, $meal_id, $meal_type]);

        // ✅ return updated counts
        $stmt = $pdo->prepare("
            SELECT meal_type, COUNT(*) as total
            FROM meal_selections
            WHERE user_email = ?
            GROUP BY meal_type
        ");
        $stmt->execute([$user]);
        $countsRaw = $stmt->fetchAll();

        $counts = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
        ];

        foreach ($countsRaw as $row) {
            $counts[$row['meal_type']] = $row['total'];
        }

        echo json_encode([
            'status' => 'success',
            'counts' => $counts,
        ]);
        exit();
    }
}
