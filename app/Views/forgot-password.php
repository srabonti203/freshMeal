<div class="min-h-screen bg-cover bg-center"
     style="background-image: url('/mealbox/public/assets/images/login-reg bg.avif');">

    <div class="bg-black bg-opacity-60 min-h-screen flex items-center justify-center">

        <div class="bg-gray-900 bg-opacity-85 backdrop-blur-md p-10 rounded-3xl shadow-2xl w-full max-w-md border border-green-500/30">

            <h2 class="text-3xl font-extrabold text-center text-green-400 mb-2">
                Forgot Password
            </h2>

            <p class="text-center text-gray-300 mb-6 text-sm">
                Enter your email to receive a reset link
            </p>

            <form method="POST" action="/mealbox/public/?url=send-reset-link" class="space-y-4">

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter your email"
                    required
                    class="w-full px-4 py-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-200 focus:ring-2 focus:ring-green-400 outline-none"
                >

                <button class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
                    Send Reset Link
                </button>

            </form>

            <p class="text-center mt-6 text-sm text-gray-300">
                Remember password?
                <a href="/mealbox/public/?url=login" class="text-green-400 font-semibold hover:underline">
                    Login
                </a>
            </p>

        </div>

    </div>
</div>