<?php

class MenuController
{
    private function expireIfNeeded($pdo, $subscription)
    {
        if (
            $subscription &&
            $subscription['status'] === 'active' &&
            !empty($subscription['expiry_date']) &&
            strtotime($subscription['expiry_date']) < strtotime(date('Y-m-d'))
        ) {
            $stmt = $pdo->prepare("
                UPDATE subscriptions
                SET status = 'expired'
                WHERE id = ?
            ");

            $stmt->execute([$subscription['id']]);

            $_SESSION['error'] = 'Your subscription has expired';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $search = trim($_GET['search'] ?? '');
        $min = ($_GET['min'] ?? '') !== '' ? (float) $_GET['min'] : null;
        $max = ($_GET['max'] ?? '') !== '' ? (float) $_GET['max'] : null;
        $sort = $_GET['sort'] ?? '';

        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        $params = [];

        $query = "
            SELECT DISTINCT meals.*
            FROM meals
        ";

        if (isset($_SESSION['user_id'])) {
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

            $stmt->execute([$_SESSION['user_id']]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->expireIfNeeded($pdo, $subscription);

            if (!$subscription || $subscription['status'] !== 'active') {
                $_SESSION['error'] = 'Please subscribe first';
                header('Location: /mealbox/public/?url=subscribe');
                exit();
            }

            if (!empty($subscription['plan_id'])) {
                $query .= "
                    INNER JOIN plan_meals
                        ON meals.id = plan_meals.meal_id
                    AND plan_meals.plan_id = ?
                ";

                $params[] = $subscription['plan_id'];
            }
        }

        $query .= "
            WHERE meals.status = 'active'
        ";

        if (!empty($search)) {
            $query .= ' AND (meals.name LIKE ? OR meals.description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($min !== null) {
            $query .= ' AND meals.price >= ?';
            $params[] = $min;
        }

        if ($max !== null) {
            $query .= ' AND meals.price <= ?';
            $params[] = $max;
        }

        switch ($sort) {
            case 'low':
                $query .= ' ORDER BY meals.price ASC';
                break;

            case 'high':
                $query .= ' ORDER BY meals.price DESC';
                break;

            case 'name':
                $query .= ' ORDER BY meals.name ASC';
                break;

            default:
                $query .= ' ORDER BY meals.id DESC';
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function detail($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            return false;
        }

        $query = "
            SELECT DISTINCT meals.*
            FROM meals
        ";

        $params = [];

        if (isset($_SESSION['user_id'])) {
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

            $stmt->execute([$_SESSION['user_id']]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->expireIfNeeded($pdo, $subscription);

            if (!$subscription || $subscription['status'] !== 'active') {
                $_SESSION['error'] = 'Please subscribe first';
                header('Location: /mealbox/public/?url=subscribe');
                exit();
            }

            if (!empty($subscription['plan_id'])) {
                $query .= "
                    INNER JOIN plan_meals
                        ON meals.id = plan_meals.meal_id
                    AND plan_meals.plan_id = ?
                ";

                $params[] = $subscription['plan_id'];
            }
        }

        $query .= "
            WHERE meals.id = ?
            AND meals.status = 'active'
            LIMIT 1
        ";

        $params[] = $id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch();
    }
}
