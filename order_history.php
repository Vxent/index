<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch the user's order history from the database
$userId = $_SESSION['user_id'];
$query_orders = "
    SELECT o.id, p.name AS product_name, p.price AS product_price, p.image_url AS product_image, o.order_date, o.status
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";
$stmt_orders = $db->prepare($query_orders);
$stmt_orders->bind_param("i", $userId);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

// Fetch customization orders (for the customized order section)
$query_custom = "
    SELECT co.*, u.username 
    FROM customization_orders co
    JOIN users u ON co.user_id = u.id
    LIMIT 20";
$result_custom = $db->query($query_custom);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-100">

<!-- Navigation (same as before, unchanged) -->
<nav class="bg-black shadow-md w-full z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            <div class="flex-1 flex justify-start">
                <div class="hidden md:flex space-x-4 p-2">
                    <a href="index.php" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Home</a>
                    <a href="#about" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">About</a>
                    <a href="#threats" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Services</a>
                </div>
            </div>
            <div class="flex-1 flex justify-center">
                <div class="text-center">
                    <img src="images/logo1.png" alt="" width="200px" class="h-20">  
                </div>
            </div>
            <div class="flex-1 flex justify-end">
                <div class="hidden md:flex space-x-4 p-2">
                    <a href="apparelShop.php" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Shop</a>
                    <a href="order_history.php" class="text-white tracking-wider px-4 xl:px-8 py-2 text-sm hover:underline">Order History</a>
                    <a href="logout.php"><button class="py-2 px-4 border-2 border-white bg-gradient-to-r from-yellow-500 to-orange-600 text-white py-2 rounded-md shadow-lg hover:from-yellow-600  hover:to-orange-700">LOGOUT</button></a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main content area with tabs -->
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-4 text-center">Your Orders</h2>

    <!-- Tab navigation -->
    <div class="flex space-x-4 mb-6">
        <button id="tab1Btn" class="px-4 py-2 bg-gray-800 text-white rounded" onclick="showTab(1)">Order History</button>
        <button id="tab2Btn" class="px-4 py-2 bg-gray-800 text-white rounded" onclick="showTab(2)">Customized Orders</button>
    </div>

    <!-- Tab content -->
    <div id="tab1Content" class="tab-content">
        <h3 class="text-xl font-bold mb-4">Apparel Orders</h3>
        <table class="min-w-full bg-white border border-gray-800">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="px-4 py-2 border-b">Order ID</th>
                    <th class="px-4 py-2 border-b">Product Image</th>
                    <th class="px-4 py-2 border-b">Product Name</th>
                    <th class="px-4 py-2 border-b">Price</th>
                    <th class="px-4 py-2 border-b">Order Date</th>
                    <th class="px-4 py-2 border-b">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_orders->num_rows > 0) { ?>
                    <?php while ($row = $result_orders->fetch_assoc()) { ?>
                        <tr>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="px-4 py-2 border-b"><img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" class="w-16 h-16 object-cover"></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['product_price']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="px-4 py-2 border-b text-center">No orders found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="tab2Content" class="tab-content">
    <h3 class="text-xl font-bold mb-4">Customized Orders</h3>
    <?php if ($result_custom->num_rows > 0) { ?>
        <table class="min-w-full bg-white border border-gray-800">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="px-4 py-2 border-b">ID</th>
                    <th class="px-4 py-2 border-b">Username</th>
                    <th class="px-4 py-2 border-b">Product Name</th>
                    <th class="px-4 py-2 border-b">Size</th>
                    <th class="px-4 py-2 border-b">Order Date</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b">Download</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_custom->fetch_assoc()) { ?>
                    <tr>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['id']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['size']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="px-4 py-2 border-b">
                            <!-- Download Button -->
                            <a href="download_order.php?order_id=<?php echo $row['id']; ?>" class="text-blue-500 hover:underline">
                                Download
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No customization orders found.</p>
    <?php } ?>
</div>


<!-- JavaScript to handle tab switching -->
<script>
    function showTab(tabNumber) {
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        // Remove active class from all buttons
        document.querySelectorAll('button').forEach(button => button.classList.remove('bg-yellow-500'));
        
        // Show selected tab content
        document.getElementById('tab' + tabNumber + 'Content').classList.add('active');
        document.getElementById('tab' + tabNumber + 'Btn').classList.add('bg-yellow-500');
    }

    // Default to show the first tab
    showTab(1);
</script>

</body>
</html>
