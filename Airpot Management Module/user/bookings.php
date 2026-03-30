<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

$uid = intval($user_id);

// Get all user bookings
$bookings = $conn->query("
    SELECT 
        b.id AS booking_id,
        b.booking_reference,
        b.passenger_name,
        b.seat_number,
        b.bags_count,
        b.status AS booking_status,
        b.price,
        b.booking_date,
        f.flight_code,
        f.origin,
        f.destination,
        f.departure_date,
        f.departure_time,
        f.arrival_date,
        f.arrival_time,
        f.gate
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    WHERE b.user_id = $uid 
    ORDER BY f.departure_date DESC, f.departure_time DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Airport System</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link active">My Bookings</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <h1 class="mb-4">My Bookings</h1>
        
        <?php if ($bookings->num_rows > 0): ?>
            <div class="grid grid-1">
                <?php while($booking = $bookings->fetch_assoc()): ?>
                    <div class="card">
                        <div class="d-flex justify-between align-center mb-3">
                            <div>
                                <h3 class="text-red"><?php echo $booking['flight_code']; ?></h3>
                                <p class="text-muted">Booking Ref: <?php echo $booking['booking_reference']; ?></p>
                            </div>
                            <span class="badge <?php echo $booking['booking_status'] === 'Confirmed' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $booking['booking_status']; ?>
                            </span>
                        </div>
                        
                        <div class="grid grid-2 mb-3">
                            <div>
                                <p><strong>From:</strong> <?php echo $booking['origin']; ?></p>
                                <p><strong>Departure:</strong> <?php echo date('d M Y, H:i', strtotime($booking['departure_date'] . ' ' . $booking['departure_time'])); ?></p>
                            </div>
                            <div>
                                <p><strong>To:</strong> <?php echo $booking['destination']; ?></p>
                                <p><strong>Arrival:</strong> <?php echo date('d M Y, H:i', strtotime($booking['arrival_date'] . ' ' . $booking['arrival_time'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-4 mb-3" style="padding: 12px; background: #222222; border-radius: 6px;">
                            <div>
                                <p class="text-muted" style="font-size: 0.875rem;">Passenger</p>
                                <p><strong><?php echo $booking['passenger_name']; ?></strong></p>
                            </div>
                            <div>
                                <p class="text-muted" style="font-size: 0.875rem;">Seat</p>
                                <p><strong><?php echo $booking['seat_number']; ?></strong></p>
                            </div>
                            <div>
                                <p class="text-muted" style="font-size: 0.875rem;">Gate</p>
                                <p><strong><?php echo $booking['gate']; ?></strong></p>
                            </div>
                            <div>
                                <p class="text-muted" style="font-size: 0.875rem;">Bags</p>
                                <p><strong><?php echo $booking['bags_count']; ?></strong></p>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="ticket.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                Download Ticket
                            </a>
                            <a href="boarding-pass.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-secondary btn-sm" target="_blank">
                                Boarding Pass
                            </a>
                            <a href="baggage.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-ghost btn-sm">
                                Track Baggage
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 60px;">
                <p style="font-size: 2rem; margin-bottom: 10px;">[ No Bookings ]</p>
                <h3>No bookings yet</h3>
                <p class="text-muted">Start by browsing available flights</p>
                <a href="flights.php" class="btn btn-primary mt-3">Browse Flights</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>
