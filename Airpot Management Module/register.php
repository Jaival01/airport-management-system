<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if username or email already exists
        $result = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        
        if ($result->num_rows > 0) {
            $error = 'Username or email already exists';
        } else {
            // Create new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password, $full_name, $phone);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
                // Redirect to login after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        if (isset($stmt)) { $stmt->close(); }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Airport Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin-top: 60px;">
        <div class="card">
            <div class="text-center mb-4">
                <h1 class="text-red">Create Account</h1>
                <p class="text-muted">Join Airport Management System</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 10px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 12px; border-radius: 10px; margin-bottom: 20px;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input 
                            type="text" 
                            name="full_name" 
                            class="form-control" 
                            placeholder="John Doe"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input 
                            type="tel" 
                            name="phone" 
                            class="form-control" 
                            placeholder="+91 9876543210"
                        >
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control" 
                            placeholder="johndoe"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="john@example.com"
                            required
                        >
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Min. 6 characters"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input 
                            type="password" 
                            name="confirm_password" 
                            class="form-control" 
                            placeholder="Confirm password"
                            required
                        >
                    </div>
                </div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" id="terms" class="form-check-input" required>
                    <label for="terms" class="text-muted" style="font-size: 0.875rem;">
                        I agree to the Terms & Conditions
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
            </form>
            
            <div class="text-center mt-3">
                <p class="text-muted">
                    Already have an account? 
                    <a href="login.php" class="text-red">Login</a>
                </p>
                <p class="text-muted">
                    <a href="index.php" class="text-muted">← Back to Home</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/app.js"></script>
</body>
</html>
