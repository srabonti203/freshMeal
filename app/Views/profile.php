<?php require '../app/Views/partials/navbar.php'; ?>

<div class="min-h-screen bg-black pt-28 px-6">

<div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6">

    <!-- PROFILE CARD -->
    <div class="bg-gray-900 p-6 rounded-xl text-center border border-gray-700">

        <img id="profileImg"
            src="/mealbox/public/uploads/<?php echo $user['profile_image'] ?:
                'default.png'; ?>"
            class="w-24 h-24 mx-auto rounded-full mb-4 object-cover">

        <input type="file" onchange="uploadImage(this)" class="text-sm text-gray-400">

        <h3 class="text-white mt-3"><?php echo $user['name']; ?></h3>
        <p class="text-gray-400 text-sm"><?php echo $user['email']; ?></p>

        <p class="text-green-400 mt-3">Orders: <?php echo $totalOrders; ?></p>
        <p class="text-yellow-400">Spent: TK <?php echo $totalSpent; ?></p>

        <!-- EDIT BUTTON -->
        <button onclick="toggleEdit()"
            class="mt-4 bg-blue-500 px-4 py-2 text-white rounded hover:bg-blue-600">
            Edit Profile
        </button>

    </div>

    <!-- RIGHT SECTION -->
    <div class="md:col-span-2">

        <!-- VIEW MODE -->
        <div id="viewMode" class="bg-gray-900 p-6 rounded-xl border border-gray-700">
            <h2 class="text-white text-xl mb-4">Profile Details</h2>

            <p class="text-gray-300 mb-2"><strong>Name:</strong> <?php echo $user[
                'name'
            ]; ?></p>
            <p class="text-gray-300 mb-2"><strong>Phone:</strong> <?php echo $user[
                'phone'
            ]; ?></p>
            <p class="text-gray-300 mb-2"><strong>Address:</strong> <?php echo $user[
                'address'
            ]; ?></p>
        </div>

        <!-- EDIT MODE (HIDDEN) -->
        <div id="editMode" class="bg-gray-900 p-6 rounded-xl border border-gray-700 hidden">

            <h2 class="text-white text-xl mb-4">Edit Profile</h2>

            <input id="name"
                placeholder="Enter your name"
                value="<?php echo $user['name']; ?>"
                class="w-full p-2 mb-3 bg-gray-800 text-white rounded">

            <input id="phone"
                placeholder="Enter phone number"
                value="<?php echo $user['phone']; ?>"
                class="w-full p-2 mb-3 bg-gray-800 text-white rounded">

            <textarea id="address"
                placeholder="Enter your address"
                class="w-full p-2 mb-3 bg-gray-800 text-white rounded"><?php echo $user[
                    'address'
                ]; ?></textarea>

            <button onclick="updateProfile()"
                class="bg-green-500 px-5 py-2 text-white rounded hover:bg-green-600">
                Save Changes
            </button>

            <button onclick="toggleEdit()"
                class="ml-2 bg-gray-600 px-5 py-2 text-white rounded hover:bg-gray-700">
                Cancel
            </button>

            <!-- PASSWORD SECTION -->
            <h3 class="text-white mt-6 mb-2">Change Password</h3>

            <!-- PASSWORD -->
            <div class="relative mb-3">
                <input id="password" type="password"
                    placeholder="New Password"
                    class="w-full p-2 bg-gray-800 text-white rounded">

                <button onclick="togglePassword('password')"
                    class="absolute right-3 top-2 text-gray-400">
                    👁
                </button>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="relative mb-3">
                <input id="confirmPassword" type="password"
                    placeholder="Confirm Password"
                    class="w-full p-2 bg-gray-800 text-white rounded">

                <button onclick="togglePassword('confirmPassword')"
                    class="absolute right-3 top-2 text-gray-400">
                    👁
                </button>
            </div>

            <button onclick="changePassword()"
                class="bg-red-500 px-5 py-2 text-white rounded hover:bg-red-600">
                Update Password
            </button>

        </div>

    </div>

</div>

<!-- ORDER HISTORY -->
<div class="max-w-5xl mx-auto mt-10 bg-gray-900 p-6 rounded-xl border border-gray-700">

    <h2 class="text-white text-xl mb-4">Order History</h2>

    <?php foreach ($orders as $order): ?>
        <div class="flex justify-between border-b border-gray-700 py-2 text-gray-300">
            <span><?php echo $order['name']; ?></span>
            <span>TK <?php echo $order['price']; ?></span>
        </div>
    <?php endforeach; ?>

</div>

<!-- SUBSCRIPTION -->
<div class="max-w-5xl mx-auto mt-6 bg-gray-900 p-6 rounded-xl border border-gray-700">

    <h2 class="text-white text-xl mb-2">Subscription</h2>

    <p class="text-gray-300">Plan: <?php echo $subscription['plan']; ?></p>
    <p class="text-gray-300">Price: TK <?php echo $subscription['price']; ?></p>

</div>

</div>

<script>
// 🔁 TOGGLE EDIT MODE
function toggleEdit() {
    document.getElementById('viewMode').classList.toggle('hidden');
    document.getElementById('editMode').classList.toggle('hidden');
}

// 👁 PASSWORD TOGGLE
function togglePassword(id) {
    let input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

// UPDATE PROFILE
function updateProfile(){
    let data = new URLSearchParams();
    data.append('name', name.value);
    data.append('phone', phone.value);
    data.append('address', address.value);

    fetch('?url=profile-update',{method:'POST', body:data})
    .then(res=>res.json())
    .then(()=>{
        alert('Updated ✅');
        location.reload();
    });
}

// CHANGE PASSWORD
function changePassword(){
    let pass = password.value;
    let confirm = confirmPassword.value;

    if(pass !== confirm){
        alert("Passwords do not match ❌");
        return;
    }

    let data = new URLSearchParams();
    data.append('password', pass);

    fetch('?url=change-password',{method:'POST', body:data})
    .then(()=>alert('Password changed 🔐'));
}

// IMAGE UPLOAD
function uploadImage(input){
    let formData = new FormData();
    formData.append('image', input.files[0]);

    fetch('?url=upload-image',{method:'POST', body:formData})
    .then(res=>res.json())
    .then(data=>{
        document.getElementById('profileImg').src =
            '/mealbox/public/uploads/' + data.image;
    });
}
</script>