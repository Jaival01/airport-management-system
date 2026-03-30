<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airport Management System - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">Airport Management</a>
            <ul class="navbar-nav">
                <li><a href="index.php" class="nav-link active">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo isAdmin() ? 'admin' : 'user'; ?>/dashboard.php" class="btn btn-primary btn-sm">Dashboard</a></li>
                    <li><button class="theme-switch" onclick="toggleTheme()" title="Toggle Light / Dark Mode"><span class="theme-switch-track"><span class="theme-switch-thumb"></span></span></button></li>
                <li><a href="logout.php" class="btn btn-outline btn-sm">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-outline btn-sm">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary btn-sm">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 20px;">
                Welcome to Airport Management System
            </h1>
            <p class="text-muted" style="font-size: 1.1rem; max-width: 700px; margin: 0 auto 30px;">
                Experience seamless flight booking, real-time tracking, and modern airport services
            </p>
            <div class="d-flex gap-3 justify-center">
                <?php if (isLoggedIn()): ?>
                    <a href="user/flights.php" class="btn btn-primary btn-lg">
                        Browse Flights
                    </a>
                    <a href="user/bookings.php" class="btn btn-secondary btn-lg">
                        My Bookings
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-lg">
                        Get Started
                    </a>
                    <a href="login.php" class="btn btn-secondary btn-lg">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" style="padding: 60px 0; background: #222222;">
        <div class="container">
            <div class="text-center mb-4">
                <h2 style="font-size: 2.5rem; margin-bottom: 15px;">Powerful Features</h2>
                <p class="text-muted">Everything you need for modern airport management</p>
            </div>

            <div class="grid grid-3">
                <div class="card">
                    <h3>Flight Management</h3>
                    <p class="text-muted">Browse, search, and book flights with real-time availability and status updates</p>
                </div>
                <div class="card">
                    <h3>Digital Tickets</h3>
                    <p class="text-muted">Download printable tickets and boarding passes instantly</p>
                </div>
                <div class="card">
                    <h3>Baggage Tracking</h3>
                    <p class="text-muted">Track your luggage status from check-in to arrival belt</p>
                </div>
                <div class="card">
                    <h3>Booking Management</h3>
                    <p class="text-muted">View and manage all your flight bookings in one place</p>
                </div>
                <div class="card">
                    <h3>Admin Dashboard</h3>
                    <p class="text-muted">Full admin control over flights, users, staff and gates</p>
                </div>
                <div class="card">
                    <h3>Gate Management</h3>
                    <p class="text-muted">Manage airport gates and assign flights to terminals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="padding: 80px 0;">
        <div class="container">
            <div class="grid grid-4">
                <div class="stat-card text-center">
                    <div class="stat-icon" style="color: #aaaaaa" style="margin: 0 auto 15px;">Flights</div>
                    <div class="stat-value"><?php echo $conn->query("SELECT COUNT(*) FROM flights")->fetch_row()[0]; ?>+</div>
                    <div class="stat-label">Active Flights</div>
                </div>
                <div class="stat-card text-center">
                    <div class="stat-icon" style="color: #aaaaaa" style="margin: 0 auto 15px;">Users</div>
                    <div class="stat-value"><?php echo $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0]; ?>+</div>
                    <div class="stat-label">Registered Users</div>
                </div>
                <div class="stat-card text-center">
                    <div class="stat-icon" style="color: #aaaaaa" style="margin: 0 auto 15px;">Bookings</div>
                    <div class="stat-value"><?php echo $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0]; ?>+</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card text-center">
                    <div class="stat-icon" style="color: #aaaaaa" style="margin: 0 auto 15px;">Gates</div>
                    <div class="stat-value"><?php echo $conn->query("SELECT COUNT(*) FROM gates")->fetch_row()[0]; ?>+</div>
                    <div class="stat-label">Airport Gates</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" style="padding: 60px 0; background: #222222;">
        <div class="container">
            <div class="text-center mb-4">
                <h2 style="font-size: 2.5rem; margin-bottom: 15px;">About the System</h2>
                <p class="text-muted" style="max-width: 800px; margin: 0 auto;">
                    Our Airport Management System is designed to provide a seamless experience for both 
                    passengers and airport administrators. With modern design, offline compatibility, and 
                    comprehensive features, we make air travel management effortless.
                </p>
            </div>

            <div class="grid grid-2 mt-4">
                <div class="card">
                    <h3 class="text-red mb-3">For Passengers</h3>
                    <ul style="line-height: 2; list-style: none;">
                        <li>? Easy flight search and booking</li>
                        <li>? Real-time flight status updates</li>
                        <li>? Digital tickets and boarding passes</li>
                        <li>? Baggage tracking system</li>
                        <li>? Multi-language support</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 class="text-red mb-3">For Administrators</h3>
                    <ul style="line-height: 2; list-style: none;">
                        <li>? Complete flight management</li>
                        <li>? User and booking oversight</li>
                        <li>? Staff and gate management</li>
                        <li>? Analytics dashboard</li>
                        <li>? Report generation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 80px 0;">
        <div class="container text-center">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to Get Started?</h2>
            <p class="text-muted mb-4" style="font-size: 1.125rem;">
                Join thousands of users managing their flights efficiently
            </p>
            <?php if (!isLoggedIn()): ?>
                <div class="d-flex gap-3 justify-center">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        Create Account
                    </a>
                    <a href="login.php" class="btn btn-outline btn-lg">
                        Already have an account? Sign in
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer style="padding: 40px 0; background: #222222; border-top: 1px solid #3a3a3a;">
        <div class="container">
            <div class="grid grid-3">
                <div>
                    <h4 class="text-red mb-2">Airport Management</h4>
                    <p class="text-muted" style="font-size: 0.875rem;">
                        Modern, efficient, and user-friendly airport management solution.
                    </p>
                </div>
                <div>
                    <h5 class="mb-2">Quick Links</h5>
                    <ul style="list-style: none; line-height: 2;">
                        <li><a href="index.php" class="text-muted">Home</a></li>
                        <li><a href="login.php" class="text-muted">Login</a></li>
                        <li><a href="register.php" class="text-muted">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="mb-2">Contact</h5>
                    <p class="text-muted" style="font-size: 0.875rem;">Airport Management System<br>For support, contact the admin.</p>
                </div>
            </div>
            <div class="text-center mt-4 pt-4" style="border-top: 1px solid #3a3a3a;">
                <p class="text-muted" style="font-size: 0.875rem;">
                    © <?php echo date('Y'); ?> Airport Management System. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
</body>
</html>
