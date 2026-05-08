<!DOCTYPE html>
<html>
<head>
    <title>MealBox</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black min-h-screen">

    <?php require '../app/Views/partials/navbar.php'; ?>

    <div>
        <?php require $view; ?>
    </div>

    <?php require '../app/Views/partials/footer.php'; ?>

    <?php if (isset($_SESSION['success'])): ?>
    <div id="toast" class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <?php echo $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div id="toast" class="fixed top-5 right-5 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

<script>
    setTimeout(() => {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.style.display = 'none';
        }
    }, 3000);
</script>

</body>
</html>