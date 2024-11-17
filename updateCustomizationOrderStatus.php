<?php
session_start();
include 'db_connection.php'; // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage
    exit();
}

// Get the order ID and new status
$orderId = $_POST['order_id'];
$newStatus = $_POST['status'];

// Update the status in the database
$query = "UPDATE customization_orders SET status = ? WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("si", $newStatus, $orderId);
$stmt->execute();

header('Location: customizationOrder.php'); // Redirect back to the order page
exit();
?>
