<?php
require_once '../config.php';
requireAdmin();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $staff_id = 'STF' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            $stmt = $conn->prepare("INSERT INTO staff (staff_id, full_name, email, phone, role, department, hired_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $staff_id, $_POST['full_name'], $_POST['email'], $_POST['phone'], $_POST['role'], $_POST['department'], $_POST['hired_date'], $_POST['status']);
            $stmt->execute();
            header('Location: staff.php?msg=added');
            exit();
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM staff WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            header('Location: staff.php?msg=deleted');
            exit();
        }
    }
}

// Get all staff
$staff = $conn->query("SELECT * FROM staff ORDER BY hired_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Admin</title>
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
                <li><a href="staff.php" class="nav-link active">Staff</a></li>
                <li><a href="gates.php" class="nav-link">Gates</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <h1>Manage Staff</h1>
            <button class="btn btn-primary" onclick="AirportApp.openModal('addStaffModal')">Add Staff Member</button>
        </div>

        <div class="card mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search staff..." onkeyup="AirportApp.searchTable('searchInput', 'staffTable')">
        </div>

        <div class="table-container">
            <table class="table" id="staffTable">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Hired Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($member = $staff->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $member['staff_id']; ?></strong></td>
                            <td><?php echo $member['full_name']; ?></td>
                            <td><?php echo $member['email']; ?></td>
                            <td><?php echo $member['phone']; ?></td>
                            <td><?php echo $member['role']; ?></td>
                            <td><?php echo $member['department']; ?></td>
                            <td><?php echo date('d M Y', strtotime($member['hired_date'])); ?></td>
                            <td>
                                <span class="badge <?php echo $member['status'] === 'Active' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $member['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-ghost" onclick="deleteStaff(<?php echo $member['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="addStaffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Staff Member</h3>
                <button class="modal-close" onclick="AirportApp.closeModal('addStaffModal')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone *</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <input type="text" name="role" class="form-control" placeholder="e.g., Ground Staff" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Department *</label>
                        <select name="department" class="form-control" required>
                            <option value="Operations">Operations</option>
                            <option value="Customer Service">Customer Service</option>
                            <option value="Security">Security</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Baggage">Baggage Handling</option>
                            <option value="Administration">Administration</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hired Date *</label>
                        <input type="date" name="hired_date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                    <button type="button" class="btn btn-secondary" onclick="AirportApp.closeModal('addStaffModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function deleteStaff(id) {
            if (confirm('Are you sure you want to remove this staff member?')) {
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
