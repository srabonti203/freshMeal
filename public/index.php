<?php

session_start();
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createMutable(dirname(__DIR__));
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
require '../app/Controllers/User/ReviewController.php';

require '../app/Controllers/Admin/AdminLoginController.php';
require '../app/Controllers/Admin/AdminMealController.php';
require '../app/Controllers/Admin/AdminUserController.php';
require '../app/Controllers/Admin/AdminOrderController.php';
require '../app/Controllers/Admin/AdminReviewController.php';
require '../app/Controllers/Admin/SubscriptionPlanController.php';

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

        $stmt = $pdo->prepare("
        SELECT 
            subscriptions.*,
            subscription_plans.duration_days,
            subscription_plans.meal_limit,
            subscription_plans.name AS plan_name
        FROM subscriptions
        LEFT JOIN subscription_plans 
            ON subscriptions.plan_id = subscription_plans.id
        WHERE subscriptions.user_id = ?
        ORDER BY subscriptions.created_at DESC
        LIMIT 1
    ");

        $stmt->execute([$userId]);
        $subscription = $stmt->fetch();

        // AUTO EXPIRE SUBSCRIPTION
        if (
            $subscription &&
            $subscription['status'] === 'active' &&
            !empty($subscription['expiry_date']) &&
            strtotime($subscription['expiry_date']) < strtotime(date('Y-m-d'))
        ) {
            $expireStmt = $pdo->prepare("
            UPDATE subscriptions
            SET status = 'expired'
            WHERE id = ?
        ");

            $expireStmt->execute([$subscription['id']]);

            $_SESSION['error'] = 'Your subscription has expired';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        if (!$subscription || $subscription['status'] !== 'active') {
            $_SESSION['error'] = 'Please choose a subscription plan first';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        $days = (int) ($subscription['duration_days'] ?? 1);

        if ($days <= 0) {
            $days = 1;
        }

        $baseDaily = (float) $subscription['price'] / $days;
        $carry = (float) ($subscription['carry_over'] ?? 0);
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
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login first';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
        SELECT 
            subscriptions.*,
            subscription_plans.name AS plan_name,
            subscription_plans.duration_days,
            subscription_plans.meal_limit

        FROM subscriptions

        LEFT JOIN subscription_plans 
            ON subscriptions.plan_id = subscription_plans.id

        WHERE subscriptions.user_id = ?
        ORDER BY subscriptions.created_at DESC
        LIMIT 1
    ");

        $stmt->execute([$userId]);
        $subscription = $stmt->fetch();

        // AUTO EXPIRE SUBSCRIPTION
        if (
            $subscription &&
            $subscription['status'] === 'active' &&
            !empty($subscription['expiry_date']) &&
            strtotime($subscription['expiry_date']) < strtotime(date('Y-m-d'))
        ) {
            $expireStmt = $pdo->prepare("
            UPDATE subscriptions
            SET status = 'expired'
            WHERE id = ?
        ");

            $expireStmt->execute([$subscription['id']]);

            $_SESSION['error'] = 'Your subscription has expired';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        if (!$subscription || $subscription['status'] !== 'active') {
            $_SESSION['error'] = 'Please subscribe first';
            header('Location: /mealbox/public/?url=subscribe');
            exit();
        }

        $daysTotal = (int) ($subscription['duration_days'] ?? 1);
        $price = (float) ($subscription['price'] ?? 0);

        $dailyLimit = $daysTotal > 0 ? $price / $daysTotal : $price;

        $stmt = $pdo->prepare("
        SELECT SUM(price) as total
        FROM orders
        WHERE user_id = ?
        AND DATE(created_at) = CURDATE()
    ");

        $stmt->execute([$userId]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        $stmt = $pdo->prepare("
        SELECT 
            orders.*, 
            meals.name,

            (
                SELECT COUNT(*) 
                FROM meal_reviews 
                WHERE meal_reviews.order_id = orders.id
            ) AS review_count

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

    case 'meal-review-store':
        $controller = new ReviewController();
        $controller->store();
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

        require '../config/database.php';

        // Total Users
        $stmt = $pdo->query('SELECT COUNT(*) FROM users');
        $totalUsers = $stmt->fetchColumn();

        // Total Orders
        $stmt = $pdo->query('SELECT COUNT(*) FROM orders');
        $totalOrders = $stmt->fetchColumn();

        // Total Revenue
        $stmt = $pdo->query('SELECT SUM(price) FROM subscriptions');
        $totalRevenue = $stmt->fetchColumn() ?? 0;

        // Total Meals
        $stmt = $pdo->query('SELECT COUNT(*) FROM meals');
        $totalMeals = $stmt->fetchColumn();

        // Today's Orders
        $stmt = $pdo->query(
            'SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()',
        );
        $todayOrders = $stmt->fetchColumn();

        // Active Subscriptions
        $stmt = $pdo->query('SELECT COUNT(*) FROM subscriptions');
        $activeSubscriptions = $stmt->fetchColumn();

        // Suspended Subscriptions
        // You do not have status column yet, so keep it 0 for now
        $suspendedSubscriptions = 0;

        // New Users Today
        $stmt = $pdo->query(
            'SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()',
        );
        $newUsersToday = $stmt->fetchColumn();

        $view = '../app/Views/admin/admin-dashboard.php';
        require '../app/Views/layouts/admin-layout.php';
        break;

    // ================= admin meal controller =================
    case 'admin-meals':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';

            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminMealController();
        $controller->index();
        break;
    // ================= admin meal store =================
    case 'admin-meal-store':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminMealController();
        $controller->store();
        break;
    // ================= EDIT MEAL =================
    case 'admin-meal-edit':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminMealController();
        $controller->edit();
        break;

    case 'admin-meal-update':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminMealController();
        $controller->update();
        break;

    case 'admin-meal-delete':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminMealController();
        $controller->delete();
        break;
    // ================= manage user =================
    case 'admin-users':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminUserController();
        $controller->index();
        break;

    case 'admin-user-suspend':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminUserController();
        $controller->suspend();
        break;

    case 'admin-user-activate':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminUserController();
        $controller->activate();
        break;
    // ================= manage order =================
    case 'admin-orders':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminOrderController();
        $controller->index();
        break;
    // ================= manage order delivery =================
    case 'admin-order-deliver':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminOrderController();
        $controller->deliver();
        break;
    // ================= manage review =================
    case 'admin-reviews':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new AdminReviewController();
        $controller->index();
        break;
    // ================= SUBSCRIPTION PLANS =================
    case 'admin-subscription-plans':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->index();
        break;

    // ================= CREATE PLAN =================
    case 'admin-subscription-plan-create':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->create();
        break;

    // ================= STORE PLAN =================
    case 'admin-subscription-plan-store':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->store();
        break;

    // ================= EDIT PLAN =================
    case 'admin-subscription-plan-edit':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->edit();
        break;

    // ================= UPDATE PLAN =================
    case 'admin-subscription-plan-update':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->update();
        break;

    // ================= TOGGLE PLAN STATUS =================
    case 'admin-subscription-plan-toggle':
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        $controller = new SubscriptionPlanController();
        $controller->toggleStatus();
        break;
    // ================= 404 =================
    default:
        echo '<h1 style="text-align:center;margin-top:100px;">404 - Page Not Found</h1>';
        break;
}
