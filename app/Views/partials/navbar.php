<nav class="bg-black bg-opacity-50 px-8 py-4 flex justify-between items-center fixed w-full top-0 z-50">

    <a href="/mealbox/public/" class="text-2xl font-extrabold text-green-400">
        FreshMeal
    </a>

    <div class="space-x-6 flex items-center">

        <a href="/mealbox/public/?url=menu" 
           class="text-gray-200 hover:text-green-400 font-medium transition">
            Menu
        </a>

        <?php if (isset($_SESSION['user'])): ?>

            <a href="/mealbox/public/?url=dashboard" 
               class="text-gray-200 hover:text-green-400 font-medium transition">
                Dashboard
            </a>

            <a href="/mealbox/public/?url=logout" 
               class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition shadow">
                Logout
            </a>

        <?php else: ?>

            <a href="/mealbox/public/?url=login" 
               class="text-gray-200 hover:text-green-400 font-medium transition">
                Login
            </a>

            <a href="/mealbox/public/?url=register" 
               class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow">
                Get Started
            </a>

        <?php endif; ?>

    </div>
</nav>
