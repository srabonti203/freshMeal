<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6 md:px-10">

    <!-- Heading -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-white border-b-4 border-green-500 inline-block pb-2">
            Dashboard
        </h2>
    </div>

    <?php
    $plan = $subscription['plan'];
    $created = strtotime($subscription['created_at']);

    $daysTotal = $plan === 'weekly' ? 7 : ($plan === 'monthly' ? 30 : 1);
    $daysUsed = floor((time() - $created) / (60 * 60 * 24));
    $daysLeft = max($daysTotal - $daysUsed, 0);

    $totalBudget = $subscription['price'];
    $carry = $subscription['carry_over'] ?? 0;

    if ($daysUsed === 0) {
        $carry = 0;
    }

    $baseDaily = $totalBudget / $daysTotal;

    $percent = $dailyLimit > 0 ? ($todayTotal / $dailyLimit) * 100 : 0;
    $percent = min($percent, 100);

    $remainingTotal = $totalBudget - $todayTotal;
    ?>

    <!-- 🔥 TOP SECTION -->
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <!-- Plan -->
        <div class="bg-gray-900 p-6 rounded-xl border border-green-500 
                    flex flex-col justify-center items-center text-center min-h-[140px]">
            <h3 class="text-gray-400">Plan</h3>
            <p class="text-green-400 text-xl font-bold mt-2">
                <?php echo strtoupper($plan); ?>
            </p>
        </div>

        <!-- Days Left -->
        <div class="bg-gray-900 p-6 rounded-xl border border-yellow-500 
                    flex flex-col justify-center items-center text-center min-h-[140px]">
            <h3 class="text-gray-400">Days Left</h3>
            <p class="text-yellow-400 text-xl font-bold mt-2">
                <?php echo $daysLeft; ?> days
            </p>
        </div>

        <!-- Budget -->
        <div class="bg-gray-900 p-6 rounded-xl border border-blue-500 shadow-lg">

            <h3 class="text-gray-400 mb-3">Budget</h3>

            <p class="text-white text-sm">
                Total Plan: 
                <span class="font-bold text-blue-400">
                    TK <?php echo number_format($totalBudget, 2); ?>
                </span>
            </p>

            <p class="text-purple-400 text-sm">
                Carry Over: TK <?php echo number_format($carry, 2); ?>
            </p>

            <hr class="border-gray-700 my-3">

            <p class="text-white text-sm">
                Daily Base: 
                <span class="text-gray-300">
                    TK <?php echo number_format($baseDaily, 2); ?>
                </span>
            </p>

            <p class="text-green-400 text-sm mt-1">
                Today Limit: 
                <span class="font-bold">
                    TK <?php echo number_format($dailyLimit, 2); ?>
                </span>
            </p>

            <p class="text-yellow-400 text-sm mt-1">
                Used Today: 
                <span class="font-bold">
                    TK <?php echo number_format($todayTotal, 2); ?>
                </span>
            </p>

            <hr class="border-gray-700 my-3">

            <p class="text-green-400 text-sm">
                Remaining Total: 
                <span class="font-bold">
                    TK <?php echo number_format($remainingTotal, 2); ?>
                </span>
            </p>

        </div>

    </div>

    <!-- 🔥 PROGRESS -->
    <div class="max-w-3xl mx-auto mb-12">
        <div class="flex justify-between text-sm text-gray-400 mb-2">
            <span>Daily Usage</span>
            <span><?php echo round($percent); ?>%</span>
        </div>

        <div class="w-full bg-gray-700 h-3 rounded-full overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-500
                <?php echo $percent > 80
                    ? 'bg-red-500'
                    : ($percent > 50
                        ? 'bg-yellow-500'
                        : 'bg-green-500'); ?>"
                style="width: <?php echo $percent; ?>%">
            </div>
        </div>
    </div>

    <!-- 🍽 MEALS -->
    <div class="max-w-5xl mx-auto">

        <?php
        function groupMeals($meals)
        {
            $grouped = [];

            foreach ($meals as $meal) {
                $key = $meal['name'];

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'name' => $meal['name'],
                        'ids' => [$meal['id']], // ✅ FIX
                        'count' => 1,
                    ];
                } else {
                    $grouped[$key]['count']++;
                    $grouped[$key]['ids'][] = $meal['id']; // ✅ FIX
                }
            }

            return $grouped;
        }

        function renderMeals($title, $meals)
        {
            ?>
            <h3 class="text-xl text-white mt-10 mb-4"><?php echo $title; ?></h3>

            <?php if (empty($meals)): ?>
                <p class="text-gray-400">No items selected</p>
            <?php else: ?>
                <?php foreach ($meals as $meal): ?>
                    <div class="bg-gray-800 p-4 mb-3 rounded-lg flex justify-between items-center">

                        <div>
                            <p class="text-white font-semibold">
                                <?php echo htmlspecialchars($meal['name']); ?>
                            </p>

                            <p class="text-yellow-400 text-sm">
                                Quantity: x<?php echo $meal['count']; ?>
                            </p>
                        </div>

                        <!-- ✅ REMOVE 1 (uses first ID) -->
                        <button onclick="openModal(<?php echo $meal[
                            'ids'
                        ][0]; ?>)"
                            class="bg-red-500 px-4 py-1.5 rounded text-white hover:bg-red-600 transition">
                            Remove 1
                        </button>

                    </div>
                <?php endforeach; ?>
            <?php endif;
        }
        ?>

        <?php renderMeals('🍳 Breakfast', groupMeals($breakfastMeals)); ?>
        <?php renderMeals('🍛 Lunch', groupMeals($lunchMeals)); ?>
        <?php renderMeals('🍽 Dinner', groupMeals($dinnerMeals)); ?>

    </div>

</div>

<!-- 🔥 MODAL -->
<div id="confirmModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">

    <div class="bg-gray-900 p-6 rounded-xl border border-gray-700 w-80 text-center">

        <h3 class="text-lg font-bold text-white mb-4">
            Remove 1 Item?
        </h3>

        <p class="text-gray-400 mb-6 text-sm">
            This will remove only one quantity.
        </p>

        <input type="hidden" id="modalSelectionId">

        <div class="flex justify-center gap-4">

            <button onclick="closeModal()"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Cancel
            </button>

            <button onclick="removeMeal()"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                Remove
            </button>

        </div>

    </div>

</div>

<!-- 🔔 TOAST -->
<div id="toast"
     class="fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg hidden z-50">
</div>

<script>
function openModal(id) {
    document.getElementById('modalSelectionId').value = id;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
}

function removeMeal() {
    const id = document.getElementById('modalSelectionId').value;

    fetch('/mealbox/public/?url=remove-meal', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'selection_id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'deleted') {
            closeModal();
            showToast('Removed 1 item ❌', 'red');
            setTimeout(() => location.reload(), 600);
        }
    });
}

function showToast(msg, type='green') {
    const toast = document.getElementById('toast');

    toast.innerText = msg;
    toast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg z-50 
        ${type === 'red' ? 'bg-red-600' : 'bg-green-600'} text-white`;

    toast.classList.remove('hidden');

    setTimeout(() => toast.classList.add('hidden'), 2000);
}

// close modal outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>