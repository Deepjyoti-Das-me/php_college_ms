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

// Get all students in subjects taught by this teacher
$students_query = "SELECT DISTINCT s.*, c.course_code, c.cname, 
                   COUNT(DISTINCT sub.id) as subject_count
                   FROM students s
                   JOIN subjects sub ON s.cid = sub.cid
                   LEFT JOIN courses c ON s.cid = c.cid
                   WHERE sub.tid = $tid AND s.status = 'active'
                   GROUP BY s.sid
                   ORDER BY s.roll_no";
$students_result = mysqli_query($conn, $students_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-friends"></i> My Students</h1>
            <p>Students enrolled in your subjects</p>
        </div>

        <div class="form-container">
            <?php if (mysqli_num_rows($students_result) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Subjects</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($student['roll_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($student['fname'] . ' ' . ($student['mname'] ? $student['mname'] . ' ' : '') . $student['lname']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['course_code'] . ' - ' . $student['cname']); ?></td>
                                <td><?php echo htmlspecialchars($student['semester'] ?? 'N/A'); ?></td>
                                <td><?php echo $student['subject_count']; ?> subject(s)</td>
                                <td>
                                    <a href="teacher_grades.php?student_id=<?php echo $student['sid']; ?>" class="btn-small btn-primary">
                                        <i class="fas fa-chart-line"></i> View Grades
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-user-friends" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No students assigned to your subjects yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

