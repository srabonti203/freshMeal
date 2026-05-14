<?php

class SubscriptionPlanController
{
    private function adminGuard()
    {
        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }
    }

    private function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }

    public function index()
    {
        $this->adminGuard();
        require '../config/database.php';

        $stmt = $pdo->query("
            SELECT 
                sp.*,
                COUNT(pm.meal_id) AS total_meals
            FROM subscription_plans sp
            LEFT JOIN plan_meals pm ON sp.id = pm.plan_id
            GROUP BY sp.id
            ORDER BY sp.created_at DESC
        ");

        $plans = $stmt->fetchAll();

        $view = '../app/Views/admin/subscriptions/index.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function create()
    {
        $this->adminGuard();
        require '../config/database.php';

        $stmt = $pdo->query("
            SELECT id, name, type, price 
            FROM meals 
            WHERE status = 'active'
            ORDER BY type, name
        ");

        $meals = $stmt->fetchAll();

        $view = '../app/Views/admin/subscriptions/create.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function store()
    {
        $this->adminGuard();
        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $durationDays = trim($_POST['duration_days'] ?? '');
        $mealLimit = trim($_POST['meal_limit'] ?? 0);
        $mealIds = $_POST['meal_ids'] ?? [];

        if ($name === '' || $price === '' || $durationDays === '') {
            $_SESSION['error'] = 'Plan name, price, and duration are required';
            header(
                'Location: /mealbox/public/?url=admin-subscription-plan-create',
            );
            exit();
        }

        if (!is_numeric($price) || $price < 0) {
            $_SESSION['error'] = 'Price must be a valid number';
            header(
                'Location: /mealbox/public/?url=admin-subscription-plan-create',
            );
            exit();
        }

        if (
            !filter_var($durationDays, FILTER_VALIDATE_INT) ||
            $durationDays <= 0
        ) {
            $_SESSION['error'] = 'Duration must be a positive number';
            header(
                'Location: /mealbox/public/?url=admin-subscription-plan-create',
            );
            exit();
        }

        if (!filter_var($mealLimit, FILTER_VALIDATE_INT) || $mealLimit < 0) {
            $_SESSION['error'] = 'Meal limit must be zero or a positive number';
            header(
                'Location: /mealbox/public/?url=admin-subscription-plan-create',
            );
            exit();
        }

        $slug = $this->slugify($name);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO subscription_plans 
                (name, slug, price, duration_days, meal_limit, status)
                VALUES (?, ?, ?, ?, ?, 'active')
            ");

            $stmt->execute([$name, $slug, $price, $durationDays, $mealLimit]);

            $planId = $pdo->lastInsertId();

            if (!empty($mealIds)) {
                $mealStmt = $pdo->prepare("
                    INSERT INTO plan_meals (plan_id, meal_id)
                    VALUES (?, ?)
                ");

                foreach ($mealIds as $mealId) {
                    if (filter_var($mealId, FILTER_VALIDATE_INT)) {
                        $mealStmt->execute([$planId, $mealId]);
                    }
                }
            }

            $pdo->commit();

            $_SESSION['success'] = 'Subscription plan created successfully';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();

            $_SESSION['error'] =
                'Plan name may already exist. Please use another name.';
            header(
                'Location: /mealbox/public/?url=admin-subscription-plan-create',
            );
            exit();
        }
    }

    public function edit()
    {
        $this->adminGuard();
        require '../config/database.php';

        $id = $_GET['id'] ?? null;

        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = 'Invalid plan ID';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $stmt = $pdo->prepare('SELECT * FROM subscription_plans WHERE id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $_SESSION['error'] = 'Subscription plan not found';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $stmt = $pdo->query("
            SELECT id, name, meal_type, price 
            FROM meals 
            WHERE status = 'active'
            ORDER BY meal_type, name
        ");
        $meals = $stmt->fetchAll();

        $stmt = $pdo->prepare("
            SELECT meal_id 
            FROM plan_meals 
            WHERE plan_id = ?
        ");
        $stmt->execute([$id]);
        $selectedMeals = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $view = '../app/Views/admin/subscriptions/edit.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function update()
    {
        $this->adminGuard();
        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $id = $_GET['id'] ?? null;

        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = 'Invalid plan ID';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $durationDays = trim($_POST['duration_days'] ?? '');
        $mealLimit = trim($_POST['meal_limit'] ?? 0);
        $mealIds = $_POST['meal_ids'] ?? [];

        if ($name === '' || $price === '' || $durationDays === '') {
            $_SESSION['error'] = 'Plan name, price, and duration are required';
            header(
                "Location: /mealbox/public/?url=admin-subscription-plan-edit&id=$id",
            );
            exit();
        }

        if (!is_numeric($price) || $price < 0) {
            $_SESSION['error'] = 'Price must be a valid number';
            header(
                "Location: /mealbox/public/?url=admin-subscription-plan-edit&id=$id",
            );
            exit();
        }

        if (
            !filter_var($durationDays, FILTER_VALIDATE_INT) ||
            $durationDays <= 0
        ) {
            $_SESSION['error'] = 'Duration must be a positive number';
            header(
                "Location: /mealbox/public/?url=admin-subscription-plan-edit&id=$id",
            );
            exit();
        }

        if (!filter_var($mealLimit, FILTER_VALIDATE_INT) || $mealLimit < 0) {
            $_SESSION['error'] = 'Meal limit must be zero or a positive number';
            header(
                "Location: /mealbox/public/?url=admin-subscription-plan-edit&id=$id",
            );
            exit();
        }

        $slug = $this->slugify($name);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                UPDATE subscription_plans
                SET name = ?, slug = ?, price = ?, duration_days = ?, meal_limit = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $slug,
                $price,
                $durationDays,
                $mealLimit,
                $id,
            ]);

            $deleteStmt = $pdo->prepare(
                'DELETE FROM plan_meals WHERE plan_id = ?',
            );
            $deleteStmt->execute([$id]);

            if (!empty($mealIds)) {
                $mealStmt = $pdo->prepare("
                    INSERT INTO plan_meals (plan_id, meal_id)
                    VALUES (?, ?)
                ");

                foreach ($mealIds as $mealId) {
                    if (filter_var($mealId, FILTER_VALIDATE_INT)) {
                        $mealStmt->execute([$id, $mealId]);
                    }
                }
            }

            $pdo->commit();

            $_SESSION['success'] = 'Subscription plan updated successfully';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();

            $_SESSION['error'] =
                'Plan name may already exist. Please use another name.';
            header(
                "Location: /mealbox/public/?url=admin-subscription-plan-edit&id=$id",
            );
            exit();
        }
    }

    public function toggleStatus()
    {
        $this->adminGuard();
        require '../config/database.php';

        $id = $_GET['id'] ?? null;

        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = 'Invalid plan ID';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $stmt = $pdo->prepare(
            'SELECT status FROM subscription_plans WHERE id = ?',
        );
        $stmt->execute([$id]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $_SESSION['error'] = 'Subscription plan not found';
            header('Location: /mealbox/public/?url=admin-subscription-plans');
            exit();
        }

        $newStatus = $plan['status'] === 'active' ? 'inactive' : 'active';

        $stmt = $pdo->prepare("
            UPDATE subscription_plans
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$newStatus, $id]);

        $_SESSION['success'] = 'Subscription plan status updated';
        header('Location: /mealbox/public/?url=admin-subscription-plans');
        exit();
    }
}
