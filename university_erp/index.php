<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // First, check if the user exists
    $stmt = $conn->prepare("SELECT id, username, password, role, full_name, email FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Debug info - Remove this in production
        // echo "User found: " . $user['username'] . "<br>";
        // echo "Password in DB: " . $user['password'] . "<br>";
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password! Please try again.";
            // Debug - Remove this in production
            // $error .= " (Password verification failed)";
        }
    } else {
        $error = "User not found! Please check your username or email.";
    }
    
    // If we reach here, login failed
    if (empty($error)) {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University ERP - Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
            border-radius: 50%;
            background: #f8f9fa;
            padding: 10px;
        }
        .login-header h3 {
            color: #2c3e50;
            font-weight: bold;
        }
        .login-header p {
            color: #7f8c8d;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            color: #34495e;
        }
        .input-group-text {
            background: #f8f9fa;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .demo-credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .demo-credentials small {
            display: block;
            color: #6c757d;
        }
        .demo-credentials strong {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="assets/images/university-logo.png" alt="University Logo" onerror="this.src='https://via.placeholder.com/80'">
            <h3>University ERP System</h3>
            <p>Login to access your dashboard</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username or email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="forgot-password">
            <a href="forgot_password.php"><i class="fas fa-key"></i> Forgot Password?</a>
        </div>
        
        <div class="demo-credentials">
            <small><strong>Demo Credentials:</strong></small>
            <small><i class="fas fa-user-shield text-primary"></i> Admin: admin / admin123</small>
            <small><i class="fas fa-user-graduate text-success"></i> Student: student1 / student123</small>
        </div>
    </div>
    
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>