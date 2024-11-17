<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage
    exit();
}

// Get the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Kween P Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');
    .font-family-karla { font-family: karla; }
    .bg-sidebar { background: orange; }
    .cta-btn { color: black; }
    .upgrade-btn { background: black; }
    .upgrade-btn:hover { background: black; }
    .active-nav-link { background: grey; }
    .nav-item:hover { background: grey; }
</style>
<body class="bg-gray-100 font-family-karla flex">

    <aside class="relative bg-sidebar h-screen w-64 hidden sm:block shadow-xl">
        <div class="p-6">
            <img src="images/logo1.png" alt="">
        </div>
        <nav class="text-white text-base font-semibold pt-3">
            <a href="adminDashboard.php" class="flex items-center <?= $current_page == 'adminDashboard.php' ? 'active-nav-link' : '' ?> text-white py-4 pl-6 nav-item">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
             <!-- Orders Accordion -->
             <div class="pl-6">
                <button class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item" onclick="toggleOrders()">
                    <i class="fas fa-box mr-3"></i>
                    Orders
                    <i class="fas fa-chevron-down ml-2" id="orders-toggle-icon"></i>
                </button>
                
                <!-- Submenu for User Orders and Customized Orders -->
                <div id="orders-submenu" class="hidden pl-6">
                    <a href="userOrder.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                        <i class="fas fa-sticky-note mr-3"></i>
                        User Orders
                    </a>
                    <a href="customizationOrder.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                        <i class="fas fa-shirt mr-3"></i>
                        Customized Orders
                    </a>
                </div>
            </div>
            <a href="myProducts.php" class="flex items-center <?= $current_page == 'myProducts.php' ? 'active-nav-link' : '' ?> text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <i class="fas fa-table mr-3"></i>
                My Products
            </a>
            <a href="adminChangePassword.php" class="flex items-center <?= $current_page == 'adminChangePassword.php' ? 'active-nav-link' : '' ?> text-white  hover:opacity-100 py-4 pl-6 nav-item">
                <i class="fa fa-cog mr-3"></i>
                Change Password
            </a>
        </nav>
    </aside>

    <div class="w-full flex flex-col h-screen overflow-y-hidden">
        <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
            <div class="w-1/2"></div>
            <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
                <button @click="isOpen = !isOpen" class="relative z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://tse1.mm.bing.net/th?id=OIP.V0NH3fa-mZ4AJ94SEQTy_wHaHa&pid=Api&P=0&h=220">
                </button>
                <button x-show="isOpen" @click="isOpen = false" class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                    <a href="logout.php" class="block px-4 py-2 hover:text-white">Log out</a>
                </div>
            </div>
        </header>
         <!-- Mobile Header & Nav -->
         <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
            <div class="flex items-center justify-between">
            <img src="images/logo1.png" alt="" class="text-center">
                <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                    <i x-show="!isOpen" class="fas fa-bars"></i>
                    <i x-show="isOpen" class="fas fa-times"></i>
                </button>
            </div>

            <!-- Dropdown Nav -->
            <nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">
                <a href="adminDashboard.php" class="flex items-center active-nav-link text-white py-2 pl-4 nav-item">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="userOrder.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-sticky-note mr-3"></i>
                    User Orders
                </a>
                <a href="myProducts.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-table mr-3"></i>
                    My Products
                </a>
                <a href="myProducts.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                <i class="fa-solid fa-shirt mr-2"> </i>
                 Customized Order
                </a>
                <a href="adminChangePassword.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                <i class="fa fa-fog mr-3"> </i>
                 Change Password
                </a>
            </nav>
        </header>

        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl font-bold mb-6">Change Password</h1>

            <!-- Notification Card -->
            <?php if (isset($_GET['status'])): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo ($_GET['status'] == 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>" role="alert">
                    <strong><?php echo ($_GET['status'] == 'success') ? 'Success!' : 'Error!'; ?></strong>
                    <?php
                    echo ($_GET['status'] == 'success') ? 'Password updated successfully.' : 'Passwords do not match or an error occurred. Please try again.';
                    ?>
                    <div class="mt-4">
                        <a href="adminDashboard.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <!-- FORM -->
            <div>
            <form action="adminUpdatePassword.php" method="POST" class="bg-white p-6 shadow-md w-96 h-auto">
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password:</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" required class="mt-1 block w-full border-gray-300 border-2 rounded-none shadow-sm focus:ring focus:ring-orange-500 focus:border-orange-500 pr-10">
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" onclick="togglePasswordVisibility('new_password')">
                            <i id="eye_icon_new" class="fas fa-eye"></i> <!-- Initial eye icon (for hidden password) -->
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full border-gray-300 border-2 rounded-none shadow-sm focus:ring focus:ring-orange-500 focus:border-orange-500 pr-10">
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" onclick="togglePasswordVisibility('confirm_password')">
                            <i id="eye_icon_confirm" class="fas fa-eye"></i> <!-- Initial eye icon (for hidden password) -->
                        </button>
                    </div>
                </div>
                <button type="submit" class="mt-4 bg-orange-500 text-white py-2 px-4 rounded-none hover:bg-orange-600">Update Password</button>
            </form>


            </div>

            
            <script>
                // JavaScript function to toggle password visibility
                function togglePasswordVisibility(inputId) {
                    var inputField = document.getElementById(inputId);
                    var eyeIcon = document.getElementById("eye_icon_" + inputId);
                    
                    // Toggle the password field type and icon based on the visibility
                    if (inputField.type === "password") {
                        inputField.type = "text"; // Show the password
                        eyeIcon.classList.remove("fa-eye"); // Remove eye icon
                        eyeIcon.classList.add("fa-eye-slash"); // Add slashed eye icon
                    } else {
                        inputField.type = "password"; // Hide the password
                        eyeIcon.classList.remove("fa-eye-slash"); // Remove slashed eye icon
                        eyeIcon.classList.add("fa-eye"); // Add eye icon back
                    }
                }
            </script>
        </main>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
    <script>
    
     // Function to toggle the Orders submenu
     function toggleOrders() {
        const submenu = document.getElementById('orders-submenu');
        const icon = document.getElementById('orders-toggle-icon');
        
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            submenu.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }
</script>
</body>
</html>
