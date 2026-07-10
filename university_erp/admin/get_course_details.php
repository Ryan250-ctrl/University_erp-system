<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM courses WHERE id = $id");
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <td><?php echo $course['id']; ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-code"></i> Course Code</th>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($course['course_code']); ?></span></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-heading"></i> Course Name</th>
                        <td><strong><?php echo htmlspecialchars($course['course_name']); ?></strong></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-star text-warning"></i> Credits</th>
                        <td><?php echo $course['credits']; ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-user-tie"></i> Lecturer</th>
                        <td><?php echo htmlspecialchars($course['lecturer'] ?: 'Not Assigned'); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-calendar-alt"></i> Created</th>
                        <td><?php echo date('F d, Y H:i', strtotime($course['created_at'] ?? 'now')); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Statistics
                    </div>
                    <div class="card-body">
                        <?php
                        // Get number of students enrolled in this course
                        $students = $conn->query("
                            SELECT COUNT(DISTINCT s.id) as count 
                            FROM students s 
                            JOIN coursework cw ON cw.student_id = s.id 
                            WHERE cw.course_id = " . $course['id']
                        );
                        $student_count = $students->fetch_assoc()['count'];
                        ?>
                        <p><i class="fas fa-user-graduate"></i> Students: <strong><?php echo $student_count; ?></strong></p>
                        
                        <?php
                        // Get number of submissions
                        $submissions = $conn->query("
                            SELECT COUNT(*) as count 
                            FROM coursework 
                            WHERE course_id = " . $course['id']
                        );
                        $submission_count = $submissions->fetch_assoc()['count'];
                        ?>
                        <p><i class="fas fa-file-upload"></i> Submissions: <strong><?php echo $submission_count; ?></strong></p>
                        
                        <?php
                        // Get average marks
                        $avg_marks = $conn->query("
                            SELECT AVG(marks) as avg 
                            FROM coursework 
                            WHERE course_id = " . $course['id'] . " AND marks IS NOT NULL"
                        );
                        $avg = $avg_marks->fetch_assoc()['avg'];
                        ?>
                        <p><i class="fas fa-chart-line"></i> Average Marks: <strong><?php echo $avg ? round($avg, 1) : 'N/A'; ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Course not found!</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request!</div>';
}
?>