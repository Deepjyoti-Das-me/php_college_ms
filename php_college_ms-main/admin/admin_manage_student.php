<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Handle delete
if (isset($_GET['delete']) && isset($_GET['sid'])) {
    $sid = intval($_GET['sid']);
    $delete_query = "DELETE FROM students WHERE sid = $sid";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Student deleted successfully!";
    } else {
        $error = "Error deleting student: " . mysqli_error($conn);
    }
}

// Get all students with course and user info
$students_query = "SELECT s.*, c.course_code, c.cname, u.email, u.status as user_status 
                   FROM students s 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   LEFT JOIN users u ON s.user_id = u.id 
                   ORDER BY s.created_at DESC";
$students_result = mysqli_query($conn, $students_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-friends"></i> Manage Students</h1>
            <a href="admin_add_student.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Student
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($students_result) > 0): ?>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($student['roll_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($student['fname'] . ' ' . ($student['mname'] ? $student['mname'] . ' ' : '') . $student['lname']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['course_code'] . ' - ' . $student['cname']); ?></td>
                                <td><?php echo htmlspecialchars($student['semester'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $student['user_status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                        color: <?php echo $student['user_status'] == 'active' ? '#155724' : '#721c24'; ?>;">
                                        <?php echo ucfirst($student['user_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="admin_view_student.php?sid=<?php echo $student['sid']; ?>" class="btn btn-sm btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="admin_edit_student.php?sid=<?php echo $student['sid']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=1&sid=<?php echo $student['sid']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this student?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No students found. <a href="admin_add_student.php">Add your first student</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


