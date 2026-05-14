<?php
$currentUrl = $_GET['url'] ?? 'admin-dashboard';

function activeAdminMenu($route, $currentUrl)
{
    return $route === $currentUrl
        ? 'bg-yellow-500 text-black'
        : 'text-gray-300 hover:bg-yellow-500 hover:text-black';
}
?>

<!-- MOBILE TOP BAR -->
<div class="lg:hidden bg-gray-900 border-b border-gray-700 p-4 flex justify-between items-center sticky top-0 z-50">

    <h1 class="text-xl font-bold text-yellow-400">
        MealBox Admin
    </h1>

    <button onclick="toggleSidebar()"
        class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-semibold">
        ☰ Menu
    </button>

</div>

<!-- SIDEBAR -->
<aside id="adminSidebar"
class="fixed left-0 top-0 bottom-0 w-64 bg-gray-900 border-r border-gray-700 z-50
transform -translate-x-full lg:translate-x-0
lg:left-4 lg:top-4 lg:bottom-4 lg:rounded-2xl
transition-transform duration-300 overflow-hidden">

    <!-- HEADER -->
    <div class="p-6 border-b border-gray-700 flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-yellow-400">
                MealBox Admin
            </h1>

            <p class="text-gray-400 text-sm mt-1">
                Control Panel
            </p>
        </div>

        <!-- MOBILE CLOSE BTN -->
        <button onclick="toggleSidebar()"
            class="lg:hidden text-white text-2xl">
            ×
        </button>

    </div>

    <!-- NAVIGATION -->
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100%-120px)]">

        <a href="/mealbox/public/?url=admin-dashboard"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-dashboard',
               $currentUrl,
           ) ?>">
            Dashboard
        </a>

        <a href="/mealbox/public/?url=admin-meals"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-meals',
               $currentUrl,
           ) ?>">
            Manage Meals
        </a>

        <a href="/mealbox/public/?url=admin-users"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-users',
               $currentUrl,
           ) ?>">
            Users
        </a>

        <a href="/mealbox/public/?url=admin-orders"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-orders',
               $currentUrl,
           ) ?>">
            Orders
        </a>

        <a href="/mealbox/public/?url=admin-reviews"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-reviews',
               $currentUrl,
           ) ?>">
            Reviews
        </a>

        <a href="/mealbox/public/?url=admin-subscription-plans"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-subscription-plans',
               $currentUrl,
           ) ?>">
            Subscription Plans
        </a>

        <a href="/mealbox/public/?url=admin-subscriptions"
           class="block px-4 py-3 rounded-lg transition <?= activeAdminMenu(
               'admin-subscriptions',
               $currentUrl,
           ) ?>">
            User Subscriptions
        </a>

    </nav>

    <!-- FOOTER -->
    <div class="absolute bottom-0 left-0 w-full p-4 border-t border-gray-700 bg-gray-900">

        <a href="/mealbox/public/?url=admin-logout"
           class="block text-center bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition">
            Logout
        </a>

    </div>

</aside>

<!-- BACKDROP -->
<div id="sidebarBackdrop"
     onclick="toggleSidebar()"
     class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
</div>