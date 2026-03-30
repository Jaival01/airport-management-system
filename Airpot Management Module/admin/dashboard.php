<?php
require_once '../config.php';
requireAdmin();

// Get statistics
$total_flights = $conn->query("SELECT COUNT(*) as count FROM flights")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_passengers = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM bookings")->fetch_assoc()['count'];
$revenue_row = $conn->query("SELECT SUM(price) as total FROM bookings WHERE status = 'Confirmed'")->fetch_assoc();
$total_revenue = isset($revenue_row['total']) ? $revenue_row['total'] : 0;

// Today's flights
$today_flights = $conn->query("SELECT COUNT(*) as count FROM flights WHERE departure_date = CURDATE()")->fetch_assoc()['count'];

// Recent bookings
$recent_bookings = $conn->query("
    SELECT b.*, f.flight_code, u.full_name 
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC 
    LIMIT 10
");

// Flight status distribution
$status_dist = $conn->query("
    SELECT status, COUNT(*) as count 
    FROM flights 
    WHERE departure_date >= CURDATE()
    GROUP BY status
");

$status_data = [];
while ($row = $status_dist->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">Bookings</a></li>
                <li><a href="users.php" class="nav-link">Users</a></li>
                <li><a href="staff.php" class="nav-link">Staff</a></li>
                <li><a href="gates.php" class="nav-link">Gates</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="text-muted">Welcome back, <?php echo $_SESSION['full_name']; ?></p>
            </div>
            <div>
                <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Flights</div>
                <div class="stat-value"><?php echo $total_flights; ?></div>
                <div class="stat-label">Total Flights</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Bookings</div>
                <div class="stat-value"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Users</div>
                <div class="stat-value"><?php echo $total_passengers; ?></div>
                <div class="stat-label">Passengers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Revenue</div>
                <div class="stat-value">Rs.<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="grid grid-3 mb-4">
            <div class="stat-card" style="border-left: 4px solid #4caf50;">
                <div class="stat-value"><?php echo $today_flights; ?></div>
                <div class="stat-label">Today's Flights</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #2196f3;">
                <div class="stat-value"><?php echo isset($status_data['On-Time']) ? $status_data['On-Time'] : 0; ?></div>
                <div class="stat-label">On-Time Flights</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f44336;">
                <div class="stat-value"><?php echo (isset($status_data['Delayed']) ? $status_data['Delayed'] : 0) + (isset($status_data['Cancelled']) ? $status_data['Cancelled'] : 0); ?></div>
                <div class="stat-label">Delayed/Cancelled</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <h3 class="mb-3">Quick Actions</h3>
            <div class="grid grid-4">
                <button onclick="window.location.href='flights.php?action=add'" class="btn btn-primary">
                    Add Flight
                </button>
                <button onclick="window.location.href='bookings.php'" class="btn btn-secondary">
                    View Bookings
                </button>
                <button onclick="window.location.href='users.php'" class="btn btn-secondary">
                    Manage Users
                </button>
                <button onclick="exportReport()" class="btn btn-secondary">
                    Export Report
                </button>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Bookings</h3>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>Passenger</th>
                            <th>Flight</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $booking['booking_reference']; ?></strong></td>
                                <td><?php echo $booking['full_name']; ?></td>
                                <td><?php echo $booking['flight_code']; ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $booking['status'] === 'Confirmed' ? 'badge-success' : 'badge-warning'; ?>">
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
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function exportReport() {
            AirportApp.exportTableToCSV('recentBookingsTable', 'airport-report-<?php echo date('Y-m-d'); ?>.csv');
            AirportApp.showNotification('Success', 'Report exported successfully');
        }
    </script>
</body>
</html>
