<?php
session_start(); // Start session to access messages
include 'db_connection.php'; // Include your database connection

$successMessage = ""; // Initialize success message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $email = $db->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_no = $db->real_escape_string($_POST['contact_no']);
    $address = $db->real_escape_string($_POST['address']);

    // Check if email is already registered
    $result = $db->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, contact_no, address) VALUES ('$username', '$email', '$hashed_password', '$contact_no', '$address')";

        if ($db->query($sql) === TRUE) {
            $userId = $db->insert_id;
            $_SESSION['username'] = $username; // Store username in session
            $db->query("INSERT INTO audit_log (user_id, action) VALUES ('$userId', 'User registered')");
            $successMessage = "You have successfully registered!"; // Set success message
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showModal(); });</script>";
        } else {
            $_SESSION['error'] = "Error: " . $db->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .modal-body {
            font-size: 18px;
        }

        .valid-email {
            border-color: green;
            outline: none;
        }

        .invalid-email {
            border-color: red;
            outline: none;
        }

        .icon {
            display: none;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .icon.visible {
            display: inline;
        }
    </style>
</head>

<body class="bg-cover bg-no-repeat min-h-screen flex items-center justify-center" style="background-image: url('http://www.pixelstalk.net/wp-content/uploads/2016/10/Black-and-Orange-Background-Full-HD.jpg');">
    <div class="container bg-black bg-opacity-50 backdrop-filter backdrop-blur-lg p-10 rounded-lg shadow-lg text-center w-96">
        <h1 class="text-2xl font-bold text-yellow-500 mb-6">Register</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="message error bg-red-500 text-white p-3 rounded mb-4"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); // Clear the error after displaying ?>
        <?php endif; ?>

        <form action="registration.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-left text-white">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your Username:" required class="w-full p-2 bg-black text-white rounded">
            </div>
            <div class="relative">
                <label for="email" class="block text-left text-white">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your Email Address:" required class="w-full p-2 bg-black text-white rounded" oninput="validateEmail()">
                <span id="valid-icon" class="icon text-green-500"><i class="fas fa-check"></i></span>
                <span id="invalid-icon" class="icon text-red-500"><i class="fas fa-times"></i></span>
            </div>
            <div>
                <label for="password" class="block text-left text-white">Password:</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Enter your Password:" required class="w-full p-2 bg-black text-white rounded">
                    <button type="button" class="absolute right-2 top-2 text-white toggle-password" onclick="togglePassword('password')">
                        <i id="eye-icon-password" class="fas fa-eye"></i> <!-- Default Eye Icon -->
                    </button>
                </div>
            </div>
            <div>
                <label for="confirm_password" class="block text-left text-white">Confirm Password:</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 bg-black text-white rounded" placeholder="Confirm Your Password:">
                    <button type="button" class="absolute right-2 top-2 text-white toggle-password" onclick="togglePassword('confirm_password')">
                        <i id="eye-icon-confirm-password" class="fas fa-eye"></i> <!-- Default Eye Icon -->
                    </button>
                </div>
            </div>
            <div>
                <label for="contact_no" class="block text-left text-white">Contact No:</label>
                <input type="text" id="contact_no" name="contact_no" class="w-full p-2 bg-black text-white rounded" placeholder="Ex: 092*******">
            </div>
            <div>
                <label for="address" class="block text-left text-white">Address:</label>
                <textarea id="address" name="address" placeholder="ex: Street, Brgy ,Municipality ,Province" class="w-full p-2 bg-black text-white rounded"></textarea>
            </div>
            <div class="flex flex-col items-center space-y-2">
                <button type="submit" class="w-full bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">REGISTER</button>
                <a href="login.php" class="text-yellow-500 underline hover:no-underline">Back to Login</a>
            </div>
        </form>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">Registration Successful</div>
            <div class="modal-body">You have successfully registered!</div>
            <a href="login.php" class="text-yellow-500 underline hover:no-underline">Back to Login</a>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
        // Function to toggle password visibility
        function togglePassword(id) {
            var passwordInput = document.getElementById(id);
            var toggleButton = passwordInput.nextElementSibling;
            var icon = toggleButton.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validate email function
        function validateEmail() {
            var emailInput = document.getElementById('email');
            var validIcon = document.getElementById('valid-icon');
            var invalidIcon = document.getElementById('invalid-icon');
            var email = emailInput.value;
            var isValid = email.includes('@') && email.includes('.');

            if (isValid) {
                emailInput.classList.remove('invalid-email');
                emailInput.classList.add('valid-email');
                validIcon.classList.add('visible');
                invalidIcon.classList.remove('visible');
            } else {
                emailInput.classList.remove('valid-email');
                emailInput.classList.add('invalid-email');
                validIcon.classList.remove('visible');
                invalidIcon.classList.add('visible');
            }
        }

        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x)
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Show the modal
        function showModal() {
            modal.style.display = "block";
        }
    </script>
</body>

</html>
