<?php
require_once '../includes/auth_check.php';
requireStudent();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Get student info
$student_info = $conn->query("
    SELECT s.*, u.full_name, u.email 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE u.id = $user_id
")->fetch_assoc();

$student_id = $student_info['id'];

// Get coursework count
$coursework_count = $conn->query("SELECT COUNT(*) as count FROM coursework WHERE student_id = $student_id")->fetch_assoc()['count'];

// Get marks
$marks_result = $conn->query("
    SELECT c.course_name, cw.marks, cw.status 
    FROM coursework cw 
    JOIN courses c ON cw.course_id = c.id 
    WHERE cw.student_id = $student_id AND cw.marks IS NOT NULL
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - University ERP</title>
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">
                    <i class="fas fa-university"></i> Student Portal
                </h4>
                <hr>
                <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="submit_coursework.php"><i class="fas fa-upload"></i> Submit Coursework</a>
                <a href="view_marks.php"><i class="fas fa-chart-bar"></i> View Marks</a>
                <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
                <a href="library.php"><i class="fas fa-book-open"></i> Library Access</a>
                <a href="request_password.php"><i class="fas fa-key"></i> Request Password</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tachometer-alt"></i> Student Dashboard</h2>
                    <div>
                        <span class="badge bg-success">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name']; ?>
                        </span>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-id-card"></i> <?php echo $student_info['student_id']; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Coursework Submitted</h5>
                                    <h2><?php echo $coursework_count; ?></h2>
                                </div>
                                <i class="fas fa-file-upload text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Total Marks</h5>
                                    <h2>
                                        <?php 
                                        $total = 0;
                                        $count = 0;
                                        while($row = $marks_result->fetch_assoc()) {
                                            $total += $row['marks'];
                                            $count++;
                                        }
                                        echo $count > 0 ? round($total/$count, 1) : 'N/A';
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-chart-line text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Courses Enrolled</h5>
                                    <h2>
                                        <?php 
                                        $course_count = $conn->query("
                                            SELECT COUNT(DISTINCT course_id) as count 
                                            FROM timetable 
                                            WHERE course_id IN (
                                                SELECT course_id FROM timetable 
                                                JOIN courses ON timetable.course_id = courses.id
                                            )
                                        ")->fetch_assoc()['count'];
                                        echo $course_count;
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-book text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-tasks"></i> Quick Actions
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="submit_coursework.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-upload text-primary"></i> Submit New Coursework
                                    </a>
                                    <a href="view_marks.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-eye text-success"></i> View Your Marks
                                    </a>
                                    <a href="timetable.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar text-info"></i> Check Timetable
                                    </a>
                                    <a href="library.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-book-open text-warning"></i> Access Library Resources
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-bell"></i> Notifications
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No new notifications
                                </div>
                                <p class="text-muted small">
                                    <i class="fas fa-clock"></i> Last updated: <?php echo date('F d, Y H:i'); ?>
                                </p>
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