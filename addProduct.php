<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session for notifications
session_start();

// Database connection
include 'db_connection.php';

// Get data from the form
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$size = $_POST['size'];

// Handle image upload
$image_name = basename($_FILES['image']['name']);
$image_path = 'uploads/' . $image_name;

$image_url = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/"; // The uploads directory
    $target_file = $target_dir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Optional: Debugging - Print file information
    // echo "File Name: " . $_FILES['image']['name'] . "<br>";
    // echo "File Type: " . $_FILES['image']['type'] . "<br>";
    // echo "File Size: " . $_FILES['image']['size'] . "<br>";
    // echo "Error Code: " . $_FILES['image']['error'] . "<br>";

    // Check if the file is a valid image using getimagesize
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        // Optional: Debugging - File is a valid image
        // echo "File is an image of type " . $check['mime'] . "<br>";

        // Optional: You can remove the following validation if you don't want to restrict image types
        /*
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, BMP & WEBP files are allowed.";
            exit;
        }
        */

        // Optional: Check file size (example: 5MB max)
        $max_size = 5 * 1024 * 1024;  // 5MB
        if ($_FILES['image']['size'] > $max_size) {
            $_SESSION['message'] = 'File is too large. Maximum allowed size is 5MB.';
            header('Location: myProducts.php');
            exit;
        }

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $target_file; // Save the path for the database
            // Optional: Debugging - Print upload success
            // echo "File uploaded successfully: " . $image_url . "<br>";
        } else {
            $_SESSION['message'] = 'Sorry, there was an error uploading your file.';
            header('Location: myProducts.php');
            exit;
        }
    } else {
        $_SESSION['message'] = 'Uploaded file is not a valid image.';
        header('Location: myProducts.php');
        exit;
    }
} else {
    // If no file is uploaded or there was an error
    $_SESSION['message'] = 'No file uploaded or an error occurred: ' . $_FILES['image']['error'];
    header('Location: myProducts.php');
    exit;
}

// Prepare and bind the query
$query = "INSERT INTO products (name, description, price, size, image_url) VALUES (?, ?, ?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bind_param('ssdss', $name, $description, $price, $size, $image_path);

// Execute the query
if ($stmt->execute()) {
    // Set session message for successful creation
    $_SESSION['message'] = "New product added successfully.";
} else {
    $_SESSION['message'] = "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$db->close();

// Redirect back to myProducts.php (or wherever you want)
header("Location: myProducts.php");
exit();
?>
