<?php
require_once '../includes/auth_check.php';
requireAdmin();
require_once '../config/database.php';

// Get statistics
$student_count = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$submission_count = $conn->query("SELECT COUNT(*) as count FROM coursework")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University ERP</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .sidebar a.active {
            background: #3498db;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card i {
            font-size: 2.5rem;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">
                    <i class="fas fa-university"></i> ERP Admin
                </h4>
                <hr>
                <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage_students.php"><i class="fas fa-user-graduate"></i> Students</a>
                <a href="manage_courses.php"><i class="fas fa-book"></i> Courses</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                    <div>
                        <span class="badge bg-success">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name']; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Total Students</h5>
                                    <h2><?php echo $student_count; ?></h2>
                                </div>
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Total Courses</h5>
                                    <h2><?php echo $course_count; ?></h2>
                                </div>
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Submissions</h5>
                                    <h2><?php echo $submission_count; ?></h2>
                                </div>
                                <i class="fas fa-file-upload"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-clock"></i> Recent Submissions
                            </div>
                            <div class="card-body">
                                <?php
                                $recent = $conn->query("
                                    SELECT c.title, s.student_id, c.submission_date 
                                    FROM coursework c 
                                    JOIN students s ON c.student_id = s.id 
                                    ORDER BY c.submission_date DESC LIMIT 5
                                ");
                                while($row = $recent->fetch_assoc()):
                                ?>
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <span><?php echo $row['title']; ?></span>
                                    <small class="text-muted"><?php echo $row['student_id']; ?></small>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($row['submission_date'])); ?></small>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-exclamation-circle"></i> Quick Actions
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="manage_students.php" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Add New Student
                                    </a>
                                    <a href="manage_courses.php" class="btn btn-success">
                                        <i class="fas fa-plus-circle"></i> Create Course
                                    </a>
                                    <a href="#" class="btn btn-info" onclick="window.print()">
                                        <i class="fas fa-print"></i> Print Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>