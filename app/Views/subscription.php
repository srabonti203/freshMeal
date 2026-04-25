<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6">

    <h2 class="text-3xl text-white font-bold text-center mb-12">
        Choose Your Plan
    </h2>

    <!--Updated Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">

        <!-- Daily -->
        <div class="bg-gray-900 p-6 rounded-xl text-center border border-gray-700 hover:scale-105 transition">
            <h3 class="text-xl text-white font-bold mb-4">Daily</h3>
            <p class="text-green-400 text-2xl mb-4">TK 99</p>

            <form method="POST" action="/mealbox/public/?url=subscribe-store">
                <input type="hidden" name="plan" value="daily">
                <input type="hidden" name="price" value="99">

                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Subscribe
                </button>
            </form>
        </div>

        <!-- Weekly -->
        <div class="bg-gray-900 p-6 rounded-xl text-center border border-gray-700 hover:scale-105 transition">
            <h3 class="text-xl text-white font-bold mb-4">Weekly</h3>
            <p class="text-green-400 text-2xl mb-4">TK 499</p>

            <form method="POST" action="/mealbox/public/?url=subscribe-store">
                <input type="hidden" name="plan" value="weekly">
                <input type="hidden" name="price" value="499">

                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Subscribe
                </button>
            </form>
        </div>

        <!-- Monthly (Highlighted) -->
        <div class="bg-gray-900 p-6 rounded-xl text-center border border-green-500 scale-105 shadow-lg shadow-green-500/20">
            <h3 class="text-xl text-white font-bold mb-4">Monthly</h3>
            <p class="text-green-400 text-2xl mb-4">TK 1799</p>

            <form method="POST" action="/mealbox/public/?url=subscribe-store">
                <input type="hidden" name="plan" value="monthly">
                <input type="hidden" name="price" value="1799">

                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Subscribe
                </button>
            </form>
        </div>

        <!-- Premium -->
        <div class="bg-gray-900 p-6 rounded-xl text-center border border-gray-700 hover:scale-105 transition">
            <h3 class="text-xl text-white font-bold mb-4">Premium</h3>
            <p class="text-green-400 text-2xl mb-4">TK 2999</p>

            <form method="POST" action="/mealbox/public/?url=subscribe-store">
                <input type="hidden" name="plan" value="premium">
                <input type="hidden" name="price" value="2999">

                <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Subscribe
                </button>
            </form>
        </div>

    </div>

</div>