<?php
if (isset($_GET['file_path'])) {
    $file_path = $_GET['file_path'];

    // Check if the file exists
    if (file_exists($file_path)) {
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));

        // Read and output the file
        readfile($file_path);
        exit();
    } else {
        echo "File not found.";
    }
}
?>
