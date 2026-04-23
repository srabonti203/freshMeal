<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6 md:px-10">

    <!-- Page Heading -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-white tracking-wide inline-block border-b-4 border-green-500 pb-2">
            Dashboard
        </h2>
    </div>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        <div class="bg-gray-900 bg-opacity-90 backdrop-blur-md p-6 rounded-xl shadow-lg border border-gray-700">
            <h3 class="text-gray-400">Total Orders</h3>
            <p class="text-2xl font-bold text-green-400">
                <?php echo $totalOrders; ?>
            </p>
        </div>

        <div class="bg-gray-900 bg-opacity-90 backdrop-blur-md p-6 rounded-xl shadow-lg border border-gray-700">
            <h3 class="text-gray-400">Total Spent</h3>
            <p class="text-2xl font-bold text-green-400">
                TK <?php echo number_format($totalSpent, 2); ?>
            </p>
        </div>

        <div class="bg-gray-900 bg-opacity-90 backdrop-blur-md p-6 rounded-xl shadow-lg border border-gray-700">
            <h3 class="text-gray-400">User</h3>
            <p class="text-sm text-gray-200 break-all">
                <?php echo htmlspecialchars($user); ?>
            </p>
        </div>

    </div>

    <!--Subscription Card -->
    <div class="max-w-7xl mx-auto">
        <div class="bg-gray-900 bg-opacity-90 backdrop-blur-md p-6 rounded-xl shadow-lg border border-green-500 mb-12">
            <h3 class="text-gray-400 mb-2">Subscription</h3>

            <?php if ($subscription): ?>
                <p class="text-green-400 font-bold text-lg">
                    <?php echo strtoupper($subscription['plan']); ?> PLAN
                </p>
            <?php else: ?>
                <p class="text-gray-400">No active plan</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orders Section -->
    <div class="max-w-7xl mx-auto">
        <h3 class="text-2xl font-bold text-white mb-6 border-b-2 border-green-500 inline-block">
            Your Orders
        </h3>

        <?php if (empty($orders)): ?>
            <p class="text-gray-400">You have no orders yet.</p>
        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($orders as $order): ?>

                    <div class="bg-gray-900 bg-opacity-90 backdrop-blur-md p-5 rounded-xl shadow-lg border border-gray-700 flex justify-between items-center">

                        <!-- Order Info -->
                        <div>
                            <h4 class="font-bold text-white">
                                <?php echo htmlspecialchars($order['name']); ?>
                            </h4>
                            <p class="text-gray-400 text-sm">
                                <?php echo $order['created_at']; ?>
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4">

                            <span class="text-green-400 font-bold">
                                TK <?php echo htmlspecialchars(
                                    $order['price'],
                                ); ?>
                            </span>

                            <!-- Cancel Button -->
                            <form method="POST" action="/mealbox/public/?url=delete-order" class="delete-form">
                                <input type="hidden" name="order_id" value="<?php echo $order[
                                    'id'
                                ]; ?>">

                                <button type="button"
                                    onclick="openModal(this)"
                                    class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition">
                                    Cancel
                                </button>
                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<!-- Confirm Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
    
    <div class="bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-700 w-80 text-center animate-fadeIn">
        
        <h3 class="text-lg font-bold text-white mb-4">
            Cancel Order?
        </h3>

        <p class="text-gray-400 mb-6 text-sm">
            This action cannot be undone.
        </p>

        <div class="flex justify-center gap-4">

            <button onclick="closeModal()"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                No
            </button>

            <button id="confirmDeleteBtn"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Yes, Cancel
            </button>

        </div>

    </div>

</div>

<!-- Modal Script -->
<script>
let selectedForm = null;

function openModal(button) {
    selectedForm = button.closest('form');

    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Confirm delete
document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (selectedForm) {
        selectedForm.submit();
    }
});

// Close on outside click
document.getElementById('confirmModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>