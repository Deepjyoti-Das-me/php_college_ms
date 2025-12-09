<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

if (isset($_GET['delete']) && isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $delete_query = "DELETE FROM courses WHERE cid = $cid";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Course deleted successfully!";
    } else {
        $error = "Error deleting course: " . mysqli_error($conn);
    }
}

$courses_query = "SELECT c.*, 
                  (SELECT COUNT(*) FROM students WHERE cid = c.cid) as student_count
                  FROM courses c 
                  ORDER BY c.created_at DESC";
$courses_result = mysqli_query($conn, $courses_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> Manage Courses</h1>
            <a href="admin_add_course.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Course
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
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Major</th>
                        <th>Duration</th>
                        <th>Semesters</th>
                        <th>Fees</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($courses_result) > 0): ?>
                        <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($course['course_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($course['cname']); ?></td>
                                <td><?php echo htmlspecialchars($course['major'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($course['duration_years']); ?> Years</td>
                                <td><?php echo htmlspecialchars($course['total_semesters']); ?></td>
                                <td>₹<?php echo number_format($course['fees'] ?? 0, 2); ?></td>
                                <td><?php echo $course['student_count']; ?></td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $course['status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                        color: <?php echo $course['status'] == 'active' ? '#155724' : '#721c24'; ?>;">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="admin_view_course.php?cid=<?php echo $course['cid']; ?>" class="btn btn-sm btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="admin_edit_course.php?cid=<?php echo $course['cid']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=1&cid=<?php echo $course['cid']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure? This will delete all related data.');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No courses found. <a href="admin_add_course.php">Add your first course</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


