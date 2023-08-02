<?php
session_start();
require_once 'config.php';
// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect the user to the dashboard
    header('Location: dashboard.php');
    exit;
}
// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // User login
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Retrieve user information
        $sql = "SELECT uid, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            // Verify the password
            $user = $result->fetch_assoc();
            // Verify the entered password against the hashed password
            if (password_verify($password, $user['password'])) {
                // Password is correct, store user information in session variables
                $_SESSION['user_id'] = $user['uid'];
                $_SESSION['username'] = $user['username'];
                // Redirect the user to the dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                // Invalid password
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } elseif (isset($_POST['register'])) {
        // User registration
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Check if the username already exists
        $sql = "SELECT uid FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            // Insert the new user into the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $username, $hashedPassword);
            $stmt->execute();
            // Redirect the user to the login page
            header('Location: login.php');
            exit;
        } else {
            $error = "Username already exists. Please choose a different username.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login / Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 50px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Login / Sign Up</h1>
    <?php if (isset($error)) : ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <h2>Login</h2>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" name="login" value="Login">
    </form>
    <h2>Sign Up</h2>
    <form method="POST" action="">
        <label for="new-username">Username:</label>
        <input type="text" id="new-username" name="username" required>
        <label for="new-password">Password:</label>
        <input type="password" id="new-password" name="password" required>
        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>
