<div class="min-h-screen bg-black pt-28 px-4 sm:px-6 md:px-10">

    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-white border-b-4 border-green-500 inline-block pb-2">
            Dashboard
        </h2>
    </div>

    <?php
    $plan =
        $subscription['plan_name'] ??
        ($subscription['plan'] ?? 'Subscription Plan');

    $daysTotal = (int) ($subscription['duration_days'] ?? 1);
    if ($daysTotal <= 0) {
        $daysTotal = 1;
    }

    $totalBudget =
        (float) ($subscription['price'] ?? ($subscription['plan_price'] ?? 0));
    $carry = (float) ($subscription['carry_over'] ?? 0);

    $todayDate = new DateTime(date('Y-m-d'));

    if (!empty($subscription['expiry_date'])) {
        $endDate = new DateTime($subscription['expiry_date']);
    } else {
        $startDate = new DateTime(
            date('Y-m-d', strtotime($subscription['created_at'])),
        );
        $endDate = (clone $startDate)->modify("+{$daysTotal} days");
    }

    $daysLeft = $todayDate >= $endDate ? 0 : $todayDate->diff($endDate)->days;

    $baseDaily = $totalBudget / $daysTotal;
    $dailyLimit = $baseDaily + $carry;

    $percent = $dailyLimit > 0 ? ($todayTotal / $dailyLimit) * 100 : 0;
    $percent = min($percent, 100);

    $remainingToday = max($dailyLimit - $todayTotal, 0);
    $remainingTotal = max($totalBudget - $todayTotal, 0);

    $mealLimit = (int) ($subscription['meal_limit'] ?? 0);
    ?>

    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="bg-gray-900 p-6 rounded-xl border border-green-500 flex flex-col justify-center items-center text-center min-h-[140px]">
            <h3 class="text-gray-400">Plan</h3>
            <p class="text-green-400 text-xl font-bold mt-2">
                <?= htmlspecialchars($plan) ?>
            </p>

            <?php if ($mealLimit > 0): ?>
                <p class="text-gray-400 text-sm mt-2">
                    <?= $mealLimit ?> meals included
                </p>
            <?php endif; ?>
        </div>

        <div class="bg-gray-900 p-6 rounded-xl border border-yellow-500 flex flex-col justify-center items-center text-center min-h-[140px]">
            <h3 class="text-gray-400">Days Left</h3>
            <p class="text-yellow-400 text-xl font-bold mt-2">
                <?= $daysLeft ?> days
            </p>

            <p class="text-gray-400 text-sm mt-2">
                <?= $daysTotal ?> days total
            </p>
        </div>

        <div class="bg-gray-900 p-6 rounded-xl border border-blue-500 shadow-lg">

            <h3 class="text-gray-400 mb-3">Budget</h3>

            <p class="text-white text-sm">
                Total Plan:
                <span class="font-bold text-blue-400">
                    TK <?= number_format($totalBudget, 2) ?>
                </span>
            </p>

            <p class="text-purple-400 text-sm">
                Carry Over: TK <?= number_format($carry, 2) ?>
            </p>

            <hr class="border-gray-700 my-3">

            <p class="text-white text-sm">
                Daily Base:
                <span class="text-gray-300">
                    TK <?= number_format($baseDaily, 2) ?>
                </span>
            </p>

            <p class="text-green-400 text-sm mt-1">
                Today Limit:
                <span class="font-bold">
                    TK <?= number_format($dailyLimit, 2) ?>
                </span>
            </p>

            <p class="text-yellow-400 text-sm mt-1">
                Used Today:
                <span class="font-bold">
                    TK <?= number_format($todayTotal, 2) ?>
                </span>
            </p>

            <hr class="border-gray-700 my-3">

            <p class="text-green-400 text-sm">
                Remaining Total:
                <span class="font-bold">
                    TK <?= number_format($remainingTotal, 2) ?>
                </span>
            </p>

        </div>

    </div>

    <div class="max-w-3xl mx-auto mb-12">
        <div class="flex justify-between text-sm text-gray-400 mb-2">
            <span>Daily Usage</span>
            <span><?= round($percent) ?>%</span>
        </div>

        <div class="w-full bg-gray-700 h-3 rounded-full overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-500
                <?= $percent > 80
                    ? 'bg-red-500'
                    : ($percent > 50
                        ? 'bg-yellow-500'
                        : 'bg-green-500') ?>"
                style="width: <?= $percent ?>%">
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto">

        <?php function renderMeals($title, $meals)
        {
            ?>
            <h3 class="text-xl text-white mt-10 mb-4"><?= htmlspecialchars(
                $title,
            ) ?></h3>

            <?php if (empty($meals)): ?>

                <p class="text-gray-400">No items selected</p>

            <?php else: ?>

                <?php foreach ($meals as $meal): ?>

                    <div class="bg-gray-800 p-4 mb-3 rounded-lg flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                        <div>
                            <p class="text-white font-semibold">
                                <?= htmlspecialchars($meal['name']) ?>
                            </p>

                            <p class="text-yellow-400 text-sm">
                                Quantity: x1
                            </p>

                            <div class="mt-2">
                                <?php if (
                                    ($meal['delivery_status'] ?? 'pending') ===
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
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">

                            <?php if (
                                ($meal['delivery_status'] ?? 'pending') ===
                                'delivered'
                            ): ?>

                                <?php if (($meal['review_count'] ?? 0) > 0): ?>

                                    <span class="bg-green-600 text-white px-4 py-2 rounded-lg text-center">
                                        Reviewed ✓
                                    </span>

                                <?php else: ?>

                                    <button 
                                        onclick="openReviewModal(
                                            <?= htmlspecialchars(
                                                $meal['id'],
                                            ) ?>,
                                            <?= htmlspecialchars(
                                                $meal['meal_id'],
                                            ) ?>,
                                            '<?= htmlspecialchars(
                                                $meal['name'],
                                                ENT_QUOTES,
                                            ) ?>'
                                        )"
                                        class="bg-blue-500 px-4 py-2 rounded-lg text-white hover:bg-blue-600 transition">
                                        Review
                                    </button>

                                <?php endif; ?>

                                <button
                                    disabled
                                    class="bg-gray-600 px-4 py-2 rounded-lg text-gray-300 cursor-not-allowed opacity-70">
                                    Delivered
                                </button>

                            <?php else: ?>

                                <button onclick="openModal(<?= htmlspecialchars(
                                    $meal['id'],
                                ) ?>)"
                                    class="bg-red-500 px-4 py-2 rounded-lg text-white hover:bg-red-600 transition">
                                    Remove 1
                                </button>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif;
        } ?>

        <?php renderMeals('Breakfast', $breakfastMeals); ?>
        <?php renderMeals('Lunch', $lunchMeals); ?>
        <?php renderMeals('Dinner', $dinnerMeals); ?>

    </div>

</div>

<!-- REMOVE MODAL -->
<div id="confirmModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">

    <div class="bg-gray-900 p-6 rounded-xl border border-gray-700 w-80 text-center mx-4">

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

<!-- REVIEW MODAL -->
<div id="reviewModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">

    <div class="bg-gray-900 p-6 rounded-xl border border-gray-700 w-full max-w-md mx-4">

        <h3 class="text-xl font-bold text-white mb-2">
            Write Review
        </h3>

        <p class="text-gray-400 mb-5">
            Reviewing:
            <span id="reviewMealName" class="text-yellow-400 font-semibold"></span>
        </p>

        <form method="POST" action="/mealbox/public/?url=meal-review-store">

            <input type="hidden" name="order_id" id="reviewOrderId">
            <input type="hidden" name="meal_id" id="reviewMealId">

            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Rating</label>
                <select name="rating"
                        required
                        class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-lg">
                    <option value="">Select rating</option>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Bad</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-gray-300 mb-2">Review</label>
                <textarea name="review"
                          rows="4"
                          required
                          class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-lg"
                          placeholder="Write your review..."></textarea>
            </div>

            <div class="flex justify-end gap-3">

                <button type="button"
                        onclick="closeReviewModal()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Submit Review
                </button>

            </div>

        </form>

    </div>

</div>

<div id="toast"
     class="fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg hidden z-50">
</div>

<script>
function openModal(id) {
    document.getElementById('modalSelectionId').value = id;

    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openReviewModal(orderId, mealId, mealName) {
    document.getElementById('reviewOrderId').value = orderId;
    document.getElementById('reviewMealId').value = mealId;
    document.getElementById('reviewMealName').textContent = mealName;

    const modal = document.getElementById('reviewModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeReviewModal() {
    const modal = document.getElementById('reviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showToast(message, color = 'green') {
    const toast = document.getElementById('toast');

    toast.innerText = message;
    toast.className = 'fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg z-50 text-white';

    if (color === 'red') {
        toast.classList.add('bg-red-600');
    } else {
        toast.classList.add('bg-green-600');
    }

    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 2000);
}

function removeMeal() {
    const id = document.getElementById('modalSelectionId').value;

    fetch('/mealbox/public/?url=remove-meal', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded' 
        },
        body: 'selection_id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'deleted') {
            closeModal();
            showToast('Removed 1 item', 'red');

            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showToast('Item not removed: ' + data.status, 'red');
        }
    })
    .catch(error => {
        console.log(error);
        showToast('Something went wrong', 'red');
    });
}
</script>