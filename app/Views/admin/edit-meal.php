<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Edit Meal</h1>
    <p class="text-gray-400">Update meal information dynamically.</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-500/20 text-red-400 p-4 rounded-xl mb-6">
        <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 max-w-3xl">
    <form action="/mealbox/public/?url=admin-meal-update" method="POST" enctype="multipart/form-data" class="space-y-5">

        <input type="hidden" name="id" value="<?php echo htmlspecialchars(
            $meal['id'],
        ); ?>">

        <div>
            <label class="block text-gray-300 mb-2">Meal Name</label>
            <input 
                type="text" 
                name="name" 
                value="<?php echo htmlspecialchars($meal['name']); ?>"
                required
                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-green-500"
            >
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Description</label>
            <textarea 
                name="description" 
                rows="4"
                required
                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-green-500"
            ><?php echo htmlspecialchars($meal['description']); ?></textarea>
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Price</label>
            <input 
                type="number" 
                step="0.01" 
                name="price" 
                value="<?php echo htmlspecialchars($meal['price']); ?>"
                required
                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-green-500"
            >
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Status</label>
            <select 
                name="status"
                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-green-500"
            >
                <option value="active" <?php echo $meal['status'] === 'active'
                    ? 'selected'
                    : ''; ?>>
                    Active
                </option>
                <option value="inactive" <?php echo $meal['status'] ===
                'inactive'
                    ? 'selected'
                    : ''; ?>>
                    Inactive
                </option>
            </select>
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Current Image</label>

            <?php if (!empty($meal['image'])): ?>
                <img 
                    src="assets/images/<?php echo htmlspecialchars(
                        $meal['image'],
                    ); ?>" 
                    alt="Meal Image"
                    class="w-32 h-32 object-cover rounded-xl border border-gray-700 mb-4"
                >
            <?php else: ?>
                <p class="text-gray-500 mb-4">No image uploaded.</p>
            <?php endif; ?>

            <label class="block text-gray-300 mb-2">Upload New Image</label>
            <input 
                type="file" 
                name="image"
                accept="image/*"
                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl"
            >

            <p class="text-gray-500 text-sm mt-2">
                Leave empty if you do not want to change the image.
            </p>
        </div>

        <div class="flex gap-4 pt-4">
            <button 
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold"
            >
                Update Meal
            </button>

            <a 
              href="/mealbox/public/?url=admin-meals"
              class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold"
             >
               Cancel
            </a>
        </div>

    </form>
</div>