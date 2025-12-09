<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $delete_query = "DELETE FROM subjects WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Subject deleted successfully!";
    } else {
        $error = "Error deleting subject: " . mysqli_error($conn);
    }
}

$subjects_query = "SELECT s.*, c.course_code, c.cname, t.teacher_id, t.fname as t_fname, t.lname as t_lname
                   FROM subjects s 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   LEFT JOIN teachers t ON s.tid = t.tid 
                   ORDER BY s.subject_code";
$subjects_result = mysqli_query($conn, $subjects_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-open"></i> Manage Subjects</h1>
            <a href="admin_add_subject.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Subject
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
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Type</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Credits</th>
                        <th>Teacher</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($subjects_result) > 0): ?>
                        <?php while ($subject = mysqli_fetch_assoc($subjects_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                        <?php echo htmlspecialchars($subject['subject_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo $subject['course_code'] ? htmlspecialchars($subject['course_code'] . ' - ' . $subject['cname']) : 'N/A'; ?></td>
                                <td><?php echo $subject['semester'] ? $subject['semester'] : 'N/A'; ?></td>
                                <td><?php echo $subject['credits'] ? $subject['credits'] : 'N/A'; ?></td>
                                <td><?php echo $subject['t_fname'] ? htmlspecialchars($subject['teacher_id'] . ' - ' . $subject['t_fname'] . ' ' . $subject['t_lname']) : 'Not Assigned'; ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="admin_edit_subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=1&id=<?php echo $subject['id']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this subject?');">
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
                                No subjects found. <a href="admin_add_subject.php">Add your first subject</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


