<?php
require_once '../includes/auth_check.php';
requireStudent();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$student_info = $conn->query("SELECT id FROM students WHERE user_id = $user_id")->fetch_assoc();
$student_id = $student_info['id'];

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Handle file upload
    $target_dir = "../uploads/coursework/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'zip'];
    
    if ($_FILES["file"]["error"] == 0) {
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Insert into database
                $stmt = $conn->prepare("
                    INSERT INTO coursework (student_id, course_id, title, description, file_path) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iisss", $student_id, $course_id, $title, $description, $file_name);
                
                if ($stmt->execute()) {
                    $success = "Coursework submitted successfully!";
                } else {
                    $error = "Error submitting coursework: " . $conn->error;
                }
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type. Allowed: PDF, DOC, DOCX, TXT, ZIP";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}

// Get courses for dropdown
$courses = $conn->query("SELECT id, course_name FROM courses");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Coursework - University ERP</title>
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
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                <a href="#" class="active"><i class="fas fa-upload"></i> Submit Coursework</a>
                <a href="view_marks.php"><i class="fas fa-chart-bar"></i> View Marks</a>
                <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
                <a href="library.php"><i class="fas fa-book-open"></i> Library Access</a>
                <a href="request_password.php"><i class="fas fa-key"></i> Request Password</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2><i class="fas fa-upload"></i> Submit Coursework</h2>
                <p class="text-muted">Upload your coursework assignment</p>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">
                                <i class="fas fa-book"></i> Select Course
                            </label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Choose a course...</option>
                                <?php while($course = $courses->fetch_assoc()): ?>
                                    <option value="<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading"></i> Title
                            </label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter coursework title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i> Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe your coursework"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                <i class="fas fa-file-upload"></i> Upload File
                            </label>
                            <input type="file" class="form-control" id="file" name="file" required>
                            <small class="text-muted">Allowed formats: PDF, DOC, DOCX, TXT, ZIP (Max 10MB)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Submit Coursework
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                    </form>
                </div>
                
                <!-- Previous Submissions -->
                <div class="mt-4">
                    <h4><i class="fas fa-history"></i> Previous Submissions</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $submissions = $conn->query("
                                    SELECT cw.*, c.course_name 
                                    FROM coursework cw 
                                    JOIN courses c ON cw.course_id = c.id 
                                    WHERE cw.student_id = $student_id 
                                    ORDER BY cw.submission_date DESC
                                ");
                                while($sub = $submissions->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub['title']); ?></td>
                                    <td><?php echo htmlspecialchars($sub['course_name']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($sub['submission_date'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $sub['status'] == 'graded' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo ucfirst($sub['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>