<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.*, c.course_code, c.cname, c.major, c.duration_years, c.total_semesters, c.fees
                   FROM students s 
                   JOIN users u ON s.user_id = u.id 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);

// Get subjects for this course
$subjects_result = null;
if (!empty($student_data['cid'])) {
    $subjects_query = "SELECT s.*, t.fname as t_fname, t.lname as t_lname, t.teacher_id
                       FROM subjects s
                       LEFT JOIN teachers t ON s.tid = t.tid
                       WHERE s.cid = " . intval($student_data['cid']) . "
                       ORDER BY s.semester, s.subject_code";
    $subjects_result = mysqli_query($conn, $subjects_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> My Courses</h1>
        </div>

        <!-- Course Information Card -->
        <div class="form-container" style="margin-bottom: 24px;">
            <h2 style="margin-bottom: 20px;"><i class="fas fa-graduation-cap"></i> Enrolled Course</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Course Code</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['course_code'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Course Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['cname'] ?? 'No Course Assigned'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Major</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['major'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['duration_years'] ?? 'N/A'); ?> Years" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Total Semesters</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['total_semesters'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Current Semester</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['semester'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" value="<?php echo htmlspecialchars($student_data['year'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Fees</label>
                    <input type="text" value="₹<?php echo number_format($student_data['fees'] ?? 0, 2); ?>" disabled style="background: #f5f5f5;">
                </div>
            </div>
        </div>

        <!-- Subjects List -->
        <div class="data-table">
            <h2 style="margin-bottom: 20px; padding: 0 16px;"><i class="fas fa-book-open"></i> Subjects</h2>
            <table>
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Type</th>
                        <th>Semester</th>
                        <th>Credits</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($subjects_result && mysqli_num_rows($subjects_result) > 0): ?>
                        <?php while ($subject = mysqli_fetch_assoc($subjects_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                        <?php echo htmlspecialchars($subject['subject_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo $subject['semester'] ? $subject['semester'] : 'N/A'; ?></td>
                                <td><?php echo $subject['credits'] ? $subject['credits'] : 'N/A'; ?></td>
                                <td>
                                    <?php 
                                    if ($subject['t_fname']) {
                                        echo htmlspecialchars($subject['teacher_id'] . ' - ' . $subject['t_fname'] . ' ' . $subject['t_lname']);
                                    } else {
                                        echo 'Not Assigned';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No subjects found for this course.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


