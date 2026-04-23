<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6 md:px-10">

    <!-- Section Heading -->
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-white tracking-wide inline-block border-b-4 border-green-500 pb-2">
            Our Menu 🍱
        </h2>
    </div>

    <!-- 🔍 Search + Filter + Sort -->
    <form method="GET" action="/mealbox/public/" 
          class="mb-12 flex flex-col md:flex-row flex-wrap gap-4 justify-center items-center">

        <input type="hidden" name="url" value="menu">

        <!-- Search -->
        <input 
            type="text" 
            name="search" 
            placeholder="Search meals..."
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            class="px-4 py-2 rounded-lg border border-gray-600 bg-gray-800 text-white w-full md:w-56 focus:outline-none focus:ring-2 focus:ring-green-500"
        >

        <!-- Min Price -->
        <input 
            type="number" 
            name="min" 
            placeholder="Min Price"
            value="<?php echo htmlspecialchars($_GET['min'] ?? ''); ?>"
            class="px-4 py-2 rounded-lg border border-gray-600 bg-gray-800 text-white w-full md:w-32 focus:outline-none focus:ring-2 focus:ring-green-500"
        >

        <!-- Max Price -->
        <input 
            type="number" 
            name="max" 
            placeholder="Max Price"
            value="<?php echo htmlspecialchars($_GET['max'] ?? ''); ?>"
            class="px-4 py-2 rounded-lg border border-gray-600 bg-gray-800 text-white w-full md:w-32 focus:outline-none focus:ring-2 focus:ring-green-500"
        >

        <!-- 🔽 Sort -->
        <select 
            name="sort"
            class="px-4 py-2 rounded-lg border border-gray-600 bg-gray-800 text-white w-full md:w-48 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
            <option value="">Sort</option>

            <option value="low" <?php if (($_GET['sort'] ?? '') === 'low') {
                echo 'selected';
            } ?>>
                Price: Low → High
            </option>

            <option value="high" <?php if (($_GET['sort'] ?? '') === 'high') {
                echo 'selected';
            } ?>>
                Price: High → Low
            </option>

            <option value="name" <?php if (($_GET['sort'] ?? '') === 'name') {
                echo 'selected';
            } ?>>
                Name A → Z
            </option>
        </select>

        <!-- Apply -->
        <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
            Apply
        </button>

        <!-- Reset -->
        <a href="/mealbox/public/?url=menu"
           class="bg-gray-700 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
            Reset
        </a>

    </form>

    <!-- 🚀 Upgrade Plan Button -->
    <?php if (isset($subscription) && $subscription['plan'] !== 'premium'): ?>
        <div class="text-center mb-8">
            <a href="/mealbox/public/?url=subscription"
               class="bg-yellow-500 text-black px-6 py-2 rounded-lg font-bold hover:bg-yellow-400 transition">
                Upgrade Plan 🚀
            </a>
        </div>
    <?php endif; ?>

    <!-- Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

        <?php if (empty($meals)): ?>

            <div class="col-span-full text-center text-gray-400">
                <p class="text-lg">No meals found 😢</p>
                <p class="text-sm mt-2">Try adjusting your filters</p>
            </div>

        <?php else: ?>

            <?php foreach ($meals as $meal): ?>

                <!-- Card -->
                <div 
                    onclick="window.location.href='/mealbox/public/?url=meal&id=<?php echo $meal[
                        'id'
                    ]; ?>'"
                    class="cursor-pointer bg-gray-900 bg-opacity-90 rounded-2xl shadow-lg overflow-hidden 
                           hover:shadow-green-500/30 transition transform hover:-translate-y-1 
                           border border-gray-700 flex flex-col">

                    <!-- Image -->
                    <div class="overflow-hidden">
                        <img 
                            src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                                $meal['image'] ?: 'default.jpg',
                            ); ?>" 
                            class="w-full h-44 object-cover transform hover:scale-105 transition duration-300"
                            alt="<?php echo htmlspecialchars($meal['name']); ?>"
                        >
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-grow">
                        
                        <h3 class="text-lg font-bold text-white">
                            <?php echo htmlspecialchars($meal['name']); ?>
                        </h3>

                        <p class="text-gray-300 mt-2 text-sm line-clamp-2">
                            <?php echo htmlspecialchars(
                                $meal['description'],
                            ); ?>
                        </p>

                        <!-- Footer -->
                        <div class="flex justify-between items-center mt-auto pt-4">

                            <span class="text-green-400 font-bold">
                                TK <?php echo htmlspecialchars(
                                    $meal['price'],
                                ); ?>
                            </span>

                            <!-- ✅ Updated Select Button -->
                            <form method="POST" action="/mealbox/public/?url=select-meal"
                                  onclick="event.stopPropagation();">
                                <input type="hidden" name="meal_id" value="<?php echo $meal[
                                    'id'
                                ]; ?>">

                                <button class="bg-blue-600 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-blue-700 transition">
                                    Select
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>
