<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product = null; // Initialize product variable

// Check if product_id is set
if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);

    // Fetch the product details
    $query = "SELECT id, name, description, price, image_url, stock, size FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product || $product['stock'] <= 0) {
        // Product is sold out
        echo "Sorry, this product is sold out.";
        exit(); // Stop further processing if sold out
    }
} else {
    echo "No product selected.";
    exit(); // Stop further processing if no product ID is provided
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form data (e.g., collect user info)
    // Assuming you have user details in the form submission

    // Only update the stock if the purchase is confirmed
    $updateQuery = "UPDATE products SET stock = stock - 1 WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bind_param("i", $productId);
    $updateStmt->execute();

    // Confirm purchase
    echo "Thank you for your purchase!";
    exit(); // Stop further processing after purchase confirmation
}

// Display the order form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kween P Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($product): ?>
<form class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md" id="orderForm" method="POST" action="process_order.php">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Name:</label>
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Image:</label>
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="mt-2 w-full h-auto rounded-md" />
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Description:</label>
        <?php echo htmlspecialchars($product['description']); ?>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Price:
            <span class="text-blue-500">$<?php echo htmlspecialchars($product['price']); ?></span>
        </label>
    </div>

    <div class="mb-4">
        <span class="block text-sm font-medium text-gray-700">Size:
            <?php echo htmlspecialchars($product['size']); ?>
        </span>
    </div>
    
    <div class="mb-4">
        <label for="facebook_account">Facebook Account:</label>
        <input type="text" id="facebook_account" name="facebook_account" placeholder="Enter your Facebook account" required class="mt-1 block w-full border border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox" name="terms" required class="mr-2" />
            <a href="">I agree to the terms and conditions</a>  
        </label>
    </div>

    <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 rounded hover:bg-blue-600">Buy Now</button>
    <a href="apparelShop.php">
        <p class="mt-3 w-full bg-red-500 text-white font-bold py-2 text-center rounded hover:bg-blue-600">Cancel</p>
    </a>
</form>
<?php endif; ?>


</body>
</html>
