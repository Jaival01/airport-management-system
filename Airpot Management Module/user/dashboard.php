<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

$uid = intval($user_id);

// Get user info
$user = $conn->query("SELECT * FROM users WHERE id = $uid")->fetch_assoc();

// Get user bookings count
$bc_row = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE user_id = $uid")->fetch_assoc();
$booking_count = $bc_row['count'];

// Get upcoming flights
$upcoming_flights = $conn->query("
    SELECT 
        b.id AS booking_id,
        b.seat_number,
        b.bags_count,
        f.flight_code,
        f.origin,
        f.destination,
        f.departure_date,
        f.departure_time,
        f.arrival_time,
        f.gate,
        f.status AS flight_status
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    WHERE b.user_id = $uid AND f.departure_date >= CURDATE() 
    ORDER BY f.departure_date ASC, f.departure_time ASC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Airport System</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">My Bookings</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 40px;">
        <!-- Welcome Section -->
        <div class="mb-4">
            <h1>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <p class="text-muted">Manage your flights and bookings</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Bookings</div>
                <div class="stat-value"><?php echo $booking_count; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Upcoming</div>
                <div class="stat-value"><?php echo $upcoming_flights->num_rows; ?></div>
                <div class="stat-label">Upcoming Flights</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #aaaaaa">Tickets</div>
                <div class="stat-value">0</div>
                <div class="stat-label">Active Tickets</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <h3 class="mb-3">Quick Actions</h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="flights.php" class="btn btn-primary">
                    Browse Flights
                </a>
                <a href="bookings.php" class="btn btn-secondary">
                    My Bookings
                </a>
                <a href="profile.php" class="btn btn-secondary">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Upcoming Flights -->
        <div class="card">
            <div class="card-header">
                <h3>Upcoming Flights</h3>
            </div>
            
            <?php if ($upcoming_flights->num_rows > 0): ?>
                <div class="grid grid-1">
                    <?php while($flight = $upcoming_flights->fetch_assoc()): ?>
                        <div class="flight-card">
                            <div class="flight-header">
                                <span class="flight-code"><?php echo $flight['flight_code']; ?></span>
                                <span class="badge <?php echo getStatusBadgeClass($flight['flight_status']); ?>">
                                    <span class="status-dot <?php echo getStatusDotClass($flight['flight_status']); ?>"></span>
                                    <?php echo $flight['flight_status']; ?>
                                </span>
                            </div>
                            
                            <div class="flight-route">
                                <div class="flight-location">
                                    <div class="flight-city"><?php echo explode(' ', $flight['origin'])[0]; ?></div>
                                    <div class="flight-time"><?php echo date('H:i', strtotime($flight['departure_time'])); ?></div>
                                </div>
                                <div class="flight-arrow">?</div>
                                <div class="flight-location">
                                    <div class="flight-city"><?php echo explode(' ', $flight['destination'])[0]; ?></div>
                                    <div class="flight-time"><?php echo date('H:i', strtotime($flight['arrival_time'])); ?></div>
                                </div>
                            </div>
                            
                            <div class="flight-details">
                                <span><?php echo date('d M Y', strtotime($flight['departure_date'])); ?></span>
                                <span>Seat: <?php echo $flight['seat_number']; ?></span>
                                <span>Gate: <?php echo $flight['gate']; ?></span>
                                <span>Bags: <?php echo $flight['bags_count']; ?></span>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <a href="ticket.php?id=<?php echo $flight['booking_id']; ?>" class="btn btn-sm btn-primary">Download Ticket</a>
                                <a href="boarding-pass.php?id=<?php echo $flight['booking_id']; ?>" class="btn btn-sm btn-secondary">Boarding Pass</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted" style="padding: 40px;">
                    <p style="font-size: 2rem; margin-bottom: 10px;">[ No Flights ]</p>
                    <p>No upcoming flights</p>
                    <a href="flights.php" class="btn btn-primary mt-2">Browse Flights</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function getStatusBadgeClass(status) {
            const map = {
                'On-Time': 'badge-success',
                'Boarding': 'badge-info',
                'Delayed': 'badge-warning',
                'Cancelled': 'badge-danger'
            };
            return map[status] || 'badge-info';
        }
        
        function getStatusDotClass(status) {
            const map = {
                'On-Time': 'status-ontime',
                'Boarding': 'status-boarding',
                'Delayed': 'status-delayed',
                'Cancelled': 'status-cancelled'
            };
            return map[status] || 'status-ontime';
        }
    </script>
</body>
</html>

<?php
function getStatusBadgeClass($status) {
    $map = array(
        'On-Time' => 'badge-success',
        'Boarding' => 'badge-info',
        'Delayed' => 'badge-warning',
        'Cancelled' => 'badge-danger'
    );
    return isset($map[$status]) ? $map[$status] : 'badge-info';
}

function getStatusDotClass($status) {
    $map = array(
        'On-Time' => 'status-ontime',
        'Boarding' => 'status-boarding',
        'Delayed' => 'status-delayed',
        'Cancelled' => 'status-cancelled'
    );
    return isset($map[$status]) ? $map[$status] : 'status-ontime';
}
?>
