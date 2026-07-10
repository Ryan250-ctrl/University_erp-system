<?php
require_once '../includes/auth_check.php';
requireAdmin();
require_once '../config/database.php';

// Handle add student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash('student123', PASSWORD_DEFAULT); // Default password
    $full_name = $_POST['full_name'];
    $student_id = $_POST['student_id'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, 'student', ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $full_name);
        $stmt->execute();
        $user_id = $conn->insert_id;
        
        // Insert student
        $stmt = $conn->prepare("INSERT INTO students (user_id, student_id, course, year, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $user_id, $student_id, $course, $year, $semester);
        $stmt->execute();
        
        $conn->commit();
        $success = "Student added successfully! Default password: student123";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error adding student: " . $e->getMessage();
    }
}

// Get all students
$students = $conn->query("
    SELECT s.*, u.full_name, u.email, u.username 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - University ERP</title>
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
                    <i class="fas fa-university"></i> ERP Admin
                </h4>
                <hr>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="#" class="active"><i class="fas fa-user-graduate"></i> Students</a>
                <a href="manage_courses.php"><i class="fas fa-book"></i> Courses</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-user-graduate"></i> Manage Students</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-user-plus"></i> Add Student
                    </button>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['id']; ?></td>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td><?php echo $student['year']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" name="student_id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" name="course" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Year</label>
                                    <input type="number" class="form-control" name="year" min="1" max="4" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <input type="number" class="form-control" name="semester" min="1" max="2" required>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Default password will be set to: student123</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_student" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>