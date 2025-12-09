<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.sid FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$sid = $student_data['sid'];

// Get all grades
$grades_query = "SELECT g.*, sub.subject_code, sub.subject_name, sub.credits, t.fname as t_fname, t.lname as t_lname
                 FROM grades g
                 JOIN subjects sub ON g.subject_id = sub.id
                 LEFT JOIN teachers t ON g.tid = t.tid
                 WHERE g.sid = $sid
                 ORDER BY g.exam_date DESC, sub.subject_code";
$grades_result = mysqli_query($conn, $grades_query);

// Calculate statistics
$stats_query = "SELECT 
                COUNT(*) as total_exams,
                AVG((marks_obtained / total_marks) * 100) as avg_percentage,
                SUM(marks_obtained) as total_obtained,
                SUM(total_marks) as total_max
                FROM grades WHERE sid = $sid AND marks_obtained IS NOT NULL";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
$overall_percentage = $stats['avg_percentage'] ? round($stats['avg_percentage'], 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades & Results - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Grades & Results</h1>
        </div>

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #4CAF50; margin-bottom: 8px;">
                    <?php echo $overall_percentage; ?>%
                </div>
                <div style="color: #666;">Overall Average</div>
            </div>
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #2196F3; margin-bottom: 8px;">
                    <?php echo $stats['total_exams'] ?? 0; ?>
                </div>
                <div style="color: #666;">Total Exams</div>
            </div>
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #FF9800; margin-bottom: 8px;">
                    <?php echo $stats['total_obtained'] ? number_format($stats['total_obtained'], 0) : 0; ?> / <?php echo $stats['total_max'] ? number_format($stats['total_max'], 0) : 0; ?>
                </div>
                <div style="color: #666;">Total Marks</div>
            </div>
        </div>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Exam Type</th>
                        <th>Exam Date</th>
                        <th>Marks Obtained</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($grades_result) > 0): ?>
                        <?php while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                            <?php
                            $percentage = $grade['total_marks'] > 0 ? round(($grade['marks_obtained'] / $grade['total_marks']) * 100, 2) : 0;
                            $grade_color = '#4CAF50';
                            if ($percentage < 50) $grade_color = '#E64A4A';
                            elseif ($percentage < 60) $grade_color = '#FF9800';
                            elseif ($percentage < 70) $grade_color = '#FFC107';
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($grade['subject_code']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($grade['subject_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($grade['exam_type'] ?? 'N/A'); ?></td>
                                <td><?php echo $grade['exam_date'] ? date('d M Y', strtotime($grade['exam_date'])) : 'N/A'; ?></td>
                                <td><strong><?php echo $grade['marks_obtained'] ?? 'N/A'; ?></strong></td>
                                <td><?php echo $grade['total_marks'] ?? 'N/A'; ?></td>
                                <td>
                                    <span style="color: <?php echo $grade_color; ?>; font-weight: 600;">
                                        <?php echo $percentage; ?>%
                                    </span>
                                </td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: <?php echo $grade_color; ?>; color: white;">
                                        <?php echo htmlspecialchars($grade['grade'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($grade['t_fname']) {
                                        echo htmlspecialchars($grade['t_fname'] . ' ' . $grade['t_lname']);
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No grades found yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


