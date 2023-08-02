<?php
session_start();
require_once 'config.php';
// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Handle form submission for adding, updating, or deleting hangars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_hangar'])) {
        // Retrieve form data
        $hangar_name = $_POST['hangar_name'];
        $hangar_location = $_POST['hangar_location'];
        // Add the hangar
        $sql = "INSERT INTO hangars (name, location, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $hangar_name, $hangar_location, $user_id);
        $stmt->execute();
        // Redirect to the page after adding
        header('Location: hangar.php');
        exit;
    } elseif (isset($_POST['update_hangar'])) {
        // Retrieve form data
        $hangar_id = $_POST['hangar_id'];
        $hangar_name = $_POST['hangar_name'];
        $hangar_location = $_POST['hangar_location'];
        // Update the hangar
        $sql = "UPDATE hangars SET name = ?, location = ? WHERE hid = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $hangar_name, $hangar_location, $hangar_id, $user_id);
        $stmt->execute();
        // Redirect to the page after updating
        header('Location: hangar.php');
        exit;
    } elseif (isset($_POST['delete_hangar'])) {
        // Retrieve form data
        $hangar_id = $_POST['hangar_id'];
        // Delete the hangar
        $sql = "DELETE FROM hangars WHERE hid = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $hangar_id, $user_id);
        $stmt->execute();
        // Redirecting to the page after deletion
        header('Location: hangar.php');
        exit;
    }
}
// Retrieve the list for the logged-in user
$sql = "SELECT * FROM hangars WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Fetch data
$hangars = [];
while ($row = $result->fetch_assoc()) {
    $hangars[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hangar Management - Hangars</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f1f1f1;
        }
        .top-bar {
            background-color: #333;
            padding: 10px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
        }
        .menu a {
            color: #fff;
            text-decoration: none;
            margin-right: 10px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 20px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        button {
            margin-bottom: 10px;
            padding: 5px;
            width: 200px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .add-hangar-btn {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .add-hangar-btn:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
        .no-data-message {
            margin-top: 10px;
        }
    </style>
    <script>
        function showEditForm(hangarId) {
            var form = document.getElementById('editForm_' + hangarId);
            form.style.display = 'table-row';
        }
        function hideEditForm(hangarId) {
            var form = document.getElementById('editForm_' + hangarId);
            form.style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="top-bar">
        <div class="logo">Airplane Hangar Management</div>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="hangar.php">Hangar Management</a>
            <a href="aircraft.php">Aircraft Management</a>
            <a href="reservations.php">Reservation Management</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <h1>Hangar Management</h1>
    <h2>Your Hangars</h2>
    <?php if (count($hangars) > 0) : ?>
        <table>
            <tr>
                <th>Hangar ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($hangars as $hangar) : ?>
                <tr>
                    <td><?php echo $hangar['hid']; ?></td>
                    <td><?php echo $hangar['name']; ?></td>
                    <td><?php echo $hangar['location']; ?></td>
                    <td>
                        <div id="editForm_<?php echo $hangar['hid']; ?>" style="display: none;">
                            <form method="POST" action="">
                                <input type="hidden" name="hangar_id" value="<?php echo $hangar['hid']; ?>">
                                <input type="text" name="hangar_name" value="<?php echo $hangar['name']; ?>" required>
                                <input type="text" name="hangar_location" value="<?php echo $hangar['location']; ?>" required>
                                <button type="submit" name="update_hangar">Save</button>
                                <button type="button" onclick="hideEditForm(<?php echo $hangar['hid']; ?>)">Cancel</button>
                            </form>
                        </div>
                        <button type="button" onclick="showEditForm(<?php echo $hangar['hid']; ?>)">Edit</button>
                        <form method="POST" action="">
                            <input type="hidden" name="hangar_id" value="<?php echo $hangar['hid']; ?>">
                            <button type="submit" name="delete_hangar" onclick="return confirm('Are you sure you want to delete this hangar?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p>No hangar information available.</p>
    <?php endif; ?>

    <h2>Add Hangar</h2>
    <form method="POST" action="">
        <label for="hangar_name">Name:</label>
        <input type="text" id="hangar_name" name="hangar_name" required><br><br>
        <label for="hangar_location">Location:</label>
        <input type="text" id="hangar_location" name="hangar_location" required><br><br>
        <input type="submit" name="add_hangar" value="Add Hangar" class="add-hangar-btn">
    </form>
</body>
</html>