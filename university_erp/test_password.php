<?php
require_once 'config/database.php';

echo "<h3>Password Verification Test</h3>";

// Test 1: Check if we can hash a password
$test_password = 'admin123';
$hashed = password_hash($test_password, PASSWORD_DEFAULT);
echo "Test 1: Hash created: " . $hashed . "<br>";

// Test 2: Verify the hash
if (password_verify($test_password, $hashed)) {
    echo "Test 2: Password verification successful! ✅<br>";
} else {
    echo "Test 2: Password verification failed! ❌<br>";
}

// Test 3: Check admin password in database
$result = $conn->query("SELECT id, username, password FROM users WHERE username = 'admin'");
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "<br>Admin found in database:<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Stored hash: " . $admin['password'] . "<br>";
    
    if (password_verify('admin123', $admin['password'])) {
        echo "✅ Admin password verified successfully!<br>";
    } else {
        echo "❌ Admin password verification failed!<br>";
        echo "Trying to fix...<br>";
        
        // Fix the admin password
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->bind_param("s", $new_hash);
        if ($stmt->execute()) {
            echo "✅ Admin password has been reset successfully!<br>";
            echo "New hash: " . $new_hash . "<br>";
        }
    }
} else {
    echo "Admin user not found in database!<br>";
    echo "Please run setup_admin.php first.<br>";
}

echo "<br><a href='index.php'>Go to Login Page</a>";
?>