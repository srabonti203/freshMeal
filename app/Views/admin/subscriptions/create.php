<div class="mb-8">

    <h1 class="text-3xl font-bold text-white mb-2">
        Create Subscription Plan
    </h1>

</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 max-w-5xl">

    <form action="/mealbox/public/?url=admin-subscription-plan-store"
          method="POST"
          class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block text-gray-300 mb-2">
                    Plan Name
                </label>

                <input type="text"
                       name="name"
                       required
                       class="w-full bg-black border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div>
                <label class="block text-gray-300 mb-2">
                    Price
                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="price"
                       required
                       class="w-full bg-black border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div>
                <label class="block text-gray-300 mb-2">
                    Duration (Days)
                </label>

                <input type="number"
                       min="1"
                       name="duration_days"
                       required
                       class="w-full bg-black border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div>
                <label class="block text-gray-300 mb-2">
                    Meal Limit
                </label>

                <input type="number"
                       min="0"
                       name="meal_limit"
                       value="0"
                       required
                       class="w-full bg-black border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500">
            </div>

        </div>

        <div>

            <label class="block text-gray-300 mb-4 text-lg font-semibold">
                Assign Meals
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <?php foreach ($meals as $meal): ?>

                    <label class="bg-black border border-gray-700 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:border-yellow-500 transition">

                        <input type="checkbox"
                               name="meal_ids[]"
                               value="<?= $meal['id'] ?>"
                               class="mt-1">

                        <div>

                            <div class="text-white font-medium">
                                <?= htmlspecialchars($meal['name']) ?>
                            </div>

                            <div class="text-sm text-gray-400 mt-1">
                                <?= ucfirst($meal['type']) ?>
                            </div>

                            <div class="text-yellow-400 text-sm mt-1">
                                TK<?= number_format($meal['price'], 2) ?>
                            </div>

                        </div>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="flex flex-wrap gap-4 pt-4">

            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-400 text-black font-semibold px-6 py-3 rounded-xl transition">
                Create Plan
            </button>

            <a href="/mealbox/public/?url=admin-subscription-plans"
               class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-xl transition">
                Cancel
            </a>

        </div>

    </form>

</div>