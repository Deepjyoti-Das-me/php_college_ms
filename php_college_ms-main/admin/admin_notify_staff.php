<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

if (isset($_POST["send_notification"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $message = mysqli_real_escape_string($conn, $_POST["message"]);
    $target_type = 'staff';
    $target_id = isset($_POST["target_id"]) && $_POST["target_id"] > 0 ? intval($_POST["target_id"]) : null;
    
    if (empty($title) || empty($message)) {
        $error = "Title and message are required!";
    } else {
        $user_id_query = "SELECT id FROM users WHERE email = '" . $_SESSION['user_email'] . "' LIMIT 1";
        $user_result = mysqli_query($conn, $user_id_query);
        $user_row = mysqli_fetch_assoc($user_result);
        $created_by = $user_row['id'];
        
        $sql = "INSERT INTO notifications (title, message, target_type, target_id, created_by, status) 
                VALUES (?, ?, ?, ?, ?, 'active')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $title, $message, $target_type, $target_id, $created_by);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Notification sent successfully to all staff!";
            $_POST = array();
        } else {
            $error = "Error sending notification: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get all staff for individual notification
$staff_query = "SELECT sf.sfid, sf.staff_id, sf.fname, sf.lname, u.email 
                FROM staff sf 
                JOIN users u ON sf.user_id = u.id 
                WHERE u.status = 'active' 
                ORDER BY sf.fname";
$staff_result = mysqli_query($conn, $staff_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notify Staff - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-bell"></i> Notify Staff</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" class="admin-form">
                <div class="form-section">
                    <h3><i class="fas fa-bullhorn"></i> Notification Details</h3>
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Notification Title <span class="required">*</span></label>
                            <input type="text" name="title" value="<?php echo $_POST['title'] ?? ''; ?>" required placeholder="Enter notification title">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Message <span class="required">*</span></label>
                            <textarea name="message" rows="6" required placeholder="Enter notification message..."><?php echo $_POST['message'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Send To</label>
                            <select name="target_id">
                                <option value="0">All Staff Members</option>
                                <?php while ($staff = mysqli_fetch_assoc($staff_result)): ?>
                                    <option value="<?php echo $staff['sfid']; ?>" <?php echo (isset($_POST['target_id']) && $_POST['target_id'] == $staff['sfid']) ? 'selected' : ''; ?>>
                                        <?php echo $staff['staff_id'] . ' - ' . $staff['fname'] . ' ' . $staff['lname']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small>Select "All Staff Members" to send to everyone, or choose a specific staff member</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="send_notification" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Notification
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Form
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>


