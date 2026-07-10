<?php
require_once 'config/database.php';

$message = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Generate reset token (in production, send email with reset link)
        $token = bin2hex(random_bytes(32));
        
        // Store token in database (you'd add a reset_token column)
        // For demo, we'll just show a message
        $message = "Password reset link has been sent to your email address.";
    } else {
        $error = "Email address not found in our system.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - University ERP</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .reset-container h3 {
            color: #2c3e50;
            text-align: center;
        }
        .btn-reset {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: bold;
        }
        .btn-reset:hover {
            transform: scale(1.02);
        }
        .back-to-login {
            text-align: center;
            margin-top: 15px;
        }
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h3><i class="fas fa-key"></i> Reset Password</h3>
        <p class="text-muted text-center">Enter your email to receive a password reset link</p>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            
            <button type="submit" class="btn btn-reset">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>
        
        <div class="back-to-login">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>