<?php
session_start(); // Start the session
include 'db_connection.php';

// Debugging: Check session variables
// var_dump($_SESSION); 
// exit();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage
    exit();
}

// Fetch daily orders
$dailyOrders = array_fill(0, 7, 0);
$today = new DateTime();
$today->setTime(0, 0);

for ($i = 0; $i < 7; $i++) {
    $date = $today->format('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(order_date) = '$date'";
    $result = $db->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $dailyOrders[6 - $i] = $row['count'];
    }

    $today->modify('-1 day');
}


// Fetch customized orders s
$dailyCustomizationOrders = array_fill(0, 7, 0);
$today = new DateTime();
$today->setTime(0, 0);

for ($i = 0; $i < 7; $i++) {
    $date = $today->format('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM customization_orders WHERE DATE(order_date) = '$date'";
    $result = $db->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $dailyCustomizationOrders[6 - $i] = $row['count'];
    }

    $today->modify('-1 day');
}




// Close the connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kween P Sports</title>
    <!-- tailwind css cdn -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="images/headlogo.png" type="image/x-icon">
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Red+Hat+Display:wght@500;700;800&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

    .font-family-karla {
        font-family: karla;
    }

    .bg-sidebar {
        background: orange;
    }

    .cta-btn {
        color: black;
    }

    .upgrade-btn {
        background: black;
    }

    .upgrade-btn:hover {
        background: black;
    }

    .active-nav-link {
        background: grey;
    }

    .nav-item:hover {
        background: grey;
    }

    .account-link:hover {
        background: grey;
    }
</style>

<body class="bg-gray-100 font-family-karla flex">

    <aside class="relative bg-sidebar h-screen w-64 hidden sm:block shadow-xl">
        <div class="p-6">
            <img src="images/logo1.png" alt="">

        </div>
        <nav class="text-white text-base font-semibold pt-3">
            <a href="adminDashboard.php" class="flex items-center active-nav-link text-white py-4 pl-6 nav-item">
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

    <div class="w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
            <div class="w-1/2"></div>
            <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
                <button @click="isOpen = !isOpen" class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://tse1.mm.bing.net/th?id=OIP.V0NH3fa-mZ4AJ94SEQTy_wHaHa&pid=Api&P=0&h=220">
                </button>
                <button x-show="isOpen" @click="isOpen = false" class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">


                    <a href="logout.php" class="block px-4 py-2  hover:text-white">Log out</a>
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

        <div class="w-full overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">
                <h1 class="text-3xl text-black pb-6">Dashboard</h1>

                <!-- Report Download Button -->
                <div class="flex justify-end mb-4">
                    <a href="generate_report.php" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">
                        Download Report (CSV)
                    </a>
                </div>

                <div class="flex flex-wrap mt-6">
                    <div class="w-full lg:w-1/2 pr-0 lg:pr-2">
                        <p class="text-xl pb-3 flex items-center">
                            <i class="fas fa-plus mr-3"></i> Total Orders Of Limited Products
                        </p>
                        <div class="p-6 bg-white">
                            <canvas id="chartOne" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                        <p class="text-xl pb-3 flex items-center">
                            <i class="fas fa-check mr-3"></i> Total Orders Of Customized Products
                        </p>
                        <div class="p-6 bg-white">
                            <canvas id="chartTwo" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                
            </main>

            
        </div>

    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

    <script>
// Fetch daily orders data from PHP
var dailyOrders = <?php echo json_encode($dailyOrders); ?>; // For regular orders
var dailyCustomizationOrders = <?php echo json_encode($dailyCustomizationOrders); ?>; // For customization orders

// Day labels for the last 7 days
var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Bar Chart for Total Orders
var chartOne = document.getElementById('chartOne').getContext('2d');
var myChart = new Chart(chartOne, {
    type: 'bar',
    data: {
        labels: days,
        datasets: [{
            label: 'Total Orders Of Limited Products',
            data: dailyOrders,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 206, 86, 0.2)' // Add another color for the seventh day
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 206, 86, 1)' // Corresponding border color
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Line Chart for Total Customization Orders
var chartTwo = document.getElementById('chartTwo').getContext('2d');
var myLineChart = new Chart(chartTwo, {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Total Orders Of Customized Products',
            data: dailyCustomizationOrders,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


</script>

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


 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</body>

</html>

</html>