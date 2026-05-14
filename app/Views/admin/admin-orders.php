<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">
            Manage Orders
        </h1>
        <p class="text-gray-400 mt-1">
            View and filter all customer meal orders.
        </p>
    </div>

</div>

<!-- SEARCH AND FILTER -->
<div class="bg-gray-900 border border-gray-700 rounded-xl p-5 mb-6">

    <form method="GET" action="/mealbox/public/" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <input type="hidden" name="url" value="admin-orders">

        <div class="sm:col-span-2">
            <input 
                type="text"
                name="search"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                placeholder="Search by order ID, user, email, or meal..."
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
        </div>

        <div>
            <select 
                name="meal_type"
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
                <option value="">All Meal Types</option>

                <option value="breakfast" <?php echo ($_GET['meal_type'] ??
                    '') ===
                'breakfast'
                    ? 'selected'
                    : ''; ?>>
                    Breakfast
                </option>

                <option value="lunch" <?php echo ($_GET['meal_type'] ?? '') ===
                'lunch'
                    ? 'selected'
                    : ''; ?>>
                    Lunch
                </option>

                <option value="dinner" <?php echo ($_GET['meal_type'] ?? '') ===
                'dinner'
                    ? 'selected'
                    : ''; ?>>
                    Dinner
                </option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-black px-5 py-3 rounded-lg font-semibold">
                Search
            </button>

            <a href="/mealbox/public/?url=admin-orders"
               class="w-full sm:w-auto text-center bg-gray-700 hover:bg-gray-600 text-white px-5 py-3 rounded-lg font-semibold">
                Reset
            </a>
        </div>

    </form>

</div>

<!-- ORDERS TABLE -->
<div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">

    <div class="overflow-x-auto w-full">

        <table class="min-w-[1150px] w-full text-left">

            <thead class="bg-gray-800 text-gray-300">
                <tr>
                    <th class="p-4">Order ID</th>
                    <th class="p-4">User</th>
                    <th class="p-4">Meal</th>
                    <th class="p-4">Meal Type</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Delivery</th>
                    <th class="p-4">Action</th>
                    <th class="p-4">Date</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($orders)): ?>

                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-400">
                            No orders found
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($orders as $order): ?>

                        <tr class="border-t border-gray-700">

                            <td class="p-4 text-yellow-400 font-semibold">
                                #<?php echo htmlspecialchars($order['id']); ?>
                            </td>

                            <td class="p-4">
                                <p class="text-white font-semibold">
                                    <?php echo htmlspecialchars(
                                        $order['user_name'] ?? 'Unknown User',
                                    ); ?>
                                </p>
                                <p class="text-gray-500 text-sm">
                                    <?php echo htmlspecialchars(
                                        $order['user_email'] ?? '-',
                                    ); ?>
                                </p>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-3">

                                    <img 
                                        src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                                            $order['meal_image'] ?:
                                            'default.jpg',
                                        ); ?>"
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-700"
                                    >

                                    <span class="text-white font-medium">
                                        <?php echo htmlspecialchars(
                                            $order['meal_name'] ??
                                                'Unknown Meal',
                                        ); ?>
                                    </span>

                                </div>
                            </td>

                            <td class="p-4">
                                <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm capitalize">
                                    <?php echo htmlspecialchars(
                                        $order['meal_type'],
                                    ); ?>
                                </span>
                            </td>

                            <td class="p-4 text-green-400 font-semibold">
                                TK <?php echo htmlspecialchars(
                                    $order['price'],
                                ); ?>
                            </td>

                            <td class="p-4">

                                <?php if (
                                    ($order['delivery_status'] ?? 'pending') ===
                                    'delivered'
                                ): ?>

                                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">
                                        Delivered ✓
                                    </span>

                                <?php else: ?>

                                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm">
                                        Pending
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="p-4">

                                <?php if (
                                    ($order['delivery_status'] ?? 'pending') !==
                                    'delivered'
                                ): ?>

                                    <form method="POST" action="/mealbox/public/?url=admin-order-deliver">

                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars(
                                            $order['id'],
                                        ); ?>">

                                        <button type="submit"
                                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                            Deliver
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <div class="text-green-400 text-xl font-bold">
                                        ✓
                                    </div>

                                <?php endif; ?>

                            </td>

                            <td class="p-4 text-gray-400">
                                <?php echo isset($order['created_at'])
                                    ? date(
                                        'd M Y, h:i A',
                                        strtotime($order['created_at']),
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