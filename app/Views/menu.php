<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6 md:px-10">

    <!-- Heading -->
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-white border-b-4 border-green-500 inline-block pb-2">
            Our Menu 🍱
        </h2>
    </div>

    <!-- Meal Counts -->
    <div class="text-center mb-6 text-gray-300">
        🍳 Breakfast: <span id="count-breakfast"><?php echo $mealCounts[
            'breakfast'
        ]; ?></span> |
        🍛 Lunch: <span id="count-lunch"><?php echo $mealCounts[
            'lunch'
        ]; ?></span> |
        🍽 Dinner: <span id="count-dinner"><?php echo $mealCounts[
            'dinner'
        ]; ?></span>
    </div>

    <!-- 🔥 Budget -->
    <div class="text-center mb-6 space-y-1">
        <p class="text-gray-300">
            Daily Limit:
            <span class="text-green-400 font-bold">
                TK <?php echo number_format($dailyLimit, 2); ?>
            </span>
        </p>

        <p class="text-purple-400 text-sm">
            Carry Over: TK <?php echo number_format($carry, 2); ?>
        </p>

        <p class="text-gray-400 text-sm">
            Used Today:
            TK <span id="used"><?php echo number_format(
                $todayTotal,
                2,
            ); ?></span>
        </p>

        <p class="text-red-400 text-sm">
            Remaining:
            TK <span id="remaining"><?php echo number_format(
                $dailyLimit - $todayTotal,
                2,
            ); ?></span>
        </p>
    </div>

    <!-- Progress -->
    <?php
    $percent = $dailyLimit > 0 ? ($todayTotal / $dailyLimit) * 100 : 0;
    $percent = min($percent, 100);
    ?>

    <div class="max-w-xl mx-auto mb-10">
        <div class="flex justify-between text-sm text-gray-400 mb-1">
            <span>Usage</span>
            <span id="percent"><?php echo round($percent); ?>%</span>
        </div>

        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
            <div id="progressBar"
                class="h-3 rounded-full transition-all duration-500"
                style="width: <?php echo $percent; ?>%;
                background: <?php echo $percent > 80
                    ? '#ef4444'
                    : ($percent > 50
                        ? '#eab308'
                        : '#22c55e'); ?>">
            </div>
        </div>
    </div>

    <!-- Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

        <?php foreach ($meals as $meal): ?>
            <div class="meal-card bg-gray-900 rounded-xl overflow-hidden border border-gray-700 flex flex-col transition hover:scale-105 hover:shadow-xl hover:shadow-green-500/20 cursor-pointer">

                <!-- ✅ CLICKABLE AREA -->
                <a href="/mealbox/public/?url=meal&id=<?php echo $meal[
                    'id'
                ]; ?>">

                    <img 
                        src="/mealbox/public/assets/images/<?php echo htmlspecialchars(
                            $meal['image'] ?: 'default.jpg',
                        ); ?>"
                        class="h-44 w-full object-cover"
                    >

                    <div class="p-4">

                        <h3 class="text-white font-bold">
                            <?php echo htmlspecialchars($meal['name']); ?>
                        </h3>

                        <p class="text-gray-400 text-sm mt-2">
                            <?php echo htmlspecialchars(
                                $meal['description'],
                            ); ?>
                        </p>

                    </div>

                </a>

                <!-- ✅ BUTTONS -->
                <div class="p-4 pt-0 mt-auto">

                    <p class="text-green-400 font-bold mb-3">
                        TK <?php echo $meal['price']; ?>
                    </p>

                    <div class="flex gap-2 flex-wrap">
                        <?php foreach (
                            [
                                'breakfast' => 'bg-yellow-500 text-black',
                                'lunch' => 'bg-blue-500 text-white',
                                'dinner' => 'bg-purple-500 text-white',
                            ]
                            as $type => $style
                        ): ?>
                            <button 
                                data-price="<?php echo $meal['price']; ?>"
                                onclick="selectMeal(event, this, <?php echo $meal[
                                    'id'
                                ]; ?>,'<?php echo $type; ?>',<?php echo $meal[
    'price'
]; ?>)"
                                class="meal-btn <?php echo $style; ?> px-3 py-1 text-xs rounded font-semibold transition transform hover:shadow-lg active:scale-90">
                                <?php echo ucfirst($type); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        <?php endforeach; ?>

    </div>

</div>

<!-- JS -->
<script>
let total = <?php echo $todayTotal; ?>;
let limit = <?php echo $dailyLimit; ?>;

let counts = {
    breakfast: <?php echo $mealCounts['breakfast']; ?>,
    lunch: <?php echo $mealCounts['lunch']; ?>,
    dinner: <?php echo $mealCounts['dinner']; ?>
};

function updateUI() {
    document.getElementById('used').innerText = total.toFixed(2);

    let percent = limit > 0 ? (total / limit) * 100 : 0;
    percent = Math.min(percent, 100);

    document.getElementById('percent').innerText = Math.round(percent) + '%';

    let bar = document.getElementById('progressBar');
    bar.style.width = percent + '%';

    if (percent > 80) bar.style.background = '#ef4444';
    else if (percent > 50) bar.style.background = '#eab308';
    else bar.style.background = '#22c55e';

    document.getElementById('count-breakfast').innerText = counts.breakfast;
    document.getElementById('count-lunch').innerText = counts.lunch;
    document.getElementById('count-dinner').innerText = counts.dinner;

    let remaining = limit - total;
    document.getElementById('remaining').innerText = remaining.toFixed(2);
}

// ✅ FIXED: prevent navigation
function selectMeal(e, button, mealId, type, price) {
    e.stopPropagation();

    let remaining = limit - total;

    if (price > remaining) {
        showToast("Budget exceeded ❌", "error");
        return;
    }

    fetch('/mealbox/public/?url=select-meal', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `meal_id=${mealId}&meal_type=${type}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'expired') {
            showToast("Plan expired ❌", "error");
            return;
        }

        if (data.status === 'success') {
            total += price;
            counts = data.counts;
            updateUI();
            showToast("Meal added ✅", "green");
        }
    });
}

// Toast
function showToast(msg, type="normal"){
    let toast = document.createElement('div');
    toast.innerText = msg;
    toast.className = `toast ${type}`;
    document.body.appendChild(toast);
    setTimeout(()=>toast.remove(),2000);
}
</script>

<style>
.meal-btn { position: relative; overflow: hidden; }

.toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px 16px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    z-index: 999;
}

.toast.green { background: #16a34a; }
.toast.error { background: #dc2626; }
.toast.normal { background: #374151; }
</style>