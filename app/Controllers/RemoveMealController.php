<?php

class RemoveMealController
{
    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit();
        }

        require '../config/database.php';

        $selection_id = $_POST['selection_id'] ?? null;
        $user = $_SESSION['user'];

        if (!$selection_id) {
            echo json_encode(['status' => 'invalid']);
            exit();
        }

        $stmt = $pdo->prepare("
            DELETE FROM meal_selections 
            WHERE id = ? AND user_email = ?
        ");
        $stmt->execute([$selection_id, $user]);

        echo json_encode(['status' => 'deleted']);
        exit();
    }
}
