<?php
// Start a session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header('Location: login.php');
    exit;
}

// Get the user information from the session
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Include the database configuration file
require_once 'config.php';

// Get the hangar information for the logged-in user
$sql = "SELECT * FROM hangars WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

// Determine user status
$newUser = true;
if ($result->num_rows > 0) {
    $newUser = false;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Hangar Management Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
        }

        h1 {
            color: #333;
        }

        .container {
            max-width: 600px;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button a {
            color: #fff;
            text-decoration: none;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:hover a {
            color: #fff;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .hangar-details {
            margin-top: 20px;
        }

        .hangar-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .hangar-details th, .hangar-details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .hangar-details th {
            background-color: #f2f2f2;
        }

        .no-hangar-message {
            margin-top: 20px;
            color: #888;
        }
        .button-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap; /* Wrap the buttons to the next line if the container width is limited */
}

.button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border: none;
    cursor: pointer;
    margin-bottom: 10px;
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

        <?php if ($newUser) : ?>
            <h2>Features of the Application</h2>
            <ul>
                <li>Manage Hangars</li>
                <li>Manage Aircraft</li>
                <li>Manage Reservations</li>
                <li>Generate Reports</li>
            </ul>
            <button><a href="hangar.php">Create Your First Hangar</a></button>
        <?php else : ?>
            <h2>Your Hangars</h2>

            <?php if ($result->num_rows > 0) : ?>
                <div class="hangar-details">
                    <table>
                        <tr>
                            <th>Hangar ID</th>
                            <th>Hangar Name</th>
                            <th>Hangar Location</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['hid']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['location']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php else : ?>
                <p class="no-hangar-message">No hangar information available.</p>
            <?php endif; ?>

            <div class="buttons button-container">
                <button><a href="hangar.php">Manage Hangars</a></button>
                <button><a href="aircraft.php">Manage Aircraft</a></button>
                <button><a href="reservations.php">Manage Reservations</a></button>
                <button><a href="reports.php">Generate Reports</a></button>
            </div>
        <?php endif; ?>

        <button><a href="logout.php">Logout</a></button>
    </div>
</body>
</html>
