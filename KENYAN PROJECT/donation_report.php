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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: auto; overflow: hidden; padding: 0 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px; }
        .header { text-align: center; padding: 20px 0; background: #f4f4f4; border-bottom: 1px solid #ddd; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; color: #333; }
        .section { margin: 20px 0; }
        .section h2 { margin-bottom: 10px; color: #0056b3; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .btn { display: inline-block; background-color: #0056b3; color: #fff; border: none; padding: 10px 20px; text-align: center; border-radius: 5px; font-size: 16px; cursor: pointer; margin: 20px 0; text-decoration: none; }
        .btn:hover { background-color: #004bb3; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f4f4f4; }
        table td { background-color: #fafafa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="btn">Logout</a>
        </div>

        <div class="section">
            <h2>Donations</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Donation Type</th>
                    <th>Details</th>
                </tr>
                <?php if ($result_donation && $result_donation->num_rows > 0): ?>
                    <?php while($row = $result_donation->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['donationType']) ?></td>
                            <td>
                                <?php if ($row['donationType'] == 'food'): ?>
                                    Food: <?= htmlspecialchars($row['food']) ?><br>Location: <?= htmlspecialchars($row['foodLocation']) ?>
                                <?php elseif ($row['donationType'] == 'clothes'): ?>
                                    Clothes: <?= htmlspecialchars($row['clothes']) ?><br>Location: <?= htmlspecialchars($row['clothesLocation']) ?>
                                <?php elseif ($row['donationType'] == 'mpesa'): ?>
                                    M-Pesa Number: <?= htmlspecialchars($row['mpesaNumber']) ?><br>Amount: <?= htmlspecialchars($row['amount']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No donations found</td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="section">
            <h2>Download Report</h2>
            <form method="post" action="download_report.php">
                <button type="submit" class="btn">Download Report</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
