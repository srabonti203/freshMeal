<div class="min-h-screen bg-black pt-28 px-6 md:px-10">

    <div class="max-w-5xl mx-auto bg-gray-900 rounded-2xl shadow-lg overflow-hidden border border-gray-700">

        <!-- Image -->
        <div class="overflow-hidden">
            <img 
                src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                    $meal['image'] ?: 'default.jpg',
                ); ?>" 
                class="w-full h-72 object-cover"
                alt="Meal Image"
            >
        </div>

        <!-- Content -->
        <div class="p-8">

            <h1 class="text-3xl font-bold text-white mb-4">
                <?php echo htmlspecialchars($meal['name']); ?>
            </h1>

            <p class="text-gray-300 mb-6 leading-relaxed">
                <?php echo htmlspecialchars($meal['description']); ?>
            </p>

            <div class="flex justify-between items-center">

                <span class="text-green-400 text-2xl font-bold">
                    TK <?php echo htmlspecialchars($meal['price']); ?>
                </span>

            </div>

            <!-- Back button -->
            <div class="mt-6">
                <a href="/mealbox/public/?url=menu" 
                   class="text-green-400 hover:underline">
                    ← Back to Menu
                </a>
            </div>

        </div>

    </div>

</div>