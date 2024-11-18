<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        .btn { display: block; width: 100%; padding: 10px; background-color: #0056b3; color: white; border: none; border-radius: 5px; cursor: pointer; text-align: center; text-decoration: none; }
        .btn:hover { background-color: #004bb3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Dashboard</h2>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</body>
</html>
