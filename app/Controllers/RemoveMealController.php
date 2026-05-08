<?php

class RemoveMealController
{
    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];
        $orderId = $_POST['selection_id'] ?? null;

        if (!$orderId) {
            echo json_encode(['status' => 'invalid']);
            exit();
        }

        // Delete only one selected item
        $stmt = $pdo->prepare("
            DELETE FROM orders
            WHERE id = ?
            AND user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$orderId, $userId]);

        if (!$stmt->rowCount()) {
            echo json_encode(['status' => 'not_found']);
            exit();
        }

        // Get subscription again
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions
            WHERE user_email = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user']]);
        $subscription = $stmt->fetch();

        $plan = $subscription['plan'];
        $totalBudget = $subscription['price'];
        $carry = $subscription['carry_over'] ?? 0;

        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);
        $baseDaily = $totalBudget / $days;
        $dailyLimit = $baseDaily + $carry;

        // Recalculate used today
        $stmt = $pdo->prepare("
            SELECT SUM(price) as total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        echo json_encode([
            'status' => 'deleted',
            'totalBudget' => number_format($totalBudget, 2),
            'carry' => number_format($carry, 2),
            'baseDaily' => number_format($baseDaily, 2),
            'dailyLimit' => number_format($dailyLimit, 2),
            'todayTotal' => number_format($todayTotal, 2),
            'remainingTotal' => number_format($totalBudget - $todayTotal, 2),
        ]);

        exit();
    }
}
