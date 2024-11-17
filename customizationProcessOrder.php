<?php
session_start(); // Start the session
require_once 'db_connection.php'; // Include the database connection file

// Check if the user is logged in (i.e., user_id exists in session)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['order_success'] = false;
    $_SESSION['error_message'] = 'You must be logged in to place an order.';
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit();
}

// Retrieve the user_id from the session
$userId = $_SESSION['user_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $size = $_POST['size'];
    $frontText = $_POST['frontText'];
    $backText = $_POST['backText'];
    $productName = $_POST['product_name'];

    // Handle file upload (save the file on the server)
    if ($_FILES['fileUpload']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadedFilePath = $uploadDir . basename($_FILES['fileUpload']['name']);
        move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploadedFilePath);
    } else {
        $_SESSION['order_success'] = false;
        $_SESSION['error_message'] = 'There was an error uploading the file.';
        header('Location: customizationProcessOrder.php');
        exit();
    }

    // Handle customizations (you could convert them into a JSON structure)
    $customizations = json_encode([
        'front_text' => $frontText,
        'back_text' => $backText
    ]);

    // Insert the order into the database
    $query = "INSERT INTO customization_orders (user_id, product_name, size, customizations, front_text, back_text, file_path)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind the statement
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("issssss", $userId, $productName, $size, $customizations, $frontText, $backText, $uploadedFilePath);

        // Execute the statement
        if ($stmt->execute()) {
            // Set session variable for success
            $_SESSION['order_success'] = true;
        } else {
            $_SESSION['order_success'] = false;
            $_SESSION['error_message'] = 'Failed to insert order: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['order_success'] = false;
        $_SESSION['error_message'] = 'Database error: ' . $db->error;
    }

    // Redirect back to the same page to show the success/error modal
    header('Location: customizationProcessOrder.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customization Order Process</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-96 relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-600 text-xl">&times;</button>
            <h2 class="text-2xl font-semibold mb-4">Order Submitted Successfully!</h2>
            <p>Your customization order has been successfully placed. We will process it soon.</p>
            <a href="customization.php"><button id="closeModalBtn" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Close</button></a>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-96 relative">
            <button id="closeErrorModal" class="absolute top-2 right-2 text-gray-600 text-xl">&times;</button>
            <h2 class="text-2xl font-semibold mb-4">Error!</h2>
            <p><?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?></p>
            <button id="closeErrorModalBtn" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Close</button>
        </div>
    </div>

    <script>
        // Check for successful order submission from the session
        <?php if (isset($_SESSION['order_success']) && $_SESSION['order_success'] == true): ?>
            window.onload = function() {
                const modal = document.getElementById('successModal');
                modal.classList.remove('hidden');
                // Clear session variable after showing the modal
                <?php unset($_SESSION['order_success']); ?>
            };
        <?php elseif (isset($_SESSION['order_success']) && $_SESSION['order_success'] == false): ?>
            window.onload = function() {
                const modal = document.getElementById('errorModal');
                modal.classList.remove('hidden');
                // Clear session variables after showing the error modal
                <?php unset($_SESSION['order_success']); unset($_SESSION['error_message']); ?>
            };
        <?php endif; ?>

        // Close success modal functionality
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        closeModal.addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });
        closeModalBtn.addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });

        // Close error modal functionality
        const closeErrorModal = document.getElementById('closeErrorModal');
        const closeErrorModalBtn = document.getElementById('closeErrorModalBtn');
        closeErrorModal.addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });
        closeErrorModalBtn.addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });
    </script>

</body>
</html>
