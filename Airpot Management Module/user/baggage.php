<?php
require_once '../config.php';
requireLogin();

$booking_id = 0;
if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
} elseif (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
}

$bid = intval($booking_id);
$uid = intval($_SESSION['user_id']);

// Get booking details
$booking = $conn->query(
    "SELECT b.*, f.flight_code, f.origin, f.destination
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.id = $bid AND b.user_id = $uid"
)->fetch_assoc();

if (!$booking) {
    die('Booking not found');
}

// Get baggage information
$baggage = $conn->query("SELECT * FROM baggage WHERE booking_id = $bid");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Baggage - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Airport System</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">My Bookings</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px; max-width: 900px;">
        <h1 class="mb-4">Baggage Tracking</h1>
        
        <!-- Booking Info -->
        <div class="card mb-4">
            <h3 class="text-red mb-3">Flight Details</h3>
            <div class="grid grid-3">
                <div>
                    <p class="text-muted">Booking Reference</p>
                    <p><strong><?php echo $booking['booking_reference']; ?></strong></p>
                </div>
                <div>
                    <p class="text-muted">Flight</p>
                    <p><strong><?php echo $booking['flight_code']; ?></strong></p>
                </div>
                <div>
                    <p class="text-muted">Route</p>
                    <p><strong><?php echo explode(' ', $booking['origin'])[0]; ?> ? <?php echo explode(' ', $booking['destination'])[0]; ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Baggage Status -->
        <div class="card">
            <div class="card-header">
                <h3>Your Baggage (<?php echo $baggage->num_rows; ?> Bag<?php echo $baggage->num_rows > 1 ? 's' : ''; ?>)</h3>
            </div>
            
            <?php if ($baggage->num_rows > 0): ?>
                <div class="grid grid-1">
                    <?php while($bag = $baggage->fetch_assoc()): ?>
                        <div class="card">
                            <div class="d-flex justify-between align-center mb-3">
                                <div>
                                    <h4>Bag Tag: <?php echo $bag['bag_tag']; ?></h4>
                                    <?php if ($bag['weight']): ?>
                                        <p class="text-muted">Weight: <?php echo $bag['weight']; ?> kg</p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge <?php echo getBaggageStatusClass($bag['status']); ?>">
                                    <?php echo $bag['status']; ?>
                                </span>
                            </div>
                            
                            <!-- Status Timeline -->
                            <div class="baggage-timeline">
                                <div class="timeline-item <?php echo in_array($bag['status'], ['Checked-in', 'Loaded', 'In Transit', 'At Belt']) ? 'active' : ''; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Checked-in</strong>
                                        <p class="text-muted">Bag checked at counter</p>
                                    </div>
                                </div>
                                <div class="timeline-item <?php echo in_array($bag['status'], ['Loaded', 'In Transit', 'At Belt']) ? 'active' : ''; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Loaded</strong>
                                        <p class="text-muted">Bag loaded on aircraft</p>
                                    </div>
                                </div>
                                <div class="timeline-item <?php echo in_array($bag['status'], ['In Transit', 'At Belt']) ? 'active' : ''; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>In Transit</strong>
                                        <p class="text-muted">Bag in transit</p>
                                    </div>
                                </div>
                                <div class="timeline-item <?php echo $bag['status'] === 'At Belt' ? 'active' : ''; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>At Baggage Belt</strong>
                                        <p class="text-muted">Ready for collection</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 40px;">
                    <p style="font-size: 2rem; margin-bottom: 10px;">[ No Baggage ]</p>
                    <p class="text-muted">No baggage registered for this booking</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-3">
            <a href="bookings.php" class="btn btn-secondary">? Back to Bookings</a>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>

<?php
function getBaggageStatusClass($status) {
    $map = array(
        'Checked-in' => 'badge-info',
        'Loaded' => 'badge-success',
        'In Transit' => 'badge-warning',
        'At Belt' => 'badge-success'
    );
    return isset($map[$status]) ? $map[$status] : 'badge-info';
}
?>
