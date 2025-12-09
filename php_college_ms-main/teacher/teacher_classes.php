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

// Get classes (subjects) taught by this teacher
$classes_query = "SELECT s.*, c.course_code, c.cname 
                  FROM subjects s 
                  LEFT JOIN courses c ON s.cid = c.cid 
                  WHERE s.tid = $tid 
                  ORDER BY s.subject_name";
$classes_result = mysqli_query($conn, $classes_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chalkboard"></i> My Classes</h1>
            <p>Subjects and courses you are teaching</p>
        </div>

        <div class="form-container">
            <?php if (mysqli_num_rows($classes_result) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Credits</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['subject_code']); ?></td>
                                <td><strong><?php echo htmlspecialchars($class['subject_name']); ?></strong></td>
                                <td><?php echo $class['cname'] ? htmlspecialchars($class['course_code'] . ' - ' . $class['cname']) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($class['semester'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($class['credits'] ?? 'N/A'); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($class['subject_type'] ?? 'Core'); ?></span></td>
                                <td>
                                    <a href="teacher_take_attendance.php?subject_id=<?php echo $class['id']; ?>" class="btn-small btn-primary">
                                        <i class="fas fa-calendar-check"></i> Attendance
                                    </a>
                                    <a href="teacher_assignments.php?subject_id=<?php echo $class['id']; ?>" class="btn-small btn-secondary">
                                        <i class="fas fa-tasks"></i> Assignments
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-chalkboard" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No classes assigned yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

