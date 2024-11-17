<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin (implement your admin authentication logic)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied.";
    exit();
}

// Fetch all orders from the database
$query = "SELECT o.id, u.username, p.name, o.order_date, o.status 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN products p ON o.product_id = p.id
          ORDER BY o.order_date DESC";

$result = $db->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>
          <tr>
              <th>Order ID</th>
              <th>User</th>
              <th>Product</th>
              <th>Order Date</th>
              <th>Status</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['order_date']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No orders found.";
}

// Close the database connection
$db->close();
?>
