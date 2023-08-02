<?php
require_once 'config.php';
// Check if the registration form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input values
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    // Check if the username is already taken
    $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $hashedPassword, $email);
        if ($stmt->execute()) {
            header('Location: login.php');
            exit;
        } else {
            $error = "Registration failed. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
</head>
<body>
    <h1>Registration</h1>
    <?php if (isset($error)) : ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>