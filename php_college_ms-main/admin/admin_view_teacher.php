<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$tid = intval($_GET['tid'] ?? 0);
$teacher_query = "SELECT t.*, u.email, u.status as user_status 
                  FROM teachers t 
                  LEFT JOIN users u ON t.user_id = u.id 
                  WHERE t.tid = $tid LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher = mysqli_fetch_assoc($teacher_result);

if (!$teacher) {
    header("Location: admin_manage_teacher.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-eye"></i> View Teacher Details</h1>
            <div>
                <a href="admin_edit_teacher.php?tid=<?php echo $tid; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="admin_manage_teacher.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #2196F3, #1976D2); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($teacher['fname'] . ' ' . ($teacher['mname'] ? $teacher['mname'] . ' ' : '') . $teacher['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($teacher['teacher_id']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Status:</strong><br>
                        <span style="color: <?php echo $teacher['user_status'] == 'active' ? '#4CAF50' : '#F44336'; ?>;">
                            <?php echo strtoupper($teacher['user_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Teacher Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Teacher ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="text" value="₹<?php echo number_format($teacher['salary'] ?? 0, 2); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input type="text" value="<?php echo $teacher['hire_date'] ? date('d M Y', strtotime($teacher['hire_date'])) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($teacher['s_no']) $address[] = $teacher['s_no'];
if ($teacher['s_name']) $address[] = $teacher['s_name'];
if ($teacher['district']) $address[] = $teacher['district'];
if ($teacher['state']) $address[] = $teacher['state'];
if ($teacher['pincode']) $address[] = $teacher['pincode'];
if ($teacher['country']) $address[] = $teacher['country'];
echo !empty($address) ? htmlspecialchars(implode(', ', $address)) : 'N/A';
?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

