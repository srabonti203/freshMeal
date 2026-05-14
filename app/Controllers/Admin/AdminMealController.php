<?php

class AdminMealController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $stmt = $pdo->query("
            SELECT *
            FROM meals
            ORDER BY id DESC
        ");

        $meals = $stmt->fetchAll();

        $view = '../app/Views/admin/admin-meals.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';
        $status = $_POST['status'] ?? 'active';

        if (!$name || !$description || !$price) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['error'] = 'Price must be a valid number';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $imageName = 'default.jpg';

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {
            $uploadDir = '../public/assets/images/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
            $ext = strtolower(
                pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION),
            );

            if (!in_array($ext, $allowedTypes)) {
                $_SESSION['error'] =
                    'Only JPG, PNG, WEBP, and AVIF images are allowed';
                header('Location: /mealbox/public/?url=admin-meals');
                exit();
            }

            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $targetPath = $uploadDir . $imageName;

            if (
                !move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)
            ) {
                $_SESSION['error'] = 'Image upload failed';
                header('Location: /mealbox/public/?url=admin-meals');
                exit();
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO meals (name, description, price, image, status)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $price, $imageName, $status]);

        $_SESSION['success'] = 'Meal added successfully';
        header('Location: /mealbox/public/?url=admin-meals');
        exit();
    }

    public function edit()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'Invalid meal ID';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $stmt = $pdo->prepare('SELECT * FROM meals WHERE id = ?');
        $stmt->execute([$id]);
        $meal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$meal) {
            $_SESSION['error'] = 'Meal not found';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $view = '../app/Views/admin/edit-meal.php';
        require '../app/Views/layouts/admin-layout.php';
    }

    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (
            !$id ||
            !is_numeric($id) ||
            empty($name) ||
            empty($description) ||
            empty($price)
        ) {
            $_SESSION['error'] = 'All fields are required.';
            header('Location: /mealbox/public/?url=admin-meal-edit&id=' . $id);
            exit();
        }

        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['error'] = 'Price must be a valid number.';
            header('Location: /mealbox/public/?url=admin-meal-edit&id=' . $id);
            exit();
        }

        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $stmt = $pdo->prepare('SELECT image FROM meals WHERE id = ?');
        $stmt->execute([$id]);
        $oldMeal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$oldMeal) {
            $_SESSION['error'] = 'Meal not found';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $imageName = $oldMeal['image'];

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
            $imageFile = $_FILES['image'];
            $extension = strtolower(
                pathinfo($imageFile['name'], PATHINFO_EXTENSION),
            );

            if (!in_array($extension, $allowedExtensions)) {
                $_SESSION['error'] =
                    'Only JPG, JPEG, PNG, WEBP, and AVIF images are allowed.';
                header(
                    'Location: /mealbox/public/?url=admin-meal-edit&id=' . $id,
                );
                exit();
            }

            $uploadDir = '../public/assets/images/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = $uploadDir . $imageName;

            if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
                $_SESSION['error'] = 'Image upload failed.';
                header(
                    'Location: /mealbox/public/?url=admin-meal-edit&id=' . $id,
                );
                exit();
            }

            if (
                !empty($oldMeal['image']) &&
                $oldMeal['image'] !== 'default.jpg'
            ) {
                $oldImagePath = '../public/assets/images/' . $oldMeal['image'];

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        $stmt = $pdo->prepare("
            UPDATE meals 
            SET name = ?, description = ?, price = ?, image = ?, status = ?
            WHERE id = ?
        ");

        $stmt->execute([$name, $description, $price, $imageName, $status, $id]);

        $_SESSION['success'] = 'Meal updated successfully.';
        header('Location: /mealbox/public/?url=admin-meals');
        exit();
    }

    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION['admin_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            $_SESSION['error'] = 'Admin login required';
            header('Location: /mealbox/public/?url=admin-login');
            exit();
        }

        require '../config/database.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'Invalid meal ID';
            header('Location: /mealbox/public/?url=admin-meals');
            exit();
        }

        $stmt = $pdo->prepare("
        UPDATE meals
        SET status = 'inactive'
        WHERE id = ?
    ");

        $stmt->execute([$id]);

        $_SESSION['success'] = 'Meal deleted successfully.';
        header('Location: /mealbox/public/?url=admin-meals');
        exit();
    }
}
