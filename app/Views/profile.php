<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6">

<div class="max-w-7xl mx-auto flex gap-6">

<!-- SIDEBAR -->
<div class="w-72 min-h-screen bg-gray-900 rounded-xl p-6 border border-gray-700 flex flex-col justify-between">

    <div>

        <div class="text-center border-b border-gray-700 pb-4">
            <img src="/mealbox/public/uploads/<?php echo $user[
                'profile_image'
            ] ?:
                'default.png'; ?>"
                class="w-20 h-20 rounded-full mx-auto mb-3 object-cover">

            <h3 id="sidebarName" class="text-white font-bold"><?php echo $user[
                'name'
            ]; ?></h3>
            <p class="text-gray-400 text-sm"><?php echo $user['email']; ?></p>
        </div>

        <div class="mt-6 space-y-2">
            <button onclick="showSection('profile')" class="menu-btn w-full px-4 py-2 rounded bg-green-500 text-white">My Profile</button>
            <button onclick="showSection('orders')" class="menu-btn w-full px-4 py-2 rounded bg-gray-800 text-gray-300">Orders</button>
            <button onclick="showSection('analytics')" class="menu-btn w-full px-4 py-2 rounded bg-gray-800 text-gray-300">Analytics</button>
        </div>

    </div>

    <a href="/mealbox/public/?url=logout"
        class="bg-red-500 text-center py-2 rounded text-white">
        Logout
    </a>

</div>

<!-- 🔥 RIGHT CONTENT -->
<div class="flex-1">

<!-- ================= PROFILE ================= -->
<div id="profileSection">

<h1 class="text-white text-2xl font-bold mb-1">Account Settings</h1>
<p class="text-gray-400 mb-6">My Profile</p>

<div class="grid md:grid-cols-1 gap-6">

<!-- PROFILE PHOTO -->
<div class="bg-gray-900 p-6 rounded-xl border text-center">
    <img id="profileImgPreview"
        src="/mealbox/public/uploads/<?php echo $user['profile_image'] ?:
            'default.png'; ?>"
        class="w-24 h-24 mx-auto rounded-full mb-4">

    <label class="text-blue-400 cursor-pointer">
        Change Photo
        <input type="file" class="hidden" onchange="uploadImage(this)">
    </label>
</div>

<!-- PERSONAL INFO -->
<div class="bg-gray-900 p-6 rounded-xl border">
    <div class="flex justify-between mb-3">
        <h3 class="text-white">Personal Info</h3>
        <button onclick="toggleEdit('info')" class="text-blue-400">✏️</button>
    </div>

    <div id="infoView" class="text-gray-300 space-y-2">
        <p>Name: <span id="viewName"><?php echo $user['name']; ?></span></p>
        <p>Email: <span id="viewEmail"><?php echo $user['email']; ?></span></p>
        <p>Phone: <span id="viewPhone"><?php echo $user['phone']; ?></span></p>
    </div>

    <div id="infoEdit" class="hidden space-y-2">
        <input id="name" value="<?php echo $user[
            'name'
        ]; ?>" class="w-full p-2 bg-gray-800 text-white">
        <input id="email" value="<?php echo $user[
            'email'
        ]; ?>" class="w-full p-2 bg-gray-800 text-white">
        <input id="phone" value="<?php echo $user[
            'phone'
        ]; ?>" class="w-full p-2 bg-gray-800 text-white">

        <button onclick="updateProfile()" class="bg-green-500 px-3 py-1 text-white">Save</button>
    </div>
</div>

<!-- ADDRESS -->
<div class="bg-gray-900 p-6 rounded-xl border">
    <div class="flex justify-between mb-3">
        <h3 class="text-white">Address</h3>
        <button onclick="toggleEdit('address')" class="text-blue-400">✏️</button>
    </div>

    <div id="addressView" class="text-gray-300">
        <span id="viewAddress"><?php echo $user['address']; ?></span>
    </div>

    <div id="addressEdit" class="hidden">
        <textarea id="address" class="w-full p-2 bg-gray-800 text-white"><?php echo $user[
            'address'
        ]; ?></textarea>
        <button onclick="updateProfile()" class="bg-green-500 px-3 py-1 mt-2 text-white">Save</button>
    </div>
</div>

<!-- 🔐 PASSWORD CARD -->
<div class="bg-gray-900 p-6 rounded-xl border">
    <h3 class="text-white mb-3">Change Password</h3>

    <div class="relative mb-2">
        <input id="password" type="password" placeholder="New Password"
            class="w-full p-2 bg-gray-800 text-white">
        <span onclick="togglePass('password')" class="absolute right-2 top-2 cursor-pointer">👁️</span>
    </div>

    <div class="relative mb-2">
        <input id="confirmPassword" type="password" placeholder="Confirm Password"
            class="w-full p-2 bg-gray-800 text-white">
        <span onclick="togglePass('confirmPassword')" class="absolute right-2 top-2 cursor-pointer">👁️</span>
    </div>

    <button onclick="changePassword()" class="bg-red-500 px-3 py-1 text-white">Update</button>
</div>

</div>

</div>

<!-- ================= ORDERS ================= -->
<div id="ordersSection" class="hidden">

<h2 class="text-white text-xl mb-4">Order History</h2>

<?php function renderOrders($title, $orders)
{
    echo "<h3 class='text-gray-300 mt-4'>$title</h3>";
    foreach ($orders as $o) {
        echo "<div class='bg-gray-800 p-3 my-2 flex justify-between'>
            <span class='text-white'>{$o['name']}</span>
            <span class='text-white'>{$o['date']}</span>
        </div>";
    }
} ?>

<?php renderOrders('Breakfast', $breakfastOrders); ?>
<?php renderOrders('Lunch', $lunchOrders); ?>
<?php renderOrders('Dinner', $dinnerOrders); ?>

</div>

<!-- ================= ANALYTICS ================= -->
<div id="analyticsSection" class="hidden">

<h2 class="text-white text-xl mb-4">Analytics</h2>

<div class="grid grid-cols-1 gap-6">

<div class="bg-gray-900 p-6 rounded-xl text-center">
    <p class="text-gray-400">Total Orders</p>
    <h3 class="text-white text-xl"><?php echo $totalOrders; ?></h3>
</div>

<div class="bg-gray-900 p-6 rounded-xl text-center">
    <p class="text-gray-400">Total Spent</p>
    <h3 class="text-green-400 text-xl">TK <?php echo $totalSpent; ?></h3>
</div>

<div class="bg-gray-900 p-6 rounded-xl text-center">
    <p class="text-gray-400">Avg Order</p>
    <h3 class="text-yellow-400 text-xl">
        TK <?php echo $totalOrders ? round($totalSpent / $totalOrders) : 0; ?>
    </h3>
</div>

</div>

</div>

</div>

</div>

</div>

<!-- TOAST -->
<div id="toast" style="display:none;"></div>

<script>
function showSection(section){

    document.getElementById('profileSection').classList.add('hidden');
    document.getElementById('ordersSection').classList.add('hidden');
    document.getElementById('analyticsSection').classList.add('hidden');

    document.getElementById(section+'Section').classList.remove('hidden');

    document.querySelectorAll('.menu-btn').forEach(btn => {
        btn.classList.remove('bg-green-500','text-white');
        btn.classList.add('bg-gray-800','text-gray-300');
    });

    let buttons = document.querySelectorAll('.menu-btn');

    if(section === 'profile'){
        buttons[0].classList.add('bg-green-500','text-white');
        buttons[0].classList.remove('bg-gray-800','text-gray-300');
    }
    else if(section === 'orders'){
        buttons[1].classList.add('bg-green-500','text-white');
        buttons[1].classList.remove('bg-gray-800','text-gray-300');
    }
    else if(section === 'analytics'){
        buttons[2].classList.add('bg-green-500','text-white');
        buttons[2].classList.remove('bg-gray-800','text-gray-300');
    }
}

function toggleEdit(type){
    document.getElementById(type+'View').classList.toggle('hidden');
    document.getElementById(type+'Edit').classList.toggle('hidden');
}

function togglePass(id){
    let input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function showToast(message, type = 'success') {

    const toast = document.getElementById('toast');

    toast.innerText = message;

    toast.className = 'fixed top-5 right-5 px-5 py-3 rounded-lg text-white shadow-lg transition-all duration-300';
    toast.style.zIndex = '999999';

    if (type === 'success') {
        toast.classList.add('bg-green-500');
    } else {
        toast.classList.add('bg-red-500');
    }

    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.display = 'none';
    }, 2500);
}

function changePassword(){
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;

    if(pass !== confirm){
        showToast("Passwords do not match", "error");
        return;
    }

    fetch('?url=change-password',{
        method:'POST',
        body:new URLSearchParams({password:pass})
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            showToast("Password updated");
            document.getElementById('password').value = '';
            document.getElementById('confirmPassword').value = '';
        } else {
            showToast("Password update failed", "error");
        }
    });
}

function updateProfile(){

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');

    let data = new URLSearchParams({
        name: nameInput.value,
        email: emailInput.value,
        phone: phoneInput.value,
        address: addressInput.value
    });

    fetch('?url=profile-update',{
        method:'POST',
        body:data
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status === 'duplicate'){
            showToast("Email already exists",'error');
            return;
        }

        if(data.status === 'success'){

            document.getElementById('viewName').innerText = nameInput.value;
            document.getElementById('viewEmail').innerText = emailInput.value;
            document.getElementById('viewPhone').innerText = phoneInput.value;
            document.getElementById('viewAddress').innerText = addressInput.value;
            document.getElementById('sidebarName').innerText = nameInput.value;

            if(!document.getElementById('infoEdit').classList.contains('hidden')){
                toggleEdit('info');
            }

            if(!document.getElementById('addressEdit').classList.contains('hidden')){
                toggleEdit('address');
            }

            showToast("Profile Updated");
        } else {
            showToast("Update failed",'error');
        }
    });
}

function uploadImage(input){
    let formData = new FormData();
    formData.append('image', input.files[0]);

    fetch('?url=upload-image',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        if(data.status === 'success'){
            document.getElementById('profileImgPreview').src = '/mealbox/public/uploads/' + data.image + '?t=' + new Date().getTime();
            showToast("Photo updated");
        } else {
            showToast("Photo upload failed", "error");
        }
    });
}
</script>