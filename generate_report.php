<?php
session_start(); // Start the session
include 'db_connection.php';
require_once('tcpdf/tcpdf.php'); // Include the TCPDF library

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage if not admin
    exit();
}

// Fetch daily orders (limited products)
$dailyOrders = array_fill(0, 7, 0);
$today = new DateTime();
$today->setTime(0, 0);

for ($i = 0; $i < 7; $i++) {
    $date = $today->format('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(order_date) = '$date'";
    $result = $db->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $dailyOrders[6 - $i] = $row['count'];
    }

    $today->modify('-1 day');
}

// Fetch customized orders
$dailyCustomizationOrders = array_fill(0, 7, 0);
$today = new DateTime();
$today->setTime(0, 0);

for ($i = 0; $i < 7; $i++) {
    $date = $today->format('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM customization_orders WHERE DATE(order_date) = '$date'";
    $result = $db->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $dailyCustomizationOrders[6 - $i] = $row['count'];
    }

    $today->modify('-1 day');
}

// Close the connection
$db->close();

// Create a new PDF document
$pdf = new TCPDF();
$pdf->SetHeaderData('', 0, 'Kween P Sports - Orders Report', 'Generated on: ' . date('Y-m-d H:i:s'));
$pdf->AddPage();

// Set font for the content
$pdf->SetFont('helvetica', '', 12);

// Title for the report
$pdf->Cell(0, 10, 'Daily Orders Report', 0, 1, 'C');
$pdf->Ln(10);

// Section for Total Orders of Limited Products
$pdf->Cell(0, 10, 'Total Orders Of Limited Products', 0, 1, 'C');
$pdf->Ln(5);

// Add table for daily orders (limited products)
$pdf->Cell(40, 10, 'Day', 1, 0, 'C');
$pdf->Cell(40, 10, 'Orders', 1, 1, 'C');
for ($i = 0; $i < 7; $i++) {
    $pdf->Cell(40, 10, date('l', strtotime("-$i days")), 1, 0, 'C');
    $pdf->Cell(40, 10, $dailyOrders[6 - $i], 1, 1, 'C');
}
$pdf->Ln(10);

// Section for Total Orders of Customized Products
$pdf->Cell(0, 10, 'Total Orders Of Customized Products', 0, 1, 'C');
$pdf->Ln(5);

// Add table for daily customization orders
$pdf->Cell(40, 10, 'Day', 1, 0, 'C');
$pdf->Cell(40, 10, 'Orders', 1, 1, 'C');
for ($i = 0; $i < 7; $i++) {
    $pdf->Cell(40, 10, date('l', strtotime("-$i days")), 1, 0, 'C');
    $pdf->Cell(40, 10, $dailyCustomizationOrders[6 - $i], 1, 1, 'C');
}

// Output the PDF document
$pdf->Output('orders_report.pdf', 'D');
exit();
?>
