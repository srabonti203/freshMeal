<div class="min-h-screen bg-black flex items-center justify-center px-6 pt-24">

    <div class="bg-gray-900 p-10 rounded-2xl border border-yellow-500/30 shadow-2xl w-full max-w-md">

        <h2 class="text-3xl font-bold text-yellow-400 text-center mb-3">
            Admin Login
        </h2>

        <p class="text-gray-300 text-center mb-8">
            Login to access admin dashboard
        </p>

        <form method="POST" action="/mealbox/public/?url=admin-login" class="space-y-4">

            <input 
                type="email" 
                name="email" 
                placeholder="Admin Email" 
                required
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 focus:ring-2 focus:ring-yellow-400 outline-none"
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
                class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-700 focus:ring-2 focus:ring-yellow-400 outline-none"
            >

            <button 
                type="submit"
                class="w-full bg-yellow-500 text-black py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                Login as Admin
            </button>

        </form>

        <p class="text-center mt-6 text-sm text-gray-300">
            Not admin?
            <a href="/mealbox/public/?url=login-choice" class="text-yellow-400 hover:underline">
                Go Back
            </a>
        </p>

    </div>

</div>