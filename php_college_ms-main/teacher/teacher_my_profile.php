<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$teacher_email = $_SESSION["user_email"];
$teacher_query = "SELECT t.*, u.email, u.phone 
                  FROM teachers t 
                  JOIN users u ON t.user_id = u.id 
                  WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user"></i> My Profile</h1>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #2196F3, #1976D2); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($teacher_data['fname'] . ' ' . ($teacher_data['mname'] ? $teacher_data['mname'] . ' ' : '') . $teacher_data['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($teacher_data['teacher_id']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px; margin-bottom: 10px;">
                        <strong>Status:</strong><br>
                        <span style="color: <?php echo $teacher_data['status'] == 'active' ? '#4CAF50' : '#F44336'; ?>;">
                            <?php echo strtoupper($teacher_data['status']); ?>
                        </span>
                    </div>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Hire Date:</strong><br>
                        <?php echo $teacher_data['hire_date'] ? date('d M Y', strtotime($teacher_data['hire_date'])) : 'N/A'; ?>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Teacher ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher_data['teacher_id']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher_data['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher_data['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher_data['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($teacher_data['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher_data['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="text" value="<?php echo $teacher_data['salary'] ? '₹' . number_format($teacher_data['salary'], 2) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($teacher_data['s_no']) $address[] = $teacher_data['s_no'];
if ($teacher_data['s_name']) $address[] = $teacher_data['s_name'];
if ($teacher_data['district']) $address[] = $teacher_data['district'];
if ($teacher_data['state']) $address[] = $teacher_data['state'];
if ($teacher_data['pincode']) $address[] = $teacher_data['pincode'];
if ($teacher_data['country']) $address[] = $teacher_data['country'];
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

