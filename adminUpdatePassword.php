<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage
    exit();
}

// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match and strength
    if ($new_password === $confirm_password && strlen($new_password) >= 8) { // Example: minimum length check
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $user_id = $_SESSION['user_id'];
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                // Redirect back to settings with a success message
                header('Location: adminChangePassword.php?status=success');
            } else {
                // Redirect back with an error message if update fails
                header('Location: adminChangePassword.php?status=error');
            }
            $stmt->close();
        } else {
            // Handle prepare statement error
            header('Location: adminChangePassword.php?status=error');
        }
    } else {
        // Redirect back with an error message
        header('Location: adminChangePassword.php?status=error');
    }
}

$db->close();
?>
