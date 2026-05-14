<div class="min-h-screen bg-black pt-28 px-4 sm:px-6 pb-16">

    <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl text-white font-bold mb-3">
            Choose Your Plan
        </h2>

        <p class="text-gray-400 max-w-2xl mx-auto">
            Select a meal subscription plan that fits your routine.
        </p>
    </div>

    <?php if (empty($plans)): ?>

        <div class="max-w-2xl mx-auto bg-gray-900 border border-gray-800 rounded-2xl p-8 text-center">
            <h3 class="text-xl text-white font-semibold mb-3">
                No plans available right now
            </h3>

            <p class="text-gray-400">
                Please check again later.
            </p>
        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">

            <?php foreach ($plans as $plan): ?>

                <?php
                $isMonthly = (int) $plan['duration_days'] === 30;
                $dailyAverage =
                    $plan['duration_days'] > 0
                        ? $plan['price'] / $plan['duration_days']
                        : 0;
                ?>

                <div class="relative bg-gray-900 p-6 rounded-2xl text-center border 
                    <?= $isMonthly
                        ? 'border-green-500 shadow-lg shadow-green-500/20 lg:scale-105'
                        : 'border-gray-700' ?>
                    hover:scale-105 transition">

                    <?php if ($isMonthly): ?>
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-green-500 text-black text-xs font-bold px-4 py-1 rounded-full">
                            Best Value
                        </div>
                    <?php endif; ?>

                    <h3 class="text-2xl text-white font-bold mb-3">
                        <?= htmlspecialchars($plan['name']) ?>
                    </h3>

                    <p class="text-green-400 text-3xl font-bold mb-2">
                        TK <?= number_format($plan['price'], 2) ?>
                    </p>

                    <p class="text-gray-400 text-sm mb-6">
                        About TK <?= number_format($dailyAverage, 2) ?> per day
                    </p>

                    <div class="space-y-3 text-left mb-8">

                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Duration</span>
                            <span class="text-white font-medium">
                                <?= (int) $plan[
                                    'duration_days'
                                ] ?> day<?= $plan['duration_days'] > 1
     ? 's'
     : '' ?>
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Meal Limit</span>
                            <span class="text-white font-medium">
                                <?= (int) $plan['meal_limit'] ?> meals
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Status</span>
                            <span class="text-green-400 font-medium">
                                Active
                            </span>
                        </div>

                    </div>

                    <form method="POST" action="/mealbox/public/?url=subscribe-store">

                        <input type="hidden" name="plan_id" value="<?= (int) $plan[
                            'id'
                        ] ?>">

                        <button class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition font-semibold">
                            Subscribe
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>