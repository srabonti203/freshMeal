<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/MenuController.php';
require_once '../app/Controllers/LoginController.php';
require_once '../app/Controllers/OrderController.php';
require_once '../app/Controllers/DeleteOrderController.php';
require_once '../app/Controllers/SubscriptionController.php';

$url = $_GET['url'] ?? '/';

switch ($url) {
    // 🏠 Home
    case '/':
        $view = '../app/Views/home.php';
        require '../app/Views/layout.php';
        break;

    // 🍱 Menu
    case 'menu':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];

        //Check subscription
        $stmt = $pdo->prepare("
        SELECT * FROM subscriptions 
        WHERE user_email = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
        $stmt->execute([$user]);
        $subscription = $stmt->fetch();

        //No plan → redirect
        if (!$subscription) {
            header('Location: /mealbox/public/?url=subscription');
            exit();
        }

        //Load menu
        $controller = new MenuController();
        $meals = $controller->index();

        $view = '../app/Views/menu.php';
        require '../app/Views/layout.php';
        break;

    // 🍽 Single Meal
    case 'meal':
        require '../config/database.php';

        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo 'Meal not found';
            exit();
        }

        $stmt = $pdo->prepare('SELECT * FROM meals WHERE id = ?');
        $stmt->execute([$id]);
        $meal = $stmt->fetch();

        if (!$meal) {
            echo 'Meal not found';
            exit();
        }

        $view = '../app/Views/meal.php';
        require '../app/Views/layout.php';
        break;

    case 'select-meal':
        $controller = new SelectMealController();
        $controller->store();
        break;

    // 🔐 Login
    case 'login':
        $controller = new LoginController();
        $controller->index();
        break;

    // 🧾 Register
    case 'register':
        $controller = new LoginController();
        $controller->register();
        break;

    // 💳 Subscription Page
    case 'subscription':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        $view = '../app/Views/subscription.php';
        require '../app/Views/layout.php';
        break;

    // 💾 Save Subscription
    case 'subscribe-store':
        $controller = new SubscriptionController();
        $controller->store();
        break;

    // 📊 Dashboard (Protected + Subscription Required)
    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];

        // 🔥 Check subscription FIRST
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user]);
        $subscription = $stmt->fetch();

        // ❌ No subscription → redirect
        if (!$subscription) {
            header('Location: /mealbox/public/?url=subscription');
            exit();
        }

        // ✅ Orders
        $stmt = $pdo->prepare("
            SELECT orders.id, meals.name, meals.price, orders.created_at
            FROM orders
            JOIN meals ON orders.meal_id = meals.id
            WHERE orders.user_email = ?
            ORDER BY orders.created_at DESC
        ");
        $stmt->execute([$user]);
        $orders = $stmt->fetchAll();

        // ✅ Stats
        $totalOrders = count($orders);

        $totalSpent = 0;
        foreach ($orders as $order) {
            $totalSpent += $order['price'];
        }

        // ✅ Load dashboard
        $view = '../app/Views/dashboard.php';
        require '../app/Views/layout.php';
        break;

    // 🛒 Order
    case 'order':
        $controller = new OrderController();
        $controller->store();
        break;

    // ❌ Delete Order
    case 'delete-order':
        $controller = new DeleteOrderController();
        $controller->delete();
        break;

    // 🚪 Logout
    case 'logout':
        session_destroy();
        header('Location: /mealbox/public/?url=login');
        exit();

    // ❌ 404
    default:
        echo '404 Not Found';
        break;
}
