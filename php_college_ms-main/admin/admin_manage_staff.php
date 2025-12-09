<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

if (isset($_GET['delete']) && isset($_GET['sfid'])) {
    $sfid = intval($_GET['sfid']);
    $delete_query = "DELETE FROM staff WHERE sfid = $sfid";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Staff deleted successfully!";
    } else {
        $error = "Error deleting staff: " . mysqli_error($conn);
    }
}

$staff_query = "SELECT s.*, u.email, u.status as user_status 
                FROM staff s 
                LEFT JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC";
$staff_result = mysqli_query($conn, $staff_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-users-cog"></i> Manage Staff</h1>
            <a href="admin_add_staff.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Staff
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
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Salary</th>
                        <th>Shift</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($staff_result) > 0): ?>
                        <?php while ($staff = mysqli_fetch_assoc($staff_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($staff['staff_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($staff['fname'] . ' ' . ($staff['mname'] ? $staff['mname'] . ' ' : '') . $staff['lname']); ?></td>
                                <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                <td><?php echo htmlspecialchars($staff['designation'] ?? 'N/A'); ?></td>
                                <td>₹<?php echo number_format($staff['salary'] ?? 0, 2); ?></td>
                                <td>
                                    <?php 
                                    $shifts = [];
                                    if ($staff['shift_morning']) $shifts[] = 'Morning';
                                    if ($staff['shift_day']) $shifts[] = 'Day';
                                    echo !empty($shifts) ? implode(', ', $shifts) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $staff['user_status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                        color: <?php echo $staff['user_status'] == 'active' ? '#155724' : '#721c24'; ?>;">
                                        <?php echo ucfirst($staff['user_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="admin_view_staff.php?sfid=<?php echo $staff['sfid']; ?>" class="btn btn-sm btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="admin_edit_staff.php?sfid=<?php echo $staff['sfid']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=1&sfid=<?php echo $staff['sfid']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this staff member?');">
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
                                No staff found. <a href="admin_add_staff.php">Add your first staff member</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


