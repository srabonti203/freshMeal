<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">
            Manage Users
        </h1>
        <p class="text-gray-400 mt-1">
            View, search, filter, activate, and suspend registered users.
        </p>
    </div>

</div>

<!-- SEARCH AND FILTER -->
<div class="bg-gray-900 border border-gray-700 rounded-xl p-5 mb-6">

    <form method="GET" action="/mealbox/public/" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

        <input type="hidden" name="url" value="admin-users">

        <div class="sm:col-span-2 xl:col-span-2">
            <input 
                type="text"
                name="search"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                placeholder="Search by name, email, or phone..."
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
        </div>

        <div>
            <select 
                name="status"
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
                <option value="">All Status</option>

                <option value="active" <?php echo ($_GET['status'] ?? '') ===
                'active'
                    ? 'selected'
                    : ''; ?>>
                    Active
                </option>

                <option value="suspended" <?php echo ($_GET['status'] ?? '') ===
                'suspended'
                    ? 'selected'
                    : ''; ?>>
                    Suspended
                </option>
            </select>
        </div>

        <div>
            <select 
                name="plan"
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"
            >
                <option value="">All Plans</option>

                <option value="daily" <?php echo ($_GET['plan'] ?? '') ===
                'daily'
                    ? 'selected'
                    : ''; ?>>
                    Daily
                </option>

                <option value="weekly" <?php echo ($_GET['plan'] ?? '') ===
                'weekly'
                    ? 'selected'
                    : ''; ?>>
                    Weekly
                </option>

                <option value="monthly" <?php echo ($_GET['plan'] ?? '') ===
                'monthly'
                    ? 'selected'
                    : ''; ?>>
                    Monthly
                </option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-black px-5 py-3 rounded-lg font-semibold">
                Search
            </button>

            <a href="/mealbox/public/?url=admin-users"
               class="w-full sm:w-auto text-center bg-gray-700 hover:bg-gray-600 text-white px-5 py-3 rounded-lg font-semibold">
                Reset
            </a>
        </div>

    </form>

</div>

<!-- USERS TABLE -->
<div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">

    <div class="overflow-x-auto w-full">

        <table class="min-w-[950px] w-full text-left">

            <thead class="bg-gray-800 text-gray-300">
                <tr>
                    <th class="p-4">User</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Phone</th>
                    <th class="p-4">Plan</th>
                    <th class="p-4">Orders</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Joined</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($users)): ?>

                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-400">
                            No users found
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr class="border-t border-gray-700">

                            <td class="p-4">
                                <div class="flex items-center gap-3">

                                    <img 
                                        src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                                            $user['profile_image'] ?:
                                            'default.jpg',
                                        ); ?>"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-700"
                                    >

                                    <div>
                                        <p class="text-white font-semibold">
                                            <?php echo htmlspecialchars(
                                                $user['name'] ?? 'No Name',
                                            ); ?>
                                        </p>
                                        <p class="text-gray-500 text-sm">
                                            ID: <?php echo htmlspecialchars(
                                                $user['id'],
                                            ); ?>
                                        </p>
                                    </div>

                                </div>
                            </td>

                            <td class="p-4 text-gray-300">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>

                            <td class="p-4 text-gray-300">
                                <?php echo htmlspecialchars(
                                    $user['phone'] ?? '-',
                                ); ?>
                            </td>

                            <td class="p-4">
                                <?php if (!empty($user['latest_plan'])): ?>
                                    <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm capitalize">
                                        <?php echo htmlspecialchars(
                                            $user['latest_plan'],
                                        ); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-500">No Plan</span>
                                <?php endif; ?>
                            </td>

                            <td class="p-4 text-yellow-400 font-semibold">
                                <?php echo htmlspecialchars(
                                    $user['total_orders'],
                                ); ?>
                            </td>

                            <td class="p-4">

                                <?php if (
                                    ($user['status'] ?? 'active') ===
                                    'active'
                                ): ?>

                                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm">
                                        Suspended
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="p-4 text-gray-400">
                                <?php echo isset($user['created_at'])
                                    ? date(
                                        'd M Y',
                                        strtotime($user['created_at']),
                                    )
                                    : '-'; ?>
                            </td>

                            <td class="p-4">

                                <?php if (
                                    ($user['status'] ?? 'active') ===
                                    'active'
                                ): ?>

                                    <form method="POST" action="/mealbox/public/?url=admin-user-suspend">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars(
                                            $user['id'],
                                        ); ?>">

                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                            Suspend
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <form method="POST" action="/mealbox/public/?url=admin-user-activate">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars(
                                            $user['id'],
                                        ); ?>">

                                        <button type="submit"
                                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                            Activate
                                        </button>
                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>