<?php

session_start();

// ================= CONTROLLERS =================
require '../app/Controllers/HomeController.php';
require '../app/Controllers/LoginController.php';
require '../app/Controllers/MenuController.php';
require '../app/Controllers/SubscriptionController.php';
require '../app/Controllers/OrderController.php';
require '../app/Controllers/DeleteOrderController.php';
require '../app/Controllers/ProfileController.php';
require '../app/Controllers/SelectMealController.php';
require '../app/Controllers/RemoveMealController.php';

// ================= URL =================
$url = $_GET['url'] ?? 'home';

switch ($url) {
    // ================= HOME =================
    case 'home':
    case '':
        $controller = new HomeController();
        $controller->index();
        break;

    // ================= LOGIN =================
    case 'login':
        $controller = new LoginController();
        $controller->index();
        break;

    // ================= REGISTER =================
    case 'register':
        $controller = new LoginController();
        $controller->register();
        break;

    // ================= VERIFY OTP =================
    case 'verify-otp':
        $controller = new LoginController();
        $controller->verifyOTP();
        break;

    // ================= LOGOUT =================
    case 'logout':
        session_destroy();

        header('Location: /mealbox/public/');
        exit();

    // ================= MENU =================
    case 'menu':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login first';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];
        $email = $_SESSION['user'];

        // Get latest subscription
        $stmt = $pdo->prepare("
        SELECT * FROM subscriptions
        WHERE user_email = ?
        ORDER BY created_at DESC
        LIMIT 1
    ");
        $stmt->execute([$email]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            $_SESSION['error'] = 'Please choose a subscription plan first';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        // Budget calculation
        $plan = $subscription['plan'];

        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);

        $baseDaily = $subscription['price'] / $days;
        $carry = $subscription['carry_over'] ?? 0;
        $dailyLimit = $baseDaily + $carry;

        // Today used amount
        $stmt = $pdo->prepare("
        SELECT SUM(price) as total
        FROM orders
        WHERE user_id = ?
        AND DATE(created_at) = CURDATE()
    ");
        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // Meal counts
        $stmt = $pdo->prepare("
        SELECT meal_type, COUNT(*) as total
        FROM orders
        WHERE user_id = ?
        AND DATE(created_at) = CURDATE()
        GROUP BY meal_type
    ");
        $stmt->execute([$userId]);
        $countsRaw = $stmt->fetchAll();

        $mealCounts = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
        ];

        foreach ($countsRaw as $row) {
            $mealCounts[$row['meal_type']] = $row['total'];
        }

        // Get meals
        $controller = new MenuController();
        $meals = $controller->index();

        $view = '../app/Views/menu.php';
        require '../app/Views/layout.php';
        break;
    // ================= meal detail =================
    case 'meal':
        $controller = new MenuController();
        $meal = $controller->detail($_GET['id'] ?? null);

        if (!$meal) {
            echo 'Meal not found';
            exit();
        }

        $view = '../app/Views/meal.php';
        require '../app/Views/layout.php';
        break;
    // ================= SUBSCRIPTION =================
    case 'subscribe':
    case 'subscription':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login first';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        $controller = new SubscriptionController();
        $controller->index();
        break;

    // ================= SAVE SUBSCRIPTION =================
    case 'subscribe-store':
        $controller = new SubscriptionController();
        $controller->store();
        break;

    // ================= DASHBOARD =================
    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Please login first';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $email = $_SESSION['user'];
        $userId = $_SESSION['user_id'];

        // ================= GET SUBSCRIPTION =================
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions
            WHERE user_email = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");

        $stmt->execute([$email]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            $_SESSION['error'] = 'Please subscribe first';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        // ================= TODAY TOTAL =================
        $stmt = $pdo->prepare("
            SELECT SUM(price) as total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");

        $stmt->execute([$userId]);

        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // ================= DAILY LIMIT =================
        $plan = $subscription['plan'];
        $price = $subscription['price'];

        $dailyLimit =
            $plan === 'daily'
                ? $price
                : ($plan === 'weekly'
                    ? $price / 7
                    : ($plan === 'monthly'
                        ? $price / 30
                        : 999999));

        // ================= ORDERS =================
        $stmt = $pdo->prepare("
            SELECT orders.*, meals.name
            FROM orders
            JOIN meals ON orders.meal_id = meals.id
            WHERE orders.user_id = ?
            ORDER BY orders.created_at DESC
        ");

        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();

        // ================= SPLIT MEALS =================
        $breakfastMeals = [];
        $lunchMeals = [];
        $dinnerMeals = [];

        foreach ($orders as $order) {
            if ($order['meal_type'] === 'breakfast') {
                $breakfastMeals[] = $order;
            } elseif ($order['meal_type'] === 'lunch') {
                $lunchMeals[] = $order;
            } else {
                $dinnerMeals[] = $order;
            }
        }

        $view = '../app/Views/dashboard.php';
        require '../app/Views/layout.php';
        break;

    // ================= PLACE ORDER =================
    case 'place-order':
        $controller = new OrderController();
        $controller->store();
        break;

    // ================= DELETE ORDER =================
    case 'delete-order':
        $controller = new DeleteOrderController();
        $controller->delete();
        break;

    // ================= PROFILE =================
    case 'profile':
        $controller = new ProfileController();
        $controller->index();
        break;

    // ================= UPDATE PROFILE =================
    case 'profile-update':
        $controller = new ProfileController();
        $controller->update();
        break;

    // ================= CHANGE PASSWORD =================
    case 'change-password':
        $controller = new ProfileController();
        $controller->changePassword();
        break;

    // ================= UPLOAD IMAGE =================
    case 'upload-image':
        $controller = new ProfileController();
        $controller->uploadImage();
        break;

    // ================= SELECT MEAL =================
    case 'select-meal':
        $controller = new SelectMealController();
        $controller->store();
        break;

    // ================= REMOVE MEAL =================
    case 'remove-meal':
        $controller = new RemoveMealController();
        $controller->delete();
        break;

    // ================= forgot & reset pass =================
    case 'forgot-password':
        $controller = new LoginController();
        $controller->forgotPassword();
        break;

    case 'send-reset-link':
        $controller = new LoginController();
        $controller->sendResetLink();
        break;

    case 'reset-password':
        $controller = new LoginController();
        $controller->resetPassword();
        break;

    case 'update-password':
        $controller = new LoginController();
        $controller->updatePassword();
        break;

    // ================= 404 =================
    default:
        echo '<h1 style="text-align:center;margin-top:100px;">404 - Page Not Found</h1>';
        break;
}
