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
require_once '../app/Controllers/SelectMealController.php';
require_once '../app/Controllers/RemoveMealController.php';
require_once '../app/Controllers/ProfileController.php';
require_once '../app/Services/CarryOverService.php';

$url = $_GET['url'] ?? '/';

switch ($url) {
    // ================= HOME =================
    case '/':
        $view = '../app/Views/home.php';
        require '../app/Views/layout.php';
        break;

    // ================= MENU =================
    case 'menu':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];

        // Subscription
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            header('Location: /mealbox/public/?url=subscription');
            exit();
        }

        // 🔥 EXPIRY CHECK
        $plan = $subscription['plan'];
        $created = strtotime($subscription['created_at']);

        $daysTotal = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);
        $daysUsed = floor((time() - $created) / (60 * 60 * 24));
        $daysLeft = max($daysTotal - $daysUsed, 0);

        if ($daysLeft <= 0) {
            $stmt = $pdo->prepare(
                "UPDATE subscriptions SET status='expired' WHERE id=?",
            );
            $stmt->execute([$subscription['id']]);

            header('Location: /mealbox/public/?url=subscription&expired=1');
            exit();
        }

        // 🔥 RUN CARRY OVER
        CarryOverService::process($pdo, $subscription, $user);

        // 🔥 REFETCH updated subscription
        $stmt = $pdo->prepare('SELECT * FROM subscriptions WHERE id=?');
        $stmt->execute([$subscription['id']]);
        $subscription = $stmt->fetch();

        // 🔥 BUDGET LOGIC
        $totalBudget = $subscription['price'];
        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);

        $baseDaily = $totalBudget / $days;
        $carry = $subscription['carry_over'] ?? 0;

        $dailyLimit = $baseDaily + $carry;

        // 🔥 TODAY TOTAL
        $stmt = $pdo->prepare("
            SELECT SUM(meals.price) as total
            FROM meal_selections
            JOIN meals ON meal_selections.meal_id = meals.id
            WHERE meal_selections.user_email = ?
            AND DATE(meal_selections.created_at) = CURDATE()
        ");
        $stmt->execute([$user]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // 🔥 MEAL COUNTS
        $stmt = $pdo->prepare("
            SELECT meal_type, COUNT(*) as total
            FROM meal_selections
            WHERE user_email = ?
            GROUP BY meal_type
        ");
        $stmt->execute([$user]);
        $countsRaw = $stmt->fetchAll();

        $mealCounts = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
        ];

        foreach ($countsRaw as $row) {
            $mealCounts[$row['meal_type']] = $row['total'];
        }

        // Meals
        $controller = new MenuController();
        $meals = $controller->index();

        $view = '../app/Views/menu.php';
        require '../app/Views/layout.php';
        break;

    // ================= SINGLE MEAL =================
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

    // ================= ACTIONS =================
    case 'select-meal':
        $controller = new SelectMealController();
        $controller->store();
        break;

    case 'remove-meal':
        $controller = new RemoveMealController();
        $controller->delete();
        break;

    case 'order':
        $controller = new OrderController();
        $controller->store();
        break;

    case 'delete-order':
        $controller = new DeleteOrderController();
        $controller->delete();
        break;

    // ================= AUTH =================
    case 'login':
        $controller = new LoginController();
        $controller->index();
        break;

    case 'register':
        $controller = new LoginController();
        $controller->register();
        break;

    case 'profile':
        $controller = new ProfileController();
        $controller->index();
        break;

    case 'profile-update':
        $controller = new ProfileController();
        $controller->update();
        break;

    case 'change-password':
        $controller = new ProfileController();
        $controller->changePassword();
        break;

    case 'upload-image':
        $controller = new ProfileController();
        $controller->uploadImage();
        break;

    case 'logout':
        session_destroy();
        header('Location: /mealbox/public/?url=login');
        exit();

    // ================= SUBSCRIPTION =================
    case 'subscription':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        $view = '../app/Views/subscription.php';
        require '../app/Views/layout.php';
        break;

    case 'subscribe-store':
        $controller = new SubscriptionController();
        $controller->store();
        break;

    // ================= DASHBOARD =================
    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        require '../config/database.php';

        $user = $_SESSION['user'];

        // Subscription
        $stmt = $pdo->prepare("
            SELECT * FROM subscriptions 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user]);
        $subscription = $stmt->fetch();

        if (!$subscription) {
            header('Location: /mealbox/public/?url=subscription');
            exit();
        }

        // 🔥 EXPIRY CHECK
        $plan = $subscription['plan'];
        $created = strtotime($subscription['created_at']);

        $daysTotal = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);
        $daysUsed = floor((time() - $created) / (60 * 60 * 24));
        $daysLeft = max($daysTotal - $daysUsed, 0);

        if ($daysLeft <= 0) {
            $stmt = $pdo->prepare(
                "UPDATE subscriptions SET status='expired' WHERE id=?",
            );
            $stmt->execute([$subscription['id']]);

            header('Location: /mealbox/public/?url=subscription&expired=1');
            exit();
        }

        // 🔥 RUN CARRY
        CarryOverService::process($pdo, $subscription, $user);

        // 🔥 REFETCH
        $stmt = $pdo->prepare('SELECT * FROM subscriptions WHERE id=?');
        $stmt->execute([$subscription['id']]);
        $subscription = $stmt->fetch();

        // 🔥 BUDGET
        $totalBudget = $subscription['price'];
        $days = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);

        $baseDaily = $totalBudget / $days;
        $carry = $subscription['carry_over'] ?? 0;

        $dailyLimit = $baseDaily + $carry;

        // 🔥 TODAY TOTAL
        $stmt = $pdo->prepare("
            SELECT SUM(meals.price) as total
            FROM meal_selections
            JOIN meals ON meal_selections.meal_id = meals.id
            WHERE meal_selections.user_email = ?
            AND DATE(meal_selections.created_at) = CURDATE()
        ");
        $stmt->execute([$user]);
        $todayTotal = $stmt->fetch()['total'] ?? 0;

        // 🔥 SELECTED MEALS
        $stmt = $pdo->prepare("
            SELECT meal_selections.id, meals.name, meals.image, meal_selections.meal_type
            FROM meal_selections
            JOIN meals ON meal_selections.meal_id = meals.id
            WHERE meal_selections.user_email = ?
        ");
        $stmt->execute([$user]);
        $selectedMeals = $stmt->fetchAll();

        $breakfastMeals = [];
        $lunchMeals = [];
        $dinnerMeals = [];

        foreach ($selectedMeals as $meal) {
            if ($meal['meal_type'] === 'breakfast') {
                $breakfastMeals[] = $meal;
            } elseif ($meal['meal_type'] === 'lunch') {
                $lunchMeals[] = $meal;
            } else {
                $dinnerMeals[] = $meal;
            }
        }

        $view = '../app/Views/dashboard.php';
        require '../app/Views/layout.php';
        break;
}
