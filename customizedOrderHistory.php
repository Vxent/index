<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch the user's customized order history from the database
$userId = $_SESSION['user_id'];
$query = "
    SELECT id, product_name, size, front_text, back_text, file_path, order_date, status
    FROM customization_orders
    WHERE user_id = ?
    ORDER BY order_date DESC
";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Customized Order History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">

<nav class="bg-black shadow-md top-0 left-0 w-full z-50">
    <!-- Navbar content here -->
</nav>

<div class="container mx-auto">
    <h2 class="text-3xl font-bold mb-4">Your Customized Order History</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-800">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="px-4 py-2 border-b">Order ID</th>
                    <th class="px-4 py-2 border-b">Product Name</th>
                    <th class="px-4 py-2 border-b">Size</th>
                    <th class="px-4 py-2 border-b">Front Text</th>
                    <th class="px-4 py-2 border-b">Back Text</th>
                    <th class="px-4 py-2 border-b">File</th>
                    <th class="px-4 py-2 border-b">Order Date</th>
                    <th class="px-4 py-2 border-b">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['size']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['front_text']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['back_text']); ?></td>
                            <td class="px-4 py-2 border-b">
                                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" download class="text-blue-500 hover:underline">Download</a>
                            </td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" class="px-4 py-2 border-b text-center">No customized orders found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php $stmt->close(); $db->close(); ?>
</body>
</html>
