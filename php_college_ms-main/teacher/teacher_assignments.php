<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$teacher_email = $_SESSION["user_email"];
$teacher_query = "SELECT tid FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$tid = $teacher_data['tid'];

// Handle assignment creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_assignment'])) {
    $subject_id = intval($_POST['subject_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    $total_marks = floatval($_POST['total_marks']);
    
    $insert_query = "INSERT INTO assignments (subject_id, tid, title, description, due_date, total_marks, status) 
                    VALUES ($subject_id, $tid, '$title', '$description', '$due_date', $total_marks, 'active')";
    if (mysqli_query($conn, $insert_query)) {
        echo "<div class='alert alert-success'>Assignment created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error creating assignment: " . mysqli_error($conn) . "</div>";
    }
}

// Handle assignment grading
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade_submission'])) {
    $submission_id = intval($_POST['submission_id']);
    $marks = floatval($_POST['marks']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    
    $update_query = "UPDATE assignment_submissions 
                    SET marks_obtained = $marks, feedback = '$feedback', status = 'graded' 
                    WHERE id = $submission_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<div class='alert alert-success'>Submission graded successfully!</div>";
    }
}

// Get all assignments created by this teacher
$assignments_query = "SELECT a.*, s.subject_code, s.subject_name 
                     FROM assignments a 
                     JOIN subjects s ON a.subject_id = s.id 
                     WHERE a.tid = $tid 
                     ORDER BY a.created_at DESC";
$assignments_result = mysqli_query($conn, $assignments_query);

// Get subjects taught by this teacher
$subjects_query = "SELECT * FROM subjects WHERE tid = $tid ORDER BY subject_name";
$subjects_result = mysqli_query($conn, $subjects_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tasks"></i> Assignments</h1>
            <button class="btn-primary" onclick="document.getElementById('createForm').style.display='block'">
                <i class="fas fa-plus"></i> Create Assignment
            </button>
        </div>

        <!-- Create Assignment Form -->
        <div id="createForm" class="form-container" style="display: none; margin-bottom: 24px;">
            <h2>Create New Assignment</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Subject *</label>
                        <select name="subject_id" required>
                            <option value="">-- Select Subject --</option>
                            <?php 
                            mysqli_data_seek($subjects_result, 0);
                            while ($subject = mysqli_fetch_assoc($subjects_result)): 
                            ?>
                                <option value="<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Due Date & Time *</label>
                        <input type="datetime-local" name="due_date" required>
                    </div>
                    <div class="form-group">
                        <label>Total Marks *</label>
                        <input type="number" name="total_marks" step="0.01" min="0" required>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="create_assignment" class="btn-primary">
                        <i class="fas fa-save"></i> Create Assignment
                    </button>
                    <button type="button" class="btn-secondary" onclick="document.getElementById('createForm').style.display='none'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Assignments List -->
        <div class="form-container">
            <?php if (mysqli_num_rows($assignments_result) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Title</th>
                            <th>Due Date</th>
                            <th>Total Marks</th>
                            <th>Status</th>
                            <th>Submissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): 
                            // Count submissions
                            $submissions_query = "SELECT COUNT(*) as count FROM assignment_submissions WHERE assignment_id = " . $assignment['id'];
                            $submissions_result_count = mysqli_query($conn, $submissions_query);
                            $submissions_data = mysqli_fetch_assoc($submissions_result_count);
                            $submission_count = $submissions_data['count'];
                            
                            // Count pending grading
                            $pending_query = "SELECT COUNT(*) as count FROM assignment_submissions 
                                             WHERE assignment_id = " . $assignment['id'] . " 
                                             AND status = 'submitted' AND marks_obtained IS NULL";
                            $pending_result = mysqli_query($conn, $pending_query);
                            $pending_data = mysqli_fetch_assoc($pending_result);
                            $pending_count = $pending_data['count'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['subject_code']); ?></td>
                                <td><strong><?php echo htmlspecialchars($assignment['title']); ?></strong></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($assignment['due_date'])); ?></td>
                                <td><?php echo $assignment['total_marks']; ?></td>
                                <td>
                                    <span class="badge <?php echo $assignment['status'] == 'active' ? 'badge-success' : 'badge-secondary'; ?>">
                                        <?php echo ucfirst($assignment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $submission_count; ?> submitted
                                    <?php if ($pending_count > 0): ?>
                                        <span style="color: #FF9800;">(<?php echo $pending_count; ?> pending)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?view_submissions=<?php echo $assignment['id']; ?>" class="btn-small btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-tasks" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No assignments created yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- View Submissions -->
        <?php if (isset($_GET['view_submissions'])): 
            $assignment_id = intval($_GET['view_submissions']);
            $assignment_query = "SELECT * FROM assignments WHERE id = $assignment_id AND tid = $tid LIMIT 1";
            $assignment_result = mysqli_query($conn, $assignment_query);
            $assignment_data = mysqli_fetch_assoc($assignment_result);
            
            if ($assignment_data):
                $submissions_query = "SELECT asub.*, s.roll_no, s.fname, s.lname 
                                     FROM assignment_submissions asub 
                                     JOIN students s ON asub.sid = s.sid 
                                     WHERE asub.assignment_id = $assignment_id 
                                     ORDER BY asub.submitted_at DESC";
                $submissions_result = mysqli_query($conn, $submissions_query);
        ?>
            <div class="form-container" style="margin-top: 24px;">
                <h2>Submissions for: <?php echo htmlspecialchars($assignment_data['title']); ?></h2>
                <?php if (mysqli_num_rows($submissions_result) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll No</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($submission = mysqli_fetch_assoc($submissions_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['fname'] . ' ' . $submission['lname']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['roll_no']); ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($submission['submitted_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $submission['status'] == 'graded' ? 'success' : ($submission['status'] == 'late' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($submission['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($submission['marks_obtained'] !== null): ?>
                                            <?php echo $submission['marks_obtained']; ?> / <?php echo $assignment_data['total_marks']; ?>
                                        <?php else: ?>
                                            <span style="color: #999;">Not graded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($submission['marks_obtained'] === null): ?>
                                            <button onclick="document.getElementById('gradeForm<?php echo $submission['id']; ?>').style.display='block'" class="btn-small btn-primary">
                                                <i class="fas fa-check"></i> Grade
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($submission['marks_obtained'] === null): ?>
                                <tr id="gradeForm<?php echo $submission['id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <form method="POST" style="padding: 20px; background: #f5f5f5; border-radius: 8px;">
                                            <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label>Marks (out of <?php echo $assignment_data['total_marks']; ?>)</label>
                                                    <input type="number" name="marks" step="0.01" min="0" max="<?php echo $assignment_data['total_marks']; ?>" required>
                                                </div>
                                                <div class="form-group" style="grid-column: 1 / -1;">
                                                    <label>Feedback</label>
                                                    <textarea name="feedback" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" name="grade_submission" class="btn-primary">
                                                <i class="fas fa-save"></i> Submit Grade
                                            </button>
                                            <button type="button" class="btn-secondary" onclick="document.getElementById('gradeForm<?php echo $submission['id']; ?>').style.display='none'">
                                                Cancel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 20px; color: #666;">No submissions yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; endif; ?>
    </main>
</body>
</html>

