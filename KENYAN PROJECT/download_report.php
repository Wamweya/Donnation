<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "kenyan_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the donation table
$sql_donation = "SELECT id, name, phone, donationType, food, foodLocation, clothes, clothesLocation, mpesaNumber, amount FROM donation ORDER BY id DESC";
$result_donation = $conn->query($sql_donation);

// Check for query errors
if ($result_donation === false) {
    die("SQL Error: " . $conn->error);
}

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=donations_report.csv');

// Open PHP output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['ID', 'Name', 'Phone', 'Donation Type', 'Food', 'Food Location', 'Clothes', 'Clothes Location', 'M-Pesa Number', 'Amount']);

// Write rows
while ($row = $result_donation->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['phone'],
        $row['donationType'],
        $row['food'],
        $row['foodLocation'],
        $row['clothes'],
        $row['clothesLocation'],
        $row['mpesaNumber'],
        $row['amount']
    ]);
}

// Close the file
fclose($output);

$conn->close();
exit();
?>
