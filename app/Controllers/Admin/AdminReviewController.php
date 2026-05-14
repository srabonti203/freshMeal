<?php

class AdminReviewController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        $search = trim($_GET['search'] ?? '');
        $rating = $_GET['rating'] ?? '';

        $query = "
            SELECT 
                meal_reviews.*,
                users.name AS user_name,
                users.email AS user_email,
                meals.name AS meal_name,
                orders.meal_type
            FROM meal_reviews
            LEFT JOIN users ON users.id = meal_reviews.user_id
            LEFT JOIN meals ON meals.id = meal_reviews.meal_id
            LEFT JOIN orders ON orders.id = meal_reviews.order_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($search)) {
            $query .= "
                AND (
                    users.name LIKE ?
                    OR users.email LIKE ?
                    OR meals.name LIKE ?
                    OR meal_reviews.review LIKE ?
                )
            ";

            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (in_array($rating, ['1', '2', '3', '4', '5'])) {
            $query .= ' AND meal_reviews.rating = ?';
            $params[] = $rating;
        }

        $query .= ' ORDER BY meal_reviews.id DESC';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/admin/admin-reviews.php';
        require '../app/Views/layouts/admin-layout.php';
    }
}
