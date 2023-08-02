<?php
session_start();
require_once 'config.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page if not logged in
    header('Location: login.php');
    exit;
}
// Retrieve the list of hangars from the database
$sql = "SELECT hid, name FROM hangars";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $hangars = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // If there are no hangars, display a message
    $noHangarsMessage = "No hangars found. Please create a hangar first.";
}
// Handle generating reports
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $hangarId = $_POST['hangar_id'];
    // Retrieve hangar information for the report
    $sql = "SELECT name FROM hangars WHERE hid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $hangarId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $hangar = $result->fetch_assoc();
    } else {
        $error = "Failed to retrieve hangar information.";
    }
    // Generate and display the report
    if (isset($hangar)) {
        $report = "Report for Hangar " . $hangar['name'] . "<br><br>";
        // Retrieve reservations for the hangar
        $sql = "SELECT a.registration_number, r.start_date, r.end_date FROM reservations r
                INNER JOIN aircraft a ON r.aircraft_id = a.aid
                WHERE r.hangar_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $hangarId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $report .= "<table>";
            $report .= "<tr><th>Aircraft</th><th>Start Date</th><th>End Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                $report .= "<tr>";
                $report .= "<td>" . $row['registration_number'] . "</td>";
                $report .= "<td>" . $row['start_date'] . "</td>";
                $report .= "<td>" . $row['end_date'] . "</td>";
                $report .= "</tr>";
            }
            $report .= "</table>";
        } else {
            $report .= "No reservations found.";
        }
        // Display the generated report
        $report = "<div class='report'>" . $report . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
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
        form {
            margin-top: 10px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        select,
        input[type="submit"] {
            margin-bottom: 10px;
            padding: 5px;
            width: 200px;
        }
        input[type="submit"] {
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
        .report {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
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
    <h1>Reports</h1>
    <?php if (isset($error)) : ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($noHangarsMessage)) : ?>
        <p class="no-data-message"><?php echo $noHangarsMessage; ?></p>
    <?php else : ?>
        <h2>Generate Report</h2>
        <form method="POST" action="">
            <label for="hangar_id">Select Hangar:</label>
            <select id="hangar_id" name="hangar_id" required>
                <?php foreach ($hangars as $hangar) : ?>
                    <option value="<?php echo $hangar['hid']; ?>"><?php echo $hangar['name']; ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <input type="submit" name="generate_report" value="Generate Report">
        </form>
        <?php if (isset($report)) : ?>
            <h2>Report</h2>
            <?php echo $report; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>