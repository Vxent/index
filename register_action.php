<?php
session_start(); // Start session to store messages

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstoneloginver2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $address = $conn->real_escape_string($_POST['address']);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
        header("Location: registration.php");
        exit();
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: registration.php");
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, contact_no, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $contact_no, $address);
        
        if ($stmt->execute()) {
            $userId = $stmt->insert_id;
            $stmt->close(); // Close previous statement
            $conn->query("INSERT INTO audit_log (user_id, action) VALUES ('$userId', 'User registered')");

            // Set success message
            $_SESSION['message'] = "Registration successful! Please log in with your new account."; // Updated message
            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $conn->error;
            header("Location: registration.php");
            exit();
        }
    }
}
$conn->close();
?>
