<?php
session_start();
include 'db_connection.php'; // Ensure this points to your db_connection.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password from the form submission
    $username = $_POST['username'];  // Changed from 'email' to 'username'
    $password = $_POST['password'];

    // Prepare and execute your SQL query to find the user
    $stmt = $db->prepare("SELECT id, password, role FROM users WHERE username = ?");  // Changed from 'email' to 'username'
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $row['id']; // Set session variable for user ID
            $_SESSION['user_role'] = $row['role']; // Set session variable for user role

            // Redirect based on user role
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: adminDashboard.php'); // Redirect to admin dashboard
            } else {
                header('Location: index.php'); // Redirect to homepage for regular users
            }
            exit();
        } else {
            // Handle invalid password
            $_SESSION['error'] = 'Incorrect password. Please try again.';
            header('Location: login.php'); // Redirect back to login
            exit();
        }
    } else {
        // Handle user not found
        $_SESSION['error'] = 'User not found. Please register or try again.';
        header('Location: login.php'); // Redirect back to login
        exit();
    }

    $stmt->close(); // Close the statement
}
?>
