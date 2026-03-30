<?php
require_once '../config.php';
requireAdmin();

// Get all bookings with details
$bookings = $conn->query("
    SELECT b.*, f.flight_code, f.origin, f.destination, f.departure_date, u.full_name, u.email
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link active">Bookings</a></li>
                <li><a href="users.php" class="nav-link">Users</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <h1>Manage Bookings</h1>
            <button class="btn btn-primary" onclick="AirportApp.exportTableToCSV('bookingsTable', 'bookings-export.csv')">
                Export CSV
            </button>
        </div>

        <div class="card mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search bookings..." onkeyup="AirportApp.searchTable('searchInput', 'bookingsTable')">
        </div>

        <div class="table-container">
            <table class="table" id="bookingsTable">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Passenger</th>
                        <th>Email</th>
                        <th>Flight</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Seat</th>
                        <th>Bags</th>
                        <th>Status</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $booking['booking_reference']; ?></strong></td>
                            <td><?php echo $booking['full_name']; ?></td>
                            <td><?php echo $booking['email']; ?></td>
                            <td><?php echo $booking['flight_code']; ?></td>
                            <td><?php echo explode(' ', $booking['origin'])[0]; ?> ? <?php echo explode(' ', $booking['destination'])[0]; ?></td>
                            <td><?php echo date('d M Y', strtotime($booking['departure_date'])); ?></td>
                            <td><?php echo $booking['seat_number']; ?></td>
                            <td><?php echo $booking['bags_count']; ?></td>
                            <td>
                                <span class="badge <?php echo $booking['status'] === 'Confirmed' ? 'badge-success' : ($booking['status'] === 'Checked-in' ? 'badge-info' : 'badge-warning'); ?>">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </td>
                            <td>Rs.<?php echo number_format($booking['price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>
