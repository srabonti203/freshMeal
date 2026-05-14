<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">
            Meal Reviews
        </h1>
        <p class="text-gray-400 mt-1">
            View customer reviews and ratings for delivered meals.
        </p>
    </div>

</div>

<div class="bg-gray-900 border border-gray-700 rounded-xl p-5 mb-6">

    <form method="GET" action="/mealbox/public/" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <input type="hidden" name="url" value="admin-reviews">

        <div class="sm:col-span-2">
            <input 
                type="text"
                name="search"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                placeholder="Search by user, email, meal, or review..."
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
        </div>

        <div>
            <select 
                name="rating"
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
                <option value="">All Ratings</option>

                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($_GET[
    'rating'
] ??
    '') ==
$i
    ? 'selected'
    : ''; ?>>
                        <?php echo $i; ?> Star
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-black px-5 py-3 rounded-lg font-semibold">
                Search
            </button>

            <a href="/mealbox/public/?url=admin-reviews"
               class="w-full sm:w-auto text-center bg-gray-700 hover:bg-gray-600 text-white px-5 py-3 rounded-lg font-semibold">
                Reset
            </a>
        </div>

    </form>

</div>

<div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">

    <div class="overflow-x-auto w-full">

        <table class="min-w-[1050px] w-full text-left">

            <thead class="bg-gray-800 text-gray-300">
                <tr>
                    <th class="p-4">Review ID</th>
                    <th class="p-4">User</th>
                    <th class="p-4">Meal</th>
                    <th class="p-4">Meal Type</th>
                    <th class="p-4">Rating</th>
                    <th class="p-4">Review</th>
                    <th class="p-4">Date</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($reviews)): ?>

                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-400">
                            No reviews found
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($reviews as $review): ?>

                        <tr class="border-t border-gray-700 align-top">

                            <td class="p-4 text-yellow-400 font-semibold">
                                #<?php echo htmlspecialchars($review['id']); ?>
                            </td>

                            <td class="p-4">
                                <p class="text-white font-semibold">
                                    <?php echo htmlspecialchars(
                                        $review['user_name'] ?? 'Unknown User',
                                    ); ?>
                                </p>
                                <p class="text-gray-500 text-sm">
                                    <?php echo htmlspecialchars(
                                        $review['user_email'] ?? '-',
                                    ); ?>
                                </p>
                            </td>

                            <td class="p-4 text-white">
                                <?php echo htmlspecialchars(
                                    $review['meal_name'] ?? 'Unknown Meal',
                                ); ?>
                            </td>

                            <td class="p-4">
                                <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm capitalize">
                                    <?php echo htmlspecialchars(
                                        $review['meal_type'] ?? '-',
                                    ); ?>
                                </span>
                            </td>

                            <td class="p-4">
                                <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars(
                                        $review['rating'],
                                    ); ?> ★
                                </span>
                            </td>

                            <td class="p-4 text-gray-300 max-w-md">
                                <?php echo htmlspecialchars(
                                    $review['review'],
                                ); ?>
                            </td>
 
                            <td class="p-4 text-gray-400">
                                <?php echo isset($review['created_at'])
                                    ? date(
                                        'd M Y, h:i A',
                                        strtotime($review['created_at']),
                                    )
                                    : '-'; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>