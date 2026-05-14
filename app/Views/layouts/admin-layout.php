<!DOCTYPE html>
<html>
<head>
    <title>MealBox Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black min-h-screen">

    <?php require '../app/Views/admin/partials/sidebar.php'; ?>

    <main class="lg:ml-72 min-h-screen bg-black">

        <div class="p-4 sm:p-6 lg:p-8">
            <?php require $view; ?>
        </div>

    </main>

    <?php if (isset($_SESSION['success'])): ?>
        <div id="toast" class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div id="toast" class="fixed top-5 right-5 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) toast.style.display = 'none';
        }, 3000);
    </script>

    <script>
         function toggleSidebar() {

         const sidebar = document.getElementById('adminSidebar');
         const backdrop = document.getElementById('sidebarBackdrop');

         sidebar.classList.toggle('-translate-x-full');

         backdrop.classList.toggle('hidden');
         }
    </script>

</body>
</html>