<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = $conn->query("SELECT id, username, password, role, full_name FROM users WHERE username = '$username' OR email = '$username'");
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        $result->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Airport Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin-top: 80px;">
        <div class="card">
            <div class="text-center mb-4">
                <h1 class="text-red">Airport Management</h1>
                <p class="text-muted">Sign in to your account</p>
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
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Enter username or email"
                        required
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter password"
                        required
                    >
                </div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" class="form-check-input">
                    <label for="remember" class="text-muted" style="font-size: 0.875rem;">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <p class="text-muted">
                    Don't have an account? 
                    <a href="register.php" class="text-red">Sign up</a>
                </p>
                <p class="text-muted">
                    <a href="index.php" class="text-muted">← Back to Home</a>
                </p>
            </div>
            
        </div>
        
        <div class="text-center mt-3 text-muted" style="font-size: 0.875rem;">
            <p>Demo Credentials:</p>
            <p>Admin: <code>admin / admin123</code></p>
        </div>
    </div>
    
    <script src="assets/js/app.js"></script>
</body>
</html>
