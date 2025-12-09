<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "staff") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$staff_email = $_SESSION["user_email"];
$staff_query = "SELECT s.sfid FROM staff s JOIN users u ON s.user_id = u.id WHERE u.email = '$staff_email' LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff_row = mysqli_fetch_assoc($staff_result);
$sfid = $staff_row['sfid'];

// Get notifications for staff
$notifications_query = "SELECT n.*, u.full_name as created_by_name
                        FROM notifications n
                        LEFT JOIN users u ON n.created_by = u.id
                        WHERE (n.target_type = 'all' OR n.target_type = 'staff')
                        AND (n.target_id IS NULL OR n.target_id = $sfid)
                        AND n.status = 'active'
                        ORDER BY n.created_at DESC";
$notifications_result = mysqli_query($conn, $notifications_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Staff Panel</title>
    <link rel="stylesheet" href="staff.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'staff_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
        </div>

        <div style="display: grid; gap: 16px;">
            <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                    <div class="form-container" style="border-left: 4px solid #FF9800;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                            <h3 style="margin: 0; color: #333;">
                                <i class="fas fa-bullhorn" style="color: #FF9800; margin-right: 8px;"></i>
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </h3>
                            <span style="color: #999; font-size: 12px;">
                                <?php echo date('d M Y, h:i A', strtotime($notification['created_at'])); ?>
                            </span>
                        </div>
                        <div style="color: #666; line-height: 1.6; margin-bottom: 8px;">
                            <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
                        </div>
                        <?php if ($notification['created_by_name']): ?>
                            <div style="color: #999; font-size: 12px;">
                                <i class="fas fa-user"></i> From: <?php echo htmlspecialchars($notification['created_by_name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="form-container" style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-bell-slash" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #999; margin-bottom: 10px;">No Notifications</h3>
                    <p style="color: #999;">You don't have any notifications at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>


