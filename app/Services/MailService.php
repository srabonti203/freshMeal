<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class MailService
{
    public static function sendOTP($email, $otp)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP SETTINGS
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // YOUR GMAIL
            $mail->Username = 'bithisrabontiakter@gmail.com';

            // APP PASSWORD (NO SPACES!)
            $mail->Password = 'pazugwkxqpztdvpx';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // OPTIONAL (helps avoid SSL issues on some XAMPP setups)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // EMAIL CONTENT
            $mail->setFrom('bithisrabontiakter@gmail.com', 'MealBox');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'OTP Verification - MealBox';

            $mail->Body = "
                <div style='font-family:Arial; text-align:center;'>
                    <h2>MealBox OTP Verification</h2>
                    <p>Your verification code is:</p>
                    <h1 style='color:green; letter-spacing:5px;'>$otp</h1>
                    <p>This code will expire in 5 minutes.</p>
                </div>
            ";

            $mail->AltBody = "Your OTP is: $otp";

            $mail->send();
        } catch (Exception $e) {
            // Debug log (check Apache error log if needed)
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        }
    }

    public static function sendResetPasswordLink($email, $link)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'bithisrabontiakter@gmail.com';
            $mail->Password = 'pazugwkxqpztdvpx';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom('bithisrabontiakter@gmail.com', 'MealBox');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your MealBox Password';

            $mail->Body = "
            <div style='font-family:Arial; text-align:center;'>
                <h2>Password Reset Request</h2>
                <p>Click the button below to reset your password.</p>

                <a href='$link' 
                   style='display:inline-block; padding:12px 20px; background:#16a34a; color:white; text-decoration:none; border-radius:8px;'>
                    Reset Password
                </a>

                <p>This link will expire in 15 minutes.</p>
            </div>
        ";

            $mail->AltBody = "Reset your password using this link: $link";

            $mail->send();
        } catch (Exception $e) {
            error_log('Reset Mail Error: ' . $mail->ErrorInfo);
        }
    }
}
