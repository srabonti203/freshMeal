<?php

class CarryOverService
{
    public static function process($pdo, $subscription, $user)
    {
        $today = date('Y-m-d');

        // جلوگیری از اجرای دوباره در یک روز
        if ($subscription['last_carry_date'] === $today) {
            return;
        }

        $plan = $subscription['plan'];

        // total budget (later from DB/admin)
        $totalBudget = match ($plan) {
            'weekly' => 1400,
            'monthly' => 6000,
            default => 200,
        };

        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);

        $baseDaily = $totalBudget / $days;
        $carry = $subscription['carry_over'];

        $dailyLimit = $baseDaily + $carry;

        // get today's usage
        $stmt = $pdo->prepare("
            SELECT SUM(meals.price) as total
            FROM meal_selections
            JOIN meals ON meal_selections.meal_id = meals.id
            WHERE meal_selections.user_email = ?
            AND DATE(meal_selections.created_at) = CURDATE()
        ");
        $stmt->execute([$user]);

        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // calculate unused
        $unused = $dailyLimit - $todayTotal;

        if ($unused > 0) {
            $carry += $unused;
        }

        // optional cap (VERY IMPORTANT)
        $carry = min($carry, $baseDaily * 3);

        // save
        $stmt = $pdo->prepare("
            UPDATE subscriptions 
            SET carry_over = ?, last_carry_date = ?
            WHERE id = ?
        ");
        $stmt->execute([$carry, $today, $subscription['id']]);
    }
}
