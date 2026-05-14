<div class="flex justify-between items-center mb-8">

    <h1 class="text-3xl font-bold text-white">
        Manage Meals
    </h1>

</div>

<!-- ADD MEAL FORM -->
<div class="bg-gray-900 border border-gray-700 rounded-xl p-6 mb-8">

    <h2 class="text-xl font-bold text-white mb-5">
        Add New Meal
    </h2>

    <form method="POST" action="/mealbox/public/?url=admin-meal-store" enctype="multipart/form-data"
          class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div>
            <label class="block text-gray-300 mb-2">Meal Name</label>
            <input type="text" name="name" required
                   class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400">
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Price</label>
            <input type="number" name="price" step="0.01" required
                   class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400">
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Status</label>
            <select name="status"
                    class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Meal Image</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700">
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="3" required
                      class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
        </div>

        <div class="md:col-span-2">
            <button type="submit"
                    class="bg-yellow-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                Add Meal
            </button>
        </div>

    </form>

</div>

<!-- MEALS TABLE -->
<div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-left">

            <thead class="bg-gray-800 text-gray-300">

                <tr>
                    <th class="p-4">Image</th>
                    <th class="p-4">Name</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Created</th>
                    <th class="p-4">Actions</th>
                </tr>

            </thead>

            <tbody>

                <?php if (empty($meals)): ?>

                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-400">
                            No meals found
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($meals as $meal): ?>

                        <tr class="border-t border-gray-700">

                            <td class="p-4">
                                <img
                                    src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                                        $meal['image'] ?: 'default.jpg',
                                    ); ?>"
                                    class="w-16 h-16 object-cover rounded-lg"
                                >
                            </td>

                            <td class="p-4 text-white">
                                <?php echo htmlspecialchars($meal['name']); ?>
                            </td>

                            <td class="p-4 text-green-400">
                                TK <?php echo htmlspecialchars(
                                    $meal['price'],
                                ); ?>
                            </td>

                            <td class="p-4">

                                <?php if (
                                    ($meal['status'] ?? 'active') ===
                                    'active'
                                ): ?>

                                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="p-4 text-gray-400">
                                <?php echo isset($meal['created_at'])
                                    ? date(
                                        'd M Y',
                                        strtotime($meal['created_at']),
                                    )
                                    : '-'; ?>
                            </td>

                            <td class="p-4 flex gap-2">

                                <a href="/mealbox/public/?url=admin-meal-edit&id=<?php echo $meal[
                                    'id'
                                ]; ?>"
                                 class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg">
                                 Edit
                                </a>

                                 <form method="POST"
                                     action="/mealbox/public/?url=admin-meal-delete"
                                     onsubmit="return confirm('Are you sure you want to delete this meal?');">

                                     <input type="hidden" name="id" value="<?php echo $meal[
                                         'id'
                                     ]; ?>">

                                     <button type="button"
                                      onclick="openDeleteModal('<?php echo $meal[
                                          'id'
                                      ]; ?>', '<?php echo htmlspecialchars(
    $meal['name'],
    ENT_QUOTES,
); ?>')"
                                     class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                     Delete
                                     </button>

                                 </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<!-- DELETE CONFIRM MODAL -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center">

    <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 w-full max-w-md shadow-xl">

        <h2 class="text-2xl font-bold text-white mb-3">
            Delete Meal?
        </h2>

        <p class="text-gray-400 mb-6">
            Are you sure you want to delete 
            <span id="deleteMealName" class="text-yellow-400 font-semibold"></span>?
        </p>

        <form method="POST" action="/mealbox/public/?url=admin-meal-delete">

            <input type="hidden" name="id" id="deleteMealId">

            <div class="flex justify-end gap-3">

                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-5 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-600">
                    Cancel
                </button>

                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
                    Yes, Delete
                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function openDeleteModal(id, name) {
        document.getElementById('deleteMealId').value = id;
        document.getElementById('deleteMealName').textContent = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>