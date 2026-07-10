<?php
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!hasRole('admin')) {
        header("Location: ../student/dashboard.php");
        exit();
    }
}

// Redirect if not student
function requireStudent() {
    requireLogin();
    if (!hasRole('student')) {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}
?>