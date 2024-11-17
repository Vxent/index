<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Fetch the current status of the order
    $query = "SELECT status FROM customization_orders WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status);
        $stmt->fetch();

        // Only allow deletion if status is "Completed"
        if ($status === 'Completed') {
            // Proceed with deletion
            $delete_query = "DELETE FROM customization_orders WHERE id = ?";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->bind_param("i", $order_id);
            $delete_stmt->execute();

            if ($delete_stmt->affected_rows > 0) {
                header('Location: customizationOrder.php?status=deleted');
                exit();
            } else {
                echo "Failed to delete the order.";
            }
        } else {
            echo "You can only delete orders with the 'Completed' status.";
        }
    } else {
        echo "Order not found.";
    }

    $stmt->close();
}

$db->close();
?>
