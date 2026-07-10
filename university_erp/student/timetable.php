<?php
require_once '../includes/auth_check.php';
requireStudent();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$student_info = $conn->query("SELECT id, course FROM students WHERE user_id = $user_id")->fetch_assoc();
$student_course = $student_info['course'];

// Get timetable for student's course
$timetable = $conn->query("
    SELECT t.*, c.course_name, c.course_code 
    FROM timetable t 
    JOIN courses c ON t.course_id = c.id 
    WHERE c.course_name LIKE '%$student_course%' 
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), start_time
");

// Group by day
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$day_schedule = [];
foreach ($days as $day) {
    $day_schedule[$day] = [];
}
while($row = $timetable->fetch_assoc()) {
    $day_schedule[$row['day_of_week']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - University ERP</title>
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
        .timetable-cell {
            background: white;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .timetable-cell:hover {
            transform: scale(1.02);
            transition: transform 0.3s;
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
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="submit_coursework.php"><i class="fas fa-upload"></i> Submit Coursework</a>
                <a href="view_marks.php"><i class="fas fa-chart-bar"></i> View Marks</a>
                <a href="#" class="active"><i class="fas fa-calendar-alt"></i> Timetable</a>
                <a href="library.php"><i class="fas fa-book-open"></i> Library Access</a>
                <a href="request_password.php"><i class="fas fa-key"></i> Request Password</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2><i class="fas fa-calendar-alt"></i> My Timetable</h2>
                <p class="text-muted">Course schedule for <?php echo htmlspecialchars($student_course); ?></p>
                
                <div class="row">
                    <?php foreach ($days as $day): ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-calendar-day"></i> <?php echo $day; ?>
                                </div>
                                <div class="card-body">
                                    <?php if (count($day_schedule[$day]) > 0): ?>
                                        <?php foreach ($day_schedule[$day] as $class): ?>
                                            <div class="timetable-cell">
                                                <strong><?php echo htmlspecialchars($class['course_code']); ?></strong><br>
                                                <span><?php echo htmlspecialchars($class['course_name']); ?></span><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    <?php echo date('h:i A', strtotime($class['start_time'])); ?> - 
                                                    <?php echo date('h:i A', strtotime($class['end_time'])); ?>
                                                </small><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-door-open"></i> Room: <?php echo htmlspecialchars($class['room']); ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No classes</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>