<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "staff") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

$staff_email = $_SESSION["user_email"];
$staff_query = "SELECT sfid FROM staff s JOIN users u ON s.user_id = u.id WHERE u.email = '$staff_email' LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff_row = mysqli_fetch_assoc($staff_result);
$sfid = $staff_row['sfid'];

if (isset($_POST["request_leave"])) {
    $leave_type = mysqli_real_escape_string($conn, $_POST["leave_type"]);
    $start_date = mysqli_real_escape_string($conn, $_POST["start_date"]);
    $end_date = mysqli_real_escape_string($conn, $_POST["end_date"]);
    $reason = mysqli_real_escape_string($conn, $_POST["reason"]);
    
    if (empty($leave_type) || empty($start_date) || empty($end_date) || empty($reason)) {
        $error = "All fields are required!";
    } else {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days = $start->diff($end)->days + 1;
        
        $sql = "INSERT INTO staff_leave (sfid, leave_type, start_date, end_date, days, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssis", $sfid, $leave_type, $start_date, $end_date, $days, $reason);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Leave request submitted successfully!";
            $_POST = array();
        } else {
            $error = "Error submitting leave request: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get leave history
$leave_query = "SELECT * FROM staff_leave WHERE sfid = $sfid ORDER BY created_at DESC";
$leave_result = mysqli_query($conn, $leave_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - Staff Panel</title>
    <link rel="stylesheet" href="staff.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'staff_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-minus"></i> Leave Request</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Request Form -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-plus-circle"></i> Request Leave</h2>
                <form method="POST" action="" class="admin-form">
                    <div class="form-group">
                        <label>Leave Type <span class="required">*</span></label>
                        <select name="leave_type" required>
                            <option value="">Select Type</option>
                            <option value="sick">Sick</option>
                            <option value="casual">Casual</option>
                            <option value="emergency">Emergency</option>
                            <option value="vacation">Vacation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date <span class="required">*</span></label>
                        <input type="date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>Reason <span class="required">*</span></label>
                        <textarea name="reason" rows="4" required placeholder="Please provide a reason for your leave..."><?php echo $_POST['reason'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="request_leave" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leave History -->
            <div class="data-table">
                <h2 style="margin-bottom: 20px; padding: 0 16px;"><i class="fas fa-history"></i> Leave History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($leave_result) > 0): ?>
                            <?php while ($leave = mysqli_fetch_assoc($leave_result)): ?>
                                <tr>
                                    <td><?php echo ucfirst($leave['leave_type']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($leave['start_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($leave['end_date'])); ?></td>
                                    <td><?php echo $leave['days']; ?> days</td>
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
                                        <small style="color: #666;">
                                            <?php echo $leave['remarks'] ? htmlspecialchars($leave['remarks']) : 'No remarks'; ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                    No leave requests yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


