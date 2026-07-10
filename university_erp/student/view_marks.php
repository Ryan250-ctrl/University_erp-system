<?php
require_once '../includes/auth_check.php';
requireStudent();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$student_info = $conn->query("SELECT id FROM students WHERE user_id = $user_id")->fetch_assoc();
$student_id = $student_info['id'];

// Get marks
$marks = $conn->query("
    SELECT c.course_name, cw.title, cw.marks, cw.status, cw.submission_date 
    FROM coursework cw 
    JOIN courses c ON cw.course_id = c.id 
    WHERE cw.student_id = $student_id AND cw.marks IS NOT NULL
    ORDER BY cw.submission_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks - University ERP</title>
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
                <a href="#" class="active"><i class="fas fa-chart-bar"></i> View Marks</a>
                <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
                <a href="library.php"><i class="fas fa-book-open"></i> Library Access</a>
                <a href="request_password.php"><i class="fas fa-key"></i> Request Password</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2><i class="fas fa-chart-bar"></i> My Marks</h2>
                <p class="text-muted">View your academic performance</p>
                
                <?php if ($marks->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Course</th>
                                    <th>Assignment</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_marks = 0;
                                $count = 0;
                                while($row = $marks->fetch_assoc()): 
                                    $total_marks += $row['marks'];
                                    $count++;
                                    $grade = '';
                                    if ($row['marks'] >= 80) $grade = 'A';
                                    else if ($row['marks'] >= 70) $grade = 'B';
                                    else if ($row['marks'] >= 60) $grade = 'C';
                                    else if ($row['marks'] >= 50) $grade = 'D';
                                    else $grade = 'F';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['marks'] >= 50 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $row['marks']; ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $grade == 'A' ? 'bg-success' : 
                                                ($grade == 'B' ? 'bg-info' : 
                                                ($grade == 'C' ? 'bg-warning' : 
                                                ($grade == 'D' ? 'bg-primary' : 'bg-danger')));
                                        ?>">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['submission_date'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="1">Average</th>
                                    <th colspan="1">
                                        <?php 
                                        $average = $count > 0 ? round($total_marks / $count, 1) : 0;
                                        echo $average . '%';
                                        ?>
                                    </th>
                                    <th colspan="3">
                                        <span class="badge <?php echo $average >= 50 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $average >= 70 ? 'Excellent' : ($average >= 50 ? 'Good' : 'Needs Improvement'); ?>
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No marks available yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>