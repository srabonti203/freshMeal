<?php

class ProfileController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        $stmt = $pdo->prepare("
            SELECT meals.name, orders.price, orders.meal_type, DATE(orders.created_at) as date
            FROM orders
            JOIN meals ON orders.meal_id = meals.id
            WHERE orders.user_id = ?
            ORDER BY orders.created_at DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();

        $breakfastOrders = [];
        $lunchOrders = [];
        $dinnerOrders = [];

        foreach ($orders as $o) {
            if ($o['meal_type'] === 'breakfast') {
                $breakfastOrders[] = $o;
            } elseif ($o['meal_type'] === 'lunch') {
                $lunchOrders[] = $o;
            } else {
                $dinnerOrders[] = $o;
            }
        }

        $totalOrders = count($orders);
        $totalSpent = array_sum(array_column($orders, 'price'));

        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user['email']]);
        $subscription = $stmt->fetch();

        $view = '../app/Views/user/profile.php';
        require '../app/Views/layouts/layout.php';
    }

    public function update()
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

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!$name || !$email || !$phone || !$address) {
            echo json_encode(['status' => 'empty']);
            exit();
        }

        $stmt = $pdo->prepare(
            'SELECT id FROM users WHERE email = ? AND id != ?',
        );
        $stmt->execute([$email, $userId]);

        if ($stmt->fetch()) {
            echo json_encode(['status' => 'duplicate']);
            exit();
        }

        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, phone = ?, address = ?
            WHERE id = ?
        ");

        $stmt->execute([$name, $email, $phone, $address, $userId]);

        $_SESSION['user'] = $email;
        $_SESSION['user_name'] = $name;

        echo json_encode(['status' => 'success']);
        exit();
    }

    public function changePassword()
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
        $password = $_POST['password'] ?? '';

        if (strlen($password) < 6) {
            echo json_encode(['status' => 'weak']);
            exit();
        }

        $newPass = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$newPass, $userId]);

        echo json_encode(['status' => 'success']);
        exit();
    }

    public function uploadImage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit();
        }

        if (
            !isset($_FILES['image']) ||
            $_FILES['image']['error'] !== UPLOAD_ERR_OK
        ) {
            echo json_encode(['status' => 'no_file']);
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];
        $file = $_FILES['image'];

        $uploadDir = '../public/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;

        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo json_encode(['status' => 'upload_failed']);
            exit();
        }

        $stmt = $pdo->prepare("
            UPDATE users 
            SET profile_image = ? 
            WHERE id = ?
        ");
        $stmt->execute([$filename, $userId]);

        echo json_encode([
            'status' => 'success',
            'image' => $filename,
        ]);
        exit();
    }
}
