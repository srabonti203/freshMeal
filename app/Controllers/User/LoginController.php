<?php

class LoginController
{
    // ================= LOGIN =================
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../config/database.php';

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // VALIDATION
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: /mealbox/public/?url=login');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Invalid email format';
                header('Location: /mealbox/public/?url=login');
                exit();
            }

            // FETCH USER
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // VERIFY PASSWORD
            if ($user && password_verify($password, $user['password'])) {
                // BLOCK UNVERIFIED USERS
                if (!$user['is_verified']) {
                    $_SESSION['error'] = 'Please verify your email first';
                    header('Location: /mealbox/public/?url=login');
                    exit();
                }

                // LOGIN
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                $_SESSION['success'] = 'Login successful';

                header('Location: /mealbox/public/?url=dashboard');
                exit();
            } else {
                $_SESSION['error'] = 'Invalid Email or Password';
                header('Location: /mealbox/public/?url=login');
                exit();
            }
        }

        // VIEW
        $view = '../app/Views/user/login.php';
        require '../app/Views/layouts/layout.php';
    }

    // ================= REGISTER =================
    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../config/database.php';

            $name = trim($_POST['name']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // VALIDATION
            if (!$name || !$phone || !$address || !$email || !$password) {
                $_SESSION['error'] = 'All fields required';
                header('Location: /mealbox/public/?url=register');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Invalid email';
                header('Location: /mealbox/public/?url=register');
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters';
                header('Location: /mealbox/public/?url=register');
                exit();
            }

            // CHECK EMAIL
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: /mealbox/public/?url=register');
                exit();
            }

            // GENERATE OTP
            date_default_timezone_set('Asia/Dhaka');

            $otp = rand(100000, 999999);
            $expiry = date('Y-m-d H:i:s', time() + 300);

            // INSERT USER
            $stmt = $pdo->prepare("
                INSERT INTO users
                (name, phone, address, email, password, otp_code, otp_expires_at, is_verified)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)
            ");

            $stmt->execute([
                $name,
                $phone,
                $address,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $otp,
                $expiry,
            ]);

            // STORE EMAIL
            $_SESSION['verify_email'] = $email;

            // SEND OTP
            require '../app/Services/MailService.php';
            MailService::sendOTP($email, $otp);

            header('Location: /mealbox/public/?url=verify-otp');
            exit();
        }

        $view = '../app/Views/user/register.php';
        require '../app/Views/layouts/layout.php';
    }

    // ================= VERIFY OTP =================
    public function verifyOTP()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        date_default_timezone_set('Asia/Dhaka');

        // SHOW OTP PAGE FIRST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $view = '../app/Views/user/verify-otp.php';
            require '../app/Views/layouts/layout.php';
            return;
        }

        require '../config/database.php';

        $email = $_SESSION['verify_email'] ?? null;
        $otp = trim($_POST['otp'] ?? '');

        if (!$email) {
            header('Location: /mealbox/public/?url=register');
            exit();
        }

        if (!$otp) {
            $_SESSION['error'] = 'OTP is required';
            header('Location: /mealbox/public/?url=verify-otp');
            exit();
        }

        $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE email = ? 
        AND otp_code = ?
        AND otp_expires_at > ?
    ");

        $stmt->execute([$email, $otp, date('Y-m-d H:i:s')]);

        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = 'Invalid or expired OTP';
            header('Location: /mealbox/public/?url=verify-otp');
            exit();
        }

        $stmt = $pdo->prepare("
        UPDATE users 
        SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL
        WHERE email = ?
    ");

        $stmt->execute([$email]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];

        unset($_SESSION['verify_email']);

        header('Location: /mealbox/public/?url=subscribe');
        exit();
    }

    // ================= FORGOT PASSWORD =================
    public function forgotPassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $view = '../app/Views/user/forgot-password.php';
        require '../app/Views/layouts/layout.php';
    }

    // ================= SEND RESET LINK =================
    public function sendResetLink()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $email = trim($_POST['email'] ?? '');

        if (!$email) {
            $_SESSION['error'] = 'Email is required';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = 'No account found with this email';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 900);

        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');
        $stmt->execute([$email]);

        $stmt = $pdo->prepare("
            INSERT INTO password_resets (email, token, expires_at)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$email, $token, $expires]);

        $resetLink =
            'http://localhost/mealbox/public/?url=reset-password&token=' .
            $token;

        require '../app/Services/MailService.php';
        MailService::sendResetPasswordLink($email, $resetLink);

        $_SESSION['success'] = 'Password reset link sent to your email';

        header('Location: /mealbox/public/?url=login');
        exit();
    }

    // ================= RESET PASSWORD PAGE =================
    public function resetPassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $token = $_GET['token'] ?? '';

        if (!$token) {
            $_SESSION['error'] = 'Invalid reset link';
            header('Location: /mealbox/public/?url=login');
            exit();
        }

        $stmt = $pdo->prepare("
            SELECT * FROM password_resets
            WHERE token = ?
            AND expires_at > ?
            LIMIT 1
        ");

        $stmt->execute([$token, date('Y-m-d H:i:s')]);

        $reset = $stmt->fetch();

        if (!$reset) {
            $_SESSION['error'] = 'Reset link expired or invalid';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        $view = '../app/Views/user/reset-password.php';
        require '../app/Views/layouts/layout.php';
    }

    // ================= UPDATE PASSWORD =================
    public function updatePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require '../config/database.php';

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$token || !$password || !$confirm) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Passwords do not match';

            header(
                'Location: /mealbox/public/?url=reset-password&token=' . $token,
            );

            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';

            header(
                'Location: /mealbox/public/?url=reset-password&token=' . $token,
            );

            exit();
        }

        $stmt = $pdo->prepare("
            SELECT * FROM password_resets
            WHERE token = ?
            AND expires_at > ?
            LIMIT 1
        ");

        $stmt->execute([$token, date('Y-m-d H:i:s')]);

        $reset = $stmt->fetch();

        if (!$reset) {
            $_SESSION['error'] = 'Reset link expired or invalid';
            header('Location: /mealbox/public/?url=forgot-password');
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');

        $stmt->execute([$hashedPassword, $reset['email']]);

        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');

        $stmt->execute([$reset['email']]);

        $_SESSION['success'] = 'Password updated successfully. Please login.';

        header('Location: /mealbox/public/?url=login');
        exit();
    }
}
