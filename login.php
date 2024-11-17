<?php
session_start(); // Start the session

// Include your database connection file
require 'db_connection.php'; // Adjust the path as needed

// Initialize an error message variable
$error = '';

// Check for success message
$successMessage = isset($_SESSION['message']) ? $_SESSION['message'] : '';

// Unset the message after displaying it
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password from the form submission
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute a query to find the user
    $stmt = $db->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on user role
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: adminDashboard.php');
            } else {
                $redirect_url = $_GET['redirect'] ?? 'index.php';
                header("Location: $redirect_url");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-cover bg-no-repeat min-h-screen flex flex-col items-center justify-center" style="background-image: url('http://www.pixelstalk.net/wp-content/uploads/2016/10/Black-and-Orange-Background-Full-HD.jpg');">
   
    <div class="container bg-black bg-opacity-50 backdrop-filter backdrop-blur-lg p-10 rounded-lg shadow-lg text-center mt-32 w-80 sm:w-96">
    <img src="images/logo.png" alt="Logo" class="w-32 mx-auto mb-6">
    <form action="" method="POST" class="flex flex-col space-y-4">
        <div>
            <label for="username" class="block text-left text-white">Your Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required class="w-full p-2 bg-black text-white rounded">
        </div>
        <div>
            <label for="password" class="block text-left text-white">Your Password:</label>
            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Enter password" required class="w-full p-2 bg-black text-white rounded">
                <!-- Eye icon for show/hide password -->
                <button type="button" class="absolute right-2 top-2 text-white toggle-password" onclick="togglePassword()">
                    <i id="eye-icon" class="fas fa-eye"></i> <!-- Default Eye Icon -->
                </button>
            </div>
        </div>
        <button type="submit" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">LOG IN</button>
    </form>

    <a href="registration.php" class="text-yellow-500 underline mt-4 inline-block hover:no-underline">REGISTER</a>

    <?php if ($successMessage): ?>
        <p class="text-green-500 mt-4"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="text-red-500 mt-4"><?php echo $error; ?></p>
    <?php endif; ?>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

<script>
    // Function to toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        // Toggle the password visibility
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Change the icon to the eye with slash (hidden password)
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            // Change the icon back to the eye (password visible)
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
