<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

if (isset($_POST["update_status"]) && isset($_POST["feedback_id"])) {
    $feedback_id = intval($_POST["feedback_id"]);
    $status = mysqli_real_escape_string($conn, $_POST["status"]);
    
    $update_query = "UPDATE staff_feedback SET status = '$status' WHERE id = $feedback_id";
    if (mysqli_query($conn, $update_query)) {
        $success = "Feedback status updated successfully!";
    } else {
        $error = "Error updating status: " . mysqli_error($conn);
    }
}

$feedback_query = "SELECT sf.*, s.staff_id, s.fname, s.lname, s.email
                   FROM staff_feedback sf
                   JOIN staff s ON sf.sfid = s.sfid
                   ORDER BY sf.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Feedback - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> Staff Feedback</h1>
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
                        <th>Date</th>
                        <th>Staff</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                        <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($feedback['created_at'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($feedback['staff_id']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($feedback['fname'] . ' ' . $feedback['lname']); ?></small>
                                </td>
                                <td><?php echo ucfirst($feedback['feedback_type']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['subject'] ?? 'N/A'); ?></td>
                                <td style="max-width: 300px;">
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($feedback['message']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $feedback['rating'] ? '<i class="fas fa-star" style="color: #FFD700;"></i>' : '<i class="far fa-star" style="color: #ccc;"></i>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php 
                                        echo $feedback['status'] == 'resolved' ? '#d4edda' : 
                                               ($feedback['status'] == 'reviewed' ? '#fff3cd' : '#f8d7da'); 
                                        ?>;
                                        color: <?php 
                                        echo $feedback['status'] == 'resolved' ? '#155724' : 
                                               ($feedback['status'] == 'reviewed' ? '#856404' : '#721c24'); 
                                        ?>;">
                                        <?php echo ucfirst($feedback['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                            <option value="pending" <?php echo $feedback['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="reviewed" <?php echo $feedback['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                            <option value="resolved" <?php echo $feedback['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No feedback received yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


