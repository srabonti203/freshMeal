<?php

class LoginController
{
    //LOGIN
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../config/database.php';

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: /mealbox/public/?url=login');
                exit();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Invalid email format';
                header('Location: /mealbox/public/?url=login');
                exit();
            } else {
                // Fetch user
                $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // Verify password
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user['email'];

                    //Success toast
                    $_SESSION['success'] = 'Login successful';
                    header('Location: /mealbox/public/?url=dashboard');
                    exit();
                } else {
                    //Error toast
                    $_SESSION['error'] = 'Invalid Email or Password';
                    header('Location: /mealbox/public/?url=login');
                    exit();
                }
            }
        }

        //ALWAYS LOAD VIA LAYOUT
        $view = '../app/Views/login.php';
        require '../app/Views/layout.php';
    }

    //REGISTER
    public function register()
    {
        // session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../config/database.php';

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: /mealbox/public/?url=register');
                exit();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Invalid email format';
                header('Location: /mealbox/public/?url=register');
                exit();
            } elseif (strlen($password) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters';
                header('Location: /mealbox/public/?url=register');
                exit();
            } else {
                // Check if email exists
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
                $stmt->execute([$email]);

                if ($stmt->fetch()) {
                    $_SESSION['error'] = 'Email already exists';
                    header('Location: /mealbox/public/?url=register');
                    exit();
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                    // Insert user
                    $stmt = $pdo->prepare(
                        'INSERT INTO users (email, password) VALUES (?, ?)',
                    );
                    $stmt->execute([$email, $hashedPassword]);

                    //Auto login
                    $_SESSION['user'] = $email;

                    //Success toast
                    $_SESSION['success'] = 'Account created successfully 🎉';
                    header('Location: /mealbox/public/?url=dashboard');
                    exit();
                }
            }
        }

        //ALWAYS LOAD VIA LAYOUT
        $view = '../app/Views/register.php';
        require '../app/Views/layout.php';
    }
}
