<div class="min-h-screen bg-cover bg-center"
     style="background-image: url('/mealbox/public/assets/images/login-reg bg.avif');">

    <div class="bg-black bg-opacity-60 min-h-screen flex items-center justify-center">

        <div class="container mx-auto px-6 flex justify-center lg:justify-end">
            <div class="bg-gray-900 bg-opacity-85 backdrop-blur-md p-10 rounded-3xl shadow-2xl w-full max-w-md border border-green-500/30">

                <h2 class="text-3xl font-extrabold text-center text-green-400 mb-2 tracking-wide">
                    Create Account
                </h2>

                <p class="text-center text-gray-300 mb-6 text-sm">
                    Start your healthy journey today
                </p>

                <?php if (isset($error)): ?>
                    <p class="text-red-400 text-center mb-4 text-sm font-medium">
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="/mealbox/public/?url=register" class="space-y-4">

                    <input type="email" name="email" placeholder="Email" required
                        class="w-full px-4 py-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-200 focus:ring-2 focus:ring-green-400 outline-none transition">

                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-200 focus:ring-2 focus:ring-green-400 outline-none transition">

                    <button class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 rounded-lg font-semibold shadow-lg hover:from-green-700 hover:to-green-600 transition">
                        Register
                    </button>
                </form>

                <p class="text-center mt-6 text-sm text-gray-300">
                    Already have an account?
                    <a href="/mealbox/public/?url=login" class="text-green-400 font-semibold hover:underline">
                        Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
