<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "staff") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$staff_email = $_SESSION["user_email"];
$staff_query = "SELECT s.*, u.email, u.phone 
                FROM staff s 
                JOIN users u ON s.user_id = u.id 
                WHERE u.email = '$staff_email' LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff_data = mysqli_fetch_assoc($staff_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Staff Panel</title>
    <link rel="stylesheet" href="staff.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'staff_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user"></i> My Profile</h1>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #FF9800, #F57C00); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($staff_data['fname'] . ' ' . ($staff_data['mname'] ? $staff_data['mname'] . ' ' : '') . $staff_data['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($staff_data['staff_id']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px; margin-bottom: 10px;">
                        <strong>Designation:</strong><br>
                        <?php echo htmlspecialchars($staff_data['designation'] ?? 'N/A'); ?>
                    </div>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Salary:</strong> ₹<?php echo number_format($staff_data['salary'] ?? 0, 2); ?><br>
                        <strong>Hire Date:</strong> <?php echo $staff_data['hire_date'] ? date('d M Y', strtotime($staff_data['hire_date'])) : 'N/A'; ?>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Staff ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['staff_id']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($staff_data['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Designation</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff_data['designation'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="text" value="₹<?php echo number_format($staff_data['salary'] ?? 0, 2); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <input type="text" value="<?php 
                            $shifts = [];
                            if ($staff_data['shift_morning']) $shifts[] = 'Morning';
                            if ($staff_data['shift_day']) $shifts[] = 'Day';
                            echo !empty($shifts) ? implode(', ', $shifts) : 'N/A';
                        ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input type="text" value="<?php echo $staff_data['hire_date'] ? date('d M Y', strtotime($staff_data['hire_date'])) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($staff_data['s_no']) $address[] = $staff_data['s_no'];
if ($staff_data['s_name']) $address[] = $staff_data['s_name'];
if ($staff_data['district']) $address[] = $staff_data['district'];
if ($staff_data['state']) $address[] = $staff_data['state'];
if ($staff_data['pincode']) $address[] = $staff_data['pincode'];
if ($staff_data['country']) $address[] = $staff_data['country'];
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


