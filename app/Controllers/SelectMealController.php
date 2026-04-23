<?php

class SelectMealController
{
    public function store()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $meal_id = $_POST['meal_id'];
        $user = $_SESSION['user'];

        $stmt = $pdo->prepare("
            INSERT INTO meal_selections (user_email, meal_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$user, $meal_id]);

        $_SESSION['success'] = 'Meal selected';

        header('Location: /mealbox/public/?url=menu');
        exit();
    }
}
