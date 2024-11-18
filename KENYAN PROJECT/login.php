<?php
session_start();

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

// Register user
if (isset($_POST['register'])) {
    $reg_username = $_POST['reg_username'];
    $reg_password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT);

    // Insert user into the database
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $reg_username, $reg_password);

    if ($stmt->execute()) {
        $reg_success = "User registered successfully!";
    } else {
        $reg_error = "Registration failed. Username may already be taken.";
    }

    $stmt->close();
}

// Login user or admin
if (isset($_POST['login'])) {
    $login_username = $_POST['login_username'];
    $login_password = $_POST['login_password'];

    // Fetch user data
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($login_password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.html");
            }
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "Invalid username.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <header>
        <h1>Welcome To Kenya Donation </h1>
    </header>
    <style>
        body { font-family: Arial, sans-serif; background-image:url(image/img4.jpg);background-size: cover;background-repeat: no-repeat;}
        h1{font-family: 'Times New Roman', Times, serif;}
        .container { max-width: 500px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        input[type="text"], input[type="password"] { padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .btn { display: block; width: 100%; padding: 10px; background-color: #0056b3; color: white; border: none; border-radius: 5px; cursor: pointer; text-align: center; text-decoration: none; }
        .btn:hover { background-color: #004bb3; }
        .error, .success { color: red; text-align: center; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <?php if (isset($reg_success)): ?>
            <p class="success"><?= $reg_success ?></p>
        <?php endif; ?>
        <?php if (isset($reg_error)): ?>
            <p class="error"><?= $reg_error ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="reg_username" placeholder="Username" required>
            <input type="password" name="reg_password" placeholder="Password" required>
            <button type="submit" name="register" class="btn">Register</button>
        </form>

        <h2>Admin/User Login</h2>
        <?php if (isset($login_error)): ?>
            <p class="error"><?= $login_error ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="login_username" placeholder="Username" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
