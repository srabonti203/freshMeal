<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>

<div class="min-h-screen bg-black flex items-center justify-center px-6">

    <div class="bg-gray-900 p-8 rounded-xl w-full max-w-md border border-green-500 shadow-2xl">

        <h2 class="text-2xl font-bold text-green-400 text-center mb-2">
            Verify Your Email
        </h2>

        <p class="text-gray-300 text-center text-sm mb-6">
            Enter the 6-digit OTP sent to your email
        </p>

        <!-- ERROR -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-400 text-center mb-4 text-sm">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </p>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST" action="/mealbox/public/?url=verify-otp" class="space-y-4">

            <input 
                type="text"
                name="otp"
                placeholder="Enter 6 digit OTP"
                maxlength="6"
                required
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 focus:ring-2 focus:ring-green-400 outline-none"
            >

            <button 
                type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                Verify OTP
            </button>

        </form>

        <p class="text-gray-400 text-sm text-center mt-4">
            Didn’t receive code?
            <span class="text-green-400 hover:underline cursor-pointer">
                Resend
            </span>
        </p>

    </div>

</div>