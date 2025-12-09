<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

if (isset($_POST["add_session"])) {
    $session_name = mysqli_real_escape_string($conn, $_POST["session_name"]);
    $start_date = mysqli_real_escape_string($conn, $_POST["start_date"]);
    $end_date = mysqli_real_escape_string($conn, $_POST["end_date"]);
    
    if (empty($session_name) || empty($start_date) || empty($end_date)) {
        $error = "All fields are required!";
    } else {
        $check_session = "SELECT * FROM sessions WHERE session_name = '$session_name'";
        $result = mysqli_query($conn, $check_session);
        if (mysqli_num_rows($result) > 0) {
            $error = "Session name already exists!";
        } else {
            $sql = "INSERT INTO sessions (session_name, start_date, end_date, status) VALUES (?, ?, ?, 'active')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $session_name, $start_date, $end_date);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Session added successfully!";
                $_POST = array();
            } else {
                $error = "Error adding session: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $delete_query = "DELETE FROM sessions WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        $success = "Session deleted successfully!";
    } else {
        $error = "Error deleting session: " . mysqli_error($conn);
    }
}

$sessions_query = "SELECT * FROM sessions ORDER BY start_date DESC";
$sessions_result = mysqli_query($conn, $sessions_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sessions - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-alt"></i> Manage Sessions</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-plus-circle"></i> Add New Session</h2>
                <form method="POST" action="" class="admin-form">
                    <div class="form-group">
                        <label>Session Name <span class="required">*</span></label>
                        <input type="text" name="session_name" value="<?php echo $_POST['session_name'] ?? ''; ?>" required placeholder="e.g., 2024-2025">
                    </div>
                    <div class="form-group">
                        <label>Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" value="<?php echo $_POST['start_date'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>End Date <span class="required">*</span></label>
                        <input type="date" name="end_date" value="<?php echo $_POST['end_date'] ?? ''; ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_session" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Session
                        </button>
                    </div>
                </form>
            </div>

            <div class="data-table">
                <h2 style="margin-bottom: 20px; padding: 0 16px;"><i class="fas fa-list"></i> All Sessions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Session Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($sessions_result) > 0): ?>
                            <?php while ($session = mysqli_fetch_assoc($sessions_result)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($session['session_name']); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($session['start_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($session['end_date'])); ?></td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                            background: <?php echo $session['status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                            color: <?php echo $session['status'] == 'active' ? '#155724' : '#721c24'; ?>;">
                                            <?php echo ucfirst($session['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?delete=1&id=<?php echo $session['id']; ?>" 
                                           class="btn btn-sm btn-delete"
                                           onclick="return confirm('Are you sure?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                                    No sessions found.
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


