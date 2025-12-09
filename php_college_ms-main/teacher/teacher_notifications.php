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

// Get notifications for teachers
$notifications_query = "SELECT n.*, u.full_name as created_by_name 
                        FROM notifications n 
                        LEFT JOIN users u ON n.created_by = u.id 
                        WHERE (n.target_type = 'all' OR n.target_type = 'teacher')
                        AND (n.target_id IS NULL OR n.target_id = $tid)
                        AND n.status = 'active'
                        ORDER BY n.created_at DESC";
$notifications_result = mysqli_query($conn, $notifications_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
        </div>

        <div class="form-container">
            <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                <div style="display: grid; gap: 16px;">
                    <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background: white;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <h3 style="margin: 0; color: #2196F3;">
                                    <i class="fas fa-bullhorn"></i> <?php echo htmlspecialchars($notification['title']); ?>
                                </h3>
                                <span style="color: #999; font-size: 14px;">
                                    <?php echo date('d M Y, h:i A', strtotime($notification['created_at'])); ?>
                                </span>
                            </div>
                            <p style="color: #666; margin: 0; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
                            </p>
                            <?php if ($notification['created_by_name']): ?>
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0; color: #999; font-size: 14px;">
                                    <i class="fas fa-user"></i> By: <?php echo htmlspecialchars($notification['created_by_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-bell" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

