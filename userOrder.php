<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch orders with user and product details
$query = "
    SELECT o.id AS order_id, u.username, u.address, u.contact_no, u.email, 
           p.name AS product_name, p.price AS product_price, 
           p.image_url, o.order_date, o.status, o.facebook_account
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN products p ON o.product_id = p.id
";
$result = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kween P Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="images/headlogo.png" type="image/x-icon">
    <!-- Other styles and links -->
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
        <a href="adminDashboard.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
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
        <a href="myProducts.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
            <i class="fas fa-table mr-3"></i>
            My Products
        </a>
            <a href="adminChangePassword.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
            <i class="fa fa-cog mr-3"></i> 
               Change Password
            </a>
        
    </nav>
</aside>
<div class="relative w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
            <div class="w-1/2"></div>
            <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
                <button @click="isOpen = !isOpen" class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://tse1.mm.bing.net/th?id=OIP.V0NH3fa-mZ4AJ94SEQTy_wHaHa&pid=Api&P=0&h=220">
                </button>
                <button x-show="isOpen" @click="isOpen = false" class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                   
             
                    <a href="logout.php" class="block px-4 py-2 account-link hover:text-white">Log out</a>
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
                <a href="customizationOrder.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                <i class="fa-solid fa-shirt mr-2"> </i>
                 Customized Order
                </a>
                <a href="adminChangePassword.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                <i class="fa fa-fog mr-3"> </i>
                 Change Password
                </a>
          
                
                
                <a href="logout.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Log Out</a>
                
            </nav>
           
        </header>


<div class="relative w-full flex flex-col h-screen overflow-hidden">
    <div class="container mx-auto px-4 flex-1">
        <h2 class="text-3xl font-bold mb-4">Order Details</h2>
        <div class="overflow-x-auto h-full">
            <div class="max-h-[calc(100vh-6rem)] overflow-y-auto">
                <table class="min-w-full border border-gray-700">
                    <thead class="bg-gray-600 sticky top-0 z-10 text-white">
                        <tr>
                            <th class="px-4 py-2 text-left border-b" >Order ID</th>
                            <th class="px-4 py-2 text-left border-b" >Customer</th>
                            <th class="px-4 py-2 text-left border-b" >Address</th>
                            
                            <th class="px-4 py-2 text-left border-b" >Contact No</th>
                            <th class="px-4 py-2 text-left border-b" >Facebook Name</th>
                            <th class="px-4 py-2 text-left border-b" >Email Address</th>
                            <th class="px-4 py-2 text-left border-b">Product Name</th>
                            <th class="px-4 py-2 text-left border-b">Product Price</th>
                            <th class="px-4 py-2 text-left border-b">Product Image</th>
                            <th class="px-4 py-2 text-left border-b">Order Date</th>
                            <th class="px-4 py-2 text-left border-b">Status</th>
                             <!-- New column -->
                            <th class="px-4 py-2 text-left border-b text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-black">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo $row['order_id']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo $row['username']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo $row['address']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo $row['contact_no']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo htmlspecialchars($row['facebook_account']); ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap text-blue-500"><?php echo $row['email']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo $row['product_name']; ?></td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo number_format($row['product_price'], 2); ?> PHP</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">
                                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" class="w-16 h-16 object-cover">
                                    </td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap"><?php echo date('Y-m-d H:i:s', strtotime($row['order_date'])); ?></td>
                                    
                                    <!-- Status column with conditional color classes -->
                                    <td class="px-4 py-2 border-b whitespace-nowrap <?php 
                                        echo $row['status'] === 'Delivery' ? 'text-blue-500' : 
                                            ($row['status'] === 'Pending' ? 'text-yellow-600' : 
                                            ($row['status'] === 'Finished' ? 'text-green-500' : '')); ?>">
                                        <?php echo $row['status']; ?>
                                    </td>
                                    
                                    <td class="px-4 py-2 border-b whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="editOrder.php?id=<?php echo $row['order_id']; ?>" class="text-blue-600 hover:underline">
                                                <button class="bg-yellow-400 hover:bg-yellow-500 px-4 py-2 rounded text-white">Update</button>
                                            </a>
                                            <a href="deleteOrder.php?id=<?php echo $row['order_id']; ?>" class="text-red-600 hover:underline">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-white">Delete</button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>


                </table>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@2.x.x/dist/alpine.min.js" defer></script>
<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

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
