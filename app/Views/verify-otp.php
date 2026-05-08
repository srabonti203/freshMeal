<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-black">

    <div class="bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md text-center border border-green-500/30">

        <h2 class="text-2xl font-bold text-green-400 mb-2">Verify Your Email</h2>
        <p class="text-gray-300 text-sm mb-6">
            Enter the 6-digit OTP sent to your email
        </p>

        <!-- ERROR MESSAGE -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-400 mb-4 text-sm">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </p>
        <?php endif; ?>

        <!-- OTP FORM -->
        <div class="min-h-screen bg-black flex items-center justify-center px-6">
    <div class="bg-gray-900 p-8 rounded-xl w-full max-w-md border border-green-500">
        <h2 class="text-2xl font-bold text-green-400 text-center mb-6">
            Verify Your Email
        </h2>

        <form method="POST" action="/mealbox/public/?url=verify-otp" class="space-y-4">
            <input 
                type="text" 
                name="otp" 
                placeholder="Enter 6 digit OTP"
                maxlength="6"
                required
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700"
            >

            <button 
                type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
                Verify OTP
            </button>
        </form>
    </div>
</div>

        <!-- RESEND (optional future) -->
        <p class="text-gray-400 text-sm mt-4">
            Didn’t receive code?
            <span class="text-green-400 cursor-pointer hover:underline">
                Resend
            </span>
        </p>

    </div>
</div>

<style>
.otp-input {
    width: 45px;
    height: 55px;
    text-align: center;
    font-size: 22px;
    border-radius: 10px;
    border: 1px solid #4b5563;
    background: #1f2937;
    color: white;
    outline: none;
}
.otp-input:focus {
    border-color: #22c55e;
    box-shadow: 0 0 5px #22c55e;
}
</style>

<script>
const inputs = document.querySelectorAll(".otp-input");
const otpField = document.getElementById("otp");

inputs.forEach((input, index) => {

    // Move forward
    input.addEventListener("input", () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateOTP();
    });

    // Move backward
    input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });

    // Paste support
    input.addEventListener("paste", (e) => {
        let pasteData = e.clipboardData.getData("text").trim();
        if (pasteData.length === 6) {
            inputs.forEach((box, i) => {
                box.value = pasteData[i];
            });
            updateOTP();
        }
        e.preventDefault();
    });
});

function updateOTP() {
    let otp = "";
    inputs.forEach(input => otp += input.value);
    otpField.value = otp;
}
</script>