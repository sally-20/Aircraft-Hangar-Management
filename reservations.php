<?php
session_start();
require_once 'config.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page if not logged in
    header('Location: login.php');
    exit;
}
// Handle reservation creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reservation'])) {
    $hangarId = $_POST['hangar_id'];
    $aircraftId = $_POST['aircraft_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    // Check if the aircraft is available for the given dates
    $sql = "SELECT r.rid FROM reservations r
            INNER JOIN aircraft a ON r.aircraft_id = a.aid
            WHERE a.aid = ? AND ((r.start_date <= ? AND r.end_date >= ?) OR (r.start_date >= ? AND r.start_date <= ?) OR (r.end_date >= ? AND r.end_date <= ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ississs', $aircraftId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error = "The aircraft is already booked for the selected dates. Please choose a different aircraft or different dates.";
    } else {
        // Check if the hangar is available for the given dates
        $sql = "SELECT r.rid FROM reservations r
                WHERE r.hangar_id = ? AND ((r.start_date <= ? AND r.end_date >= ?) OR (r.start_date >= ? AND r.start_date <= ?) OR (r.end_date >= ? AND r.end_date <= ?))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ississs', $hangarId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "The hangar is already reserved for the selected dates. Please choose different dates or hangar.";
        } else {
            // Insert the reservation into the database
            $sql = "INSERT INTO reservations (user_id, hangar_id, aircraft_id, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiiss', $_SESSION['user_id'], $hangarId, $aircraftId, $startDate, $endDate);
            if ($stmt->execute()) {
                header('Location: reservations.php');
                exit;
            } else {
                $error = "Failed to create the reservation. Please try again later.";
            }
        }
    }
}
// Handle reservation cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservationId = $_POST['reservation_id'];
    // Delete the reservation from the database
    $sql = "DELETE FROM reservations WHERE rid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reservationId);
    if ($stmt->execute()) {
        // Reservation cancellation successful
        header('Location: reservations.php');
        exit;
    } else {
        $error = "Failed to cancel the reservation. Please try again later.";
    }
}
// Handle reservation update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation'])) {
    $reservationId = $_POST['reservation_id'];
    $hangarId = $_POST['hangar_id'];
    $aircraftId = $_POST['aircraft_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    // Check if the aircraft is available for the given dates
    $sql = "SELECT r.rid FROM reservations r
            INNER JOIN aircraft a ON r.aircraft_id = a.aid
            WHERE a.aid = ? AND ((r.start_date <= ? AND r.end_date >= ?) OR (r.start_date >= ? AND r.start_date <= ?) OR (r.end_date >= ? AND r.end_date <= ?)) AND r.rid <> ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ississi', $aircraftId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $reservationId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error = "The aircraft is already booked for the selected dates. Please choose a different aircraft or different dates.";
    } else {
        // Check if the hangar is available for the given dates
        $sql = "SELECT r.rid FROM reservations r
                WHERE r.hangar_id = ? AND ((r.start_date <= ? AND r.end_date >= ?) OR (r.start_date >= ? AND r.start_date <= ?) OR (r.end_date >= ? AND r.end_date <= ?)) AND r.rid <> ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ississi', $hangarId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "The hangar is already reserved for the selected dates. Please choose different dates or hangar.";
        } else {
            // Update the reservation in the database
            $sql = "UPDATE reservations SET hangar_id = ?, aircraft_id = ?, start_date = ?, end_date = ? WHERE rid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiiss', $hangarId, $aircraftId, $startDate, $endDate, $reservationId);
            if ($stmt->execute()) {
                header('Location: reservations.php');
                exit;
            } else {
                $error = "Failed to update the reservation. Please try again later.";
            }
        }
    }
}
// Retrieve the list of hangars from the database
$sql = "SELECT hid, name FROM hangars";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $hangars = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $noHangarsMessage = "No hangars found. Please create a hangar first.";
}
// Retrieve the list of aircraft from the database with designated hangars
$sql = "SELECT a.aid, a.registration_number FROM aircraft a
        INNER JOIN hangars h ON a.hangar_id = h.hid";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $aircraft = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $noAircraftMessage = "No aircraft found. Please add an aircraft first.";
}
// Retrieve the list of reservations from the database with filter and sort options
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'rid';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
$sql = "SELECT r.rid, h.name AS hangar_name, a.registration_number AS aircraft_number, r.start_date, r.end_date
        FROM reservations r
        INNER JOIN hangars h ON r.hangar_id = h.hid
        INNER JOIN aircraft a ON r.aircraft_id = a.aid
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $noReservationsMessage = "No reservations found.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation Management</title>
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
        input[type="date"],
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
        .no-data-message {
            margin-top: 10px;
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
    <h1>Reservation Management</h1>
    <?php if (isset($error)) : ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($noHangarsMessage)) : ?>
        <p class="no-data-message"><?php echo $noHangarsMessage; ?></p>
    <?php endif; ?>
    <?php if (isset($noAircraftMessage)) : ?>
        <p class="no-data-message"><?php echo $noAircraftMessage; ?></p>
    <?php endif; ?>
    <h2>Create Reservation</h2>
    <form method="POST" action="">
        <label for="hangar_id">Hangar:</label>
        <select id="hangar_id" name="hangar_id" required>
            <?php foreach ($hangars as $hangar) : ?>
                <option value="<?php echo $hangar['hid']; ?>"><?php echo $hangar['name']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <label for="aircraft_id">Aircraft:</label>
        <select id="aircraft_id" name="aircraft_id" required>
            <?php foreach ($aircraft as $plane) : ?>
                <option value="<?php echo $plane['aid']; ?>"><?php echo $plane['registration_number']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required><br>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required><br>
        <input type="submit" name="create_reservation" value="Create Reservation">
    </form>
    <h2>Reservations</h2>
    <form method="GET" action="">
        <label for="order_by">Sort By:</label>
        <select id="order_by" name="order_by">
            <option value="rid" <?php echo ($order_by === 'rid') ? 'selected' : ''; ?>>Reservation ID</option>
            <option value="hangar_name" <?php echo ($order_by === 'hangar_name') ? 'selected' : ''; ?>>Hangar</option>
            <option value="aircraft_number" <?php echo ($order_by === 'aircraft_number') ? 'selected' : ''; ?>>Aircraft</option>
            <option value="start_date" <?php echo ($order_by === 'start_date') ? 'selected' : ''; ?>>Start Date</option>
            <option value="end_date" <?php echo ($order_by === 'end_date') ? 'selected' : ''; ?>>End Date</option>
        </select>
        <label for="sort_order">Sort Order:</label>
        <select id="sort_order" name="sort_order">
            <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>
        <input type="submit" name="sort" value="Sort">
    </form>
    <?php if (!empty($reservations)) : ?>
        <table>
            <tr>
                <th><a href="?order_by=rid&sort_order=<?php echo $sort_order; ?>">Reservation ID</a></th>
                <th><a href="?order_by=hangar_name&sort_order=<?php echo $sort_order; ?>">Hangar</a></th>
                <th><a href="?order_by=aircraft_number&sort_order=<?php echo $sort_order; ?>">Aircraft</a></th>
                <th><a href="?order_by=start_date&sort_order=<?php echo $sort_order; ?>">Start Date</a></th>
                <th><a href="?order_by=end_date&sort_order=<?php echo $sort_order; ?>">End Date</a></th>
                <th>Action</th>
            </tr>
            <?php foreach ($reservations as $reservation) : ?>
                <tr>
                    <td><?php echo $reservation['rid']; ?></td>
                    <td><?php echo $reservation['hangar_name']; ?></td>
                    <td><?php echo $reservation['aircraft_number']; ?></td>
                    <td><?php echo $reservation['start_date']; ?></td>
                    <td><?php echo $reservation['end_date']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['rid']; ?>">
                            <input type="submit" name="cancel_reservation" value="Cancel">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p class="no-data-message">No reservations found.</p>
    <?php endif; ?>
</body>
</html>