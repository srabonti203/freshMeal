<?php

session_start();
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// ================= CONTROLLERS =================
require '../app/Controllers/HomeController.php';

require '../app/Controllers/User/LoginController.php';
require '../app/Controllers/User/MenuController.php';
require '../app/Controllers/User/SubscriptionController.php';
require '../app/Controllers/User/OrderController.php';
require '../app/Controllers/User/DeleteOrderController.php';
require '../app/Controllers/User/ProfileController.php';
require '../app/Controllers/User/SelectMealController.php';
require '../app/Controllers/User/RemoveMealController.php';

require '../app/Controllers/Admin/AdminLoginController.php';

// ================= URL =================
$url = $_GET['url'] ?? 'home';

switch ($url) {
    // ================= HOME =================
    case 'home':
    case '':
        $controller = new HomeController();
        $controller->index();
        break;

    // ================= LOGIN CHOICE =================
    case 'login-choice':
        $view = '../app/Views/user/login-choice.php';
        require '../app/Views/layouts/layout.php';
        break;

    // ================= USER LOGIN =================
    case 'login':
        $controller = new LoginController();
        $controller->index();
        break;

    // ================= ADMIN LOGIN =================
    case 'admin-login':
        $controller = new AdminLoginController();
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

    // ================= ADMIN LOGOUT =================
    case 'admin-logout':
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['role']);

        header('Location: /mealbox/public/?url=admin-login');
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

        $plan = $subscription['plan'];
        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);

        $baseDaily = $subscription['price'] / $days;
        $carry = $subscription['carry_over'] ?? 0;
        $dailyLimit = $baseDaily + $carry;

        $stmt = $pdo->prepare("
            SELECT SUM(price) as total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

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

        $controller = new MenuController();
        $meals = $controller->index();

        $view = '../app/Views/user/menu.php';
        require '../app/Views/layouts/layout.php';
        break;

    // ================= MEAL DETAIL =================
    case 'meal':
        $controller = new MenuController();
        $meal = $controller->detail($_GET['id'] ?? null);

        if (!$meal) {
            echo 'Meal not found';
            exit();
        }

        $view = '../app/Views/user/meal.php';
        require '../app/Views/layouts/layout.php';
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

        $stmt = $pdo->prepare("
            SELECT SUM(price) as total
            FROM orders
            WHERE user_id = ?
            AND DATE(created_at) = CURDATE()
        ");

        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

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

        $stmt = $pdo->prepare("
            SELECT orders.*, meals.name
            FROM orders
            JOIN meals ON orders.meal_id = meals.id
            WHERE orders.user_id = ?
            ORDER BY orders.created_at DESC
        ");

        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();

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

        $view = '../app/Views/user/dashboard.php';
        require '../app/Views/layouts/layout.php';
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

    // ================= FORGOT & RESET PASSWORD =================
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

    // ================= ADMIN DASHBOARD =================
    case 'admin-dashboard':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $view = '../app/Views/admin/dashboard.php';
        require '../app/Views/layouts/layout.php';
        break;

    // ================= 404 =================
    default:
        echo '<h1 style="text-align:center;margin-top:100px;">404 - Page Not Found</h1>';
        break;
}
