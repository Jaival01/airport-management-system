<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name       = sanitize($_POST['full_name']);
    $email           = sanitize($_POST['email']);
    $phone           = sanitize($_POST['phone']);
    $passport_number = sanitize($_POST['passport_number']);
    $date_of_birth   = sanitize($_POST['date_of_birth']);

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, passport_number=?, date_of_birth=? WHERE id=?");
    $stmt->bind_param("sssssi", $full_name, $email, $phone, $passport_number, $date_of_birth, $user_id);

    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Failed to update profile. Please try again.';
    }
}

// Get user data
$user = $conn->query("SELECT * FROM users WHERE id = " . intval($user_id))->fetch_assoc();

// Stats
$total_bookings  = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = $user_id")->fetch_row()[0];
$active_bookings = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = $user_id AND status = 'Confirmed'")->fetch_row()[0];
$days_member     = round((time() - strtotime($user['created_at'])) / (60 * 60 * 24));

// Initials for avatar
$initials = '';
foreach (explode(' ', trim($user['full_name'])) as $part) {
    $initials .= isset($part[0]) ? strtoupper($part[0]) : '';
}
$initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Airport Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 28px;
            background-color: #1f1f1f;
            border-radius: 10px;
            border: 1px solid #2e2e2e;
            margin-bottom: 24px;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #cc0000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            flex-shrink: 0;
            border: 3px solid #3a3a3a;
        }
        .profile-info h2 { margin-bottom: 4px; font-size: 1.4rem; }
        .profile-info p  { color: #888888; font-size: 0.9rem; margin-bottom: 2px; }
        .profile-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            background-color: #3a0000;
            color: #cc0000;
            border: 1px solid #cc0000;
            margin-top: 6px;
        }
        .section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777777;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid #2e2e2e;
        }
        .alert-success {
            background-color: #0f3a0f;
            color: #4caf50;
            border: 1px solid #2a7a2a;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-error {
            background-color: #3a0000;
            color: #f44336;
            border: 1px solid #7a1a1a;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .field-note { font-size: 0.8rem; color: #666666; margin-top: 4px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Airport System</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="flights.php" class="nav-link">Flights</a></li>
                <li><a href="bookings.php" class="nav-link">My Bookings</a></li>
                <li><a href="profile.php" class="nav-link active">Profile</a></li>
                <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="../logout.php" class="btn btn-outline btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px; max-width: 860px; padding-bottom: 60px;">

        <?php if (isset($success)): ?>
            <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar"><?php echo htmlspecialchars($initials); ?></div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars(isset($user['full_name']) ? $user['full_name'] : ''); ?></h2>
                <p><?php echo htmlspecialchars(isset($user['email']) ? $user['email'] : ''); ?></p>
                <p>@<?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : ''); ?></p>
                <span class="profile-badge"><?php echo ucfirst($user['role']); ?></span>
            </div>
            <div style="margin-left: auto; text-align: right;">
                <p class="field-note">Member since</p>
                <p style="font-weight: 600; color: #cccccc; margin-bottom: 0;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-3 mb-4">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $active_bookings; ?></div>
                <div class="stat-label">Active Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $days_member; ?></div>
                <div class="stat-label">Days as Member</div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <p class="section-title">Personal Information</p>
            <form method="POST">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Full Name <span style="color:#cc0000">*</span></label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['full_name']) ? $user['full_name'] : ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : ''); ?>" disabled>
                        <p class="field-note">Username cannot be changed.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:#cc0000">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['email']) ? $user['email'] : ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['phone']) ? $user['phone'] : ''); ?>"
                               placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Passport Number</label>
                        <input type="text" name="passport_number" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['passport_number']) ? $user['passport_number'] : ''); ?>"
                               placeholder="e.g. A1234567">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="<?php echo htmlspecialchars(isset($user['date_of_birth']) ? $user['date_of_birth'] : ''); ?>">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>