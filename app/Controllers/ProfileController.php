<?php

class ProfileController
{
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $email = $_SESSION['user'];

        // USER
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // ORDERS
        $stmt = $pdo->prepare("
            SELECT meals.name, meals.price, orders.created_at
            FROM orders
            JOIN meals ON orders.meal_id = meals.id
            WHERE orders.user_email=?
            ORDER BY orders.created_at DESC
        ");
        $stmt->execute([$email]);
        $orders = $stmt->fetchAll();

        // ANALYTICS
        $totalOrders = count($orders);
        $totalSpent = array_sum(array_column($orders, 'price'));

        // SUBSCRIPTION
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email=? 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$email]);
        $subscription = $stmt->fetch();

        $view = '../app/Views/profile.php';
        require '../app/Views/layout.php';
    }

    public function update()
    {
        require '../config/database.php';

        $email = $_SESSION['user'];

        $stmt = $pdo->prepare("
            UPDATE users SET name=?, phone=?, address=? WHERE email=?
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['phone'],
            $_POST['address'],
            $email,
        ]);

        echo json_encode(['status' => 'success']);
    }

    public function changePassword()
    {
        require '../config/database.php';

        $email = $_SESSION['user'];

        $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('UPDATE users SET password=? WHERE email=?');
        $stmt->execute([$newPass, $email]);

        echo json_encode(['status' => 'success']);
    }

    public function uploadImage()
    {
        $email = $_SESSION['user'];

        $file = $_FILES['image'];

        $filename = time() . '_' . $file['name'];
        move_uploaded_file($file['tmp_name'], '../public/uploads/' . $filename);

        require '../config/database.php';

        $stmt = $pdo->prepare("
            UPDATE users SET profile_image=? WHERE email=?
        ");
        $stmt->execute([$filename, $email]);

        echo json_encode(['status' => 'success', 'image' => $filename]);
    }
}
