<?php

class SubscriptionController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        // ONLY ACTIVE PLANS
        $stmt = $pdo->query("
            SELECT *
            FROM subscription_plans
            WHERE status = 'active'
            ORDER BY price ASC
        ");

        $plans = $stmt->fetchAll();

        $view = '../app/Views/user/subscription.php';
        require '../app/Views/layouts/layout.php';
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Login required';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];

        $planId = $_POST['plan_id'] ?? null;

        if (!$planId || !filter_var($planId, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = 'Invalid subscription plan';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        // GET PLAN
        $stmt = $pdo->prepare("
        SELECT *
        FROM subscription_plans
        WHERE id = ?
        AND status = 'active'
    ");

        $stmt->execute([$planId]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $_SESSION['error'] = 'Subscription plan not found';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        // EXPIRE OLD ACTIVE SUBSCRIPTIONS
        $stmt = $pdo->prepare("
        UPDATE subscriptions
        SET status = 'expired'
        WHERE user_id = ?
        AND status = 'active'
    ");

        $stmt->execute([$userId]);

        // CALCULATE EXPIRY DATE
        $durationDays = (int) $plan['duration_days'];

        $expiryDate = date('Y-m-d', strtotime("+$durationDays days"));

        // INSERT NEW SUBSCRIPTION
        $stmt = $pdo->prepare("
            INSERT INTO subscriptions
            (user_id, plan, plan_id, price, status, expiry_date)
            VALUES (?, ?, ?, ?, 'active', ?)
        ");

        $stmt->execute([
            $userId,
            $plan['slug'],
            $plan['id'],
            $plan['price'],
            $expiryDate,
        ]);

        $_SESSION['success'] = 'Subscription activated successfully 🎉';

        header('Location: /mealbox/public/?url=dashboard');
        exit();
    }
}
