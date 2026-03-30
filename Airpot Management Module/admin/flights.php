<?php
require_once '../config.php';
requireAdmin();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare("INSERT INTO flights (flight_code, airline, origin, destination, departure_date, departure_time, arrival_date, arrival_time, gate, status, total_seats, available_seats, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssiid", 
                $_POST['flight_code'], $_POST['airline'], $_POST['origin'], $_POST['destination'],
                $_POST['departure_date'], $_POST['departure_time'], $_POST['arrival_date'], $_POST['arrival_time'],
                $_POST['gate'], $_POST['status'], $_POST['total_seats'], $_POST['total_seats'], $_POST['price']
            );
            $stmt->execute();
            header('Location: flights.php?msg=added');
            exit();
        } elseif ($_POST['action'] === 'update') {
            $stmt = $conn->prepare("UPDATE flights SET flight_code=?, airline=?, origin=?, destination=?, departure_date=?, departure_time=?, arrival_date=?, arrival_time=?, gate=?, status=?, price=? WHERE id=?");
            $stmt->bind_param("ssssssssssdi",
                $_POST['flight_code'], $_POST['airline'], $_POST['origin'], $_POST['destination'],
                $_POST['departure_date'], $_POST['departure_time'], $_POST['arrival_date'], $_POST['arrival_time'],
                $_POST['gate'], $_POST['status'], $_POST['price'], $_POST['id']
            );
            $stmt->execute();
            header('Location: flights.php?msg=updated');
            exit();
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM flights WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            header('Location: flights.php?msg=deleted');
            exit();
        }
    }
}

// Get all flights
$flights = $conn->query("SELECT * FROM flights ORDER BY departure_date DESC, departure_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link active">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">Bookings</a></li>
                <li><a href="users.php" class="nav-link">Users</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <h1>Manage Flights</h1>
            <button class="btn btn-primary" onclick="openModal('addFlightModal')">Add New Flight</button>
        </div>

        <div class="card mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search flights..." onkeyup="AirportApp.searchTable('searchInput', 'flightsTable')">
        </div>

        <div class="table-container">
            <table class="table" id="flightsTable">
                <thead>
                    <tr>
                        <th>Flight Code</th>
                        <th>Airline</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Gate</th>
                        <th>Status</th>
                        <th>Seats</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($flight = $flights->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $flight['flight_code']; ?></strong></td>
                            <td><?php echo $flight['airline']; ?></td>
                            <td><?php echo $flight['origin']; ?> ? <?php echo $flight['destination']; ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($flight['departure_date'] . ' ' . $flight['departure_time'])); ?></td>
                            <td><?php echo $flight['gate']; ?></td>
                            <td>
                                <span class="badge <?php echo getStatusClass($flight['status']); ?>">
                                    <?php echo $flight['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $flight['available_seats']; ?>/<?php echo $flight['total_seats']; ?></td>
                            <td>Rs.<?php echo number_format($flight['price'], 2); ?></td>
                            <td>
                                <button class="btn btn-sm btn-ghost" onclick='editFlight(<?php echo json_encode($flight); ?>)'>Edit</button>
                                <button class="btn btn-sm btn-ghost" onclick="deleteFlight(<?php echo $flight['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Flight Modal -->
    <div id="addFlightModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>Add New Flight</h3>
                <button class="modal-close" onclick="closeModal('addFlightModal')">�</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Flight Code *</label>
                        <input type="text" name="flight_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Airline *</label>
                        <input type="text" name="airline" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Origin *</label>
                        <input type="text" name="origin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Destination *</label>
                        <input type="text" name="destination" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departure Date *</label>
                        <input type="date" name="departure_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departure Time *</label>
                        <input type="time" name="departure_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Date *</label>
                        <input type="date" name="arrival_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Time *</label>
                        <input type="time" name="arrival_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gate *</label>
                        <input type="text" name="gate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="On-Time">On-Time</option>
                            <option value="Boarding">Boarding</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Seats *</label>
                        <input type="number" name="total_seats" class="form-control" value="180" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (Rs.) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Add Flight</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addFlightModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Flight Modal -->
    <div id="editFlightModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>Edit Flight</h3>
                <button class="modal-close" onclick="closeModal('editFlightModal')">�</button>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <!-- Same fields as add form -->
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Flight Code *</label>
                        <input type="text" name="flight_code" id="edit_flight_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Airline *</label>
                        <input type="text" name="airline" id="edit_airline" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Origin *</label>
                        <input type="text" name="origin" id="edit_origin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Destination *</label>
                        <input type="text" name="destination" id="edit_destination" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departure Date *</label>
                        <input type="date" name="departure_date" id="edit_departure_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departure Time *</label>
                        <input type="time" name="departure_time" id="edit_departure_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Date *</label>
                        <input type="date" name="arrival_date" id="edit_arrival_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Time *</label>
                        <input type="time" name="arrival_time" id="edit_arrival_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gate *</label>
                        <input type="text" name="gate" id="edit_gate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="On-Time">On-Time</option>
                            <option value="Boarding">Boarding</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (Rs.) *</label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Update Flight</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editFlightModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function editFlight(flight) {
            document.getElementById('edit_id').value = flight.id;
            document.getElementById('edit_flight_code').value = flight.flight_code;
            document.getElementById('edit_airline').value = flight.airline;
            document.getElementById('edit_origin').value = flight.origin;
            document.getElementById('edit_destination').value = flight.destination;
            document.getElementById('edit_departure_date').value = flight.departure_date;
            document.getElementById('edit_departure_time').value = flight.departure_time;
            document.getElementById('edit_arrival_date').value = flight.arrival_date;
            document.getElementById('edit_arrival_time').value = flight.arrival_time;
            document.getElementById('edit_gate').value = flight.gate;
            document.getElementById('edit_status').value = flight.status;
            document.getElementById('edit_price').value = flight.price;
            AirportApp.openModal('editFlightModal');
        }

        function deleteFlight(id) {
            if (confirm('Are you sure you want to delete this flight?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
function getStatusClass($status) {
    $map = array('On-Time' => 'badge-success', 'Boarding' => 'badge-info', 'Delayed' => 'badge-warning', 'Cancelled' => 'badge-danger');
    return isset($map[$status]) ? $map[$status] : 'badge-info';
}
?>
