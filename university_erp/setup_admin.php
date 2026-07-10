<?php
require_once 'config/database.php';

// Hash the password using PHP's password_hash function
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = $conn->query("SELECT id FROM users WHERE username = 'admin'");
if ($check->num_rows > 0) {
    // Update existing admin password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->bind_param("s", $hashed_password);
    if ($stmt->execute()) {
        echo "Admin password updated successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    }
} else {
    // Insert new admin
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
    $full_name = 'System Administrator';
    $role = 'admin';
    $email = 'admin@university.edu';
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $role, $full_name);
    
    $username = 'admin';
    if ($stmt->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Also create a test student account
$student_password = password_hash('student123', PASSWORD_DEFAULT);
$check_student = $conn->query("SELECT id FROM users WHERE username = 'student1'");
if ($check_student->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
    $username = 'student1';
    $email = 'student1@university.edu';
    $full_name = 'Test Student';
    $role = 'student';
    $stmt->bind_param("sssss", $username, $email, $student_password, $role, $full_name);
    $stmt->execute();
    $user_id = $conn->insert_id;
    
    // Add student details
    $stmt = $conn->prepare("INSERT INTO students (user_id, student_id, course, year, semester) VALUES (?, ?, ?, ?, ?)");
    $student_id = 'S2024001';
    $course = 'Computer Science';
    $year = 1;
    $semester = 1;
    $stmt->bind_param("issii", $user_id, $student_id, $course, $year, $semester);
    $stmt->execute();
    
    echo "<br>Test student account created!<br>";
    echo "Username: student1<br>";
    echo "Password: student123<br>";
}

echo "<br><a href='index.php'>Go to Login Page</a>";
?>