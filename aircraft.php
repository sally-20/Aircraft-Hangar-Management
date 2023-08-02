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
// Handle form submission for adding, updating, or deleting aircraft
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_aircraft'])) {
        // Retrieve form data
        $registration_number = $_POST['registration_number'];
        $model = $_POST['model'];
        $hangar_id = $_POST['hangar_id'];
        // Add the aircraft
        $sql = "INSERT INTO aircraft (registration_number, model, hangar_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $registration_number, $model, $hangar_id);
        $stmt->execute();
        // Redirect to the page after adding
        header('Location: aircraft.php');
        exit;
    } elseif (isset($_POST['update_aircraft'])) {
        // Retrieve form data
        $aircraft_id = $_POST['aircraft_id'];
        $registration_number = $_POST['registration_number'];
        $model = $_POST['model'];
        $hangar_id = $_POST['hangar_id'];
        // Update the aircraft
        $sql = "UPDATE aircraft SET registration_number = ?, model = ?, hangar_id = ? WHERE aid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $registration_number, $model, $hangar_id, $aircraft_id);
        $stmt->execute();
        // Redirect to the page after updating
        header('Location: aircraft.php');
        exit;
    } elseif (isset($_POST['delete_aircraft'])) {
        // Retrieve form data
        $aircraft_id = $_POST['aircraft_id'];
        // Delete the aircraft
        $sql = "DELETE FROM aircraft WHERE aid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $aircraft_id);
        $stmt->execute();
        // Redirect to the page after deleting
        header('Location: aircraft.php');
        exit;
    }
}
// Retrieve the list for the logged-in user
$sql = "SELECT * FROM aircraft WHERE hangar_id IN (SELECT hid FROM hangars WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Fetch the data
$aircraft = [];
while ($row = $result->fetch_assoc()) {
    $aircraft[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hangar Management - Manage Aircraft</title>
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
        .error-message {
            color: red;
            margin-top: 10px;
        }
        .no-data-message {
            margin-top: 10px;
        }
    </style>
    <script>
        function showEditForm(aircraftId) {
            var form = document.getElementById('editForm_' + aircraftId);
            form.style.display = 'table-row';
        }

        function hideEditForm(aircraftId) {
            var form = document.getElementById('editForm_' + aircraftId);
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
    <h1>Aircraft Management</h1>
    <h2>Manage Aircraft</h2>
    <?php if (count($aircraft) > 0) : ?>
        <table>
            <tr>
                <th>Aircraft ID</th>
                <th>Registration Number</th>
                <th>Model</th>
                <th>Hangar ID</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($aircraft as $ac) : ?>
                <tr>
                    <td><?php echo $ac['aid']; ?></td>
                    <td><?php echo $ac['registration_number']; ?></td>
                    <td><?php echo $ac['model']; ?></td>
                    <td><?php echo $ac['hangar_id']; ?></td>
                    <td>
                        <div id="editForm_<?php echo $ac['aid']; ?>" style="display: none;">
                            <form method="POST" action="">
                                <input type="hidden" name="aircraft_id" value="<?php echo $ac['aid']; ?>">
                                <input type="text" name="registration_number" value="<?php echo $ac['registration_number']; ?>" required>
                                <input type="text" name="model" value="<?php echo $ac['model']; ?>" required>
                                <input type="text" name="hangar_id" value="<?php echo $ac['hangar_id']; ?>" required>
                                <button type="submit" name="update_aircraft">Save</button>
                                <button type="button" onclick="hideEditForm(<?php echo $ac['aid']; ?>)">Cancel</button>
                            </form>
                        </div>
                        <button type="button" onclick="showEditForm(<?php echo $ac['aid']; ?>)">Edit</button>
                        <form method="POST" action="">
                            <input type="hidden" name="aircraft_id" value="<?php echo $ac['aid']; ?>">
                            <button type="submit" name="delete_aircraft" onclick="return confirm('Are you sure you want to delete this aircraft?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p class="no-data-message">No aircraft information available.</p>
    <?php endif; ?>
    <h2>Add New Aircraft</h2>
    <form method="POST" action="">
        <label for="registration_number">Registration Number:</label>
        <input type="text" id="registration_number" name="registration_number" required><br><br>
        <label for="model">Model:</label>
        <input type="text" id="model" name="model" required><br><br>
        <label for="hangar_id">Hangar ID:</label>
        <input type="number" id="hangar_id" name="hangar_id" required><br><br>
        <button type="submit" name="add_aircraft">Add Aircraft</button>
    </form>
</body>
</html>