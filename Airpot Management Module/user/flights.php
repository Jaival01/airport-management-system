<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get all available flights
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? sanitize($_GET['date']) : '';

$query = "SELECT * FROM flights WHERE 1=1";

if ($search) {
    $query .= " AND (flight_code LIKE '%$search%' OR origin LIKE '%$search%' OR destination LIKE '%$search%')";
}

if ($date_filter) {
    $query .= " AND departure_date = '$date_filter'";
}

$query .= " ORDER BY departure_date ASC, departure_time ASC";
$flights = $conn->query($query);

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_flight'])) {
    $flight_id = intval($_POST['flight_id']);
    $passenger_name = sanitize($_POST['passenger_name']);
    $passport_number = sanitize($_POST['passport_number']);
    $bags_count = intval($_POST['bags_count']);
    
    // Get flight details
    $flight = $conn->query("SELECT * FROM flights WHERE id = " . intval($flight_id))->fetch_assoc();
    
    if ($flight && $flight['available_seats'] > 0) {
        $booking_ref = generateBookingReference();
        $seat_number = chr(65 + rand(0, 5)) . rand(1, 30); // Random seat
        
        $book_stmt = $conn->prepare("INSERT INTO bookings (booking_reference, user_id, flight_id, passenger_name, passport_number, seat_number, bags_count, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $book_stmt->bind_param("siisssid", $booking_ref, $user_id, $flight_id, $passenger_name, $passport_number, $seat_number, $bags_count, $flight['price']);
        
        if ($book_stmt->execute()) {
            // Update available seats
            $update_stmt = $conn->prepare("UPDATE flights SET available_seats = available_seats - 1 WHERE id = ?");
            $update_stmt->bind_param("i", $flight_id);
            $update_stmt->execute();
            
            // Create baggage entries
            $booking_id = $book_stmt->insert_id;
            for ($i = 0; $i < $bags_count; $i++) {
                $bag_tag = generateBagTag();
                $bag_stmt = $conn->prepare("INSERT INTO baggage (booking_id, bag_tag) VALUES (?, ?)");
                $bag_stmt->bind_param("is", $booking_id, $bag_tag);
                $bag_stmt->execute();
            }
            
            header('Location: bookings.php?msg=booked');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Flights - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Airport System</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link active">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">My Bookings</a></li>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <h1 class="mb-4">Browse Available Flights</h1>
        
        <!-- Search & Filter -->
        <div class="card mb-4">
            <form method="GET" class="grid grid-3">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by code, origin, or destination" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="flights.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Flights List -->
        <?php if ($flights->num_rows > 0): ?>
            <div class="grid grid-1">
                <?php while($flight = $flights->fetch_assoc()): ?>
                    <div class="flight-card">
                        <div class="flight-header">
                            <div>
                                <span class="flight-code"><?php echo $flight['flight_code']; ?></span>
                                <span class="text-muted" style="font-size: 0.875rem; margin-left: 10px;"><?php echo $flight['airline']; ?></span>
                            </div>
                            <span class="badge <?php echo getStatusBadgeClass($flight['status']); ?>">
                                <?php echo $flight['status']; ?>
                            </span>
                        </div>
                        
                        <div class="flight-route">
                            <div class="flight-location">
                                <div class="flight-city"><?php echo $flight['origin']; ?></div>
                                <div class="flight-time"><?php echo date('d M Y, H:i', strtotime($flight['departure_date'] . ' ' . $flight['departure_time'])); ?></div>
                            </div>
                            <div class="flight-arrow">?</div>
                            <div class="flight-location">
                                <div class="flight-city"><?php echo $flight['destination']; ?></div>
                                <div class="flight-time"><?php echo date('d M Y, H:i', strtotime($flight['arrival_date'] . ' ' . $flight['arrival_time'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="flight-details">
                            <span>Gate: <?php echo $flight['gate']; ?></span>
                            <span>Seats available: <?php echo $flight['available_seats']; ?>/<?php echo $flight['total_seats']; ?></span>
                            <span>Rs.<?php echo number_format($flight['price'], 2); ?></span>
                        </div>
                        
                        <div class="mt-3">
                            <?php if ($flight['available_seats'] > 0 && $flight['status'] !== 'Cancelled'): ?>
                                <button onclick="openBookingModal(<?php echo $flight['id']; ?>, '<?php echo $flight['flight_code']; ?>', <?php echo $flight['price']; ?>)" class="btn btn-primary">Book Now</button>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 60px;">
                <p style="font-size: 2rem; margin-bottom: 10px;">[ No Flights ]</p>
                <h3>No flights found</h3>
                <p class="text-muted">Try adjusting your search criteria</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Book Flight <span id="modalFlightCode"></span></h3>
                <button class="modal-close" onclick="closeModal('bookingModal')">�</button>
            </div>
            <form method="POST">
                <input type="hidden" name="flight_id" id="flightId">
                <input type="hidden" name="book_flight" value="1">
                
                <div class="form-group">
                    <label class="form-label">Passenger Name *</label>
                    <input type="text" name="passenger_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Passport Number *</label>
                    <input type="text" name="passport_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Number of Bags</label>
                    <select name="bags_count" class="form-control">
                        <option value="1">1 Bag</option>
                        <option value="2">2 Bags</option>
                        <option value="3">3 Bags</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Total Price</label>
                    <div style="font-size: 1.5rem; color: #cc0000; font-weight: bold;">
                        Rs.<span id="modalPrice">0</span>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('bookingModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function openBookingModal(flightId, flightCode, price) {
            document.getElementById('flightId').value = flightId;
            document.getElementById('modalFlightCode').textContent = flightCode;
            document.getElementById('modalPrice').textContent = price.toFixed(2);
            AirportApp.openModal('bookingModal');
        }
    </script>
</body>
</html>

<?php
function getStatusBadgeClass($status) {
    $map = array('On-Time' => 'badge-success', 'Boarding' => 'badge-info', 'Delayed' => 'badge-warning', 'Cancelled' => 'badge-danger');
    return isset($map[$status]) ? $map[$status] : 'badge-info';
}
?>
