<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

if (isset($_GET['delete']) && isset($_GET['tid'])) {
    $tid = intval($_GET['tid']);
    $delete_query = "DELETE FROM teachers WHERE tid = $tid";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Teacher deleted successfully!";
    } else {
        $error = "Error deleting teacher: " . mysqli_error($conn);
    }
}

$teachers_query = "SELECT t.*, u.email, u.status as user_status 
                  FROM teachers t 
                  LEFT JOIN users u ON t.user_id = u.id 
                  ORDER BY t.created_at DESC";
$teachers_result = mysqli_query($conn, $teachers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-tie"></i> Manage Teachers</h1>
            <a href="admin_add_teacher.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Teacher
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
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Salary</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($teachers_result) > 0): ?>
                        <?php while ($teacher = mysqli_fetch_assoc($teachers_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($teacher['teacher_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($teacher['fname'] . ' ' . ($teacher['mname'] ? $teacher['mname'] . ' ' : '') . $teacher['lname']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['ph_no'] ?? 'N/A'); ?></td>
                                <td>₹<?php echo number_format($teacher['salary'] ?? 0, 2); ?></td>
                                <td><?php echo $teacher['hire_date'] ? date('d M Y', strtotime($teacher['hire_date'])) : 'N/A'; ?></td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $teacher['user_status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                        color: <?php echo $teacher['user_status'] == 'active' ? '#155724' : '#721c24'; ?>;">
                                        <?php echo ucfirst($teacher['user_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="admin_view_teacher.php?tid=<?php echo $teacher['tid']; ?>" class="btn btn-sm btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="admin_edit_teacher.php?tid=<?php echo $teacher['tid']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=1&tid=<?php echo $teacher['tid']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this teacher?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-user-tie" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3;"></i>
                                No teachers found. <a href="admin_add_teacher.php">Add a new teacher</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

