<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-white mb-2">
            Subscription Plans
        </h1>

        <p class="text-gray-400">
            Manage SaaS subscription plans dynamically.
        </p>
    </div>

    <a href="/mealbox/public/?url=admin-subscription-plan-create"
       class="bg-yellow-500 hover:bg-yellow-400 text-black font-semibold px-6 py-3 rounded-xl transition text-center">
        + Create Plan
    </a>

</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full min-w-[900px]">

            <thead class="bg-gray-800">

                <tr>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Plan
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Price
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Duration
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Meal Limit
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Meals
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Status
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Created
                    </th>

                    <th class="text-left text-gray-300 px-6 py-4">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($plans)): ?>

                    <tr>
                        <td colspan="8" class="text-center text-gray-400 py-10">
                            No subscription plans found.
                        </td>
                    </tr>

                <?php endif; ?>

                <?php foreach ($plans as $plan): ?>

                    <tr class="border-t border-gray-800 hover:bg-gray-800/40 transition">

                        <td class="px-6 py-5">

                            <div class="font-semibold text-white">
                                <?= htmlspecialchars($plan['name']) ?>
                            </div>

                            <div class="text-sm text-gray-400 mt-1">
                                <?= htmlspecialchars($plan['slug']) ?>
                            </div>

                        </td>

                        <td class="px-6 py-5 text-yellow-400 font-semibold">
                            TK<?= number_format($plan['price'], 2) ?>
                        </td>

                        <td class="px-6 py-5 text-gray-300">
                            <?= (int) $plan['duration_days'] ?> Days
                        </td>

                        <td class="px-6 py-5 text-gray-300">
                            <?= (int) $plan['meal_limit'] ?>
                        </td>

                        <td class="px-6 py-5 text-gray-300">
                            <?= (int) $plan['total_meals'] ?>
                        </td>

                        <td class="px-6 py-5">

                            <?php if ($plan['status'] === 'active'): ?>

                                <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">
                                    Active
                                </span>

                            <?php else: ?>

                                <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm">
                                    Inactive
                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="px-6 py-5 text-gray-400 text-sm">
                            <?= date('d M Y', strtotime($plan['created_at'])) ?>
                        </td>

                        <td class="px-6 py-5">

                            <div class="flex flex-wrap gap-2">

                                <a href="/mealbox/public/?url=admin-subscription-plan-edit&id=<?= $plan[
                                    'id'
                                ] ?>"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition text-sm">
                                    Edit
                                </a>

                                <a href="/mealbox/public/?url=admin-subscription-plan-toggle&id=<?= $plan[
                                    'id'
                                ] ?>"
                                   class="<?= $plan['status'] === 'active'
                                       ? 'bg-red-500 hover:bg-red-600'
                                       : 'bg-green-500 hover:bg-green-600' ?>
                                   text-white px-4 py-2 rounded-lg transition text-sm">

                                    <?= $plan['status'] === 'active'
                                        ? 'Deactivate'
                                        : 'Activate' ?>

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>