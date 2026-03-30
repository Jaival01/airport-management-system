<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'airport_management');

// Site Configuration
define('SITE_NAME', 'Airport Management System');
define('SITE_URL', 'http://localhost/Airpot%20Management%20Module');

// Session Configuration
session_start();

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}

function generateBookingReference() {
    return 'BK' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function generateBagTag() {
    return 'BAG' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');
?>
