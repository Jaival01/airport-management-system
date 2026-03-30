<?php
require_once '../config.php';
requireAdmin();

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                <li><a href="users.php" class="nav-link active">Users</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <div class="d-flex justify-between align-center mb-4">
            <h1>Manage Users</h1>
            <button class="btn btn-primary" onclick="AirportApp.exportTableToCSV('usersTable', 'users-export.csv')">
                Export CSV
            </button>
        </div>

        <div class="card mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search users..." onkeyup="AirportApp.searchTable('searchInput', 'usersTable')">
        </div>

        <div class="table-container">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <?php
                        $booking_count = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = {$user['id']}")->fetch_row()[0];
                        ?>
                        <tr>
                            <td><strong>#<?php echo $user['id']; ?></strong></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['full_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone'] ?: 'N/A'; ?></td>
                            <td>
                                <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-danger' : 'badge-info'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            <td><?php echo $booking_count; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>
