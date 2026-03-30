<?php
require_once '../config.php';
requireAdmin();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare("INSERT INTO gates (gate_number, terminal, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['gate_number'], $_POST['terminal'], $_POST['status']);
            $stmt->execute();
            header('Location: gates.php?msg=added');
            exit();
        } elseif ($_POST['action'] === 'update') {
            $stmt = $conn->prepare("UPDATE gates SET gate_number=?, terminal=?, status=? WHERE id=?");
            $stmt->bind_param("sssi", $_POST['gate_number'], $_POST['terminal'], $_POST['status'], $_POST['id']);
            $stmt->execute();
            header('Location: gates.php?msg=updated');
            exit();
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM gates WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            header('Location: gates.php?msg=deleted');
            exit();
        }
    }
}

// Get all gates
$gates = $conn->query("
    SELECT g.*, f.flight_code 
    FROM gates g 
    LEFT JOIN flights f ON g.current_flight_id = f.id 
    ORDER BY g.gate_number ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gates - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">Bookings</a></li>
                <li><a href="users.php" class="nav-link">Users</a></li>
                <li><a href="staff.php" class="nav-link">Staff</a></li>
                <li><a href="gates.php" class="nav-link active">Gates</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <h1>Manage Gates</h1>
            <button class="btn btn-primary" onclick="AirportApp.openModal('addGateModal')">Add Gate</button>
        </div>

        <!-- Gates Grid -->
        <div class="grid grid-4">
            <?php while($gate = $gates->fetch_assoc()): ?>
                <div class="card" style="border-left: 4px solid <?php echo $gate['status'] === 'Available' ? '#22c55e' : ($gate['status'] === 'Occupied' ? '#cc0000' : '#ffc107'); ?>;">
                    <div class="d-flex justify-between align-center mb-2">
                        <h3 class="text-red"><?php echo $gate['gate_number']; ?></h3>
                        <span class="badge <?php echo $gate['status'] === 'Available' ? 'badge-success' : ($gate['status'] === 'Occupied' ? 'badge-danger' : 'badge-warning'); ?>">
                            <?php echo $gate['status']; ?>
                        </span>
                    </div>
                    
                    <p class="text-muted" style="font-size: 0.875rem;">
                        Terminal: <strong><?php echo $gate['terminal']; ?></strong>
                    </p>
                    
                    <?php if ($gate['flight_code']): ?>
                        <p class="text-muted" style="font-size: 0.875rem;">
                            Flight: <strong class="text-red"><?php echo $gate['flight_code']; ?></strong>
                        </p>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-ghost" onclick='editGate(<?php echo json_encode($gate); ?>)'>Edit</button>
                        <button class="btn btn-sm btn-ghost" onclick="deleteGate(<?php echo $gate['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Gate Modal -->
    <div id="addGateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Gate</h3>
                <button class="modal-close" onclick="AirportApp.closeModal('addGateModal')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label class="form-label">Gate Number *</label>
                    <input type="text" name="gate_number" class="form-control" placeholder="e.g., A12" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Terminal *</label>
                    <select name="terminal" class="form-control" required>
                        <option value="T1">Terminal 1 (T1)</option>
                        <option value="T2">Terminal 2 (T2)</option>
                        <option value="T3">Terminal 3 (T3)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Add Gate</button>
                    <button type="button" class="btn btn-secondary" onclick="AirportApp.closeModal('addGateModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Gate Modal -->
    <div id="editGateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Gate</h3>
                <button class="modal-close" onclick="AirportApp.closeModal('editGateModal')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label class="form-label">Gate Number *</label>
                    <input type="text" name="gate_number" id="edit_gate_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Terminal *</label>
                    <select name="terminal" id="edit_terminal" class="form-control" required>
                        <option value="T1">Terminal 1 (T1)</option>
                        <option value="T2">Terminal 2 (T2)</option>
                        <option value="T3">Terminal 3 (T3)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Update Gate</button>
                    <button type="button" class="btn btn-secondary" onclick="AirportApp.closeModal('editGateModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function editGate(gate) {
            document.getElementById('edit_id').value = gate.id;
            document.getElementById('edit_gate_number').value = gate.gate_number;
            document.getElementById('edit_terminal').value = gate.terminal;
            document.getElementById('edit_status').value = gate.status;
            AirportApp.openModal('editGateModal');
        }

        function deleteGate(id) {
            if (confirm('Are you sure you want to delete this gate?')) {
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
