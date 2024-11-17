<?php
// In your PHP backend, you will handle the form submission here
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the form submission (e.g., saving the customized model, handling file upload)
    $orderSuccess = true; // For testing, simulate a successful order submission.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customization Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Form Container -->
    <div class="flex justify-center items-center min-h-screen py-10">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">

            <h2 class="text-2xl font-semibold mb-4">Complete Your Order</h2>

            <!-- The Form -->
            <form id="customizationForm" action="customizationProcessOrder.php" method="POST" enctype="multipart/form-data">

                <!-- Hidden Field for Product Name -->
                <input type="hidden" id="product_name" name="product_name">

                <div class="flex flex-col mb-4">
                    <label for="size" class="mb-2">Size:</label>
                    <select id="size" name="size" class="border rounded p-2" required>
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option value="L">Large</option>
                        <option value="XL">X Large</option>
                        <option value="XXL">XX Large</option>
                    </select>
                </div>

                <div class="flex flex-col mb-4">
                    <label for="frontText" class="mb-2">Front Text (optional):</label>
                    <input type="text" id="frontText" name="frontText" class="border rounded p-2" placeholder="Enter text for the front" />
                </div>

                <div class="flex flex-col mb-4">
                    <label for="backText" class="mb-2">Back Text (optional):</label>
                    <input type="text" id="backText" name="backText" class="border rounded p-2" placeholder="Enter text for the back" />
                </div>

                <div class="flex flex-col mb-4">
                    <label for="fileUpload" class="mb-2">Upload Customized Product:</label>
                    <input type="file" id="fileUpload" name="fileUpload" class="border rounded p-2" accept=".glb,.gltf" required />
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Order</button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-96 relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-600 text-xl">&times;</button>
            <h2 class="text-2xl font-semibold mb-4">Order Submitted Successfully!</h2>
            <p>Your customization order has been successfully placed. We will process it soon.</p>
            <button id="closeModalBtn" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Close</button>
        </div>
    </div>

    <script>
        // Retrieve customization data from localStorage
        const selectedModel = localStorage.getItem('selectedModel');
        const selectedColor = localStorage.getItem('selectedColor');
        const productName = localStorage.getItem('selectedProductName');
        document.getElementById('product_name').value = productName;

        if (selectedModel && selectedColor) {
            console.log('Model Selected:', selectedModel);
            console.log('Color Selected:', selectedColor);
        } else {
            alert('No customization data found!');
        }

        // Show the success modal if the order is successful (Simulated here)
        <?php if (isset($orderSuccess) && $orderSuccess): ?>
            window.onload = function() {
                const modal = document.getElementById('successModal');
                modal.classList.remove('hidden');
            };
        <?php endif; ?>

        // Close modal functionality
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        closeModal.addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });
        closeModalBtn.addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });
    </script>

</body>
</html>
