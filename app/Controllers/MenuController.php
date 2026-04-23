<?php

class MenuController
{
    public function index()
    {
        require '../config/database.php';

        // 🔍 Get inputs safely
        $search = trim($_GET['search'] ?? '');
        $min = ($_GET['min'] ?? '') !== '' ? (float) $_GET['min'] : null;
        $max = ($_GET['max'] ?? '') !== '' ? (float) $_GET['max'] : null;
        $sort = $_GET['sort'] ?? '';

        // 🔒 Fix: if min > max, swap them
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        $query = 'SELECT * FROM meals WHERE 1=1';
        $params = [];

        // 🔍 Search by name OR description (better UX)
        if (!empty($search)) {
            $query .= ' AND (name LIKE ? OR description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // 💰 Min price
        if ($min !== null) {
            $query .= ' AND price >= ?';
            $params[] = $min;
        }

        // 💰 Max price
        if ($max !== null) {
            $query .= ' AND price <= ?';
            $params[] = $max;
        }

        // 🔽 Sorting
        switch ($sort) {
            case 'low':
                $query .= ' ORDER BY price ASC';
                break;

            case 'high':
                $query .= ' ORDER BY price DESC';
                break;

            case 'name':
                $query .= ' ORDER BY name ASC';
                break;

            default:
                $query .= ' ORDER BY id DESC'; // latest meals
        }

        // 🎯 Limit meals based on plan
        if (isset($_SESSION['user'])) {
            $stmt = $pdo->prepare("
                SELECT plan FROM subscriptions 
                WHERE user_email = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$_SESSION['user']]);
            $sub = $stmt->fetch();

            if ($sub) {
                switch ($sub['plan']) {
                    case 'daily':
                        $query .= ' LIMIT 3';
                        break;

                    case 'weekly':
                        $query .= ' LIMIT 10';
                        break;

                    case 'monthly':
                        $query .= ' LIMIT 20';
                        break;

                    case 'premium':
                        // unlimited
                        break;
                }
            }
        }

        // ✅ Execute safely
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $meals = $stmt->fetchAll();

        return $meals;
    }
}
