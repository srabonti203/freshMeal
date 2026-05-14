<h1 class="text-3xl font-bold text-white mb-2">
    Admin Dashboard
</h1>

<p class="text-gray-400 mb-8">
    Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
</p>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Total Users</p>
        <h2 class="text-3xl font-bold text-yellow-400 mt-2">
            <?php echo $totalUsers; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Total Orders</p>
        <h2 class="text-3xl font-bold text-green-400 mt-2">
            <?php echo $totalOrders; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Total Revenue</p>
        <h2 class="text-3xl font-bold text-blue-400 mt-2">
            TK <?php echo number_format($totalRevenue, 2); ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Total Meals</p>
        <h2 class="text-3xl font-bold text-purple-400 mt-2">
            <?php echo $totalMeals; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Today's Orders</p>
        <h2 class="text-3xl font-bold text-orange-400 mt-2">
            <?php echo $todayOrders; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Active Subscriptions</p>
        <h2 class="text-3xl font-bold text-emerald-400 mt-2">
            <?php echo $activeSubscriptions; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">Suspended Subscriptions</p>
        <h2 class="text-3xl font-bold text-red-400 mt-2">
            <?php echo $suspendedSubscriptions; ?>
        </h2>
    </div>

    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <p class="text-gray-400">New Users Today</p>
        <h2 class="text-3xl font-bold text-cyan-400 mt-2">
            <?php echo $newUsersToday; ?>
        </h2>
    </div>

</div>