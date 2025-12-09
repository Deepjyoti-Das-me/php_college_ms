<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Handle leave approval/rejection
if (isset($_POST["update_leave"]) && isset($_POST["leave_id"])) {
    $leave_id = intval($_POST["leave_id"]);
    $status = mysqli_real_escape_string($conn, $_POST["status"]);
    $remarks = mysqli_real_escape_string($conn, $_POST["remarks"] ?? "");
    
    // Get admin ID
    $aid_query = "SELECT aid FROM admin WHERE user_id = (SELECT id FROM users WHERE email = '" . $_SESSION['user_email'] . "') LIMIT 1";
    $aid_result = mysqli_query($conn, $aid_query);
    $aid_row = mysqli_fetch_assoc($aid_result);
    $approved_by = $aid_row['aid'];
    
    $update_query = "UPDATE staff_leave SET status = '$status', approved_by = $approved_by, remarks = '$remarks' WHERE id = $leave_id";
    if (mysqli_query($conn, $update_query)) {
        $success = "Leave status updated successfully!";
    } else {
        $error = "Error updating leave: " . mysqli_error($conn);
    }
}

$leave_query = "SELECT sl.*, s.staff_id, s.fname, s.lname, s.designation
                FROM staff_leave sl
                JOIN staff s ON sl.sfid = s.sfid
                ORDER BY sl.created_at DESC";
$leave_result = mysqli_query($conn, $leave_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Leave - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-times"></i> Staff Leave Management</h1>
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
                        <th>Staff Name</th>
                        <th>Designation</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($leave_result) > 0): ?>
                        <?php while ($leave = mysqli_fetch_assoc($leave_result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($leave['staff_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($leave['fname'] . ' ' . $leave['lname']); ?></td>
                                <td><?php echo htmlspecialchars($leave['designation'] ?? 'N/A'); ?></td>
                                <td><?php echo ucfirst($leave['leave_type']); ?></td>
                                <td><?php echo date('d M Y', strtotime($leave['start_date'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($leave['end_date'])); ?></td>
                                <td><strong><?php echo $leave['days']; ?> days</strong></td>
                                <td style="max-width: 200px;">
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($leave['reason']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php 
                                        echo $leave['status'] == 'approved' ? '#d4edda' : 
                                               ($leave['status'] == 'rejected' ? '#f8d7da' : '#fff3cd'); 
                                        ?>;
                                        color: <?php 
                                        echo $leave['status'] == 'approved' ? '#155724' : 
                                               ($leave['status'] == 'rejected' ? '#721c24' : '#856404'); 
                                        ?>;">
                                        <?php echo ucfirst($leave['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($leave['status'] == 'pending'): ?>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="leave_id" value="<?php echo $leave['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 6px; font-size: 12px; margin-bottom: 4px; display: block;">
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                            <input type="text" name="remarks" placeholder="Remarks (optional)" style="padding: 4px 8px; border-radius: 6px; font-size: 12px; width: 150px; margin-top: 4px;">
                                            <input type="hidden" name="update_leave" value="1">
                                        </form>
                                    <?php else: ?>
                                        <small style="color: #666;">
                                            <?php echo $leave['remarks'] ? htmlspecialchars($leave['remarks']) : 'No remarks'; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No leave requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


