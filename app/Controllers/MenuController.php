<?php

class MenuController
{
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

        $query = 'SELECT * FROM meals WHERE 1=1';
        $params = [];

        if (!empty($search)) {
            $query .= ' AND (name LIKE ? OR description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($min !== null) {
            $query .= ' AND price >= ?';
            $params[] = $min;
        }

        if ($max !== null) {
            $query .= ' AND price <= ?';
            $params[] = $max;
        }

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
                $query .= ' ORDER BY id DESC';
        }

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
                if ($sub['plan'] === 'daily') {
                    $query .= ' LIMIT 3';
                } elseif ($sub['plan'] === 'weekly') {
                    $query .= ' LIMIT 10';
                } elseif ($sub['plan'] === 'monthly') {
                    $query .= ' LIMIT 20';
                }
            }
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

        if (!$id) {
            return false;
        }

        $stmt = $pdo->prepare('SELECT * FROM meals WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}
