<?php
// MySQL database credentials
$servername = "localhost";
$username ="root";
$password = "";
$database = "kenyan_project";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<?php
session_start();
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Message = $_POST['Message'];

    // Insert the user's reply into the database
    $insertQuery = "INSERT INTO contact (Name, Email, Message) VALUES ('$Name', '$Email', '$Message')";
    if ($conn->query($insertQuery) === TRUE) {
        // Successfully inserted the reply into the database
        // You can redirect the user back to the index.html page or display a success message
        header("Location: contact.php");
        exit;
    } else {
        // An error occurred while inserting the reply into the database
        // You can redirect the user back to the index.html page or display an error message
        header("Location: contact.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="contactstyle.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.html">Home</a>
            <a href="contact.php">Contact Us</a>
            <a href="problems.html">Info</a>
        </nav>
    </header>
    <div class="container">
        <h1>Contact Us</h1>
        <form action="contact.php" method="post">
            <label for="Name">Name</label>
            <input type="text" id="Name" name="Name" required>

            <label for="Email">Email</label>
            <input type="text" id="Email" name="Email" required>

            <label for="Message">Message</label>
            <textarea id="Message" name="Message" rows="5" required></textarea>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>