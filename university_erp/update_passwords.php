<?php
require_once 'config/database.php';

// List of usernames and their plain‑text passwords
$users = [
    'admin'    => 'admin123',
    'sarahj'   => 'student123',
    'michaelc' => 'student123',
    'emilyr'   => 'student123',
    'davidk'   => 'student123',
    'lisat'    => 'student123'
];

echo "<h2>Updating Passwords with Correct Hashes</h2>";
foreach ($users as $username => $plain) {
    $hashed = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashed, $username);
    if ($stmt->execute()) {
        echo "✅ Updated password for <strong>$username</strong><br>";
    } else {
        echo "❌ Failed to update $username: " . $conn->error . "<br>";
    }
}
echo "<br><a href='index.php'>Go to Login Page</a>";
?>